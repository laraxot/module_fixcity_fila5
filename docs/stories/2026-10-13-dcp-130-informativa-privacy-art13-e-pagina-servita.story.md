---
id: dcp-130
title: "DCP-130: informativa privacy completa (art. 13) e pagina /privacy servita"
type: story
epic: dc-parity-v2
status: ready-for-dev
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

## Criteri di accettazione
- [ ] `curl` su `/{it,en,de,es}/privacy` risponde 200 con `<h1>` e senza `<main>` vuoto.
- [ ] Il link dell'informativa nel passo 1 del wizard e quello del footer portano a quella pagina (200).
- [ ] Test Pest: pagina 200 con policy valida; avviso esplicito con policy segnaposto; la guardia elenca le sezioni mancanti.
- [ ] `grep -rn 'Firenze' laravel/Modules/Fixcity/app laravel/Modules/Fixcity/resources laravel/Themes/Sixteen/resources/views` senza risultati nel runtime.
- [ ] axe (wcag2a, wcag2aa, wcag21a, wcag21aa) senza violazioni sulla pagina.

## Dipendenze e priorita'
Testo e identita' del titolare: decisione del Comune (DPO). Link localizzati: DCP-123. Priorita' P0: senza informativa raggiungibile il servizio non va in produzione.
