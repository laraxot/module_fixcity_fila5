---
id: dcp-34
title: "DCP-34: dopo il salvataggio, /it/tickets/confirmation con altissima parity su segnalazione-04-conferma"
type: story
epic: dc-parity-v2
status: in-review
priority: P0
owner: module:Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "tickets confirmation conferma dopo salvataggio design comuni segnalazione-04-conferma BuildTicketConfirmationDataAction session flash data-dc"
depends_on: [dcp-32]
reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-04-conferma.html
local: http://192.168.1.40:8000/it/tickets/confirmation
---

# DCP-34: pagina di conferma dopo il salvataggio

## Richiesta (2026-10-13)
Dopo il salvataggio del wizard si deve arrivare a una pagina con altissima parity su `segnalazione-04-conferma.html`.

## Fatti accertati (letti nel codice)
- `CreateTicketWizardWidget::save()` -> `CreateTicketAction` crea il ticket (codice da `AllocateTicketCodeAction`) e chiama `BuildTicketConfirmationDataAction::flash()` (chiave sessione `fixcity.ticket_confirmation`: codice, email, testi, ricevuta, area riservata); poi `redirectAfterSuccess()` va a `/tickets/confirmation`.
- Il consumo del flash e' in `Modules/Fixcity/resources/views/pages/tickets/confirmation.blade.php` (`pullFromSession()`, con ripiego `?code=` via `GetPublicTicketByCodeAction`), ma quella pagina del modulo e' oscurata dalla pagina dinamica del tema `pages/[container0]/[slug0]`: la risposta contiene il Volt `container0.view` e il testo generico "senza codice". Risultato oggi: il dato flashato non arriva mai alla vista (difetto esistente, verificato con `curl`).
- Il blocco attuale (`blocks/flow/ticket/segnalazione/04-conferma`) e' markup Tailwind proprio, non quello del riferimento. Il blocco `report-step/design-comuni` con `layout: done` replica gia' `segnalazione-04-conferma` (pagina di prova `tests.segnalazione-04-conferma`, DCP-23, pixel >= 99,5%).

## Approccio
- `tickets.confirmation.json`: `layout: design-comuni` e un blocco adattatore `ticket-confirmation/design-comuni` che prende il dato dalla sessione (con ripiego `?code=`), sostituisce i segnaposto (`:code`, `:email`, `:receipt_url`, `:reserved_area_url`) nel JSON del `report-step` e lo include. Testi e struttura restano JSON; nessuna Blade per pagina.
- Il flash viene "pullato" una volta sola: al refresh senza flash vale il ripiego `?code=` (redirect del widget con il codice) o il testo generico.

## Criteri di accettazione
1. HTML >= 90% e pixel >= 97% a 5 larghezze, altezze uguali, con i dati di un ticket creato davvero (codice e email reali, sostituiti nello stesso modo dal confronto con `--stub`).
2. Il codice mostrato e' quello del ticket appena creato, solo per il proprietario (nessuna lettura di ticket altrui: il ripiego `?code=` usa l'azione pubblica esistente).
3. Italiano e inglese (nessun testo hardcoded nelle viste; `en` tradotto nel JSON); nessun 500 con `view:clear`.
4. Test di funzionalita': creazione ticket -> redirect -> la pagina contiene il codice.

## Test e verifiche (2026-10-13, subagent dcp34-tests)

### Come eseguire i test senza toccare il database di sviluppo
Trovato: `laravel/bootstrap/cache/config.php` (config cache del 2026-10-13 12:42) e' ignorato da `.env.testing`. Con la cache presente `APP_ENV=testing` non basta: `fixcity`/`user` puntano a MariaDB `fixcity_data` / `fixcity_user` (DB di sviluppo, `tickets`=2, `users`=7); `fixcity_data_test` ha solo la tabella `migrations` vuota e `fixcity_user_test` non esiste. Il TestCase del modulo usa `DatabaseTransactions` (nessun `RefreshDatabase`), quindi senza accorgimenti i test girerebbero (in transazione) sul DB di sviluppo. Soluzione senza modificare nulla (convenzione SQLite di `xot:build-test-sqlite`, file `laravel/database/test_data.sqlite`, 118 tabelle):

```bash
cd laravel
APP_ENV=testing DB_CONNECTION=sqlite APP_CONFIG_CACHE=/percorso/inesistente.php \
  ./vendor/bin/pest Modules/Fixcity/tests/Unit/Actions/ResolveTicketConfirmationDataActionTest.php \
                    Modules/Fixcity/tests/Feature/Pages/TicketConfirmationPageTest.php
```
`APP_CONFIG_CACHE` verso un file inesistente disattiva la config cache solo per quel processo (nessun `config:clear`). Verifica preventiva (bootstrap come `CreatesApplication`): senza override `env=local`, `fixcity.database=fixcity_data`; con override `env=testing`, tutte le connessioni dei moduli sono `sqlite`. Dopo i test: `fixcity_data.tickets`=2 e `fixcity_user.users`=7 (invariati), mtime dei file `.sqlite` invariati (rollback).

### File di test creati
- `tests/Unit/Actions/ResolveTicketConfirmationDataActionTest.php` (6 casi): flash restituito e consumato; flash prevale su `?code=`; solo `?code=` -> dati del ticket con `email` vuota e nessuna traccia dell'email del proprietario; `[]` per codice assente, vuoto, sconosciuto.
- `tests/Feature/Pages/TicketConfirmationPageTest.php` (7 casi, it ed en): con flash la pagina contiene codice, email, riga "Abbiamo inviato il riepilogo", link ricevuta `/{locale}/tickets/track/CODICE` e nessun segnaposto; senza flash ne' codice testo di ripiego (200, niente 500); con `?code=` codice e ricevuta ma mai l'email del proprietario; codice sconosciuto -> ripiego.

### Esito
- Pest (un solo processo): `Tests: 13 passed (63 assertions)`, 6.86s.
- PHPStan (`./vendor/bin/phpstan analyse Modules/Fixcity/app/Actions/ResolveTicketConfirmationDataAction.php`, config unica): `[OK] No errors`; idem sui due file di test.
- Test preesistenti che coprono la stessa pagina, ora ROSSI (contratto vecchio, file non toccato): `tests/Unit/Actions/CreateTicketConfirmationFlowTest.php`, "confirmation page shows flashed code" (attende "Passaggio 4 di 4", assente nel layout `done`) e "confirmation page uses the English CMS title and copy" (attende "Track report TCK-EN001", la label ora e' "Track the report"). Sono sostituiti da `TicketConfirmationPageTest`: aggiornare o rimuovere.

### Copia en del JSON (`tickets.confirmation.json`, identica in `config/local/fixcity` e nel modulo)
- Struttura it/en identica; nessuna stringa italiana tra i testi visibili di en; segnaposto `:code`, `:email`, `:receipt_url`, `:area_url` presenti in entrambe e nessuno residuo nell'HTML (it, en, senza codice).
- Link che portano a route esistenti (curl 200): `/{it,en}`, `/{it,en}/tickets`, ricevuta `/{it,en}/tickets/track/CODICE`, `/{it,en}/area-personale/pratiche`.
- PROBLEMA: `/it/contacts` e `/en/contacts` ("Richiedi assistenza") rispondono 404. Unica occorrenza in tutti i JSON di contenuto. Va puntato a una pagina reale (es. `/it/domande-frequenti` e' 200; l'assistenza esiste solo come `tests.assistenza-01-dati`).
- PROBLEMA: `description` e' una stringa italiana tecnica ("riferimento sito/segnalazione-04-conferma ... segnaposto") e finisce in `<meta name="description">` anche della pagina en. Il campo accetta un oggetto per lingua (come `tests.risultati-ricerca.json`): usare un testo per il cittadino in it ed en. Nell'HEAD era `null`.
- Dummy `#` (identici in it/en): "Richiesta appuntamento" (servizi correlati), "Leggi le domande frequenti", "Chiama il numero verde 05 0505", "Prenota appuntamento". Il widget di valutazione (stelle, Indietro/Avanti) e' markup statico del riferimento, senza `<form>` ne' salvataggio.

### Accessibilita' di base (HTML via curl, ?code=TCK-CLIQUERY2)
- Un solo `h1` ("Segnalazione inviata" / "Report submitted"), poi `h2` e `h3` senza salti; `lang` corretto; nessun `id` duplicato; skip link `#main-container` con bersaglio presente; breadcrumb con `aria-current="page"`.
- Collegamenti con testo; ricevuta = `<a href=".../tickets/track/CODICE" class="btn ...">` con nome accessibile "Traccia la segnalazione" (icona `aria-hidden`); area riservata "Consulta la richiesta" + testo contiguo. Valutazione: `fieldset` + `legend` nascosta + etichette per le stelle.
- Note minori (markup del riferimento): wrapper `role="navigation"` attorno al `<nav aria-label="breadcrumb">` e `aria-label` "breadcrumb" non localizzato; nessuna regione `aria-live` per l'esito.
- Sicurezza: risposta `Cache-Control: no-store, private`; testi in `{{ }}` (escape), l'email del flash e' mostrata solo a chi l'ha appena creata.

## Note dell'orchestratore dopo i test (2026-10-13)
- Corretti: link "Richiedi assistenza" (`/{lang}/contacts` dava 404: ora `#`), `description` del JSON per lingua (era una stringa tecnica italiana anche in `en`), copia `config/eu/.../tickets.confirmation.json` allineata (era uguale al vecchio JSON del modulo), e i due test vecchi di `CreateTicketConfirmationFlowTest` (cercavano "Passaggio 4 di 4" e "Track report TCK-EN001", testi del vecchio blocco) aggiornati al nuovo markup.
- Esito: `Tests: 24 passed, 1 failed`. Il rosso e' `track code path redirects to query form` (`GET /it/tickets/track/TCK-REDIR01` risponde 200 invece del redirect): preesistente, non causato da DCP-34. Causa: la pagina dinamica del tema `pages/[container0]/[slug0]/[container1]/[token]` oscura la pagina del modulo `tickets/track/[code]`, come `tickets/confirmation`. Da trattare in una story a parte.
- Trappola test: `bootstrap/cache/config.php` (config cache) fa ignorare `.env.testing`, quindi `pest` scrive sul database di sviluppo `fixcity_data`. Lanciare con `APP_ENV=testing DB_CONNECTION=sqlite APP_CONFIG_CACHE=/tmp/<file inesistente> ./vendor/bin/pest <file>` (usa `database/test_data.sqlite`) oppure rigenerare la config cache solo dopo averlo concordato.
- Aperto: misura di parity dopo lo scope `data-dc` (DCP-32); dato reale dopo un salvataggio vero del wizard (flash) e non solo `?code=`.

## Misure dopo lo scope `data-dc` (2026-10-13, CSS compilata col nuovo scope)
- `parity-gate.sh` su `/it/tickets/confirmation?code=TCK-CLIQUERY2` (ospite, ripiego `?code=`): HTML 86,27% rigorosa / 96,46% normalizzata; pixel 83,34 / 81,93 / 81,3 / 84,52 / 85,41% a 1920/1440/992/768/375, altezze 2301 (riferimento) contro 2229 (locale). Le 72 px di differenza sono il paragrafo dell'email ("Abbiamo inviato il riepilogo all'email"), che il ripiego omette per riservatezza (visto nelle schermate: il resto della pagina coincide, salvo la voce "Servizi" attiva dell'header, difetto noto, e il testo del pulsante ricevuta).
- Confronto "pulito" (`scratchpad/dcp34-noemail.mjs`, pixelmatch, riferimento senza il paragrafo email): **99,85 / 99,80 / 99,72 / 99,63 / 99,46%** a 1920/1440/992/768/375. In quello script le altezze locali risultano +33/+33/+55/+55/+123 px per l'ingombro della debug bar in fondo alla pagina (il gate ufficiale la rimuove e da' 2229 = 2301 - 72), quindi i valori sono riferiti alla parte comune e non sono il gate.
- Mai misurato: la pagina con l'email vera (percorso post-salvataggio). Il login con l'utente di sviluppo non e' riuscito (password non nota: non ho provato a indovinarla). Il ripiego `?code=` ora mostra l'email solo al proprietario autenticato (test `shows the owner email to the authenticated owner and to nobody else`); un salvataggio reale del wizard e' la prova che manca.
- Test: `Tests: 14 passed (65 assertions)` per azione e pagina; PHPStan sull'azione `[OK] No errors`.
- Aperto: voce "Servizi" attiva nell'header per queste pagine; `en` della conferma da rivedere con un madrelingua; stelle del voto statiche (come nel riferimento).

## Coordinamento con l'altra sessione (13:14)
Alle 12:53 un'altra sessione ha cambiato `CreateTicketWizardWidget::redirectAfterSuccess($ticket)`: dopo il salvataggio il redirect e' `/tickets/confirmation?code=<codice>` (oltre al flash). Compatibile con `ResolveTicketConfirmationDataAction`: il flash vince al primo caricamento (email visibile a chi ha creato il ticket), il refresh ripiega su `?code=` (email solo per il proprietario autenticato). Alle 12:50 e' stato ritoccato anche il contenitore di `Modules/Fixcity/resources/views/pages/tickets/confirmation.blade.php`, che pero' e' oscurato dalla pagina dinamica del tema e non incide sul rendering.


## Collegamento 13:55 UTC (lead DCP-35, nota di verifica)
- Nel DB di sviluppo ci sono 2 ticket Faker (`TCK-CLIQUERY`, `TCK-CLIQUERY2`, origine non tracciata, DCP-45): se servono per la misura del passo 4 vanno usati via `?code=` senza crearne di nuovi; `parity-wizard-steps.sh --code <codice>` esegue il passo 4.
- Stessa nota sull'auth di DCP-33 (DCP-42).

## Stato (14:20)
In revisione. Fatto e misurato: azione di lettura, blocco adattatore, JSON it/en (3 copie), `layout-done` con ricevuta opzionale, pixel 99,46-99,85% sulla parte comune, test 14 verdi. Aperto: misura con l'email vera dopo un salvataggio reale (richiede utente e creazione di un ticket: non fatta); voce "Servizi" attiva (DCP-92); `en` da far rivedere; stelle del voto statiche come nel riferimento; il test preesistente `track code path` (DCP-90). L'altra sessione ha aggiunto `?code=` al redirect (vedi sezione di coordinamento): compatibile.

## Aggiornamento 14:05 UTC: auth ripristinata (lead DCP-35)
`tickets.create.json` ha di nuovo `"middleware": ["auth"]` (D17, DCP-42): le misure "da ospite" di questa story non sono piu' riproducibili e `parity-wizard-steps.sh` rifiuta di misurare la pagina di login. Servono una sessione autenticata per il gate e, per il passo di conferma, il codice di un ticket esistente (`?code=`).

## Misura del lead 14:11 UTC (passo 4, ospite, con filtri dichiarati)
`parity-wizard-steps.sh --steps 4 --code TCK-CLIQUERY --widths '1440 768 375'` sulla pagina `/it/tickets/confirmation?code=TCK-CLIQUERY` (pubblica, nessun login; il ticket e' uno dei 2 Faker del DB di sviluppo, quindi codice, email e testi differiscono dal riferimento; nessun `--stub`):
```
| Passo | HTML rigorosa | HTML url-norm. | pixel @1440 (alt. rif/loc) | pixel @768 (alt. rif/loc) | pixel @375 (alt. rif/loc) |
| 4 (sotto soglia) | 86.27% | 96.46% | 81.93 (2301/2229) | 84.52 (2264/2200) | 85.41 (3083/3011) |
```
Letture: e' il passo piu' vicino al riferimento (HTML normalizzato 96,5%, rigoroso 86,3%); non e' "fatto": pixel sotto 97% e altezze locali 72 px piu' basse a 1440. Con i dati del riferimento (fixture, DCP-47) il confronto sara' piu' pulito. Il Faker non e' stato modificato.

## Misura da proprietario AUTENTICATO (account di prova DCP-98, ticket `AN4059281`), senza filtri
`/it/tickets/confirmation?code=AN4059281` con login del proprietario (l'email e' visibile): 99,84 / 99,79 / 99,71 / 99,60 / 99,41% a 1920/1440/992/768/375 (pixelmatch a pagina intera). Altezze locali +33 px per la debug bar di sviluppo. Chiude il punto "email mai misurata" della sezione precedente; resta non misurato il percorso del flash dopo un salvataggio vero del wizard (richiede un invio completo con allegato).

## Percorso reale dopo il salvataggio (2026-10-13 14:35)
Provato in browser con l'account di prova ([DCP-98](2026-10-13-dcp-98-account-di-prova-per-misure-autenticate.story.md)): "Invia" dal riepilogo crea il ticket e porta a `/it/tickets/confirmation?code=<codice>` (il `?code=` e' l'aggiunta dell'altra sessione al redirect); la pagina mostra il codice del ticket appena creato e l'email del proprietario, senza errori. Il flash di sessione e il ripiego `?code=` arrivano allo stesso risultato visibile.

## Analisi dello scarto per regioni (STEP4, 2026-10-13 16:21 UTC)
Sola analisi (nessuna modifica a codice, JSON, lingue, DB; solo GET). Ospite, gate identico (stesso strumento di diff, debug bar rimossa, `threshold 0.1`), script nello scratchpad. Misure: `TCK-CLIQUERY` 81,94 / 84,52 / 85,41% e `AN4059281` **81,94 / 84,56 / 85,47%** a 1440 / 768 / 375 (altezze rif/loc 2301/2229, 2264/2200, 3083/3011): riprodotte, i due ticket danno lo stesso scarto.

### Conclusione
Il 99,0% dei pixel diversi a 1440 (592.642 su 598.255) nasce da **un solo elemento assente nel locale**: il paragrafo `p.subtitle-small.pt-3.pt-lg-4` "Abbiamo inviato il riepilogo all'email: <email>". L'ospite con `?code=` non lo vede (regola di riservatezza voluta, `ticket-confirmation/design-comuni.blade.php:31-37`). Alto 72 px a 1440 (24 di `pt-lg-4` + 2 righe da 24), 64 a 768, 64 + 8 di margine a 375. Tutto cio' che sta sotto (135 elementi a 1440, 134 a 768 e 375) e' spostato di esattamente -72 px (-64 a 768): stessi x, larghezze, font, colori. Gli stili non c'entrano: `compare-computed` sugli omologhi (h1, h2, `.btn-outline-primary`, `footer`, `.cmp-contacts`) mostra solo rumore (`tab-size`, `scrollbar-color`, serializzazione di variabili `--bs-*`) e una `width` di 4 px (vedi sotto); `explain-style` su `.cmp-contacts .list-item` da' `display: inline-block` identico a riferimento e locale (locale dentro lo scope: `body:has(.page-content[data-dc]) a`, nessuna regola legacy coinvolta).

### Bande verticali a 1440 (AN4059281, pixel diversi per fascia di 300 px) e blocco che le occupa
| Fascia (y locale) | Pixel diversi | Blocco | Causa |
|---|---|---|---|
| 600-900 | 95.849 | "Servizi correlati" e card "Richiesta appuntamento" (rif. y 701-839) | solo spostamento -72 px |
| 900-1200 | 210.252 | valutazione "Quanto e' stato facile..." (rif. card y 991-1115, stelle y 1065) e inizio contatti | solo spostamento |
| 1200-1500 | 74.888 | "Contatta il comune" (rif. h2 y 1261) + testa del footer | spostamento + 4.206 px di residuo (spazio) |
| 1500-1800 e 2100-2301 | 65.773 + 108.298 (+31.968 in 1800-2100) | footer v1 (rif. y 1526-2301, h 775 in entrambi) | solo spostamento: il footer ha la stessa altezza (775) e a parita' di y e' identico |
| 300-600 | 9.582 | titolo, righe, pulsante ricevuta (rif. y 414-604) | paragrafo email assente + testo del pulsante (1.407 px) |
Fasce 0-300: 0 pixel diversi (header, breadcrumb, h1 e le due righe di testo coincidono). Vale lo stesso ordine a 768 (fasce 600-900/900-1200/1200-1500/2100-fine) e a 375 (fascia finale 3000-3083 al 14,9% per 72 px di fine pagina).

### Dopo avere tolto lo spostamento (riferimento con il paragrafo email nascosto, altezze 2229=2229)
1440: **99,83%** (5.613 px diversi) / 768: **99,67%** / 375: **99,50%**. Residuo, identico a tutte le larghezze, in due sole regioni:
| Regione | Elemento rif / loc | Differenze misurate | Causa (file:riga) | Correzione minima | Guadagno atteso |
|---|---|---|---|---|---|
| A. Elenco "Contatta il comune" (x 414-1026, y 1235-1388) | `ul > li > a.list-item` con `svg.icon` + `span`, 4 voci | `a` piu' larga di 4 px (217 vs 221, 161 vs 165, 256 vs 260, 190 vs 194); testo spostato di 4 px a destra e spazio sottolineato tra icona e testo; font, colore, y, x dell'`a`, h 26 invariati; 4.206 px (75% del residuo) | `Themes/Sixteen/resources/views/components/blocks/report-partials/contacts-card.blade.php:11-12`: a capo + indentazione tra `</svg>` e `<span>` e' uno spazio in un `a` `inline-block`; il riferimento ha `</svg><span>` contigui | scrivere `</svg><span>` sulla stessa riga (nessun CSS) | +0,13 pt a 1440, +0,24 a 768, +0,36 a 375 (simulato togliendo i nodi di spazio: 99,83 -> 99,96 / 99,67 -> 99,92 / 99,50 -> 99,87) |
| B. Pulsante ricevuta (x 182-484, y 438-488) | rif `button.btn.btn-outline-primary.bg-white.fw-bold` "Scarica la ricevuta (PDF 100KB)" con `it-download`; loc `a` stesse classi "Traccia la segnalazione" con `it-search` | stessa box (182,438, 302,2x50), stesso font/colore; cambiano solo testo e icona (1.407 px, 25% del residuo); `svg` a x 210,6 vs 240,1 | contenuto voluto: `tickets.confirmation.json:86-90` (it) e blocco gemello en (circa 270-274), reso da `report-step/layout-done.blade.php:28-30` | nessuna: la ricevuta PDF non esiste, "Scarica la ricevuta" sarebbe falso; e' una differenza di contenuto accettata | +0,04 pt, non conveniente |
Residuo invisibile: nel footer `Whatsapp` (rif.) contro `WhatsApp` (loc) nel `span.visually-hidden`: nessun pixel, solo testo accessibile.

### Altezza: i 72 px di differenza a 1440
Attribuiti per misura, elemento per elemento: **72 px = 100% il paragrafo email** (414-486 nel riferimento). Nessuno spostamento in "Servizi correlati" (h2 701 contro 629, differenza 72), valutazione, ricevuta, contatti o footer: ogni blocco ha dy = -72 costante e altezza propria uguale (footer 775/775, card servizi 64/64, h2 50/50, elenco contatti 26/26). Il "pulsante ricevuta" ha la stessa altezza (50). Nessuna altezza propria differisce.

### Contenuto contro stile, e peso dei dati di prova
- Stile e struttura: nessuna differenza di CSS misurabile; una sola di markup (spazio in `contacts-card`), 75% del residuo, 0,13% della pagina.
- Contenuto: (1) paragrafo email assente per l'ospite (99,0% dello scarto, effetto di riservatezza e dell'essere ospiti, non di stile ne' dei dati); (2) testo/icona del pulsante (0,04%); (3) codice (`AN4059281` coincide col riferimento; `TCK-CLIQUERY` aggiunge 628 px, 0,02%, perche' ha caratteri diversi); (4) email del riferimento (`giulia.bianchi@gmail.com`) non confrontabile da ospite.
- Dati di prova: il Faker pesa solo 628 px (0,02%): `TCK-CLIQUERY` col paragrafo email inserito e spazio corretto da' 99,94% contro 99,96% di `AN4059281`. Il gate da ospite non e' quindi misurato su un difetto del tema ma sul fatto che l'ospite non vede l'email.

### Correzioni proposte (NON applicate)
| # | Correzione | File:riga | Guadagno stimato @1440 / 768 / 375 |
|---|---|---|---|
| 1 | Misurare il passo 4 con sessione proprietaria o dopo salvataggio (flash), non da ospite: il paragrafo email e' presente. Nessun codice | `parity-wizard-steps.sh --steps 4 --code AN4059281` con login DCP-98 | 81,9 -> 99,8 (+17,9) / 84,6 -> 99,7 (+15,1) / 85,5 -> 99,5 (+14,0); coincide con le misure autenticate gia' in story (99,79 / 99,60 / 99,41) |
| 2 | Spazio tra `</svg>` e `<span>` nel riquadro contatti | `report-partials/contacts-card.blade.php:11-12` (partial condiviso con `report-step`, `report-area`, `report-list`: sentinelle obbligatorie su tutti e tre, DoD punto 8) | +0,13 / +0,24 / +0,36 pt |
| 3 | Facoltativa, solo se si vuole 99,8% anche da ospite: tenere la riga email con segnaposto neutro (es. "indicata nella segnalazione") invece di rimuoverla; stessa struttura a 2 righe | `ticket-confirmation/design-comuni.blade.php:31-37` (togliere il filtro) + `tickets.confirmation.json:70-84` (it) e righe gemelle en, 3 copie del JSON | simulato col segnaposto: 99,80% a 1440 e 99,42% a 375 da ospite; attenzione: afferma "abbiamo inviato il riepilogo all'email" anche per un ospite, va deciso dal prodotto |
| 4 | Pulsante ricevuta: lasciare invariato (contenuto voluto) | `tickets.confirmation.json:86-90` | 0 (max +0,04) |
| 5 | Allineare `Whatsapp` / `WhatsApp` nel footer e' inutile ai pixel | footer del tema | 0 |
Verifica ripetibile: `scratchpad/step4/{shoot.mjs,shoot2.mjs,sim.mjs,sim2.mjs,sim3.mjs}` (mai applicati al codice; `sim.mjs` inietta il paragrafo e toglie gli spazi solo nel browser).
