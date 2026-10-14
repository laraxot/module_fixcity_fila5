---
id: dcp-180
title: "DCP-180: audit di sicurezza del pannello Filament di Fixcity con Filament Blueprint"
type: story
epic: dc-parity-v2
status: in-progress
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "filament blueprint audit sicurezza relation manager auto-discovery preserveFilenames RestrictsFileUploadsToSchemaComponents XotBaseWidget upload disk uploads"
related: [dcp-49, dcp-139]
---

# DCP-180: audit di sicurezza Filament con Blueprint

## Fase BMAD
Analisi (studio del plugin) e verifica (audit), poi correzione (dev) di un solo difetto. Gli altri due restano aperti con la correzione scritta.

## Cos'e' Filament Blueprint (verificato sul pacchetto installato)
- Pacchetto a pagamento (49 USD) del team Filament, **solo linee guida in Markdown per agenti AI**: nessun codice a runtime. Richiede `laravel/boost`.
- Gia' presente nel progetto: `filament/blueprint` v2.4.0 in `laravel/composer.json` (require-dev), `auth.json` con le credenziali di `packages.filamentphp.com`, `laravel/boost.json` con le tre skill attive, skill copiate per gli agenti in `laravel/.claude/skills`, `.agents`, `.cursor`, `.github`, `.pi`. Versioni: Filament 5.10.1, Livewire 4.4.7, Boost 2.10.3.
- Tre skill:
  - `planning-filament`: scrive un piano (un file Markdown) per una funzione Filament; **non implementa**. Legge `vendor/filament/blueprint/resources/markdown/planning/` (22 file: overview, resources, forms, tables, authorization, testing, ...). Per regola chiede all'utente le scelte di dominio (stati, permessi, ritenzione dati) prima di dare il piano per chiuso.
  - `reviewing-filament-plans`: confronta il codice con un piano gia' salvato (stati Conforms / Deviates / Blocked / Not verified). Serve un blueprint: **nel progetto non ne esiste ancora nessuno**.
  - `filament-security-audit`: catalogo di 22 controlli (A accessi, B upload, C XSS, D query e tenant, E dipendenze), produce un piano di correzione per ogni difetto. E' quella usata qui.

## Perimetro dell'audit
Modulo Fixcity (`Modules/Fixcity/app` e `resources/views`), pannello `fixcity/admin`, piu' i punti trasversali che lo toccano: `BaseUser::canAccessPanel`, i widget base di Xot, `composer audit`. **Non** coperti: gli altri moduli con risorse Filament (User 26, Xot 9, Job 9, Notify 6, Cms 5, Blog 5, ...). Un giro per modulo e' il passo successivo.

## 1. Sintesi
Tre difetti distinti: A 1, B 2, C 0, D 0, E 0. `composer audit`: nessun avviso. La policy dei ticket e' coerente (`delete` e `deleteAny` allineate, il bulk delete custom dichiara `->authorize('deleteAny')`), nessun importer, nessuna colonna modificabile in linea, nessun sink HTML con dati utente non escapati. F-01 e' corretto e provato; F-02 e F-03 sono aperti.

## 2. Difetti

### [F-01] Relation manager dimenticato, attivo per auto-discovery, upload con nome originale su disco servito (CORRETTO)
Check: B3 (con B2)
Location: `Modules/Fixcity/app/Filament/Resources/TicketResource/RelationManagers/MediaRelationManager.php:28-32` (file rimosso)
Component: `Filament\Forms\Components\FileUpload`
Docs: https://filamentphp.com/docs/5.x/forms/file-upload#security-implications-of-controlling-file-names

Issue: `XotBaseResource::getRelations()` (`Modules/Xot/app/Filament/Resources/XotBaseResource.php:327`) scopre per glob ogni `*RelationManager.php` nella cartella del resource. Verificato a runtime: `TicketResource::getRelations()` restituiva `CommentsRelationManager` **e** `MediaRelationManager`. Il campo `FileUpload::make('file')->preserveFilenames()` scriveva sul disco di default di Filament, `public` (`config('filament.default_filesystem_disk')`, root `storage/app/public`, raggiungibile via il link `public_html/storage`). La tabella `media` non ha le colonne `file` ne' `description`: il salvataggio del record non poteva riuscire, ma il file restava su disco con il nome scelto dal client. Un poliglotta (magic bytes di immagine piu' codice PHP) passa la whitelist `image/*` e conserva l'estensione `.php`.

Fix applicato: rimosso il file. Il caricamento allegati e' gia' coperto da `SpatieMediaLibraryFileUpload('images')` in `TicketForm.php:307` e dal relation manager generico del modulo Media. Alternativa se serve davvero un relation manager: `SpatieMediaLibraryFileUpload` con `->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])`, senza `preserveFilenames()`.

Verify: `Modules/Fixcity/tests/Unit/TicketResourceRelationManagersTest.php`. Rosso prima (array con `MediaRelationManager` in piu'), verde dopo. Solo reflection, nessun database.

### [F-02] Widget pubblici senza `RestrictsFileUploadsToSchemaComponents` (APERTO)
Check: A5
Location: `Modules/Xot/app/Filament/Widgets/XotBaseWidget.php:36-37` (`InteractsWithForms`), `XotBaseSchemaWidget.php:39` e `XotBaseInfolistWidget.php:23` (`InteractsWithSchemas`). Componenti Livewire che compongono la trait direttamente: `Modules/User/app/Http/Livewire/Auth/Login.php`, `Register.php` (da verificare se instradati).
Component: `Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents`
Docs: https://filamentphp.com/docs/5.x/advanced/security#restricting-livewire-file-uploads-to-schema-components

Issue: verificato che la trait esiste in `vendor/filament/schemas/src/Concerns/`, che `InteractsWithSchemas` e `InteractsWithForms` (Filament 5.10.1) **non** la includono e che nessun file in `Modules/*/app` la usa. Senza di essa ogni componente con `InteractsWithSchemas` espone `_startUpload` / `_finishUpload` anche se lo schema non ha campi di upload. I widget di login, registrazione e recupero password sono pagine senza autenticazione (widget `Auth` del modulo User, `RegisterWidget` del modulo Gdpr): un visitatore anonimo puo' caricare file nella cartella temporanea di Livewire. Esposizione pubblica di ogni singola classe **non** confermata una per una.

Fix (alla radice, una riga per classe base): aggiungere `use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;` e `use RestrictsFileUploadsToSchemaComponents;` in `XotBaseWidget.php`, `XotBaseSchemaWidget.php`, `XotBaseInfolistWidget.php`. La trait blocca (403) solo gli upload il cui bersaglio non e' un campo di upload dello schema, quindi il wizard con `SpatieMediaLibraryFileUpload('images')` deve continuare a funzionare.

Perche' non applicato: tocca la base condivisa da tutti i widget dei moduli e va provato con il wizard reale su DB scratch (procedura in `memory/pest-scratch-db-needs-config-cache-bypass.md`), non con un test a secco.

Verify: vedi sezione 4.

### [F-03] Campo `images` senza whitelist di tipi (APERTO)
Check: B2
Location: `Modules/Fixcity/app/Filament/Resources/TicketResource.php:114-125`
Component: `Filament\Forms\Components\SpatieMediaLibraryFileUpload`
Docs: https://filamentphp.com/docs/5.x/forms/file-upload#file-type-validation

Issue: la riga `->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])` e' commentata (riga 124). Il campo scrive sul disco `uploads`, la cui root e' `public_html/uploads/photos`, cioe' **dentro il docroot**. Oggi lo salva dall'alto la collezione `ticket` (`Ticket.php:423-424`, `acceptsMimeTypes` jpeg e png), che pero' scatta alla creazione del media, dopo `Ticket::create`: lo stesso modo di rottura gia' descritto in `TicketForm.php:309-311` (ticket orfano, SEC-04 DCP-49). Lo schema e' usato da `CreateTicketWidget.php:112` (widget con login obbligatorio).

Fix: sostituire la riga 124 con `->acceptedFileTypes(['image/jpeg', 'image/png'])`, gli stessi mime della collezione.

Verify: vedi sezione 4.

## 3. Controlli eseguiti
| Check | Esito | Nota |
|---|---|---|
| A1 | Pass | `TicketPolicy`: `delete` e `deleteAny` allineate; bulk delete custom con `->authorize('deleteAny')`. La policy dei commenti (modulo Comment) non e' stata letta |
| A2 | N/A | nessun `ImportAction` ne' `Importer` in Fixcity |
| A3 | Pass | nessun override `can*()` in `app/Filament` |
| A4 | N/A | nessuna colonna modificabile in linea |
| A5 | Finding | F-02 |
| A6 | Pass | i `mount()` dei widget non fanno scritture prima dell'autorizzazione: `CreateTicketWidget` rimanda al login se ospite, il wizard riempie i valori iniziali, `ViewWidget` legge solo ticket pubblici, il rating legge soltanto e `SubmitCitizenTicketRatingAction` verifica il proprietario |
| B1 | Pass | i dischi in gioco (`public`, `uploads`) sono serviti: il percorso e' gia' indirizzabile |
| B2 | Finding | F-03 |
| B3 | Finding | F-01 |
| C1 | Pass | `resources/views/tickets/show.blade.php:33` stampa `{!! $ticket->content !!}` (testo del cittadino) ma la vista non e' servita: nessuna rotta `tickets.show` in `route:list`, nessun codice la renderizza |
| C2 | Pass | ogni valore dinamico in `HtmlString` passa da `e()` (`TicketForm.php:102,366`, `CreateTicketWidget.php:106`); il markdown della privacy usa `html_input => strip` |
| C3 | N/A | nessun `->url()` o `->recordUrl()` Filament con dati utente |
| C4 | N/A | nessun `allowHtml` |
| C5 | Pass | tutti gli `extraAttributes` sono array statici |
| C6 | N/A | nessun `allowHtmlValidationMessages` |
| C7 | N/A | nessuna espressione JS lato client |
| D1 | Pass | il pannello e' per operatori PA (`viewAny`); `SelectFilter` con elenco completo e non ristretto per ruolo. Sorgente dati della mappa pubblica non tracciata |
| D2 | Pass | `email` e `owner_id` del ticket sono campi che gli operatori vedono comunque |
| D3 | N/A | nessun tenant nel pannello Fixcity |
| D4 | Pass | `canAccessPanel` richiede il ruolo del pannello; il ramo `accesso` non corrisponde a nessun pannello esistente |
| D5 | N/A | nessun tenant |
| E1 | Pass | `composer audit`: nessun avviso di sicurezza |

## 4. Test consigliati
- F-02: con Livewire, da ospite, caricare un file su una proprieta' che non e' un campo di upload di un widget derivato da `XotBaseWidget`, atteso 403. Regressione: nel `CreateTicketWizardWidget` il campo `images` accetta ancora un jpeg. Su DB scratch con `APP_CONFIG_CACHE=/nonexistent/config.php` e `DB_DATABASE_LIMESURVEY=`.
- F-03: compilare il campo `images` con un file `.txt` e uno `.php`, attesi errori di validazione sul campo (`assertHasFormErrors`), e con un `.jpg` valido, atteso nessun errore.

## Limiti dichiarati
- Non verificato se il web server nega l'esecuzione di `.php` sotto `/storage` e `/uploads`: per F-01 e F-03 il rischio di esecuzione dipende da questo.
- Non verificato se Spatie Media Library conserva l'estensione del nome originale (`.php`) quando il mime sniffato e' immagine. Se la conserva, il campo `images` va anche reso con nome casuale (`getUploadedFileNameForStorageUsing`).
- Filament dopo il login (CSP, DCP-139) e la sorgente dati della mappa pubblica non sono stati provati.

## Prossimi passi con Blueprint
1. Audit degli altri moduli con risorse Filament, uno alla volta (User per primo).
2. `planning-filament` per le funzioni del back office che mancano: richiede prima le scelte di dominio (chi puo' cambiare stato, quali stati, notifiche, ritenzione). Va fatta con il responsabile, non decisa dall'agente.
3. `reviewing-filament-plans` appena esiste un blueprint salvato.

## Nota di coordinamento 2026-10-14 (altra sessione)
Nello stesso momento e' stata aperta [DCP-181](./dcp-181-filament-blueprint-audit-degli-altri-pannelli-e-piano-richieste-interessato.story.md): audit con la stessa skill sugli altri 11 pannelli (user, notify, xot, cms, blog, job, rating, comment, lang, tenant, ui), piu' un piano `planning-filament` per il registro delle richieste dell'interessato (modulo Gdpr). Non sovrapposti a questo perimetro.

