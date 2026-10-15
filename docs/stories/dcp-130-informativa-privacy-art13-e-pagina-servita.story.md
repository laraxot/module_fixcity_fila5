---
id: dcp-130
title: "DCP-130: informativa privacy completa (art. 13) e pagina /privacy servita"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity + theme:Sixteen
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "informativa privacy art 13 gdpr pagina privacy 404 policy.md titolare dpo base giuridica conservazione diritti reclamo garante"
related: [dcp-112, dcp-131, dcp-134, dcp-123, dcp-133]
---

# DCP-130: informativa privacy completa e pagina servita

Nata da DCP-112 (matrice normativa, righe N-03, N-04, N-05).

## Lacuna e prova (2026-10-13)
- `curl -s -o /dev/null -w '%{http_code}' http://192.168.1.40:8000/it/privacy` (e `/en/privacy`, `/privacy`) risponde 404. Il wizard (`TicketForm::getGdprHtml`) e il footer puntano li'.
- La pagina `Themes/Sixteen/resources/views/pages/privacy/index.blade.php` e' stata cancellata dal commit `30d521c` (2026-10-11, messaggio "."); `docs/architecture/privacy-policy-folio-route.md` ("status: active") e la story `STORY-FIXCITY-PRIVACY-PUBLICATION-GATE` (criteri spuntati) la danno ancora per presente.
- Il testo tenant `config/local/fixcity/lang/{it,en,de,es}/policy.md` (circa 700 byte) non contiene: titolare, contatti del DPO, base giuridica, destinatari, termine di conservazione, elenco dei diritti, reclamo al Garante, natura obbligatoria del conferimento, trasferimenti. Dice «acconsenti» (vedi DCP-134).

## Norma (testo letto, registro fonti 02)
GDPR art. 12 par. 1: informazioni «in forma concisa, trasparente, intelligibile e facilmente accessibile»; art. 13: informativa «nel momento in cui i dati personali sono ottenuti», con identita' del titolare, DPO, finalita' e base giuridica, destinatari, conservazione, diritti, reclamo; art. 37 par. 1 lett. a): DPO obbligatorio per l'autorita' pubblica.

## Requisiti
1. Ripristinare `/{lang}/privacy` (it, en, de, es) come pagina del tema o come JSON CMS, che rende il Markdown del tenant tramite `GetPublishedPrivacyPolicyAction`. Con testo segnaposto: pagina con messaggio esplicito (non 404, non 200 vuoto).
2. Fornire un modello del testo con tutte le sezioni dell'art. 13 e i campi obbligatori; i valori (titolare, DPO, base, termini) li fornisce il Comune: il codice non li inventa (nessun nome di ente nel runtime).
3. Una guardia automatica che fallisce se `policy.md` del tenant attivo contiene frasi segnaposto o manca una sezione obbligatoria.
4. Collegare i documenti stali (non cancellarli): nota di correzione in `privacy-policy-folio-route.md` e nella story del gate.

## Criteri di accettazione (stato al 2026-10-13, sera)
- [x] `curl` su `/{it,en,de,es}/privacy` risponde 200 con `<h1>` e senza `<main>` vuoto. Prova: 200 x4, un solo `<h1>` per lingua, testo del `<main>` di 2798, 2644, 2908, 2875 caratteri (it, en, de, es).
- [x] Il link dell'informativa nel passo 1 del wizard e quello del footer portano a quella pagina (200). Prova: `TicketForm::getGdprHtml` costruisce il link con `LaravelLocalization::getLocalizedURL(lingua, '/privacy')`; il test `PrivacyPolicyPageTest > is the page the wizard consent link and the footer point to` verifica che quell'URL e l'unico `href` `/privacy` del footer coincidano e rispondano 200 (4/4 lingue). Non ho aperto il passo 1 del wizard in un browser (richiede il login e il wizard e' di DCP-128/146).
- [x] Test Pest: pagina 200 con policy valida; avviso esplicito con policy segnaposto; la guardia elenca le sezioni mancanti (vedi Prove).
- [ ] `grep -rn 'Firenze' laravel/Modules/Fixcity/app laravel/Modules/Fixcity/resources laravel/Themes/Sixteen/resources/views` senza risultati nel runtime. NON soddisfatto: 0 risultati nei file di questa story, ma restano 7 righe di dati dimostrativi «Via Solferino - 50100 Firenze» in 3 file di altri perimetri (`Modules/Fixcity/resources/json/info-list.json` righe 399 e 417; `Modules/Fixcity/app/Actions/LoadDesignComuniElencoDemoCardsAction.php` righe 36, 44, 52; `Themes/Sixteen/resources/views/components/blocks/segnalazioni-elenco/default.blade.php` righe 21 e 22) e 3 in `pages_old/prova01.blade.php` (non servita). Sono dati copiati dal riferimento Design Comuni (che usa quell'indirizzo): toglierli cambia la parity delle repliche. Serve una decisione: restringere il criterio ai file dell'informativa o sostituire i dati dimostrativi.
- [x] axe (wcag2a, wcag2aa, wcag21a, wcag21aa) senza violazioni sulla pagina: `a11y-page-probe.mjs` su `/it|en|de|es/privacy` a 375 e 1440 px, 0 violazioni (nessuna regola violata, non solo 0 serious/critical).

## Implementazione (2026-10-13)
- Pagina: JSON CMS `privacy.index.json` (`layout: design-comuni`, 4 lingue, 3 copie identiche: `config/local/fixcity`, `config/eu/fixcity`, `Modules/Fixcity/config`) + blocco `Themes/Sixteen/resources/views/components/blocks/legal-document/design-comuni.blade.php` (breadcrumb e hero del riferimento, nota `callout`). Nessuna pagina Folio nuova, nessun layout nuovo. Il blocco e' generico (`document`, `markdown`, `assess` dal JSON) e serve anche DCP-131.
- Azioni (`Modules/Fixcity/app/Actions`): `GetPublishedLegalDocumentAction` (Markdown del tenant per nome file; `GetPublishedPrivacyPolicyAction` ora delega, comportamento del wizard invariato), `AssessPrivacyPolicyAction` (10 sezioni dell'art. 13 par. 1 e 2: titolare, DPO, finalita' e base giuridica, destinatari, trasferimenti, conservazione, diritti, reclamo, natura del conferimento, processi automatizzati; segnaposto `[[...]]`, "Edit this file", lorem ipsum, TODO/TBD, example.it, xxxx), `BuildLegalDocumentAction` (HTML sicuro, intestazioni abbassate di un livello: un solo `<h1>`).
- Lingue: `Modules/Fixcity/lang/{it,en,de,es}/privacy.php` (titoli e indicazioni per il titolare, uno per sezione) e `Themes/Sixteen/lang/{it,en,de,es}/legal.php` (cornice e avviso). Nessun nome di ente nel runtime.
- Modello da compilare: `Modules/Fixcity/resources/templates/privacy-policy.{it,en,de,es}.md` (tutte le sezioni, solo segnaposto `[[DA COMPILARE: ...]]`, istruzioni in un commento HTML che non viene pubblicato). Il Comune lo copia in `config/<tenant>/lang/<lingua>/policy.md`.
- Comportamento: con testo completo la pagina mostra il testo; con testo incompleto o assente risponde 200 e un `callout` elenca le sezioni mancanti o con segnaposto, ciascuna con l'articolo e che cosa scrivere. Il testo oggi nel tenant (circa 700 byte, senza titolare, DPO, base giuridica, termini) e' incompleto: la pagina lo mostra sotto l'avviso con tutte e 10 le sezioni elencate come mancanti.
- Guardia: `Feature/Pages/PrivacyPolicyPublicationGateTest.php` (gruppo `publication-gate`) legge il `policy.md` VERO e FALLISCE finche' c'e' un segnaposto o manca una sezione. E' rossa per costruzione fino a quando il Comune fornisce il testo (4 test rossi, uno per lingua, con l'elenco delle sezioni mancanti nel messaggio).
- Documenti stali collegati (non cancellati): note di correzione in `Themes/Sixteen/docs/architecture/privacy-policy-folio-route.md` e in `docs/bmad/stories/STORY-FIXCITY-PRIVACY-PUBLICATION-GATE.md` (la pagina Folio non esiste piu' dal commit `30d521c`; ora 200 e non 503).
- `Feature/Pages/TicketPrivacyPublicationTest.php`: i due test sulla pagina `/privacy` erano rotti dalla cancellazione della pagina Folio (404 al posto di 503/200); riscritti sul nuovo contratto, il terzo (consenso del wizard senza policy) e' invariato.

## Prove (comandi e output, 2026-10-13)
| Criterio | Comando | Esito |
|---|---|---|
| Pagina 200 in 4 lingue | `for l in it en de es; do curl -s -o /dev/null -w '%{http_code} ' $B/$l/privacy; done` | `200 200 200 200` |
| Test unitari rilevatore | `pest Modules/Fixcity/tests/Unit/AssessPrivacyPolicyActionTest.php` (via `pest-scratch-db.sh`) | 17 passati (completa, nulla, sezioni mancanti o vuote, 6 tipi di segnaposto dentro e fuori sezione, sotto-intestazioni, 4 modelli) |
| Test pagina | `pest Modules/Fixcity/tests/Feature/Pages/PrivacyPolicyPageTest.php` | 17 passati (testo completo e sanificato, avviso con segnaposto in 4 lingue, assenza di testo, una sola sezione mancante, link wizard = link footer) |
| Il test cade senza la correzione | JSON della pagina nascosto; footer e rilevatore ripristinati ai vecchi; destinatario sbagliato | `PrivacyPolicyPageTest` 4 falliti (404); `AssessPrivacyPolicyActionTest` 10 falliti con il rilevatore disattivato |
| Guardia di pubblicazione | `pest ... --group=publication-gate` | 4 falliti per costruzione: «Informativa privacy (it) NON pubblicabile. Da fornire: Titolare del trattamento (missing); Responsabile della protezione dei dati (missing); ...» |
| PHPStan | `./vendor/bin/phpstan analyse <file toccati> --no-progress --error-format=raw` | 0 errori su 4 Action, 3 file di test, 1 modifica a `TicketPrivacyPublicationTest` |
| axe | `node bashscripts/tools/visual/a11y-page-probe.mjs http://192.168.1.40:8000/<lingua>/privacy` | 0 violazioni (it, en, de, es; 375 e 1440 px) |

## Cosa serve dal Comune (nessun valore e' inventato dal codice)
Testo completo dell'informativa in 4 lingue nel `policy.md` del tenant: titolare (denominazione, sede, recapiti), DPO (recapiti; designazione art. 37 par. 1 lett. a), finalita' e base giuridica, destinatari (inclusi i fornitori: posta, hosting, mappe e geocodifica), eventuali trasferimenti extra SEE, termini di conservazione di segnalazioni, account, consensi e registri, diritti e come esercitarli, reclamo al Garante, natura obbligatoria o meno del conferimento, eventuali processi automatizzati. Il testo attuale dice ancora «acconsenti» (DCP-134). Finche' non c'e', la guardia resta rossa e il servizio non va in produzione.

## Dipendenze e priorita'
Testo e identita' del titolare: decisione del Comune (DPO). Link localizzati: DCP-123. Priorita' P0: senza informativa raggiungibile il servizio non va in produzione.

## Nota del coordinatore (2026-10-14 22:55 UTC): guardia di pubblicazione
I test `PrivacyPolicyPublicationGateTest` (e quello dell'accessibilita', DCP-132) sono rossi per costruzione finche' il Comune non fornisce i dati. Per non rendere inutile la suite generale sono SALTATI se non c'e' la variabile `PUBLICATION_GATE`: la messa in produzione richiede `PUBLICATION_GATE=1 ./vendor/bin/pest --group=publication-gate` VERDE (4 lingue x 2 documenti = 8 test). Provato: senza variabile 8 saltati; con la variabile 8 falliti con 8 asserzioni (dati del Comune mancanti). Questa condizione va nella checklist di go-live.

