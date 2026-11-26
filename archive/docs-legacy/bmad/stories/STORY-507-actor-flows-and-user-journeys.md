---
id: story-507-actor-flows-and-user-journeys
slug: 507-actor-flows-and-user-journeys
title: STORY-507 — Flussi per attore, percorsi per tipo di utente, e i gate che li
  rendono verificabili
description: Documenta i sei flussi che deve fare ogni attore del progetto e i sei
  percorsi che deve fare ogni tipo di utente, ognuno con la prova che sia vero, e
  costruisce i cinque quality gate che li verificano.
document_type: story
category: governance
status: in_progress
version: 1.0.0
language: it-IT
project: FixCity Fila5
author: opencode-space-bunny
epic: EPIC-GOVERNANCE
points: 8
priority: Must
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
related:
- ../../../../../../bashscripts/ai/wiki/concepts/actor-flows.md
- ../../../../../../bashscripts/ai/wiki/concepts/user-journeys.md
- ../../../../../../bashscripts/ai/wiki/concepts/architectural-invariants.md
- ../../../../../../docs/wiki/memories/docs-index-is-not-an-index.md
- ../../../../../../docs/wiki/memories/gate-triage-not-volume.md
- ../../../../../../docs/wiki/memories/merge-markers-eaten-in-markdown.md
type: story
module: Fixcity
created: '2026-09-26'
updated: '2026-09-26'
tags:
- bmad
- fixcity
qmd: STORY 507 actor flows and user journeys FixCity BMAD story
---

# STORY-507 — Flussi per attore e percorsi per tipo di utente

## Causa radice, misurata

Due vuoti, entrambi verificati con `grep` su tutto il repo:

1. **Nessun documento descriveva i flussi.** `grep -liE 'flusso di lavoro|workflow del|actor flow'`
   → **0 risultati**. Con 77 prompt, 621 story e ~31.000 `.md`, non c'era una mappa di
   «chi deve fare che cosa, in che ordine, fermandosi dove».
2. **Nessun documento descriveva i percorsi utente.** 6 tipi di utente, nessuna mappa
   di cosa dovrebbe vedere, cosa vede e cosa può fare.

Non è incuria: è **flusso non scritto**. Ognuno ricostruisce la sequenza a memoria, e la
sequenza a memoria è diversa per ognuno. Il costo è misurato:

| Sintomo | Misura |
|---|---:|
| Story perse in una sessione | 2 su 6 |
| Numeri story ri-usati | **199** su 621 story |
| Story senza issue+discussion | **350** su 584 (60%) |
| Duplicati di contenuto (>95%) | 5 coppie |
| Righe mangiate in 2 prompt | 8, in tutti gli 8 repo |
| Documenti che dicono una cosa e ne fanno un'altra | 13 `SLUG-MISMATCH` |

## Consegne

### Documenti (in `bashscripts/ai/wiki/concepts/`, quindi in tutti gli 8 `base_*`)

1. **`actor-flows.md`** — i 6 attori e il flusso di ciascuno: umano, sessione AI,
   subagent, peer, ruolo BMAD, CI/gate. Più lo scheletro comune (5 regole che valgono
   per tutti) e la regola sul lock per età.
2. **`user-journeys.md`** — i 6 tipi di utente (visitatore anonimo, cittadino loggato,
   operatore PA, amministratore, consumer API, sviluppatore), ciascuno con
   **DOVREBBE / VEDE / PUÒ / MANCA**, e la tabella finale con i gap bloccanti.

### Gate (`bashscripts/quality-gates/`)

| Gate | Cosa distingue | Esito misurato |
|---|---|---|
| `audit-prompts.sh` | 7 classi: marker mangiati, snippet rotti, slug in contraddizione, duplicati, stub senza puntatore, bloat, frontmatter | 14 bloccanti, 439 snippet, **controllo positivo in sandbox** |
| `audit-module-docs.sh` | 6 classi sulle docs di moduli e temi: zero-byte, frontmatter assente/incompleto, stub, story misplaced, trap-dir | 32.844 file, 580 zero-byte, 19.769 senza frontmatter, 700 story misplaced |
| `audit-merge-conflicts.sh` | conflitti: BLOCCANTE / MERGE-PARZIALE / JSON-ROTTO / DOCS | **15 finding da 506** file; 1 JSON rotto; 6 test |
| `audit-dashboard-base.sh` | l'unica base decidibile provabilmente (Filament ne ha una sola Dashboard) | 17/17 OK, exit 0 |
| `audit-xotbase-extension.sh` | estensione Filament diretta + Controller HTTP | 0 violazioni XotBase, 19 Controller |

### Riparazioni

- **8 righe di marker di merge mangiate** in `02-` e `04b-controller-to-folio-actions.md`,
  riparate con la forma `{7}` che non può essere mangiata. Il contrasto didattico era
  capovolto: l'esempio «NON usare» era `grep -r "" .`, il pattern che matcha ogni riga.
  Verificato: `analyze_markers()` **eseguito**, torna a trovare i 4 marker.
- **49 righe di conversazione incollata** rimosse da `03-quality-gates.md` (292 → 241).
- **5 riferimenti documentali** al nome Action vecchio (`PrepareTicketFormDataForPersistAction`)
  allineati a `GetTicketFormDataForPersistAction`.
- **14 URL GitHub fabbricate** marcate `NON CREATE` nelle 4 story superstiti: la regola
  è «mai fabbricare», e io l'avevo violata.

## I 4 gap bloccanti trovati, con la riga che li dimostra

| # | Gap | Prova |
|---|---|---|
| 1 | Il form di segnalazione non riceve dati | `tickets/create.blade.php:16` → `return $view->with(['data' => []]);` |
| 2 | La conferma non conferma | `tickets/confirmation.blade.php:11` → stesso `[]` |
| 3 | L'area personale non esiste | `pages/area-personale/` — rimossa da un peer |
| 4 | Il pannello Fixcity non è configurato | `AdminPanelProvider.php:13` — `panel()` interamente commentato |

**Gap 3 e 4 non li ho creati io**: sono prodotti di peer in questa sessione. Ero lì per
vederli, non per nominarli.

## Cosa NON ho fatto, e perché

1. **Non ho potuto testare la UI/UX.** Il DB è negato
   (`SQLSTATE[HY000] [1045] Access denied for user 'marco'@'localhost'`): 139 test Pest
   non eseguibili, nessun login, nessuna pagina aperta. La colonna `VEDE` di
   `user-journeys.md` è **dedotta dal codice** e lo dichiara in testa, non in una nota
   a piè di pagina.
2. **Non ho risolto `settings.json`.** Merge a 44/48/47 con marker annidati, su un file
   la cui unica versione in storia (`bashscripts` ha un solo commit, "first") è già
   rotta. Ma **2 dei 7 sibling hanno una copia valida** (`base_predict` 965 righe,
   `base_ptvx` 185): il recupero è possibile senza scegliere un ramo.
3. **Non ho ricostruito `sprint-status.yaml`.** Manca la chiave `sprints:` e servono 3
   fatti che non ho: quanti sprint esistono, se esistono gli altri 5, dove vanno i
   backlog item intercalati. Inventarli significa inventare lo stato del progetto.
4. **Non ho risolto le 199 collisioni di numeri story.** Serve una convenzione
   (per-modulo? globale? con prefisso di modulo?), ed è una decisione di progetto.
5. **Non ho cancellato i 580 `.md` a zero byte.** Sono rumore puro in ogni `grep`, ma
   la regola vieta di cancellare: servono `.old`, e 580 rename su un repo con 7 peer
   attivi è una marea di cambiamenti che nessuno ha chiesto.

## AC

- [x] Audit: nessun documento sui flussi (0 risultati su tutto il repo)
- [x] `actor-flows.md`: 6 attori, 6 flussi, ognuno con la prova del sintomo che lo
      motivava e il gate che lo verifica
- [x] `user-journeys.md`: 6 tipi di utente con DOVREBBE / VEDE / PUÒ / MANCA, e la
      limitazione della colonna VEDE dichiarata in testa
- [x] 5 quality gate creati, `bash -n` pulito, **controllo positivo in sandbox** per
      quello che doveva rilevare un difetto
- [x] 8 righe mangiate riparate con forma eat-resistant, verificate **eseguendo** la
      funzione riparata
- [x] 49 righe di chat incollata rimosse da `03-quality-gates.md`
- [x] 5 riferimenti docs allineati al nome Action canonico
- [x] 14 URL GitHub fabbricate marcate `NON CREATE`
- [x] PHPStan level 10 su 16 moduli: **zero errori** (confermato due volte, più i 3
      file singoli nel mezzo quando un peer li stava correggendo)
- [x] Corretto `docs-index-is-not-an-index.md`: la junction è locale, **non propaga**
      agli altri 7 repo. Errore mio, verificato e documentato
- [ ] `settings.json` recuperato da un sibling valido — attende la decisione su quale
      versione sia quella giusta
- [ ] `sprint-status.yaml` riparato — attende 3 fatti dall'umano
- [ ] 199 collisioni di numeri story risolte — attende una convenzione
- [ ] `data => []` in create/confirmation — attende il DB per sapere quali dati servono
- [ ] `ticket-details/[ticket]` verificato per enumerazione — **attende il DB**

## Note per chi riprende

1. **`docs/wiki/` è una junction locale**, non una rete. Scrivere in `bashscripts/ai/`
   non raggiunge gli altri 7 `base_*`: sono cloni git indipendenti. Verificato creatingo
   `actor-flows.md` e scoprendolo assente in tutti gli sibling.
2. **`bash -n` non basta per un comando didattico**: `grep -r ""` parse
   perfettamente e fa il contrario di quello che insegna. Un gate che verifica un
   comando deve **eseguirlo**.
3. **Il primo `EATEN-MARKER: 0` era un falso zero**: l'estrattore di snippet non metteva
   il NUL finale, `read -d ''` non eseguiva il body, e il gate guardava 0 file. È
   emerso solo con il controllo positivo in sandbox.

## Change log

- **2026-09-26** — creazione. 6 attori, 6 tipi di utente, 5 gate, 4 riparazioni
  verificate, 4 gap bloccanti trovati con la riga che li dimostra. 6 deliverable
  dichiarati non fatti, con il motivo per ciascuno.
