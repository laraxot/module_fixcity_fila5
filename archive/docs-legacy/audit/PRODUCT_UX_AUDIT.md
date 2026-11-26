---
title: "Fixcity — Audit prodotto, UX e accessibilità"
type: audit
tags: [fixcity, audit, product]
created: 2026-11-05
updated: 2026-11-05
status: active
related:
  - ./README.md
  - ./COMPLETION_ROADMAP.md
---

> Analisi statica del branch `dev` del 2026-11-05: niente vendor/, PHPStan e Pest non eseguiti in questo ambiente.
> Lo stato aggiornato di ogni rilievo (risolto/aperto) è in [COMPLETION_ROADMAP.md](./COMPLETION_ROADMAP.md).

# Audit prodotto / UX / accessibilita' / compliance — Fixcity

## Cosa e' il prodotto

**Intento dichiarato** (`PRODUCT.md:13-25`): portale civico in cui il cittadino scopre le segnalazioni esistenti, ne capisce lo stato, ne invia una nuova e ne segue l'avanzamento; il personale del Comune le gestisce. Promesse distintive: (1) **trasparenza dello stato** dopo login e invio; (2) **velocita' da mobile** ("pochi tap, senza un flusso account che scoraggi"); (3) **piattaforma multi-comune**. `DESIGN.md` impone Design Comuni / Bootstrap Italia nel tema Sixteen, WCAG AA.

Nota: `README.md` alla radice (3 righe) descrive il modulo **Notify**, non il prodotto (`README.md:1-3`); `PROJECT.md` descrive solo la milestone di parita' visiva del wizard. Non esiste un documento di requisiti per la vendita alla PA (SPID/CIE, protocollo, app IO): quei punti sono "da validare con i requisiti".

**Cosa fa davvero il codice** (ricostruito):

- **Front office** (Folio + blocchi CMS JSON del tenant `config/local/fixcity/database/content/pages/*`, letti da `Modules/Tenant/app/Models/Traits/SushiToJsons.php:106-139`), 4 lingue (`/it`, `/en`, `/de`, `/es`):
  - Home a blocchi (`pages/home.json`: hero, anteprima mappa da `/api/tickets/geojson`, "come funziona", novita', eventi, "segui la tua segnalazione").
  - Elenco pubblico + mappa `/{lang}/tickets` (`pages/tickets/index.json`, `components/blocks/ticket-list/design-comuni.blade.php`), con stato vuoto (`:93-98`).
  - Dettaglio pubblico `/{lang}/tickets/{id}` (`pages/[container0]/[slug0]/index.blade.php:75-77,112-121` → `BuildPublicTicketDetailAction`): titolo, tipo, indirizzo, foto, descrizione, timeline pubblica, commenti, "segui".
  - Tracciamento per codice `/{lang}/tickets/track?code=TCK-…` (`components/pages/ticket-track.blade.php`), con rate limit (`GetPublicTicketByCodeAction`, 10 errori/min).
  - Wizard di segnalazione `/{lang}/tickets/create` (solo autenticati, `pages/tickets/create.json:273`): privacy → dati → riepilogo → conferma con codice.
  - Area personale (`area-personale/{pratiche,seguite,notifiche,impostazioni}` da JSON CMS), cancellazione account con purge a 30 giorni (`routes/console.php`, `PurgeDeletedAccountsAction`).
  - Pagine legali da Markdown del tenant (privacy, note legali, termini, media policy, cookie policy) e dichiarazione di accessibilita' (`accessibilita.index.json`).
- **Autenticazione**: email+password (Gdpr `RegisterWidget`, User `LoginWidget`), Google OAuth se configurato (`components/utilities/google-button.blade.php:9,22`), callback anche per microsoft/github (`pages/auth/login/[provider]/callback.blade.php:15`). **Nessuna SPID/CIE** (nessuna occorrenza fuori da pagine `tests.*` e testi).
- **Back office Filament** (`Modules/Fixcity/app/Filament/Resources/TicketResource`): lista con export, vista con azioni Assegna / Approva (pending→in_review) / Rifiuta / Risolvi / Chiudi / Cambia stato (matrice `TicketStatusEnum::allowedTargets`) / Invia comunicazione email / Registra risposta / Sollecito / Elimina (`Pages/ViewTicket.php:47-277`); commenti; widget KPI/SLA/valutazioni.
- **Moderazione**: una segnalazione nasce `pending` (forzato lato server, `CreateTicketWizardWidget.php:118-119`) e diventa pubblica solo negli stati di `TicketStatusEnum::canViewByAll()` (`Enums/TicketStatusEnum.php:46-57`).

## Journey

| Journey | Passi | Stato | Evidenze | Cosa blocca |
|---|---|---|---|---|
| (a) Cittadino anonimo: home → mappa → elenco → dettaglio | `/it` → mappa in home → `/it/tickets` (alias `/it/segnalazioni` 301) → `/it/tickets/{id}` | **Implementata non verificata** | `pages/index.blade.php:13-55`; `home.json` blocco `ticket.map-preview`; `ResolveContainer0IndexAction.php:26-29`; `FrontofficeContainerAliases.php:21-25`; `pages/[container0]/[slug0]/index.blade.php:112-121` | Mappa senza stato di caricamento/errore visibile (UX-03); popup mappa non usabile da tastiera (dichiarato, `accessibilita.index.json`); `/de`,`/es` di `tickets/index.json` senza blocchi propri (fallback all'italiano, UX-08) |
| (b) Registrazione / login | `/it/auth/register` (nome, cognome, email, password forte, privacy+termini) → login automatico; `/it/auth/login` email+password, "Accedi con Google" se configurato; recupero password | **Implementata non verificata**; **SPID/CIE assente** | `Modules/Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php:61-70,119-160`; `components/pages/auth/login.blade.php:29-35`; `google-button.blade.php:9,22` | Email marcata "verificata" senza verifica (PRD-02); niente SPID/CIE (PRD-01) |
| (c) Creazione segnalazione | Login obbligatorio → passo 1 Privacy (informativa integrale + checkbox) → passo 2 Dati: luogo (indirizzo + punto su mappa), tipo (enum), titolo (3-255), dettagli (10-200), allegati opz. (max 5, JPEG/PNG/PDF, 10 MB) + scheda autore dal profilo → passo 3 Riepilogo → "Invia" (disabilitato durante l'invio, idempotente) → `/it/tickets/confirmation?code=TCK-…` | **Implementata non verificata** (codice robusto; flusso non eseguito) | `TicketForm.php:39-51,171-189,271-291,324-345,358,368-389`; `CreateTicketWizardWidget.php:85-173`; `report-partials/steps-nav.blade.php:8,24`; `tickets/create.json` nav `next_click: $wire.submit()`; `tickets/confirmation.json` | Invio rifiutato se `policy.md` manca; accettato pero' con informativa piena di segnaposto `[[ente.*]]` (PRD-04); limite 200 caratteri (UX-06) |
| — cosa riceve il cittadino | Pagina di conferma con codice `TCK-` + 16 caratteri; notifica in area personale; email "ricevuta" solo se email verificata e preferenza attiva | **Parziale** | `CreateTicketAction.php:36-44`; `SendTicketReceiptAction.php:26-41,67-77`; `CanUserReceiveTicketMailAction.php:22-29`; `AllocateTicketCodeAction.php:23` | Per via di PRD-02 le email partono anche verso indirizzi mai verificati |
| (d) Segui la tua segnalazione / area personale | Codice in `/tickets/track`; area personale: pratiche, seguite, notifiche, impostazioni (preferenze email, cancellazione account); "Segui" su dettaglio altrui | **Implementata non verificata** con **conflitto di rotta** | `ticket-track.blade.php:18-37`; `pages/area-personale/*.json`; `routes/web.php:34-38` (follow); `pages/area-personale/pratiche.blade.php` (tema, file vietato) e `Modules/User/resources/views/pages/area-personale/pratiche.blade.php` | Tre sorgenti per la stessa URL; il file del tema chiama `route('segnalazioni')` inesistente (PRD-05) |
| (e) Operatore comunale | `/fixcity/admin` → lista → vista: Approva (presa in carico, pending→in_review), Assegna, Cambia stato, Risolvi, Chiudi, Rifiuta, Comunicazione email, Commenti | **Implementata non verificata** | `Pages/ViewTicket.php:47-277`; `Filament/Actions/ChangeStatus.php:39-80`; `Policies/TicketPolicy.php:54-71`; `AssignTicketAction.php` | Nessuno strumento per oscurare volti/targhe nelle foto prima della pubblicazione (PRD-07); la "presa in carico" e' `in_review`, non `in_progress` (da validare con il glossario del Comune) |
| (f) Amministratore | Utenti/ruoli (modulo User), contenuti CMS (Cms), tipi di segnalazione, dati del Comune | **Parziale** | `Enums/TicketTypeEnum.php:96-111` (tipi cablati); `Models/Category.php` senza Resource; `config/local/fixcity/ente.php:22-43` (dati ente solo da file) | Tipi non configurabili dal pannello; dati dell'ente (nome, DPO, PEC) modificabili solo da sviluppatore (PRD-08) |
| (g) Multi-comune | Tenant = cartella `config/<tld>/<nome>` risolta dall'host (`GetTenantNameAction`), symlink `config/eu/fixcity → local/fixcity` | **Parziale / da validare** | `Modules/Tenant/app/Actions/GetTenantNameAction.php:26-36,65-97`; `config/local/fixcity/database.php:7-8` (DB da `env`); `Models/Ticket.php` senza colonna comune/tenant | E' multi-istanza per host, non una piattaforma condivisa: nessuna procedura/comando di onboarding, DB da `.env` unico (PRD-09) |

## Classificazione delle funzionalita'

| Funzionalita' | Classe | Evidenza principale |
|---|---|---|
| Home CMS a blocchi (4 lingue) | implementata non verificata | `pages/home.json`, `pages/index.blade.php` |
| Mappa pubblica GeoJSON | implementata non verificata; UX parziale (niente stato errore) | `Modules/Geo/resources/js/components/map-lit.js:425-482` |
| Elenco pubblico con filtri e stato vuoto | implementata non verificata | `ticket-list/design-comuni.blade.php:93-98` |
| Dettaglio pubblico, timeline, commenti, "segui" | implementata non verificata | `blocks/ticket-detail/design-comuni.blade.php:27-186` |
| Tracciamento per codice (rate limit) | implementata non verificata | `GetPublicTicketByCodeAction.php`, `ticket-track.blade.php` |
| Wizard segnalazione (privacy, dati, riepilogo, invio idempotente) | implementata non verificata | `TicketForm.php`, `CreateTicketWizardWidget.php` |
| Ricevuta email al cittadino | presente ma difettosa (verifica email aggirata) | PRD-02 |
| Login email/password, Google | implementata non verificata | sopra |
| SPID / CIE / eIDAS | **progettata non implementata** (solo pagine demo `tests.*`) | PRD-01 |
| Area personale (pratiche, seguite, notifiche, impostazioni) | implementata non verificata; route `pratiche` difettosa / da validare | PRD-05 |
| Back office: approva/rifiuta/assegna/stati/comunicazioni | implementata non verificata | `ViewTicket.php` |
| Configurazione tipi/categorie da pannello | parziale (enum cablato; `Category` scollegata) | PRD-08 |
| Configurazione dati Comune da pannello | progettata non implementata (solo `ente.php`) | PRD-08 |
| Multi-comune | parziale / da validare | PRD-09 |
| Conservazione dati (purge account, log) | implementata non verificata (richiede cron) | `routes/console.php` |
| Cookie banner (Gdpr) | implementata non verificata | `Modules/Gdpr/resources/views/cookie-consent/index.blade.php` |
| Dichiarazione accessibilita' | parziale (segnaposto dell'ente, non pubblicata su AgID) | UX-01 |
| Pagine `/tests/*` | obsolete per produzione, raggiungibili | PRD-06 |
| `counter.blade.php`, `pages/[slug]`, `area-personale/` nel tema | obsolete / violano la regola | PRD-05, PRD-10 |
| Modulo Mobile (`/{lang}/mobile`) | presente ma difettosa / da validare | PRD-11 |
| App IO, protocollo informatico, PEC in uscita, pagoPA | progettata non implementata (nessun codice) | PRD-12 |
| AdSense / "Spazio in vendita" (`config/comune.php:52-60`, `components/footer-comune.blade.php:156-210`) | obsoleta (non montata dal footer attivo `sections/footer/v1`) | PRD-13 |

## Promesse di PRODUCT.md vs codice

- **Trasparenza dello stato**: mantenuta nel codice — timeline pubblica con motivazioni (`ApproveTicketAction.php:47-51`, visibilita' `Public`), tracciamento, notifiche di cambio stato ai follower (`NotifyTicketSubscribersOfStatusChangeAction`). Indebolita da PRD-02 (email) e dall'assenza di un canale certificato (PEC/protocollo).
- **Velocita' da mobile**: in parte contraddetta. `PRODUCT.md:21` richiede account; `PRODUCT.md:24` promette "senza un flusso account che scoraggi". Oggi il primo invio richiede registrazione con 5 campi + password forte + 2 consensi (`RegisterWidget.php:61-70`), poi 3 passi. Fotocamera mobile c'e' (`dc-file-upload.blade.php:150,166`, `capture="environment"`). Nessuna misura reale di "tap" disponibile.
- **Multi-comune**: non mantenuta come "piattaforma condivisa" (PRD-09).

## Rilievi

Scala gravita': Critica / Alta / Media / Bassa. Tipi: Funzionale, Contenuto, Compliance, Accessibilita', UX, i18n, Documentazione.

### PRD-01 — SPID/CIE assenti: requisito di accesso ai servizi online della PA
- **Gravita'**: Alta (per la vendita a un Comune) — **Tipo**: Compliance / Funzionale
- **Evidenza**: login solo email/password e OAuth (`components/pages/auth/login.blade.php:29-32`; `pages/auth/login/[provider]/callback.blade.php:15` ammette `google, microsoft, github`); nessuna occorrenza di SPID/CIE in `Modules/*/app` (le uniche sono in `config/.../pages/tests/*.json` e in `Modules/UI/lang/*/services.php`).
- **Impatto**: un Comune italiano che eroga un servizio online che richiede identificazione deve consentire l'accesso con SPID/CIE (art. 64 CAD, da validare con l'ufficio legale del cliente); oggi il cittadino deve creare un'ulteriore credenziale e il Comune non ha un'identita' certa del segnalante (manca anche il codice fiscale: `TicketForm.php:384-386`).
- **Correzione**: integrare SPID/CIE (OIDC/SAML tramite un intermediario o gateway del cliente) come metodo di login che crea/aggancia l'account, valorizzando nome, cognome, codice fiscale; mantenere email/password solo se il requisito lo consente.
- **Criterio di accettazione**: da `/it/auth/login` un utente di test SPID (ambiente validator AgID) e CIE accede, l'account ha `fiscal_code` valorizzato e il riepilogo del wizard lo mostra.
- **Confidenza**: 90 — ricerca su tutto il codice applicativo senza risultati; l'obbligatorieta' per questo specifico servizio e' "da validare con i requisiti".

### PRD-02 — L'email del cittadino risulta "verificata" alla registrazione senza alcuna verifica
- **Gravita'**: Alta — **Tipo**: Funzionale / Compliance
- **Evidenza**: `Modules/Gdpr/app/Actions/Validation/ValidateUserDataAction.php:40` (`'email_verified_at' => now()`); `HandleSuccessfulRegistrationAction.php:20-34` non invia verifiche; `config/auth.php:30` `must_verify_email` default `false`; la regola di invio email si fida di `hasVerifiedEmail()` (`Modules/Fixcity/app/Actions/CanUserReceiveTicketMailAction.php:24-28`); la pagina di conferma promette "Ti invieremo il riepilogo all'email" (`tickets/confirmation.json`, condizione `when: mail`).
- **Impatto**: chiunque puo' registrarsi con l'indirizzo di un altro e far partire verso di lui ricevute e aggiornamenti (dati personali a un terzo, possibile abuso/spam a nome del Comune); il messaggio "indirizzo non verificato" del ramo `in_app` non comparira' mai; la regola DCP-284/286 ("email solo se verificata") e' svuotata.
- **Correzione**: non impostare `email_verified_at` in `ValidateUserDataAction`; inviare `sendEmailVerificationNotification()` dopo la creazione (Gdpr `RegisterWidget::submit`); per Google/SPID valorizzarlo solo se il provider attesta l'email.
- **Criterio di accettazione**: test Feature: dopo la registrazione `email_verified_at` e' null, parte una `VerifyEmail`; la conferma della segnalazione mostra il ramo `in_app` finche' l'utente non clicca il link.
- **Confidenza**: 85 — codice esplicito; non ho verificato se un altro listener sovrascrive il campo (ricerca di `email_verified_at` in User/Gdpr/Fixcity senza altri scrittori).

### PRD-03 — Pagine demo `/tests/*` pubbliche in produzione con dati finti
- **Gravita'**: Media — **Tipo**: Contenuto / Compliance
- **Evidenza**: `Themes/Sixteen/resources/views/pages/tests/index.blade.php:12-13` e `tests/[slug].blade.php:12-13`: solo `PageSlugMiddleware`, nessun `auth`/gate di ambiente; i 84 JSON `config/local/fixcity/database/content/pages/tests/*.json` hanno tutti `middleware: null` (conteggio con python); contengono lorem ipsum (10 file, es. `tests.argomenti.json`), 22 file con codici fiscali di esempio (es. `GLABNC72H25H501Y`), flussi finti di pagamento IMU/multe/permessi e 982 link `"url": "#"`. L'unica difesa e' `public_html/robots.txt:46` (`Disallow`), che lo stesso file dichiara "NON e' una protezione" (`robots.txt:28-30`).
- **Impatto**: un cittadino (o un valutatore del Comune) puo' trovare pagine di "pagamento multa" e "IMU" che sembrano servizi reali del Comune ma non funzionano; dati personali di esempio esposti; rischio reputazionale.
- **Correzione**: dichiarare nei JSON `tests.*` `"middleware": ["auth", "can:viewDesignReference"]` o bloccare `tests` fuori da `local/testing` (middleware di ambiente sul mount del tema, come `BlockLegacyPublicPages`).
- **Criterio di accettazione**: con `APP_ENV=production` `GET /it/tests` e `/it/tests/pagamenti-multa-dati-personali` rispondono 404; in `local` restano 200.
- **Confidenza**: 80 — percorso Folio e assenza di middleware certi; non ho eseguito la richiesta HTTP.

### PRD-04 — Informativa privacy e dichiarazione di accessibilita' con segnaposto visibili; il wizard accetta il consenso comunque
- **Gravita'**: Alta (blocca la messa in produzione per un Comune) — **Tipo**: Compliance / Contenuto
- **Evidenza**: tutti i campi dell'ente sono vuoti (`config/local/fixcity/ente.php:24-42`); l'informativa usa `{{ente.name}}`, `{{ente.dpo_name}}`, `{{ente.pec}}` (`config/local/fixcity/lang/it/policy.md:7,11`) che diventano `[[ente.<campo>]]` (`Modules/Fixcity/app/Actions/InterpolateEnteTokensAction.php:47`); il wizard blocca l'invio solo se il file manca o e' il modello iniziale (`GetPublishedLegalDocumentAction.php:31`; `CreateTicketWizardWidget.php:95-99`), non se ci sono segnaposto. La dichiarazione di accessibilita' ha `published_at`, `updated_at`, `officer`, `feedback_email` come `[[...]]` e `agid_declaration_url: null` (`accessibilita.index.json:31-35`). Il `policy.md` radice del tenant e' ancora il modello ("Edit this file", `config/local/fixcity/policy.md:3`), usato come fallback.
- **Impatto**: il cittadino presta il consenso su un'informativa che non nomina il titolare ne' il DPO (art. 13 GDPR); la dichiarazione di accessibilita' non e' quella pubblicata su form.agid.gov.it (obbligo L. 4/2004, linee guida AgID).
- **Correzione**: (1) compilare `ente.php` per il tenant di produzione; (2) far bloccare l'invio (`save()`) anche quando `AssessPrivacyPolicyAction` riporta problemi (es. presenza di `[[`), con lo stesso messaggio `ticket.privacy.not_configured`; (3) pubblicare la dichiarazione su AgID e valorizzare `agid_declaration_url`.
- **Criterio di accettazione**: con `ente.php` vuoto il wizard mostra l'errore e non crea il ticket; con `ente.php` compilato `/it/privacy` non contiene `[[`; `/it/accessibilita` non mostra il riquadro `accessibility-incomplete`.
- **Confidenza**: 85 — catena di funzioni letta per intero; il rendering esatto della pagina non e' verificato in browser.

### PRD-05 — `area-personale/pratiche`: tre sorgenti per la stessa URL, una con rotta inesistente
- **Gravita'**: Alta se servita / Media altrimenti — **Tipo**: Funzionale / Regola repo
- **Evidenza**: `Themes/Sixteen/resources/views/pages/area-personale/pratiche.blade.php:9,38` (rotta nominata `area-personale.pratiche`, link `route('segnalazioni')` nello stato vuoto); nessuna rotta `segnalazioni` registrata (solo in `pages_old*` e in `Themes/Sixteen/components/*-comune.blade.php`); seconda sorgente `Modules/User/resources/views/pages/area-personale/pratiche.blade.php:13` con lo stesso nome; terza sorgente CMS `config/.../pages/area-personale/pratiche.json` via `[container0]/[slug0]`. Il test `Modules/Fixcity/tests/Feature/Pages/PersonalAreaPagesTest.php:130-146` si aspetta contenuti (link `/it/tickets/track/…`, "Nuova segnalazione") che il file del tema non produce. Il test di regola `Themes/Sixteen/tests/Unit/NoSemanticFolioPageDirectoriesTest.php:20,73` ammette solo `[container0]`, `auth`, `tests` e `index.blade.php`: con `area-personale/`, `pages/` e `counter.blade.php` presenti fallirebbe.
- **Impatto**: se Folio serve il file statico del tema, un nuovo cittadino senza segnalazioni che apre "Le mie pratiche" riceve un errore 500 (RouteNotFoundException); in ogni caso la pagina mostrata dipende dall'ordine di montaggio Folio, non da una scelta.
- **Correzione**: eliminare `Themes/Sixteen/resources/views/pages/area-personale/` e `Modules/User/resources/views/pages/area-personale/` lasciando solo il JSON CMS (come prescrive `CLAUDE.md` e `ResolveContainer0IndexAction.php:31-36`).
- **Criterio di accettazione**: `vendor/bin/pest Themes/Sixteen/tests/Unit/NoSemanticFolioPageDirectoriesTest.php` e `PersonalAreaPagesTest.php` verdi; `GET /it/area-personale/pratiche` per un utente senza ticket risponde 200 con il link a `/it/tickets/create`.
- **Confidenza**: 70 — il difetto del link e' certo; quale delle tre pagine Folio serva davvero non e' verificabile senza eseguire l'app.

### PRD-06 — Wizard di produzione montato da un blocco nella cartella `tests`
- **Gravita'**: Bassa — **Tipo**: Manutenibilita' / Regola
- **Evidenza**: `config/local/fixcity/database/content/pages/tickets/create.json` → `view: pub_theme::components.blocks.tests.ticket-crea`; il blocco `Themes/Sixteen/resources/views/components/blocks/tests/ticket-crea.blade.php:3` monta `CreateTicketWizardWidget`.
- **Impatto**: chi ripulisce le pagine di prova rischia di rompere la funzione principale del prodotto.
- **Correzione**: spostare il blocco in `components/blocks/ticket-create/` e aggiornare i JSON (4 lingue).
- **Criterio di accettazione**: nessun JSON non-`tests.*` riferisce `blocks.tests.`.
- **Confidenza**: 95.

### PRD-07 — Foto e testi pubblicati senza strumento di oscuramento; allegati PDF ammessi
- **Gravita'**: Media — **Tipo**: Compliance (rilievo tecnico)
- **Evidenza**: la pubblicazione avviene con "Approva" (`ViewTicket.php:77-92` → `in_review`, stato pubblico in `TicketStatusEnum.php:48-55`); le foto sono esposte nel dettaglio e nel tracciamento (`BuildPublicTicketDetailAction.php:115-127`, `ticket-track.blade.php:131-150`); nessuna azione di oscuramento/redazione nel pannello (ricerca `oscur|redact|blur` in `Modules/Fixcity/app/Filament`: nessun risultato); i testi legali delegano al cittadino ("Non mettere nelle foto … volti riconoscibili, targhe", `Modules/Fixcity/resources/templates/legal-notes.it.md:45`) e dicono "L'ente puo' oscurare". Gli EXIF/GPS sono rimossi (`policy.md`, `TicketMediaObserver`). Allegati sul disco `public` (`config/local/fixcity/media-library.php:34`), anche per segnalazioni `pending`/rifiutate.
- **Impatto**: volti e targhe possono finire sulla mappa pubblica dopo un'approvazione distratta; i file di segnalazioni rifiutate restano serviti da URL pubblici.
- **Correzione**: nell'azione "Approva" aggiungere una checklist obbligatoria ("foto controllate per dati di terzi") e un'azione "Nascondi allegato dal pubblico"; servire gli allegati di ticket non pubblici da disco privato o con URL firmati.
- **Criterio di accettazione**: un allegato marcato nascosto non compare in `BuildPublicTicketDetailAction`; l'URL di un allegato di ticket `pending` restituisce 403/404 a un anonimo.
- **Confidenza**: 65 — nomi file degli upload probabilmente casuali (Filament), quindi l'enumerazione e' difficile; la mancanza dello strumento di oscuramento e' certa.

### PRD-08 — Tipi di segnalazione cablati e dati del Comune non configurabili dal pannello
- **Gravita'**: Media — **Tipo**: Funzionale
- **Evidenza**: il wizard usa `Select::make('type')->options(TicketTypeEnum::class)` (`TicketForm.php:324-326`), 15 valori fissi (`Enums/TicketTypeEnum.php:96-111`); il modello `Models/Category.php` alimenta solo `/lista-categorie` (`ListPublicCategoriesAction.php:19`) e non ha Resource Filament ne' relazione con `Ticket`; i dati dell'ente sono un file PHP (`config/local/fixcity/ente.php:22-43`).
- **Impatto**: ogni Comune che vuole categorie proprie (es. "verde pubblico" separato da "arredo urbano") o cambiare PEC/DPO deve chiedere un rilascio software; due tassonomie divergenti (tipo vs categoria).
- **Correzione**: rendere `type` una relazione con `Category` gestita da una Resource Filament; esporre `ente` in una pagina impostazioni (Tenant `SettingPage`).
- **Criterio di accettazione**: un amministratore crea una categoria dal pannello e la vede nel wizard senza deploy; cambia la PEC e la vede nel footer e in `/it/privacy`.
- **Confidenza**: 85.

### PRD-09 — "Multi-comune" e' multi-istanza per host, senza onboarding
- **Gravita'**: Media (rispetto alla promessa) — **Tipo**: Funzionale / Prodotto
- **Evidenza**: tenant = directory `config/<tld>/<nome>` scelta dall'host (`GetTenantNameAction.php:26-36,65-97`); DB del tenant da `env('DB_DATABASE', 'fixcity_data')` (`config/local/fixcity/database.php:7-8`), quindi un processo = un DB; `Ticket` senza colonna comune; nessun comando o procedura di creazione tenant (unico file in `Modules/Tenant/app/Console/Commands` e' `TestCommand.php`); `config/local/fixcity/modules_statuses.json:2,19` contiene `"<nome progetto>"`, `BarberShop`, `RealEstate`, `Food` (copiato da altri prodotti); `config/it/` contiene tenant di altri progetti (`quaeris`, `qualitymonitor`).
- **Impatto**: attivare un nuovo Comune richiede intervento tecnico (copia cartella config + JSON + .env/deploy dedicato); la promessa di `PRODUCT.md:25` non e' dimostrabile a un acquirente.
- **Correzione**: definire (story BMAD) il modello: multi-istanza documentata con comando `tenant:create {host}` che copia i JSON da `Modules/Fixcity/config/database/content` e crea `ente.php`, oppure vero multi-tenant con `tenant_id` su ticket/utenti.
- **Criterio di accettazione**: un comando crea un tenant funzionante per un nuovo host in un ambiente pulito; documento in `Modules/Tenant/docs/`.
- **Confidenza**: 70 — la configurazione database per vhost potrebbe essere gestita fuori repo (env per vhost Apache).

### PRD-10 — File e rotte vietati nella cartella pages del tema
- **Gravita'**: Bassa — **Tipo**: Regola repo / Obsoleto
- **Evidenza**: `pages/counter.blade.php:16-17,46-50` (pagina di prova di login con contatore, riusa il nome Volt `auth.login` contro il commento di `pages/auth/login.blade.php:51-53`); `pages/pages/[slug].blade.php:19,26,42` (alias generico che rende qualunque pagina CMS a `/{lang}/pages/{slug}`); `pages/area-personale/` (PRD-05). `counter` e' bloccato solo dal middleware del mount tema (`app/Http/Middleware/BlockLegacyPublicPages.php:32-50`, registrato in `app/Providers/FolioServiceProvider.php:33-40`); il mount dei moduli (`Modules/Cms/app/Providers/FolioVoltServiceProvider.php:162-186`) non lo applica, e i moduli hanno ancora `Modules/Fixcity/resources/views/pages/prova.blade.php`, `home.blade.php`, `tickets/*.blade.php`.
- **Impatto**: URL duplicati per la stessa pagina CMS (SEO, layout diverso), superficie di regressione.
- **Correzione**: eliminare `counter.blade.php`, `pages/pages/`, `pages/area-personale/` e le pagine Folio duplicate nei moduli; aggiungere al test la scansione delle `resources/views/pages` dei moduli.
- **Criterio di accettazione**: `NoSemanticFolioPageDirectoriesTest` verde; `GET /it/pages/home` → 404.
- **Confidenza**: 80 (raggiungibilita' delle pagine dei moduli non verificata).

### PRD-11 — Modulo Mobile con rotta inesistente
- **Gravita'**: Bassa — **Tipo**: Funzionale
- **Evidenza**: `Modules/Mobile/resources/views/pages/mobile/index.blade.php:9` `route('mobile.create')`, nessuna definizione (`rg "mobile.create"` solo qui); modulo attivo in `modules_statuses.json:22` (radice) ma assente nello statuses del tenant; testi solo italiani con emoji, colori fissi (`mobile/map.blade.php:8-12`).
- **Impatto**: se attivo, `/it/mobile` va in errore 500; esperienza mobile parallela incoerente con Design Comuni.
- **Correzione**: disattivare il modulo o rimuovere le pagine; la "velocita' mobile" va ottenuta nel wizard principale.
- **Criterio di accettazione**: `/it/mobile` 404 o pagina funzionante senza link rotti.
- **Confidenza**: 55 (stato di attivazione effettivo non determinabile staticamente).

### PRD-12 — Integrazioni PA assenti: protocollo, PEC in uscita, app IO
- **Gravita'**: Media (commerciale) — **Tipo**: Funzionale — **da validare con i requisiti**
- **Evidenza**: nessun codice per protocollo informatico, app IO o PEC (ricerca `protocollo|app io|io.italia|pagopa` in `Modules/*/app`: solo enum/indirizzi). La comunicazione dell'operatore e' una email semplice (`RecordTicketCommunicationAction.php:60`).
- **Impatto**: molti Comuni chiedono la protocollazione delle istanze e notifiche via IO; senza, Fixcity resta un canale "parallelo".
- **Correzione**: raccogliere i requisiti del primo Comune; prevedere un connettore (Action + coda) verso il protocollo e un canale notifiche IO nel modulo Notify.
- **Criterio di accettazione**: requisito scritto in una story; poi una segnalazione approvata genera un numero di protocollo memorizzato e visibile nella timeline interna.
- **Confidenza**: 85 per l'assenza; necessita' da validare.

### PRD-13 — Codice pubblicitario (AdSense, "Spazio in vendita") nel tema di un portale PA
- **Gravita'**: Bassa — **Tipo**: Obsoleto / Compliance
- **Evidenza**: `config/comune.php:35-60` (`adsense.footer_enabled` default `true`); `Themes/Sixteen/resources/views/components/footer-comune.blade.php:156-210` (script `pagead2.googlesyndication.com`); `sections/footer-full.blade.php:149-151` ("Spazio in vendita" verso `/pubblicita`, pagina inesistente). Il footer attivo e' `components/sections/footer/v1.blade.php` (da `sections/footer.json`), che non li include.
- **Impatto**: oggi nessuno; se riattivato, cookie di profilazione e pubblicita' contraddicono la cookie policy ("solo cookie tecnici").
- **Correzione**: rimuovere i blocchi AdSense, `config/comune.php` e `components/footer-comune.blade.php` (anche `config('comune.nome', 'Nome Comune')` e' un segnaposto).
- **Criterio di accettazione**: `rg adsbygoogle Themes/Sixteen` vuoto.
- **Confidenza**: 75.

### PRD-14 — Documentazione di prodotto incoerente
- **Gravita'**: Bassa — **Tipo**: Documentazione
- **Evidenza**: `README.md:1-3` descrive Notify; `DESIGN.md:39-41` stima "~300 `!important`" in due file, ma `resources/css/ticket-parity.css` ne ha 1939, `homepage-parity-v2.css` 349, `civic-design-global.css` 228 (conteggio grep); `PRODUCT.md:21` vs `:24` (account obbligatorio vs "senza flusso account che scoraggi").
- **Correzione**: README di prodotto; aggiornare i numeri di DESIGN.md; riformulare la promessa mobile.
- **Confidenza**: 95.

### UX-01 — Dichiarazione di accessibilita': problemi gia' noti e non chiusi
- **Gravita'**: Alta — **Tipo**: Accessibilita'
- **Evidenza**: `accessibilita.index.json` dichiara `partially_compliant` (valutazione 2026-10-26) e elenca: focus non visibile nelle FAQ (2.4.7); testo al 200% tagliato in home a 375 px (1.4.4); wizard: focus dopo "Avanti", errori non annunciati, "Usa la tua posizione" non attivabile con spazio, mappa senza nome (2.4.3, 3.3.1, 4.1.3, 2.1.1, 4.1.2); popup della mappa non usabile da tastiera; contrasto 3,77:1 e 4,13:1 nell'intestazione (1.4.3). Nel codice c'e' gia' gestione del focus dopo un "Avanti" respinto (`components/wizard/data-step.blade.php:22`) e `aria-live` sui passi (`:45`), quindi alcune voci potrebbero essere superate.
- **Impatto**: utenti con tastiera o screen reader non completano la segnalazione; obbligo di correggere o motivare.
- **Correzione**: una story per voce; riverificare il wizard con NVDA/VoiceOver; aggiornare la dichiarazione dopo ogni correzione.
- **Criterio di accettazione**: audit axe + prova manuale con lettore di schermo su `/it/tickets/create` e `/it/tickets` senza violazioni AA; elenco `non_accessible` aggiornato.
- **Confidenza**: 75 — gli elementi sono dichiarati dal progetto stesso; non riverificati qui.

### UX-02 — Colori fissi e debito di specificita' CSS
- **Gravita'**: Media — **Tipo**: Accessibilita' / Manutenibilita'
- **Evidenza**: 1113 occorrenze di colori esadecimali nelle viste Blade del tema (escluse `pages_old` e `tests`), 27 viste con `style="…color…#hex"`; esempio `components/utilities/google-button.blade.php:26-28`; colori di stato nei marker in esadecimale (`Modules/Fixcity/app/Enums/TicketStatusEnum.php:54-60`) non legati ai token del tema; `!important` vedi PRD-14.
- **Impatto**: il cambio colore per Comune (multi-comune) e il tema ad alto contrasto non si applicano in modo uniforme; rischio di contrasti insufficienti.
- **Correzione**: sostituire con custom properties di `civic-design-theme.css`; verificare il contrasto dei colori di stato su sfondo mappa.
- **Criterio di accettazione**: zero `style=` con colori nelle viste FO attive; contrasto >= 4.5:1 per testo, >= 3:1 per marker.
- **Confidenza**: 70 (conteggio grezzo, include commenti e SVG).

### UX-03 — Mappa senza stati di caricamento e di errore visibili
- **Gravita'**: Media — **Tipo**: UX / Accessibilita'
- **Evidenza**: `Modules/Geo/resources/js/components/map-lit.js:424-482` — `fetch` del GeoJSON, errori solo `console.error` (`:430,:482`), nessun messaggio in pagina, nessun `aria-live`; `:194-195` init fallito solo in console.
- **Impatto**: con rete lenta o API in errore il cittadino vede una mappa vuota e conclude che non ci sono segnalazioni (contro la promessa di trasparenza).
- **Correzione**: stato "Caricamento segnalazioni…" e messaggio d'errore con link all'elenco, in una regione `role="status"`.
- **Criterio di accettazione**: con `/api/tickets/geojson` che risponde 500 la mappa mostra un messaggio leggibile e annunciato.
- **Confidenza**: 80.

### UX-04 — Componente di navigazione del wizard con testi non tradotti e "Salva" che invia
- **Gravita'**: Bassa (oggi non montato) — **Tipo**: UX / i18n
- **Evidenza**: `Themes/Sixteen/resources/views/components/wizard/cmp-nav-steps.blade.php:30,39` ("Salva richiesta" chiama `$wire.save()`, che crea il ticket: `CreateTicketWizardWidget.php:85-130`), `:50` testo `'Invia' : 'Avanti'` cablato in italiano. Nessuna vista attiva lo include (il wizard attivo usa `wizard.blade.php:71-90` e `report-partials/steps-nav.blade.php`).
- **Impatto**: se riusato, invii prematuri e testi italiani in en/de/es.
- **Correzione**: eliminare il componente.
- **Confidenza**: 85.

### UX-05 — Validazioni e messaggi del wizard
- **Gravita'**: Bassa — **Tipo**: UX
- **Evidenza**: messaggi propri del passo dati per evitare l'italiano in tutte le lingue (`TicketForm.php:209-223`); privacy con focus sull'errore (`filament/widgets/create-ticket-wizard.blade.php:13-29,44-46`); sessione scaduta con "Accedi di nuovo" (`CreateTicketWizardWidget.php:87-93,193-202`); doppio invio disabilitato (`steps-nav.blade.php:8,24`). Punto debole: il messaggio generico `ticket_form.validation.required` per tutti i campi.
- **Impatto**: positivo nel complesso; messaggi poco specifici.
- **Correzione**: messaggi per campo ("Indica il luogo sulla mappa").
- **Confidenza**: 70.

### UX-06 — Dettagli della segnalazione limitati a 200 caratteri
- **Gravita'**: Bassa — **Tipo**: UX / Prodotto
- **Evidenza**: `TicketForm.php:358` (`maxLength(200)` nel front office, motivato dalla parita' con il riferimento Design Comuni); il back office non ha il limite (`:333-336`).
- **Impatto**: descrizioni di problemi complessi troncate; il cittadino puo' abbandonare.
- **Correzione**: da validare con il Comune; contatore di caratteri visibile e limite piu' ampio (es. 1000).
- **Confidenza**: 90.

### UX-07 — Copertura traduzioni de/es incompleta nei testi effettivamente usati
- **Gravita'**: Media — **Tipo**: i18n
- **Evidenza**: lingue dichiarate `it, en, de, es` (`config/laravellocalization.php:39,46,51,84`). Chiavi `__('fixcity::…')`/`__('pub_theme::…')` usate nelle viste e nel codice FO (2053) mancanti: it 92, en 108, de 212, es 212 (script php sui file lang). Esempi mancanti in de/es: `fixcity::ticket.privacy.not_configured`, `ticket.notification.title`, `ticket.notification.status_changed` (email e notifiche), `ticket.subscription.*`, `ticket_kpi.*`, `ticket.warning.*`. File `ticket.php`: it 393 chiavi, de/es 33. Tema Sixteen: de/es mancano ~560 chiavi rispetto a it (`ui` 120, `segnalazione` 76, `ticket` 60, `footer` 34). Modulo Fixcity: `tickets.php` it 2349 chiavi assenti nelle altre lingue (probabile file generato). Con `fallback_locale = it` (`config/app.php:137`) l'utente vede italiano; dove anche it manca (es. `fixcity::ticket.filters.apply/reset` in `blocks/ticket-list/mobile-filters-modal.blade.php:82,86`) vede la chiave grezza.
- **Impatto**: utenti tedeschi/spagnoli ricevono notifiche e messaggi in italiano; possibile chiave grezza nei filtri mobili.
- **Correzione**: test che confronta le chiavi usate dalle viste FO con i 4 file lang (simile a `PersonalAreaPagesTest:83-84`); completare de/es; ridurre le lingue dichiarate se non mantenute.
- **Criterio di accettazione**: 0 chiavi mancanti nelle 4 lingue per le viste FO attive.
- **Confidenza**: 65 — l'estrazione regex include viste non montate; il numero esatto di chiavi davvero visibili e' inferiore.

### UX-08 — Pagine CMS senza blocchi de/es e JSON orfani
- **Gravita'**: Bassa — **Tipo**: i18n / Contenuto
- **Evidenza**: senza blocchi `de`/`es`: `pages/tickets/index.json`, `services/index.json`, `lista-categorie.index.json`, `mappa-sito.index.json`, `segnalazioni.index.json` (verifica python); fallback alla lingua primaria in `Modules/Cms/app/Models/Traits/HasBlocks.php` (`getBlocks`, righe ~48-58). Orfani: `segnalazioni.index.json` (l'URL `/segnalazioni` e' 301 verso `/tickets`, `FrontofficeContainerAliases.php:25`) e `segnalazione-dettaglio.json` (slug senza `.index`, non raggiungibile da `[container0]`). Duplicato integrale dei JSON in `Modules/Fixcity/config/database/content/pages` (122 file, identici per i campioni confrontati).
- **Impatto**: basso (i blocchi elenco usano chiavi lang); confusione su quale JSON modificare.
- **Correzione**: aggiungere de/es o rimuovere le lingue; eliminare gli orfani; dichiarare il duplicato del modulo come "modello per nuovi tenant".
- **Confidenza**: 75.

### UX-09 — Social con URL segnaposto nel tenant
- **Gravita'**: Bassa — **Tipo**: Contenuto
- **Evidenza**: `config/local/fixcity/social.php:8-23` (facebook, instagram, pinterest, vimeo con `'url' => '#'`); i social attivi vengono invece da `ente.php` (vuoto, quindi nascosti).
- **Correzione**: rimuovere `social.php` o allinearlo a `ente.social`.
- **Confidenza**: 60 (uso effettivo non tracciato).

## Confronto con quanto serve per vendere a un Comune italiano

| Requisito | Stato nel codice | Esito |
|---|---|---|
| Modello Design Comuni "Segnalazione disservizio" (privacy → dati → riepilogo → conferma, elenco e dettaglio) | Implementato con markup di parita' (`PROJECT.md`, `tickets/*.json`, viste `design-comuni`) | Coperto, da verificare con il validatore/checklist Designers Italia |
| Accesso SPID/CIE (art. 64 CAD) | Assente | PRD-01 — da validare con i requisiti |
| Dichiarazione di accessibilita' AgID | Pagina presente, dati dell'ente a segnaposto, non collegata a form.agid.gov.it | PRD-04, UX-01 |
| Informativa art. 13 GDPR, DPO, conservazione | Testo completo e ben strutturato; segnaposto dell'ente vuoti; purge schedulato | PRD-04 |
| Cookie (solo tecnici + banner) | Banner Gdpr; tile OSM/Esri e Google Fonts dichiarati nell'informativa | Coperto, da verificare in browser |
| Note legali / licenza del software | Note legali presenti; codice `MIT` in `laravel/composer.json:10`; nessun `LICENSE` ne' `publiccode.yml` alla radice | Da validare: per il riuso PA (Linee guida acquisizione e riuso, Developers Italia) servono LICENSE e publiccode.yml |
| App IO | Assente | PRD-12 — da validare |
| Protocollo informatico / PEC | Assente | PRD-12 — da validare |
| Multi-ente / onboarding | Parziale | PRD-09 |
| Conformita' a linee guida di design (misurazione) | Non verificabile staticamente | Da validare |

## Cosa non ho potuto verificare

- **Nessuna esecuzione**: app, browser, test Pest e richieste HTTP non eseguiti; tutti gli stati "implementata" sono quindi **non verificati** a runtime.
- **Precedenza Folio** fra i mount (tema montato due volte, moduli montati con le stesse URI `/{lang}`): non determinabile con certezza staticamente (PRD-05, PRD-10, PRD-11).
- **Configurazione di produzione**: `.env`, vhost Apache, cron dello scheduler, disco media effettivo, se `config/local/fixcity/modules_statuses.json` sostituisce quello di radice.
- **Accessibilita' reale**: contrasto, focus, lettori di schermo, zoom 200%, dimensioni dei bersagli 44 px: solo dedotti dal codice e dalla dichiarazione del progetto.
- **Mobile**: numero reale di tap/tempo per una segnalazione, comportamento fotocamera e geolocalizzazione su dispositivi.
- **Contenuti dinamici**: segnalazioni, notizie ed eventi in database (eventuali dati demo caricati in produzione); i seeder demo sono protetti per ambiente (`FixcityDatabaseSeeder.php:20`) tranne `Modules/Fixcity/database/seeders/DatabaseSeeder.php:16-29`, non chiamato dal seeder radice.
- **Valutazioni legali**: i punti GDPR/CAD/L. 4/2004 sono rilievi tecnici, non pareri.
