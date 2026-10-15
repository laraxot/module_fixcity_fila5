---
id: dcp-176
title: "DCP-176: plugin Claude Cowork, studio, installazione e cosa adottare"
type: story
epic: dc-parity-v2
status: done
priority: P2
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "plugin cowork knowledge-work-plugins legal design engineering compliance-check accessibility-review deploy-checklist go-live"
related: [dcp-140, dcp-143, dcp-154, dcp-163]
---
# DCP-176: plugin Claude Cowork, studio, installazione e cosa adottare

Fase BMAD: Analyze -> Implement (piccola) -> Verify. Richiesta dell'utente: studiare e installare i plugin della guida `pasqualepillitteri.it/en/news/200/...` e migliorare il progetto.

## Che cosa e' stato letto (e che cosa no)
- La pagina della guida **non e' stata letta**: WebFetch risponde «socket hang up», `curl` fallisce con errore TLS (anche con `--http1.1`). Della guida si conosce solo il sommario riportato dalla ricerca web (11 plugin, installazione da marketplace o da terminale, struttura del plugin, integrazione con Claude Code, limiti). Non e' stato aggirato.
- Fonte primaria usata al suo posto: il repository `anthropics/knowledge-work-plugins`, gia' registrato come marketplace in `~/.claude/plugins/marketplaces/knowledge-work-plugins` (commit 02ffa99, 2026-10-11) e la sua pagina GitHub. Ogni plugin e' una cartella di Markdown (`skills/*/SKILL.md`) piu' `.mcp.json` (connettori); nei quattro esaminati (legal, design, engineering, product-management) **nessuno script e nessun hook**.

## Installato (scope utente, `claude plugin install <nome>@knowledge-work-plugins`)
`legal` 1.3.0 (9 skill), `design` 1.2.0 (7), `engineering` 1.2.0 (10). Cambia solo `~/.claude/settings.json` (3 righe in `enabledPlugins`; copia precedente in scratchpad). Attivi dalla prossima sessione o dopo il ricarico dei plugin.
**Non installato: `product-management`**, perche' la stesura di specifiche e il piano sprint sono gia' di BMAD (regola «BMAD always»): due flussi concorrenti per le stesse story sarebbero un costo senza beneficio. Non installati `sales`, `marketing`, `finance`, `bio-research`, `customer-support`, `productivity`, `operations`: fuori dal lavoro del progetto.
Costo: ogni plugin porta 7-10 server MCP (Slack, Gmail, Atlassian, GitHub, PagerDuty, Datadog...) che chiedono autenticazione; nessuno e' stato autenticato e nessun dato e' stato inviato. Se compaiono come «non connessi», disabilitare il plugin con `claude plugin disable`.

## Che cosa le skill aggiungono davvero (confronto con il progetto)
| Skill | Esito |
|---|---|
| `compliance-check` (richieste dell'interessato) | Il progetto ha gia' DCP-140, piu' preciso. Dalla skill mancavano in DCP-140: verifica dell'identita' del richiedente, registro con tipo di richiesta e incaricato, esenzioni documentate, informazione sul diritto di reclamo al Garante, proroga di due mesi con avviso (GDPR art. 12 par. 3). Aggiunte come nota in DCP-140. |
| `deploy-checklist` | Mancava tutta la parte operativa: backup e ripristino, migrazioni, rollback, worker e scheduler, prova dopo il deploy, soglie di rollback. Aggiunta a DCP-154. |
| `accessibility-review` | Elenco WCAG 2.1 AA di base: piu' povero di DCP-143 (axe-core) e della matrice. **Contiene un errore**: indica 2.5.5 (bersaglio 44 px) come AA, ma in WCAG 2.1 e' AAA; il criterio AA e' 2.5.8 in WCAG 2.2 (24 px). Non importare la skill alla lettera. Spunto utile: reflow e dimensione dei bersagli, non coperti da axe. |
| `architecture`, `code-review`, `testing-strategy`, `tech-debt`, `incident-response` | Generiche; non usate. `code-review` si sovrappone al plugin `code-review` gia' installato. |

## Misure fatte (2026-10-14, `http://127.0.0.1:8081`, Chromium)
Sonda: `reflow-target-probe.mjs` (scratchpad), 320 px di larghezza, banner cookie chiuso.
- **Reflow (WCAG 1.4.10): nessuno scroll orizzontale a 320 px** su 8 pagine esistenti (home, privacy, cookie-policy, note-legali, domande-frequenti, login, tickets/track, tickets). `/it/servizi` risponde 404 e non conta.
- **Bersagli < 24 px (WCAG 2.2, 2.5.8)**: esclusi i link nel testo e i link di salto (1x1 finche' non hanno il focus, esenti perche' non visibili). Resta **un solo elemento**, il link «Vai al portale ...» dell'intestazione, alto 21 px (larghezza 118). Non e' stata valutata l'eccezione per spaziatura (cerchio di 24 px senza intersezioni): da verificare prima di correggere. Il markup e' quello di riferimento Design Comuni; nessuna modifica.
- Non misurato: ingrandimento 200% del solo testo, lettore schermo, Firefox/Safari.

## Altri difetti trovati applicando la checklist di rilascio (corretti)
Vedi DCP-154: `MAIL_DRIVER` ignorato (posta in produzione nel log), `LOG_CHANNEL=sentry` senza pacchetto ne' canale, percorso Pest del gate. Nuovo controllo `ENV-09` nel gate.

## Criteri di accettazione
- [x] Plugin installati con comando ufficiale e `enabledPlugins` verificato (diff di 3 righe).
- [x] Ogni skill adottata ha una modifica concreta in una story o nel codice (DCP-140, DCP-154, gate).
- [x] Cio' che non e' stato letto o misurato e' dichiarato.
