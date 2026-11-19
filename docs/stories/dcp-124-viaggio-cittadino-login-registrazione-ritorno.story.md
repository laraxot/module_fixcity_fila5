---
id: dcp-124
title: "DCP-124: il viaggio del cittadino (registrazione, login, ritorno alla pagina richiesta) ha senso"
type: story
epic: dc-parity-v2
status: done
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [auth, journey, registrazione, login, intended, ux]
related:
  - Themes/Sixteen/docs/bmad/stories/dcp-104-pagine-reali-elenco-e-dettaglio-segnalazioni.story.md
  - Modules/Fixcity/docs/stories/dcp-35-tickets-create-privacy-dati-coordinamento.story.md
  - Themes/Sixteen/docs/decision-log/D18-login-e-registrazione-tornano-alla-pagina-richiesta.md
qmd: "dcp-124 viaggio del cittadino registrazione login url.intended redirectIntended dashboard 404 segnala disservizio"
---

# DCP-124: registrazione, login e ritorno alla pagina richiesta

## Richiesta (2026-10-13, utente)
«Per entrare creati un utente utilizzando gli strumenti che da a disposizione il sito [...] tutto deve avere un senso, una funzionalita', una logica.» Applicata a `/it/tickets`, il cui invito a segnalare («Segnala disservizio») e' la porta d'ingresso del prodotto.

## Fase BMAD
Analysis + Dev + Verify. Nessuna modifica all'elenco `/it/tickets`: quello e' di DCP-104 (claim dcp113); questa story copre cio' che l'elenco promette ("accedi e segnala") e che nessuno aveva percorso con un utente vero.

## Perche' (scopo)
`/it/tickets` e' pubblica e in sola lettura; la sola azione utile per un cittadino e' segnalare, e segnalare richiede un account (D17: un ticket anonimo e' rifiutato da widget, action e DB). Quindi il percorso ospite -> «Segnala disservizio» -> accesso o registrazione -> modulo di segnalazione e' il percorso critico del prodotto. Se si rompe in un punto, la funzione dell'elenco (trasparenza + invito a partecipare) non produce segnalazioni.

## Prova eseguita (utente creato dal form di registrazione del sito, dev, 390x844)
Playwright su `http://192.168.1.40:8000`, utente `@example.test` creato da `/it/auth/register` (password casuale in un file privato dello scratchpad, mai nel repo):

| Passo | Prima | Dopo (fixture isolata, DB usa-e-getta) |
|---|---|---|
| Registrazione completata | atterra su `/it/dashboard` = **404** | atterra su `/it` (o sulla pagina richiesta) |
| Login da `/it/auth/login` | `/it` | `/it` (invariato) |
| Ospite -> `/it/tickets/create` -> login | atterra su `/it`: l'URL richiesto va perso, il cittadino deve rifare il percorso | torna a `/it/tickets/create` |
| Pagine d'area dopo il login | `area-personale/pratiche` 200 «Le mie segnalazioni», `impostazioni` 200 | invariato |
| `/it/area-personale` (senza sotto-pagina) | 404 | invariato, vedi Aperto |

## Causa radice (verificata nel codice)
- `Modules/Gdpr/.../HandleSuccessfulRegistrationAction::execute()` faceva `$widget->redirect(route('dashboard'))` senza controllo. La route `dashboard` e' una pagina Folio del modulo User (template «Genesis» con testo promozionale e immagine remota, `middleware(['auth','verified'])`), oscurata dal catch-all `pages/[container0]` del tema: nel front office `/it/dashboard` risponde 404.
- `Modules/User/.../LoginWidget::login()` usava `redirect(...)` e ignorava `url.intended`, che `redirectGuestsTo` di `bootstrap/app.php` imposta correttamente quando un ospite apre una pagina protetta.

## Cambio (minimo, alla radice)
Entrambi usano `redirectIntended(default: ...)` di Livewire 4.4.7 (`HandlesRedirects::redirectIntended`): torna alla pagina richiesta; altrimenti `route('dashboard')` se esiste (altri progetti che usano il modulo User la hanno), altrimenti `/{lingua}`. File: `Modules/User/app/Filament/Widgets/Auth/LoginWidget.php`, `Modules/Gdpr/app/Actions/Registration/HandleSuccessfulRegistrationAction.php`.

## Verifica
- Test: `Modules/Fixcity/tests/Feature/Auth/AuthReturnsToRequiredPageTest.php` (3 test, 8 asserzioni) su DB usa-e-getta (`pest-scratch-db.sh` con `APP_CONFIG_CACHE=/nonexistent/config.php`); conteggi del DB di sviluppo prima/dopo identici (utenti 10, ticket 15). Mutation check: senza il cambio al `LoginWidget` il test del ritorno fallisce (1 failed, 2 passed).
- Browser: fixture `scratch-auth-fixture.sh` (porta 8091, DB scratch) con Playwright: login da `/it/tickets/create` -> `/it/tickets/create`; registrazione -> `/it`.
- I test dei moduli Cms e Gdpr non girano con il runner di root («A facade root has not been set», anche i test preesistenti): per questo i nuovi test stanno in Fixcity, che dichiara `uses(TestCase::class)` file per file.

## Norme e senso (non un parere legale)
- GDPR art. 5(1)(c) minimizzazione e art. 25 protezione dei dati fin dalla progettazione: dopo l'accesso non si chiede di rifare un percorso (meno passaggi, meno dati reinseriti).
- CAD art. 7 (diritto a servizi online) e Linee guida AgID design servizi digitali PA: il flusso di autenticazione non deve interrompere il servizio richiesto. Evidenza: ritorno alla pagina richiesta.
- Limite dichiarato: l'accesso e' solo email e password; SPID/CIE (CAD art. 64) non esiste ancora (vedi DCP-54). Il testo del riferimento «dopo esserti autenticato con SPID o CIE» NON va copiato nell'elenco finche' non esiste.

## Aperto (per i proprietari indicati, non toccato qui)
1. `/it/dashboard` resta una pagina «Genesis» nel modulo User, oscurata dal tema: o si elimina il nome `dashboard` dal front office o si crea una pagina CMS `dashboard.index` che porti all'area personale. Proprietario: DCP-52/DCP-106 (area personale).
2. `/it/area-personale` (senza sotto-pagina) risponde 404: un link logico dal menu utente porterebbe a `pratiche`.
3. Utente appena registrato: `middleware(['auth','verified'])` nelle pagine del modulo User + invio email di verifica solo se `auth.must_verify_email`; in dev la posta esterna non e' provata (memoria `local-mail-postfix-sendmail-verified`). Da decidere con il DPO se la verifica email e' requisito.
4. Intestazione per l'utente autenticato (mobile): il testo del `header` non mostra nome ne' uscita; e' DCP-52.
5. Coordinamento: un'altra sessione ha convertito a Tailwind `components/blocks/ticket/*` e `ticket-layout/layout` dentro il chrome Design Comuni (pixel 54% a 1440). Sostituito dal blocco `ticket-list/design-comuni` di dcp113; i file `ticket/*` non servono piu' a `/it/tickets`.

## Second brain
Memorie: `tickets-index-was-hardcoded-native-folio-page`, `login-register-redirect-intended-dcp124`, aggiornamento di `parallel-claude-sessions-same-repo` (sessioni doppie sulla stessa richiesta).
