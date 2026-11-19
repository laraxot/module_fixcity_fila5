---
id: dcp-211
title: "DCP-211: anteprima delle foto nel riepilogo, i residui di misura e due decisioni"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P2
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "anteprima foto riepilogo wizard residui parity pixel CLS temporaryUrl url firmato pdf riquadro documento informativa dcp-182"
related: [dcp-182, dcp-33, dcp-200]
tags: [wizard, upload, misura, decisione]
---

# DCP-211: i residui di DCP-182

## Fase BMAD
Verify. La funzione e' consegnata e provata ([DCP-182](./dcp-182-riepilogo-wizard-anteprima-delle-foto.story.md): Pest 54 su 54 sui file toccati, spec del browser, axe senza violazioni sul gruppo, revisione `reviewing-filament-plans` con 5 deviazioni chiuse). Qui restano solo le misure che nessuno ha fatto e due decisioni.

## Gia' chiuso
- Il `catch` di `BuildTicketReviewSummaryAction::temporaryPreviewUrl()` ora registra un avviso con classe e messaggio, senza nome del file ne' URL firmato (test rosso prima, verde dopo).

## Compiti (misure, nessuna decisione necessaria)
- [ ] **Parity a zero foto.** Rimisurare il riepilogo senza allegati con il gate pixel di DCP-33: il test attuale confronta il nuovo HTML con se stesso, non con `sito/segnalazione-03-riepilogo.html`. Atteso: nessuna differenza.
- [ ] **CLS con foto non 4:3.** Le miniature hanno `width=320 height=240` fissi con `img-fluid`. Misurare lo spostamento di layout con una foto verticale e una orizzontale (la sonda `bashscripts/tools/visual/wizard-summary-photos-probe.mjs` usa gia' 1600x900 e 600x900) a 375 e 1440 px.
- [ ] **URL ricalcolato a ogni resa.** Il passo chiede un `temporaryUrl()` nuovo ad ogni render (firmato 30 minuti). Misurare quante volte si rende il passo in un uso normale; **ottimizzare solo se la misura lo giustifica**.
- [ ] **Browser mancanti.** Firefox e Safari, e un lettore di schermo sul gruppo delle anteprime, non sono stati provati.
- [ ] **File temporaneo scaduto nel browser.** Oggi coperto solo da test con doppi; provarlo davvero lasciando scadere o cancellando il file.

## Decisioni
| ID | Decisione | Opzioni | Proposta | Chi decide |
|---|---|---|---|---|
| DEC-37 | Riquadro documento per i PDF (rinviato in DCP-182) e menzione nell'informativa del link dell'anteprima | (a) riquadro piu' menzione; (b) solo menzione; (c) niente | (b): il riquadro e' cosmetico (prodotto); la menzione e' del DPO | prodotto per il riquadro, DPO per l'informativa |
Perche' la menzione: l'URL firmato di Livewire (`livewire-<hash>/preview-file/...`) e' un link al portatore valido 30 minuti, non legato alla sessione, e la risposta ha `Cache-Control: public, max-age=31536000`. Le foto sono del cittadino stesso, ma un URL copiato resta apribile fino alla scadenza. Non e' un parere legale.

## Criteri di accettazione
- [ ] Le tre misure (parity, CLS, URL) hanno un numero scritto qui.
- [ ] DEC-37 ha un esito datato nel registro [DCP-200](./dcp-200-registro-unico-delle-decisioni-aperte.story.md).
- [ ] Nessuna ottimizzazione senza misura.

## Non verificato
Tutto cio' che e' elencato nei compiti.
