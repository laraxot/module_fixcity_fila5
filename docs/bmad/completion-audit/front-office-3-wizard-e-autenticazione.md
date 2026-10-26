---
title: "Audit front office 3: wizard e autenticazione"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "audit front office 3: wizard e autenticazione audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

# Audit front office, gruppo fo-3-wizard-e-autenticazione

Data: 2026-10-26. Istanza: http://127.0.0.1:8091 (isolata, DB usa e getta). Persone: ospite (nessun cookie), cittadina giulia.bianchi@example.test, cittadini creati da me tramite UI (audit.fo3@example.test, audit.fo3b@example.test).
Strumenti: script Node con Playwright (da `laravel/Themes/Sixteen/node_modules`), uno alla volta, nessun tool MCP browser. Cartella di lavoro: `/tmp/claude-1000/-mnt-nas07-var-www--bases-base-fixcity-fila5/fa79ffe9-c6fb-4eb5-892f-259fcfdd19d2/scratchpad/fo-audit/fo-3-wizard-e-autenticazione/` (33 script `s01..s33`, 119 screenshot in `shots/`, risultati JSON in `out/`). Nel testo `shots/x` significa questa cartella.
Etichette: MISURATO = visto da me con script o screenshot letto; DEDOTTO = dedotto dal codice o da un indizio, non provato.
Nessun file del repository e' stato modificato. Dati creati solo tramite la UI su :8091: 4 segnalazioni della persona audit.fo3 (una e' il doppione dimostrato in FO-3-02), 2 account di prova.

## 1. Inventario di cio' che ho aperto

Conteggio approssimativo dai 33 script: circa 45 URL distinti e 26 flussi, tra desktop 1280 e mobile 375, in italiano e (per login, recupero password, wizard) in en, de, es.

| Flusso o pagina | URL | Persona | Esito HTTP/visivo | Screenshot |
|---|---|---|---|---|
| Wizard da ospite | `/it/tickets/create` | ospite | 302 al login, dopo il login si torna al wizard | `shots/s01-guest-create-d`, `s01-guest-create-m` |
| Login pulito, errori, throttle | `/it/auth/login` | ospite | 200; errori inline; dopo 5 errori "Troppi tentativi... 54 secondi" | `s02-login-clean-d`, `s03-login-empty-d`, `s03-login-wrongpw-d`, `s03-login-bademail-d`, `s03-login-throttle-d` |
| Accedi con Google | `/it/auth/login` poi redirect | ospite | torna al login con avviso rosso (APP_DEBUG=true qui) | `s04-google-click-d` |
| Ritorno dopo login (wizard, pratiche, impostazioni, notifiche) | vari | ospite poi cittadina | ritorno corretto alla pagina richiesta | `s05-after-login-from-create-d`, `s05-after-direct-login-d` |
| Pagine auth da loggata | `/it/auth/login`, `/it/auth/register`, `/it/auth/password/reset` | cittadina | login mostra ancora il form; register 403 grezzo inglese | `s06-loggedin_it_auth_login`, `s06-loggedin_it_auth_register`, `s06-loggedin_it_auth_password_reset` |
| Recupero password | `/it/auth/password/reset` (it, en, de, es) | ospite | 200, non a tema, in inglese, chiavi grezze | `s07-forgot-d`, `s07-forgot-empty-d`, `s07-forgot-known-d`, `s07-forgot-unknown-d`, `s07-forgot-m`, `s07-forgot-en`, `s07-forgot-de`, `s07-forgot-es` |
| Reset con token | `/it/auth/password/{token}`, `/it/auth/password/reset/{token}` | ospite e loggata | primo 200 non a tema; secondo 404 | `s13-reset-token-guest`, `s12-guest-_it_auth_password_reset_token_finto_email_audit_fo3_example_test` |
| Registrazione, errori, esito | `/it/auth/register` (it; en, de, es solo vista) | ospite | 200; 5 scenari di errore; successo = loggata subito su `/it` | `s08-register-d`, `s08-register-m`, `s08-register-en`, `s08-register-de`, `s08-register-es`, `s09-1-empty`, `s09-2-weakpw-noconsent`, `s09-3-mismatch-confirm`, `s09-4-existing-email`, `s09-5-success`, `s10-after-register-d` |
| Registrazione da wizard e ritorno | `/it/tickets/create` -> login -> register | ospite | torna al wizard dopo la registrazione | `s29-register-from-wizard`, `s29-after-register` |
| Impostazioni / privacy | `/it/area-personale/impostazioni` | cittadina | 200, solo lettura | `s11-settings-audit-d`, `s11-settings-audit-m`, `s11-settings-giulia-d`, `s11-settings-giulia-m` |
| URL di profilo, password, account, cancellazione, admin | `/it/profile`, `/it/profile/edit`, `/it/area-personale/{profilo,password,account}`, `/it/account/delete`, `/admin`, `/user/admin` | ospite e cittadina | 404 o 403 grezzo | `s12-audit-_it_account_delete`, `s12-audit-_user_admin`, `s12-guest-_it_account_delete`, `s12-guest-_user_admin` |
| Altre pagine auth | `/it/auth/password/confirm`, `/it/auth/verify`, `/it/auth/email/verify`, `/it/auth/logout` | cittadina | confirm e verify 200 non a tema in inglese; email/verify 404; logout GET pagina di conferma a tema | `s13-confirm-audit`, `s13-confirm-guest`, `s13-verify-audit`, `s13-logout-get-audit`, `s12-audit-_it_auth_logout` |
| Wizard passo 1 (consenso) | `/it/tickets/create` | cittadina | 200; Avanti senza consenso = errore | `s14-wizard-step1-d`, `s14-wizard-step1-m`, `s15-step1-noconsent-d`, `s23-step1-top`, `s23-step1-consent` |
| Wizard passo 2 (dati, luogo, foto) | stesso URL, `?step=` | cittadina | 200; ricerca luogo, geolocalizzazione, upload | `s15-step2-d`, `s16-step2-autocomplete`, `s16-step2-empty-errors-d`, `s16-step2-empty-errors-viewport`, `s17-after-enter-address`, `s17-geo-denied`, `s17-geo-granted`, `s17-map-click`, `s18-after-location`, `s18-map-search-open`, `s18-map-search-result`, `s23-step2-a`, `s23-step2-b`, `s23-map-search-mobile` |
| Upload: valido, troppo grande, formati sbagliati, 6 file, PDF, finto JPG | passo 2 | cittadina | vedi FO-3-19 | `s19-up-valid2`, `s20-up-big`, `s20-up-txt`, `s20-up-svg`, `s20-up-gif`, `s20-up-webp`, `s20-up-fakejpg`, `s20-up-six`, `s20-up-pdf` |
| Wizard completo fino alla conferma, 1280 e 375, e doppio clic | passo 3 e `/it/tickets/confirmation?code=` | cittadina | 4 segnalazioni create (normale, mobile, doppio clic = 2); il doppio clic ne crea 2 per un solo invio | `s21-normal-1280-step2-filled`, `s21-normal-1280-summary`, `s21-normal-1280-confirmation`, `s21-double-1280-summary`, `s21-double-1280-confirmation`, `s21-normal-375-step2-filled`, `s21-normal-375-summary`, `s21-normal-375-confirmation` |
| Dopo la creazione | `/it/area-personale/pratiche`, `/notifiche`, `/it/tickets/track?code=` | cittadina e ospite | pratica in elenco; notifiche vuote; tracciamento 200 | `s22-pratiche-audit`, `s22-pratiche-audit-m`, `s22-notifiche-audit`, `s22-track-audit`, `s22-track-guest`, `s22-confirmation-audit-reload`, `s22-confirmation-guest`, `s32-guest-track` |
| Link diretto al riepilogo e invio a vuoto | `?step=form.riepilogo::...` | cittadina | riepilogo senza dati; Invia riporta al passo 1 | `s24-deeplink-summary`, `s24-deeplink-submit` |
| Wizard in en, de, es | `/{lang}/tickets/create` | cittadina | tradotto tranne dettagli in FO-3-15 | `s25-en-step1`, `s25-en-step2`, `s25-de-step1`, `s25-de-step2`, `s25-es-step1`, `s25-es-step2` |
| Tastiera passo 1 e dopo | `/it/tickets/create` | cittadina | vedi FO-3-23 | `s26-after-enter-step` |
| Sessione scaduta (cookie cancellati; logout in altra scheda), anche con Invia | wizard | cittadina | Avanti prosegue; Invia fallisce in silenzio | `s27-expired-click-next`, `s27-after-logout`, `s27-after-logout-next`, `s28-clearCookies`, `s28-logoutOtherTab` |
| Registrazione da wizard, menu utente, Esci, Indietro | header | cittadina | Esci subito, atterra su `/it`; Indietro porta al login | `s30-user-menu`, `s30-after-esci`, `s30-after-back` |
| Login e recupero in en, de, es | `/{lang}/auth/login`, `/{lang}/auth/password/reset` | ospite | messaggi diversi per lingua | `s31-login-en`, `s31-login-de`, `s31-login-es` |
| Conferma: valutazione e stampa | `/it/tickets/confirmation?code=` | cittadina e ospite | 200; widget stelle senza invio; stampa uguale allo schermo | `s32-confirm-after-star`, `s32-confirm-print` |
| Confronto produzione (solo stato HTTP) | `https://fixcity.eu/it/...` | ospite | `/it` 200, `/it/auth/login` 200, `/it/tickets/create` 302 al login, `/it/auth/password/reset` 200, impostazioni 302 | nessuno |

Prima verifica ambientale: alle 12:23 una sessione parallela stava modificando `Modules/User/app/Providers/UserServiceProvider.php` (`Class Modules\User\Providers\RateLimiter not found`, DCP-282). Ha causato un 500 spurio sull'upload PDF; rifatto dopo la correzione: upload PDF OK. Non e' un difetto del PDF.

## 2. Difetti e funzioni mancanti (tabella)

Gravita': BLOCCA = blocca il go-live, QUALITA' = da correggere prima del go-live se c'e' tempo, RIFINITURA = lucidatura. Taglia: S fino a mezza giornata, M 1-3 giorni, L oltre.

| ID | Pagina | Persona | Tipo | Gravita' | Taglia | Collegamenti |
|---|---|---|---|---|---|---|
| FO-3-01 | `/{lang}/auth/password/reset`, `/auth/password/{token}` | ospite | difetto + funzione incompleta | BLOCCA | M | D-05, D-31 |
| FO-3-02 | Wizard passo 3, Invia | cittadina | difetto | BLOCCA | S | A-26 |
| FO-3-03 | `/it/tickets/confirmation` | cittadina | difetto (promessa falsa) | BLOCCA | M | A-01, D-15 |
| FO-3-04 | `/it/area-personale/impostazioni` e footer | cittadina | funzione mancante | BLOCCA | L | D-01, D-09, D-10 |
| FO-3-05 | Wizard passo 1 | cittadina | difetto + funzione mancante | BLOCCA | M | A-03, A-04 |
| FO-3-06 | Wizard Invia con sessione scaduta | cittadina | difetto | BLOCCA | S | nuovo |
| FO-3-07 | Wizard passo 2, "Cerca un luogo" | cittadina | difetto | BLOCCA | S | nuovo |
| FO-3-08 | Registrazione, verifica email | ospite | difetto | QUALITA' | M | D-03, D-04 |
| FO-3-09 | Esito registrazione e header | ospite | difetto | QUALITA' | S | nuovo |
| FO-3-10 | `/it/auth/register` aspetto | ospite | difetto | QUALITA' | S | nuovo |
| FO-3-11 | Messaggi di errore registrazione | ospite | difetto | QUALITA' | S | D-11, D-31, D-33 |
| FO-3-12 | Google OAuth | ospite | difetto + decisione | QUALITA' | S | D-07 |
| FO-3-13 | Pagine di sistema in inglese o non a tema | ospite e cittadina | difetto | QUALITA' | M | nuovo |
| FO-3-14 | Login: focus, contrasto, toggle password, utente gia' loggato | ospite e cittadina | difetto | QUALITA' | S | nuovo |
| FO-3-15 | Lingue en, de, es di auth e wizard | ospite e cittadina | difetto | QUALITA' | S | D-34 |
| FO-3-16 | Terze parti (OSM, Nominatim) prima del consenso | cittadina | difetto | QUALITA' | M | nuovo |
| FO-3-17 | Wizard passo 2, errori e focus | cittadina | difetto | QUALITA' | S | nuovo |
| FO-3-18 | Geolocalizzazione negata, messaggio tagliato | cittadina | difetto | RIFINITURA | S | nuovo |
| FO-3-19 | Allegati: HEIC, camera, testo, EXIF | cittadina | difetto | QUALITA' | M | A-27 |
| FO-3-20 | Nessuna bozza, stepper incoerente | cittadina | funzione mancante | QUALITA' | M | nuovo |
| FO-3-21 | Riepilogo: testo spezzato, email doppia | cittadina | difetto | QUALITA' | S | nuovo |
| FO-3-22 | Mobile 375 | cittadina | difetto | QUALITA' | S | nuovo |
| FO-3-23 | Tastiera e focus nel wizard | cittadina | difetto | QUALITA' | S | nuovo |
| FO-3-24 | Link morti in "Contatta il comune" | tutte | difetto | QUALITA' | S | nuovo |
| FO-3-25 | Conferma: valutazione, copia/stampa, banda vuota | cittadina | difetto + funzione mancante | QUALITA' | M | nuovo |
| FO-3-26 | Livewire senza cookie di sessione risponde 200 | cittadina | da verificare (sicurezza) | QUALITA' | S | nuovo |
| FO-3-27 | Menu attivo e breadcrumb su `/create` | tutte | difetto | RIFINITURA | S | nuovo |
| FO-3-28 | Logout | cittadina | difetto | RIFINITURA | S | nuovo |
| FO-3-29 | Login: aspetto campi e link sotto Google | ospite | difetto | RIFINITURA | S | nuovo |
| FO-3-30 | Marker `|---LINE:n---|` e avviso asset Livewire | tutte | ambientale | RIFINITURA | S | nota di memoria |

Conteggio: BLOCCA 7, QUALITA' 18, RIFINITURA 5. Totale 30.

## 3. Dettaglio dei difetti

Per ogni voce: evidenza, e "modo Filament" (Filament 5, Livewire 4, Schemas). Regole del progetto rispettate: logica in Actions, form FO come Widget/Schema di modulo montati in Folio/Volt, mai file nuovi in `Themes/*/resources/views/pages`.

### FO-3-01 Recupero password: pagina non a tema, in inglese, chiavi grezze

- Evidenza MISURATO: `/it/auth/password/reset` e' una pagina senza header e footer del sito, con logo generico, testo in inglese in tutte e 4 le lingue ("Reset password", "Email address", "Send password reset link", "Or return to login"). Email sconosciuta: in it, de ed es compare la chiave grezza `passwords.user`; in en compare "We can't find a user with that email address." (quindi si scopre se un indirizzo e' registrato). Successo: compare la sola chiave `passwords.sent`, senza dire "controlla la posta". Email vuota o non valida: nessun messaggio visibile. La pagina con token `/it/auth/password/{token}` e' pure in inglese e non a tema e il campo email NON e' precompilato da `?email=`. `/it/auth/password/reset/{token}` risponde 404 (l'URL giusto e' `/auth/password/{token}`). Screenshot: `shots/s07-forgot-d`, `s07-forgot-unknown-d`, `s07-forgot-known-d`, `s07-forgot-empty-d`, `s07-forgot-de`, `s13-reset-token-guest`.
- DEDOTTO: contenuto della mail di reset (D-05) e arrivo reale della mail non provabili (MAIL_MAILER=array).
- Modo Filament: widget Livewire `RequestPasswordResetWidget` nel modulo User, con `Filament\Schemas\Schema` e `Forms\Components\TextInput::make('email')->email()->required()->autofocus()`, `Actions\Action::make('send')` che chiama una Action (Spatie QueueableAction) `SendPasswordResetLinkAction`. Risposta sempre neutra e uguale per email nota o ignota ("Se l'indirizzo e' registrato, riceverai un'email entro pochi minuti"), mostrata con `Filament\Notifications\Notification::make()->success()` o con un `Schemas\Components\Text` nel widget. Stesso schema per `ResetPasswordWidget` (con `TextInput::make('password')->password()->revealable()->confirmed()` e `Password::default()`), email precompilata ma `readOnly`. Le pagine Folio restano in `Modules/User/resources/views/pages/auth/password/` ma usano il layout del tema (`pub_theme::layouts.app`), niente file nuovi nel tema. Stringhe in `Modules/User/lang/{it,en,de,es}/auth.php`.

### FO-3-02 Doppio clic su "Invia" crea due segnalazioni

- Evidenza MISURATO: pulsante `x-on:click="$wire.submit()"` senza `wire:loading` ne' `disabled`. Doppio clic: creati TCK-9N8ZUFZGJD1GOZKU e TCK-3NEVCSMQ4Q7FEUQR, stesso titolo, a 1 secondo di distanza; l'utente vede una sola conferma e non sa di avere un doppione in elenco. Screenshot: `shots/s21-double-1280-summary`, `s21-double-1280-confirmation`.
- DEDOTTO: nessun limite di frequenza sulla creazione (A-26).
- Modo Filament: sul pulsante di invio `Actions\Action::make('submit')->action('save')` con `->disabled(fn () => $this->isSubmitting)` o semplicemente `wire:loading.attr="disabled"` e `wire:target="save"`; sul server un `#[Locked]` token di invio (uuid generato al mount, salvato in cache per 10 minuti) controllato in `CreateTicketAction` (idempotenza), piu' `RateLimiter::attempt('ticket-create:'.$userId, 5, ..., 600)` in una Action. La proprieta' del codice e' `Modules/Fixcity` (widget e Action).

### FO-3-03 La conferma promette un'email che non parte

- Evidenza MISURATO: la pagina dice "Abbiamo inviato il riepilogo all'email: <indirizzo>". Dopo la creazione la tabella `notifications` ha 0 righe in entrambi i DB scratch e `ticket_activities` 0 righe; `/it/area-personale/notifiche` mostra "Non hai ancora ricevuto notifiche". Il listener `TicketCreatedListener` imposta solo lo stato. Screenshot: `shots/s21-normal-1280-confirmation`, `s22-notifiche-audit`.
- DEDOTTO: nessuna mail e' partita (il mailer qui e' `array`, non osservabile), ma non c'e' codice che la invii (A-01).
- Modo Filament: Notification Laravel `TicketReceivedNotification` (canale `mail` e `database`) inviata da una Action nel listener registrato; nel pannello l'elenco usa `Filament\Notifications` con `databaseNotifications()`. Finche' non e' vero, la frase va tolta dalla vista della conferma (`BuildTicketConfirmationDataAction` e vista `pub_theme::...confirmation`). Titolare: Modulo Notify con Fixcity.

### FO-3-04 Nessuna gestione dei propri dati: modifica profilo, password, cancellazione, consensi, esportazione, contatti

- Evidenza MISURATO: `/it/area-personale/impostazioni` contiene solo la preferenza di notifica, "I tuoi dati" in sola lettura ("Da questa pagina non puoi modificarli") e un elenco dei diritti GDPR con la frase "Oggi l'area personale non permette di scaricare o cancellare i dati in autonomia: la richiesta si invia all'ente" e il bottone "Vai ai contatti del Comune" che porta a `#footer`, dove "CONTATTI" non ha alcun contatto vero (solo FAQ, privacy, note legali). 404 su `/it/profile`, `/it/profile/edit`, `/it/area-personale/{profilo,password,account}`. La rotta `{lang}/account/delete` esiste ma da cittadina da' 403 grezzo "THIS ACTION IS UNAUTHORIZED." Screenshot: `shots/s11-settings-audit-d`, `s11-settings-audit-m`, `s12-audit-_it_account_delete`.
- Funzioni mancanti: modifica nome/cognome/email, cambio password, cancellazione account, revoca dei consensi dati in registrazione (privacy, termini, marketing), esportazione dei dati (art. 15, 17, 20 GDPR). Per una PA e' obbligo avere almeno un canale reale per le richieste (DPO/ufficio con email o PEC).
- Modo Filament: pagina Folio dell'area personale che monta un widget `AccountSettingsWidget` (modulo User) con `Schemas\Components\Section` per Profilo (`TextInput` nome, cognome, email), Password (`TextInput::password()->revealable()`, `->currentPassword()`, `->confirmed()`), Consensi (`Forms\Components\Toggle` per marketing con salvataggio nella tabella dei consensi del modulo Gdpr) e "I tuoi dati" con `Actions\Action::make('export')` (Action che genera lo ZIP e lo scarica) e `Actions\Action::make('delete')->requiresConfirmation()->modalHeading()->form([TextInput::make('password')->currentPassword()])` che invoca `DeleteAccountAction` del modulo Gdpr (policy che autorizza il proprietario, risolve il 403). Il contatto del Comune va nel footer come `Infolists\Components` alimentato dal JSON CMS della pagina, non scritto a mano nel tema.

### FO-3-05 Informativa incompleta e prova del consenso non salvata

- Evidenza MISURATO: passo 1 mostra un'informativa breve (3 paragrafi): parla di "recapiti pubblicati sul portale" ma non ce ne sono; mancano titolare, DPO, base giuridica, tempi di conservazione, destinatari/terze parti, diritti e reclamo al Garante (art. 13 GDPR). Il checkbox "Ho letto e compreso l'informativa sulla privacy" non lascia una prova del consenso (A-04: tabella `treatments` vuota). Il wizard fallisce chiuso se non c'e' una privacy policy pubblicata (comportamento corretto). Screenshot: `shots/s14-wizard-step1-d`, `s15-step1-noconsent-d`.
- Modo Filament: contenuto dell'informativa in `Forms\Components\RichEditor` del back office (risorsa `PrivacyPolicyResource` del modulo Gdpr) con versione e data; nel wizard uno `Schemas\Components\Section` con testo sintetico piu' link alla versione completa; al submit una Action `RecordConsentAction` salva utente, versione, data, IP anonimizzato. Se il consenso e' solo "presa visione" (art. 13) il checkbox puo' diventare un semplice avviso, non un consenso.

### FO-3-06 Invia con sessione scaduta: nessun messaggio

- Evidenza MISURATO: con i cookie cancellati nel browser, oppure dopo logout fatto in un'altra scheda, "Avanti" prosegue come se nulla fosse e "Invia" non fa nulla: la pagina resta sul riepilogo, nessun errore visibile, nessuna segnalazione creata, i dati restano sullo schermo. Le risposte Livewire sono 200. Screenshot: `shots/s28-clearCookies`, `s28-logoutOtherTab`.
- Causa DEDOTTO dal codice: `CreateTicketWizardWidget::save()` per un ospite fa `addError('global', __('fixcity::ticket.auth.required'))`, ma (a) nessuna vista del tema o del modulo Fixcity mostra l'errore `global` (grep), (b) la chiave `fixcity::ticket.auth.required` non risulta nei file `lang/*/ticket.php`.
- Modo Filament: al posto di `addError('global')` usare `Notification::make()->danger()->persistent()->title(__('fixcity::ticket.auth.required'))->actions([Action::make('login')->url(route('login'))])->send()` e `$this->redirect(login con intended)`. Aggiungere la chiave in it/en/de/es. Per non perdere i dati vedi FO-3-20. Il middleware `auth` sulla pagina non basta perche' la sessione puo' scadere dopo il caricamento.

### FO-3-07 "Cerca un luogo" non cerca

- Evidenza MISURATO: il campo "Cerca un luogo*" e' un semplice input di testo. Digitando un indirizzo e premendo Invio non succede nulla: Latitudine e Longitudine restano "--", nessun suggerimento, la mappa non si sposta; l'errore "Il campo indirizzo del disservizio e' obbligatorio." resta anche con il campo compilato. La ricerca vera e' la piccola lente sulla mappa ("Cerca indirizzo"), che chiama Nominatim dal browser e funziona (compila lat 43.775598, lon 11.239637 e l'indirizzo). "Usa la tua posizione" funziona con permesso concesso. Screenshot: `shots/s16-step2-autocomplete`, `s17-after-enter-address`, `s18-map-search-open`, `s18-map-search-result`, `s17-geo-granted`.
- Modo Filament: nel pacchetto Geo il campo `CoordinatePicker` deve collegare l'input "Cerca un luogo" alla ricerca (`->live(debounce: 500)` con `afterStateUpdated` che chiama una Action `GeocodeAddressAction` lato server, oppure lo script Alpine del picker). In alternativa togliere il campo di testo e lasciare solo la lente in evidenza con etichetta visibile. Il campo "indirizzo" dovrebbe essere compilato dal risultato, non obbligatorio prima della ricerca.

### FO-3-08 Verifica email finta

- Evidenza MISURATO: il modulo di registrazione promette "Ti invieremo un'email di conferma", ma l'utente e' loggato subito, `email_verified_at` e' gia' valorizzato (controllo DB in sola lettura) e le impostazioni dicono "Il tuo indirizzo email e' verificato". La rotta `/it/auth/email/verify` e' 404, `/it/auth/verify` esiste in inglese e raggiungibile anche da verificata. Screenshot: `shots/s10-after-register-d`, `s13-verify-audit`.
- Modo Filament: User implementa `MustVerifyEmail`; dopo la registrazione redirect a una pagina Folio `auth/verify` a tema con widget (`Actions\Action::make('resend')` con throttle) e middleware `verified` sulle rotte che inviano notifiche. Decisione di prodotto (vedi domande).

### FO-3-09 Esito della registrazione senza conferma, nome vuoto nell'header

- Evidenza MISURATO: dopo "Crea account" si atterra su `/it` senza messaggio di benvenuto o di conferma. L'header mostra l'indirizzo email al posto del nome perche' `users.name` e' vuoto (sono valorizzati first_name e last_name), mentre la pagina impostazioni mostra "Marco Prova". Se la registrazione parte dal wizard si torna correttamente al wizard (vedi punti positivi). Screenshot: `shots/s10-after-register-d`, `s29-after-register`.
- Modo Filament: `RegisterAction` del modulo Gdpr imposta `name = trim(first_name.' '.last_name)` (o accessor sul modello), e al termine `Notification::make()->success()->title(__('...registered'))->send()` letta dal tema (sessione flash).

### FO-3-10 Registrazione: aspetto

- Evidenza MISURATO: il pulsante Google e' un'icona enorme (circa 100x145 px) con l'etichetta sotto, anche a 375px; "Mostra password" e' un link sottolineato tra i campi solo per la prima password; nella pagina di registrazione compaiono "Password dimenticata?" e "Accedi con Google" (dovrebbe essere "Registrati con Google"). Screenshot: `shots/s08-register-d`, `s08-register-m`.
- Modo Filament: stessi componenti del login (`TextInput::password()->revealable()` su entrambi i campi) riusati dal tema; il pulsante social come un solo componente Blade con dimensioni fisse condiviso fra login e registrazione. Titolare: Modules/Gdpr (registrazione) e tema Sixteen (stile).

### FO-3-11 Messaggi di errore della registrazione

- Evidenza MISURATO: nomi di campo grezzi o in minuscolo ("first name e' richiesto.", "password confirmation e' richiesto."); gli errori sui consensi compaiono solo nel banner in alto, non vicino ai checkbox; i messaggi sono duplicati (banner piu' campo); grammatica ("Il password fornito e' apparso in una violazione di dati. Scegli un password diverso"). Email gia' registrata rivela "email e' gia' stato utilizzato" (D-31). La password `Abcdef1!` e' rifiutata come violata (chiamata a un servizio di terzi, D-33); regole di password diverse tra 8, 9 e 12 caratteri (D-11); nessuna eta' minima (D-12, DEDOTTO). Screenshot: `shots/s09-1-empty` fino a `s09-4-existing-email`.
- Modo Filament: etichette sugli attributi con `->validationAttribute(__('...'))` su ogni campo del `Schema`, errori resi da Filament accanto al campo (`->required()`, `Toggle::accepted()` o `Checkbox::accepted()` per i consensi), regola unica `Password::min(12)->mixedCase()->numbers()->symbols()` in una sola sede e `->uncompromised()` solo se si accetta il servizio esterno (decisione di privacy).

### FO-3-12 Google OAuth

- Evidenza MISURATO: "Accedi con Google" e' sempre visibile. Cliccato senza chiavi riporta al login con un avviso rosso che elenca i nomi delle variabili d'ambiente (GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI), URL locali e l'URL del back office, perche' qui APP_DEBUG=true. Screenshot: `shots/s04-google-click-d`.
- DEDOTTO: con APP_DEBUG=false il codice (`ProviderNotConfigured`) mostra un messaggio generico.
- Modo Filament: mostrare il pulsante solo se `config('services.google.client_id')` e' valorizzato (check in un unico punto, esito per tutte le pagine) e mai la guida di configurazione nel FO; la guida va nel pannello admin come `Notification` o `Widget` di diagnostica.

### FO-3-13 Pagine di sistema in inglese o non a tema

- Evidenza MISURATO: `/it/auth/register` da loggata: pagina bianca "403 FORBIDDEN" in inglese; `/it/account/delete` da cittadina: "THIS ACTION IS UNAUTHORIZED."; `/it/auth/password/confirm`: "Confirm your password ... Forgot your password?" senza h1; `/it/auth/verify`: "Verify your email address ... Before proceeding, please check your email"; `/admin` e `/user/admin` da cittadina: 403 grezzo. La pagina `/it/auth/logout` (GET) e' a tema ma con bottone blu primario non allineato al verde e "Annulla" stretto. Screenshot: `shots/s06-loggedin_it_auth_register`, `s12-audit-_it_account_delete`, `s13-confirm-audit`, `s13-verify-audit`, `s12-audit-_user_admin`, `s13-logout-get-audit`.
- Modo Filament: per i 403 usare i gestori di errore a tema gia' presenti (DCP-151, `bootstrap/app.php`) anche per 403; un utente loggato che apre register/login va rimandato all'area personale con `redirect()->intended()` (middleware `guest`). Le pagine confirm e verify con widget Filament a tema come FO-3-01.

### FO-3-14 Login: focus, contrasto, toggle, utente loggato

- Evidenza MISURATO: dopo l'errore il focus resta su BODY (non va al primo errore ne' al banner); il testo d'errore e' 12px rosa rgb(217,54,79) su bianco, contrasto 4,57:1 (appena AA per testo piccolo); il pulsante "Mostra password" funziona ma non ha `aria-pressed` ne' etichetta; il banner superiore e' `aria-live` assertive e i campi hanno `aria-invalid` e `aria-describedby` (bene). Un utente gia' loggato che apre `/it/auth/login` rivede il form (200, nessun redirect). Throttle: dopo 5 tentativi "Troppi tentativi di accesso. Riprova tra 54 secondi." (conto statico, non scorre). Screenshot: `shots/s03-login-empty-d`, `s03-login-wrongpw-d`, `s03-login-throttle-d`, `s06-loggedin_it_auth_login`.
- Modo Filament: `TextInput::password()->revealable()` (gestisce aria), `Notification` con `role=alert` e focus automatico al primo campo invalido, `->extraInputAttributes(['autocomplete' => 'current-password'])`; middleware `guest` sulle rotte di auth.

### FO-3-15 Lingue

- Evidenza MISURATO: recupero password in inglese in de ed es, con `passwords.user` grezzo (in en: "We can't find a user..."); login de: errori in minuscolo ("e-Mail-Adresse ist erforderlich.", "passwort ist erforderlich."); login en ed es corretti; errore consenso del wizard in de: "ich habe die Datenschutzerklaerung gelesen und verstanden must Akzeptert werden." con "must" inglese; es con frase poco naturale; "Mostra password" resta in italiano nella registrazione in en, de, es. Wizard in en, de, es tradotto per passi, tipi e messaggi (tranne i punti detti). Screenshot: `shots/s31-login-en`, `s31-login-de`, `s31-login-es`, `s25-de-step1`, `s25-es-step1`, `s08-register-en`, `s08-register-de`, `s08-register-es`.
- Modo Filament: file `lang/{it,en,de,es}` per modulo (User, Gdpr, Fixcity) con `->validationAttribute()` sui campi; audit dei file con gli strumenti gia' presenti (`audit-locales.py`).

### FO-3-16 Terze parti prima del consenso

- Evidenza MISURATO: gia' al passo 1 la pagina carica le tile di `a.tile.openstreetmap.org`; ricerca e geolocalizzazione inversa chiamano `nominatim.openstreetmap.org` dal browser, quindi l'indirizzo IP e le coordinate vanno a terzi. L'informativa del passo 1 non lo dice. Il banner cookie dice solo cookie tecnici.
- Modo Filament: caricare la mappa solo dal passo 2 (`Schemas\Components\Wizard\Step` con `lazy`), citare OpenStreetMap/Nominatim nell'informativa, oppure usare un geocoder proprio (server-side, `GeocodeAddressAction` con cache) cosi' non serve contattare terzi dal browser. Titolare: Modules/Geo.

### FO-3-17 Passo 2: errori e focus

- Evidenza MISURATO: "Avanti" a vuoto mostra errori per indirizzo, punto sulla mappa, tipo, titolo, dettagli, ma il focus resta sul pulsante "Avanti" (BUTTON), non c'e' un riepilogo errori in alto e la vista non scorre al primo errore (scrollY 825, primo errore fuori schermo). Il messaggio per il punto e' gergo tecnico: "Indica un punto valido sulla mappa: latitudine tra -90 e 90, longitudine tra -180 e 180." Gli errori sono testo rosso piccolo (circa 12px). Screenshot: `shots/s16-step2-empty-errors-d`, `s16-step2-empty-errors-viewport`.
- Modo Filament: `Schema` con `->extraAttributes(['x-on:...'])` per `scrollIntoView` sul primo `.fi-fo-field-wrp-error-message`, una `Notification` danger (o un blocco `Schemas\Components\Section` con ruolo `alert`) in cima con l'elenco ("Mancano 5 informazioni") e messaggio umano per le coordinate ("Tocca la mappa o cerca un indirizzo per indicare dove si trova il problema").

### FO-3-18 Geolocalizzazione negata

- Evidenza MISURATO: con permesso negato il suggerimento "Non riesco a leggere la tua posizione da questo dispositivo. Cerca un indirizzo o tocca la mappa." appare tagliato nel piccolo riquadro della mappa a 1280 (seconda frase nascosta). Altezza del box fissa. Screenshot: `shots/s17-geo-denied`.
- Modo Filament: messaggio fuori dalla mappa, in un `Schemas\Components\Text` sotto il campo, con altezza libera.

### FO-3-19 Allegati: HEIC, fotocamera, testo di aiuto, EXIF

- Evidenza MISURATO: input `accept="image/jpeg,image/png,application/pdf,image/heic,image/heif"`, `multiple`, `capture="environment"`. Server: `acceptedFileTypes(['image/jpeg','image/png','application/pdf'])`, 10 MB, 5 file. JPG e PNG: miniatura con pulsante di rimozione (aria-label "Elimina nome"); >10 MB: "Il file supera 10 MB"; txt, svg, gif, webp: "Formato non consentito: usa JPG, PNG o PDF"; 6 file: 5 accettati e "Puoi allegare al massimo 5 file"; PDF accettato anche se il testo di aiuto parla di immagini; un file `fake-image.jpg` con contenuto testuale e' accettato dal client (esito lato server non provato). L'originale salvato mantiene EXIF con posizione GPS e Make/Model (verificato con PIL, A-27). Messaggi in rosso da 12px. Screenshot: `shots/s19-up-valid2`, `s20-up-big`, `s20-up-txt`, `s20-up-svg`, `s20-up-gif`, `s20-up-webp`, `s20-up-fakejpg`, `s20-up-six`, `s20-up-pdf`.
- DEDOTTO: HEIC (foto predefinita di iPhone) e' accettato dal browser ma rifiutato dal server; `capture="environment"` su Android apre solo la fotocamera e non la galleria.
- Modo Filament: `SpatieMediaLibraryFileUpload` con `->acceptedFileTypes([...])` allineato al campo (HEIC convertito in JPEG con una Action server-side o tolto da `accept`), `->image()->imageResizeTargetWidth(2000)`, `->imageEditor()` non necessario; rimozione EXIF con Media Library conversion o `ImageOptimizer` all'upload, tenendo la posizione solo nel campo coordinate; testo di aiuto con `->helperText()` "Foto o PDF, fino a 5 file da 10 MB"; togliere `capture` con `->extraInputAttributes()`.

### FO-3-20 Nessuna bozza

- Evidenza MISURATO: ricaricare la pagina a meta' perde titolo, tipo, dettagli e foto; il link diretto al riepilogo (`?step=form.riepilogo::...`) apre un riepilogo vuoto con i passi 1 e 2 segnati "Confermato" anche se il consenso non e' stato registrato; "Invia" li' porta al passo 1 con errore sul consenso (validazione lato server sicura). "Indietro" invece mantiene i dati (titolo, tipo, indirizzo, 2 file). Screenshot: `shots/s24-deeplink-summary`, `s24-deeplink-submit`.
- Modo Filament: `Wizard->persistStepInQueryString()` (gia' in uso, vedi il parametro `?step=`) piu' salvataggio bozza: stato del form in sessionStorage con Alpine (`$persist`) per la versione veloce, oppure campi `->live(onBlur: true)` che chiamano una Action `SaveDraftAction` che salva in una tabella bozze, con avviso "Hai una segnalazione iniziata, riprendila". Taglia M perche' le foto richiedono salvataggio temporaneo.

### FO-3-21 Riepilogo

- Evidenza MISURATO: il testo va a capo a meta' parola ("It / alia", "rende m / olto", "civi / co", "pas / saggio") a 1280 e 375; riga Email duplicata in "Contatti". Screenshot: `shots/s21-normal-1280-summary`, `s21-normal-375-summary`.
- Modo Filament: nel template del riepilogo `Infolists\Components\TextEntry` con `->wrap()`; causa probabile (DEDOTTO, CSS non ispezionato) una regola `word-break`/`overflow-wrap: anywhere` sul contenitore: da sostituire con `overflow-wrap: break-word`; una sola `TextEntry` per l'email. Titolare: tema Sixteen (`pub_theme::components.wizard.summary-*`).

### FO-3-22 Mobile 375

- Evidenza MISURATO: nessun overflow orizzontale (scrollWidth 375), flusso completo (TCK-CGUAS4SGGROTMQSF), indicatore passi "2/3"; ma la ricerca sulla mappa ha un campo largo circa 69px con testo tagliato; target piccoli: "Indietro" 82x26, "Usa la tua posizione" 28px di altezza, link del breadcrumb 24px; il banner cookie copre parte del modulo al primo accesso. Screenshot: `shots/s23-map-search-mobile`, `s23-step2-a`, `s23-step2-b`, `s21-normal-375-step2-filled`.
- Modo Filament: `->extraAttributes(['class' => 'min-h-11'])` e `Actions\Action::make('back')->size('lg')` sui pulsanti del wizard; ricerca mappa a tutta larghezza sotto i 480px.

### FO-3-23 Tastiera e focus nel wizard

- Evidenza MISURATO (passo 1): l'ordine dei Tab e' sensato (header, menu, breadcrumb, link informativa, checkbox, Avanti), con contorno solido da 3px su ogni sosta; Spazio attiva il consenso; Invio su "Avanti" avanza. Pero' il checkbox reale non e' visibile (custom) e il riquadro nero compare intorno all'etichetta; dopo il cambio passo il focus va su BODY e non sull'intestazione del nuovo passo, e il Tab seguente riparte dall'inizio della pagina (primi stop: link del footer). Passo 2 e riepilogo da tastiera non completati a fondo, per questo il resto e' DEDOTTO. Screenshot: `shots/s26-after-enter-step`.
- Modo Filament: dopo `nextStep` un `$dispatch('focus-step-heading')` con `x-init` sull'`h2` con `tabindex="-1"`; stile `:focus-visible` sull'input reale (o sul wrapper con `:has(input:focus-visible)`).

### FO-3-24 Link morti in "Contatta il comune"

- Evidenza MISURATO: nel wizard "Richiedi assistenza" punta a `#`; nella conferma anche "Leggi le domande frequenti", "Chiama il numero verde 05 0505", "Prenota appuntamento" e "Richiesta appuntamento" in "Servizi correlati". Il numero verde sembra un segnaposto. Screenshot: `shots/s28-logoutOtherTab`, `s21-normal-1280-confirmation`.
- Modo Filament: collegare i link al JSON CMS delle pagine `tickets/create.json` e `confirmation.json` (non al tema), con `Forms\Components\Repeater` di contatti nel back office e voce nascosta se vuota.

### FO-3-25 Conferma: valutazione, copia e stampa

- Evidenza MISURATO: il codice e' in testo semplice senza pulsante "Copia" o "Stampa"; il widget "Quanto e' stato facile usare questo servizio?" funziona nell'interfaccia (stelle, domanda sulle difficolta' 1/2, "Vuoi aggiungere altri dettagli?" 2/2, poi "Grazie, il tuo parere ci aiutera' a migliorare il servizio!") ma non invia nulla: l'unica richiesta POST registrata e' `/_boost/browser-logs`, quindi il "Grazie" e' falso; la stampa (media print) e' identica allo schermo, con header verde, cookie e widget; il widget sta dentro una grande banda verde vuota. Un ospite con il codice apre la pagina di conferma (200) e vede il codice ma non l'email. Screenshot: `shots/s32-confirm-after-star`, `s32-confirm-print`, `s21-normal-1280-confirmation`.
- Modo Filament: widget di feedback con `Actions\Action` che salva in una tabella (Action `StoreServiceFeedbackAction`) oppure rimuovere il widget; codice con `Actions\Action::make('copy')->extraAttributes(['x-on:click' => 'navigator.clipboard...'])` e CSS `@media print` che nasconde header, cookie e rating.

### FO-3-26 Livewire accetta richieste senza cookie di sessione

- Evidenza MISURATO: dopo `clearCookies`, "Avanti" e "Invia" ricevono 200 dall'endpoint Livewire `/livewire-.../update` (3 su 3) e il server rimette nuovi `XSRF-TOKEN` e `fixcity_scratch_session`; nessun 419.
- Causa DEDOTTO, da verificare: eccezione CSRF per Livewire, oppure un middleware che riemette il token. Se c'e' un'eccezione e' un buco; se e' il comportamento standard del framework, e' solo una nota. Non provato altrove.
- Modo Filament/Laravel: verificare `bootstrap/app.php` (`validateCsrfTokens(except: ...)`) e riportare Livewire sotto il middleware `web` standard. Taglia S.

### FO-3-27 Menu e breadcrumb su `/it/tickets/create`

- Evidenza MISURATO: la voce "Segnalazioni" resta attiva su `/create` (dovrebbe essere "Segnala"); il breadcrumb "Servizi" porta a `/it/tickets`. Screenshot: `shots/s14-wizard-step1-d`.
- Modo Filament: chiave `header_active` nel primo blocco del JSON della pagina (la regola del progetto: non e' leggibile a livello pagina).

### FO-3-28 Logout

- Evidenza MISURATO: "Esci" nel menu utente esce subito e atterra su `/it` senza messaggio "Hai effettuato l'uscita"; Indietro dopo l'uscita porta alla pagina di login (non mostra dati privati: bene). La pagina `/it/auth/logout` (GET) e' un'altra via, con conferma, bottone blu fuori tema. Screenshot: `shots/s30-user-menu`, `s30-after-esci`, `s30-after-back`, `s13-logout-get-audit`.
- Modo Filament: `Notification::make()->success()->title('Sei uscito')` flash; unificare le due vie.

### FO-3-29 Login: aspetto dei campi

- Evidenza MISURATO: il campo email e' reso solo con sottolineatura mentre la password e' riquadrata; "Password dimenticata?" subito sotto il pulsante Google senza spazio; area scura vuota sotto il footer. Screenshot: `shots/s02-login-clean-d`.
- Modo Filament: stessi `TextInput` con lo stesso stile per tutti i campi.

### FO-3-30 Marker `|---LINE:151---|` e avviso asset Livewire

- Evidenza MISURATO: su molte pagine compare il testo `|---LINE:151---|` e `|---LINE:153---|` (residuo dell'exception renderer nelle viste compilate; 2 viste su 190 nello storage scratch). Console di ogni pagina: "Livewire: The published Livewire assets are out of date".
- Nota: il marker e' un problema dell'ambiente di prova, gia' noto in memoria di progetto (`view:clear`); in produzione verificare. L'avviso richiede `php artisan livewire:publish --assets` nel deploy.

## 4. Cosa funziona bene (con prova)

- Ospite su `/it/tickets/create`: 302 al login, e dopo il login si torna al wizard (e a pratiche, impostazioni, notifiche). Anche dalla registrazione si torna al wizard (`s29-after-register`: URL `/it/tickets/create`, passo 1). Il login diretto atterra sulla home.
- Login: nessuna enumerazione (password sbagliata e email sconosciuta danno lo stesso "Credenziali non valide"), freno a 5 tentativi per email e IP, banner `aria-live`, `aria-invalid` e `aria-describedby`.
- Wizard: validazione lato server, proprietario, stato e priorita' decisi dal server; fallisce chiuso senza privacy policy; link al riepilogo e Invia vuoto non creano nulla; foto con miniatura e rimozione; limiti di formato, dimensione e numero rispettati e spiegati; il riepilogo mostra foto, mappa e dati; "Indietro" conserva i dati.
- Dopo la creazione: la pratica compare in `/it/area-personale/pratiche` ("In attesa"), il tracciamento mostra lo stato e "Non ci sono ancora aggiornamenti pubblici", `/tickets/track/{code}` rimanda a `?code=`.
- Mobile 375: flusso completo senza overflow orizzontale.
- Wizard tradotto in en, de, es (passi, tipi, messaggi principali).
- Produzione https://fixcity.eu/it: stesso comportamento di stato HTTP (302 al login per le pagine private).

## 5. Domande per l'utente

1. Verifica email e notifiche: al go-live l'email del cittadino deve essere verificata (D-03) e deve partire un'email alla creazione (A-01)? Senza questa decisione FO-3-03 e FO-3-08 non si possono chiudere.
2. Cancellazione account, esportazione e revoca consensi (FO-3-04): vanno in autonomia nell'area personale o si accetta per ora la richiesta al Comune? In questo caso servono un indirizzo email o una PEC reali del Comune da mostrare (chi li fornisce? nessun DEC trovato).
3. Google OAuth al go-live: si o no? (FO-3-12)
4. Accesso obbligatorio per segnalare (D17) confermato? E' corretto che un ospite con il codice veda la pagina di conferma (senza email)?
5. HEIC: accettare e convertire lato server, o togliere HEIC da `accept`? Foto con GPS: mantenere l'originale con EXIF o rimuoverlo?
6. Politica password: quale regola unica (8, 9 o 12) e si tiene il controllo di violazione con servizio esterno (D-33)?
7. Il widget di valutazione della conferma va collegato a un salvataggio o rimosso?
8. Bozza del wizard: salvataggio in sessione (veloce, perde le foto) o in tabella (piu' lavoro, riprende anche altrove)?
9. Il tema decide: il pulsante Google della registrazione deve dire "Registrati con Google" e il numero verde "05 0505" e' un segnaposto?
10. FO-3-26: l'eccezione CSRF per Livewire e' voluta?

## 6. Osservazioni su commenti

Il log scratch mostra ripetutamente `CommentWidget::$uiConfig` TypeError ("Cannot assign array to property ... CommentWidgetUiData"): non toccato, di competenza dell'altra sessione.

## 7. Cosa non ho potuto provare

- Consegna reale delle email (conferma, reset, verifica): MAIL_MAILER=array, non osservabile. Il percorso felice del reset con token non provato.
- Scadenza naturale della sessione per tempo e vero 419: simulata con cookie cancellati e logout in altra scheda; il 419 non e' comparso (vedi FO-3-26).
- Foto HEIC reale, `capture` su telefono, GPS reale del dispositivo (usata geolocalizzazione simulata da Playwright; da HTTP non sicuro il browser reale non la concede).
- Lettori di schermo (NVDA, VoiceOver): non provati; i punti di accessibilita' sono misurati su DOM e focus.
- Google OAuth reale e confronto Design Comuni a pixel per queste pagine: non fatto; confronto produzione limitato allo stato HTTP.
- Tastiera su passo 2 e riepilogo, contrasto del testo d'errore del wizard: solo parziale.
- Tracciamento da ospite: un primo tentativo ha dato 429 per il limite condiviso sull'IP 127.0.0.1 con le altre sessioni di audit; ripetuto dopo l'attesa, 200 (`s32-guest-track`).
- Registrazione in en, de, es: solo vista delle etichette, non inviata (limite 10 per 10 minuti condiviso).
- Rumore ambientale: un 500 spurio sul PDF alle 12:23 per modifica in corso di `UserServiceProvider` da un'altra sessione (non attribuibile al PDF).
