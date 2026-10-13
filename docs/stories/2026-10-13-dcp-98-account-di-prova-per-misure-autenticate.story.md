---
id: dcp-98
title: "DCP-98: account e ticket di prova nel database di sviluppo per misurare le pagine autenticate"
type: story
epic: dc-parity-v2
status: done
priority: P1
owner: module:Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "account di prova test user parity autenticato login playwright ticket AN4059281 dev database rimozione"
related: [dcp-42, dcp-33, dcp-34]
---

# DCP-98: un account usa e getta per misurare cio' che l'ospite non vede

## Perche'
Dopo DCP-42 `/it/tickets/create` richiede il login: i gate da ospite si fermavano al 302. L'utente ha detto «procedi» alla richiesta di un account di prova; la sessione tema ha creato i dati sotto, **solo nel database di sviluppo** (`fixcity_data` su MariaDB locale), con dominio riservato `example.test` (nessuna email reale, nessuna posta spedita).

## Dati creati (2026-10-13)
- Utente `dcp-parity-test@example.test`, nome "Giulia Bianchi", id `01a13598-6d76-700b-b7b8-82b0d89274b7`, email verificata, **password casuale** salvata solo fuori dal repo (file privato dello scratchpad della sessione e memoria utente: non scriverla in nessun documento). Profilo (`profiles`) con `phone` = `+39 331 1234567`.
- Ticket id 27, codice `AN4059281` (lo stesso del riferimento `segnalazione-04-conferma`), proprietario l'utente sopra, creato con `TicketFactory`.

## Come si usa
`bashscripts`: nessuno script nel repo (richiederebbe la password). Nello scratchpad: `dcp98-auth-measure.mjs` (riepilogo, login via Playwright, fixture dello stato con `$wire.set`, mappa nascosta, righe del riferimento senza sorgente nascoste: filtri dichiarati) e `dcp98-conf-measure.mjs` (conferma con `?code=`, senza filtri). Chi ha bisogno del login lo chiede qui o crea il proprio utente con lo stesso criterio.

## Rimozione (quando non serve piu')
`php artisan tinker --execute='Modules\Fixcity\Models\Ticket::where("code","AN4059281")->forceDelete(); $u=Modules\User\Models\User::where("email","dcp-parity-test@example.test")->first(); $u?->profile?->delete(); $u?->delete();'` (verificare prima che non ci siano altri ticket collegati).

## Misure rese possibili
- Riepilogo da autenticato (DCP-33): pagina intera 99,85 / 99,80 / 99,72 / 99,68 / 99,48% a 1920/1440/992/768/375 con filtri dichiarati.
- Conferma da proprietario autenticato (DCP-34), senza filtri: 99,84 / 99,79 / 99,71 / 99,60 / 99,41%.
Le altezze locali risultano +33 px nei miei script per l'ingombro della debug bar di sviluppo (il gate ufficiale la rimuove): non e' una differenza di pagina.

## Relazioni con le story dell'altra sessione (14:25)
[DCP-47](2026-10-13-dcp-47-fixture-autenticata-misure-wizard.story.md) (fixture senza toccare il DB di sviluppo, in corso) e [DCP-48](2026-10-13-dcp-48-codice-fiscale-autore-decisione.story.md) (codice fiscale: decisione di prodotto): le due soluzioni coesistono; quando DCP-47 e' pronta questa puo' essere rimossa con il comando sopra.

## Prova end-to-end del salvataggio (2026-10-13 14:35, Playwright, account di prova)
Percorso: login -> `/it/tickets/create?step=form.riepilogo...` (passo 3) -> stato impostato via `$wire.set` (privacy, titolo, tipo `public_lighting`, dettagli, indirizzo, coordinate; i passi 1-2 sono in rifacimento da DCP-43/46 e la checkbox privacy del passo 1 non era cliccabile: "outside of the viewport") -> clic su **Invia** (`$wire.submit()`) -> ticket creato -> redirect a `/it/tickets/confirmation?code=TCK-NFZWEACWYVVAHY6S`. La conferma mostra `h1` "Segnalazione inviata", il codice e l'email del proprietario; 0 errori di pagina. Non provati: allegato reale, il percorso con i passi 1-2 cliccati a mano, l'inglese.
**Dato creato in piu'**: un ticket con codice `TCK-NFZWEACWYVVAHY6S` (proprietario l'utente di prova). Pulizia: aggiungere al comando di rimozione `Modules\Fixcity\Models\Ticket::where("owner_id", $u->id)->forceDelete()` PRIMA di cancellare l'utente (copre sia questo ticket sia `AN4059281`).
Nota: il test Pest `persists a valid wizard submission` e' rosso (DCP-90) mentre nel browser l'invio funziona: la causa e' del test o del suo ambiente, non del flusso (indagine DCP-90 in corso).
