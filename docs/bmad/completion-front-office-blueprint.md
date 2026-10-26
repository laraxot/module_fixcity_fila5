---
title: "Blueprint Filament delle funzioni mancanti del front office"
type: architecture
status: draft
updated: 2026-10-26
tags: [bmad, filament, blueprint, front-office, widget, azioni, completamento]
qmd: "blueprint filament front office funzioni mancanti widget livewire schema action azioni proprietario ricerca sito diritti gdpr dati ente reset password bozza wizard valutazione"
related: ["completion-gap-register.md", "completion-architecture.md", "completion-planning.md", "completion-audit/front-office-2-segnalazioni-e-area-personale.md", "completion-audit/front-office-3-wizard-e-autenticazione.md", "filament-blueprint.md"]
---

# Blueprint Filament delle funzioni mancanti del front office

## Fase BMAD

Solutioning. Per ogni funzione che manca dal front office dice come si costruisce alla maniera Filament: quale widget Livewire, quale `Schema`, quali `Action`, quale policy, quale Action Spatie Queueable e quale test. Non e' codice. Le funzioni gia' in lavorazione hanno il numero della storia.

Il modello e' quello gia' usato dal wizard di creazione ([CreateTicketWizardWidget](../CreateTicketWizardWidget.md)): una pagina Folio sottile monta un widget Livewire del modulo che possiede il dominio; il widget usa uno `Schema` e `Action` di Filament; ogni operazione e' una Action Queueable; il tema mette solo la veste.

## Regole comuni

1. Nessun Controller, Service o Job. Un'operazione e' `app/Actions/...` con `QueueableAction` e un metodo `execute()`.
2. Autorizzazione con la policy del modello, sempre lato server. `->visible()` nasconde il pulsante, non protegge.
3. Le proprieta' Livewire sensibili sono `#[Locked]`. Ogni campo `Hidden` lo puo' riscrivere il client.
4. Etichette con `->validationAttribute(__('...'))` in it, en, de, es: nessun nome tecnico dell'attributo nell'errore.
5. Dopo un invio non valido il focus va al primo campo errato, e un avviso `role="alert"` dice quanti sono.
6. Esito con `Filament\Notifications\Notification`, non con testo scritto a mano.
7. Un componente usato in due punti si estrae una volta sola.
8. Un test Pest per ogni combinazione ruolo per stato, scritto prima.

## Funzioni

### F1. Azioni del proprietario sul dettaglio (FO-2-17, A-15)

- Oggi: il proprietario vede la stessa pagina di un estraneo; la sua segnalazione non pubblica da' 404. Il primo passo (vedere la propria) e' DCP-287.
- Dove: widget `OwnerTicketActionsWidget` in `Modules/Fixcity`, montato dal blocco `ticket-detail` solo se `auth()->id() === owner_id`.
- Azioni, ognuna con `->visible()` per stato e policy:
  - Integra: `Action::make('integrate')` con `schema([Textarea::make('note')->required()->maxLength(2000), SpatieMediaLibraryFileUpload::make('photos')->image()->multiple()->maxFiles(3)->maxSize(5120)])`.
  - Ritira: `requiresConfirmation()`, colore danger, motivo con `Select`.
  - Riapri: solo per stati risolto e chiuso entro un numero di giorni da decidere, con motivo obbligatorio.
  - Conferma e valuta: `ToggleButtons::make('score')->options([1,2,3,4,5])->inline()` piu' `Textarea`; usa la `SubmitCitizenTicketRatingAction` che esiste gia'.
- Action Queueable per ognuna (`IntegrateTicketAction`, `WithdrawTicketAction`, `ReopenTicketAction`); le transizioni ammesse stanno in `TicketStatusEnum::allowedTargets()`.
- Decisione necessaria: quali stati ammettono quali azioni (decisione 10).
- Storie proposte: tre, piccole (modifica e integra; ritira; riapri e valuta).

### F2. Dati dell'ente (FO-1-02, FO-1-03, FO-1-04, FO-4-05)

- Fonte: configurazione per comune, `Data` tipizzato, Action di lettura che normalizza. Vedi DCP-289.
- Maschera di modifica per l'ente, da fare dopo la decisione 1: pagina impostazioni nel pannello del modulo scelto con `Section` per Identita', Contatti, Responsabile della protezione dei dati, Social; `TextInput::make('phone')->tel()`, `TextInput::make('pec')->email()`, `Repeater::make('social')->schema([Select::make('network'), TextInput::make('url')->url()])`, salvataggio per comune.
- Regola dei blocchi: dato vuoto, blocco assente. Mai un valore di ripiego che sembri vero.

### F3. Ricerca nel sito (FO-1-01, FO-1-07, DEC-22)

- Rimedio immediato in DCP-288: il campo si chiama `q`.
- Soluzione piena, solo dopo la decisione 3: `SiteSearchWidget` nel modulo Cms con `TextInput::make('q')->type('search')`, risultati in un componente `View`, `SearchSiteAction` che interroga segnalazioni pubbliche, pagine, articoli, eventi e FAQ. Blog e Fixcity si registrano come sorgenti con lo stesso schema a tag di `SitemapPathsContract`. Aperta da `Action::make('search')->modal()`: focus, trap, Esc e ritorno del focus sono nel componente.

### F4. Password dimenticata e reimpostazione (FO-3-01, FO-5-02)

- Widget `RequestPasswordResetWidget` e `ResetPasswordWidget` in `Modules/User`, con `TextInput::make('email')->email()->required()` e `TextInput::make('password')->password()->revealable()->confirmed()`.
- Risposta sempre neutra, uguale per email nota e ignota: «Se l'indirizzo e' registrato, riceverai un'email». Mai la chiave grezza, mai la rivelazione dell'esistenza dell'account.
- Messaggi in `lang/*/passwords.php` del modulo User, in quattro lingue, dentro `role="status"`.
- Pagina Folio nel modulo, non nel tema. Il template di mail e' gia' in DCP-285. Storia: DCP-290.

### F5. Diritti dell'interessato e dati personali (FO-2-29, FO-3-04, A-05, D-01)

- Widget `AccountSettingsWidget` in `Modules/User` con `Section` per Profilo, Password (`->currentPassword()`, `->confirmed()`), Consensi (un `Toggle` per il marketing con salvataggio nel modulo Gdpr) e I tuoi dati.
- Scarica i miei dati: `Action::make('exportData')` che chiama una Action di Gdpr e restituisce un archivio.
- Cancella il mio account: `Action::make('deleteAccount')->requiresConfirmation()->schema([TextInput::make('password')->password()->currentPassword()])` che chiama `DeleteAccountAction`. La policy deve prevedere la capacita' `deleteOwnAccount` (DEC-10).
- Contatti del Comune e del DPO dalla fonte dati dell'ente, non scritti nel tema.
- Dipende da: decisioni 1, 5, 13 e dallo schema (registro delle richieste).

### F6. Informativa e prova del consenso (FO-3-05, A-04)

- Testo dell'informativa in un `RichEditor` del pannello (`PrivacyPolicyResource` di Gdpr) con versione e data.
- Nel wizard e nella registrazione: una `Section` con testo breve e link alla versione completa; al salvataggio una Action `RecordConsentAction` con utente, versione, data, indirizzo.
- Dipende da: schema approvato.

### F7. Bozza del wizard (FO-3-20)

- `Wizard::persistStepInQueryString()` c'e' gia'. Per non perdere i dati: versione veloce con lo stato del form in `sessionStorage` tramite Alpine `$persist`; versione piena con `SaveDraftAction` e tabella delle bozze.
- Dipende da: schema, e dalla decisione su come trattare le foto.

### F8. Geocodifica del campo "Cerca un luogo" (FO-3-07)

- `CoordinatePicker` di Geo collega l'input alla ricerca con `->live(debounce: 500)` e una Action `GeocodeAddressAction` lato server con cache. Cosi' il browser non contatta terzi.
- Dipende da: decisione 14 sul fornitore.

### F9. Valutazione della pagina (B-05, FO-5-17)

- Se si salva: `Action::make('rate')` che chiama `StoreServiceFeedbackAction`; schema per salvare il voto, ordine DOM 1..5, un solo nome accessibile, ringraziamento in `role="status"`.
- Se non si salva: si nasconde il blocco finche' non salva davvero (decisione 15).

### F10. Commenti con risposta e editor Markdown

- Fatto dalla sessione f5 (CMT-022): widget con "Rispondi" per commento fino a profondita' 3, `MarkdownEditor` di Filament, freno a 5 in 60 secondi, 403 lato server. Qui resta solo la decisione sulla visibilita' del thread ai non collegati (decisione 2) e sugli avvisi di risposta (D-CMT-6).

### F11. Notifiche e preferenze

- Le notifiche del ciclo di vita sono fatte (DCP-284). Resta la pagina delle preferenze per canale e lingua, con `Toggle` per tipo di evento, dopo le decisioni DD-1..DD-6.

## Verifica delle funzioni

Ogni funzione ha tre prove: il test Pest della policy per ruolo e stato, il test del widget (`Livewire::test(...)->callAction(...)`) che prova l'effetto e l'assenza di effetti per chi non ha il permesso, e una misura nel browser con l'utente di prova, a tre larghezze, con axe prima e dopo.
