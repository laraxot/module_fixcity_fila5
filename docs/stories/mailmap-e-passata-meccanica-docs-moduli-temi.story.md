---
id: docs-mailmap-2026-10-14
title: ".mailmap creato dalle prove e passata meccanica sulle docs di moduli e temi (continua DCP-56)"
type: story
epic: docs-organization
status: in-progress
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
tags: [docs, mailmap, worklog, organizzazione, second-brain]
related:
  - dcp-56-organizzazione-docs-moduli-e-temi.story.md
  - ../../../../../bmad-output/stories/docs-organization/README.md
  - ../../../../../bmad-output/docs-architecture/module-theme-docs-contract.md
qmd: "mailmap identita git alias marco76tv worklog docs-organizer frontmatter link rotti moduli temi DOC-003 DOC-005"
---

# `.mailmap` e passata meccanica sulle docs di moduli e temi

## Richiesta (2026-10-14, utente)
«Se `.mailmap` manca, devi crearlo; e nel caso anche aggiornarlo e migliorarlo. Aggiorna, studia e migliora le cartelle docs dentro i moduli e dentro i temi, e fai tutto con BMAD + second brain.» Si aggiunge alla richiesta permanente già registrata in DCP-56.

## Analisi
### Identità Git
Il file non esisteva (nessun `.mailmap` alla radice né nei repo). Autori (`%an|%ae`) su tutta la storia di tutti i repo, misurati il 2026-10-14: 21 coppie nome/email distinte.

| Prova | Commit | Conclusione |
|---|---|---|
| `marco76tv <marco.sottana@gmail.com>` | 19.923 | stessa email di `Marco Xot <marco.sottana@gmail.com>` (1.730): stessa persona |
| `Marco Xot <marco76tv@hotmail.com>` | 5.446 | stesso nome con un'altra email: stessa persona |
| `marco`, `zorin` con `marco.sottana@gmail.com` | 33 e 1 | stessa email; `zorin` è l'utente di sistema della macchina |
| `marco76tv` con i due indirizzi noreply di GitHub | 1.508 e 860 | il nome è quello che firma con l'email canonica; riga vincolata a nome più email |
| `Malebestia` con yahoo e due noreply | 5.253, 80, 42 | altra persona: nessuna email in comune con Marco Xot; solo normalizzata |
| `dependabot[bot]` con due email | 883 e 10 | stessa automazione |

Risultato: 21 identità diventano 12, di cui 2 persone (Marco Xot, Malebestia) e 10 automazioni. Il caso più costoso è il 2026-09-22, dove i report separati davano 96 + 83 commit al posto dei 179 di un'unica persona.

### Docs
Base misurata con `docs-organizer.py audit` (sola lettura, 23 perimetri, 20.356 file `.md`): 12.602 link rotti su 24.369 (52%), 1.619 file senza frontmatter, 3.342 blocchi frontmatter spuri, 1.253 file con frontmatter completo.

## Modifiche
1. `.mailmap` alla radice, con la prova di ogni riga nei commenti. Le automazioni non sono state unite fra loro per somiglianza.
2. Strumenti del worklog (`bashscripts/tools/worklog/`): `BOT` riconosce `kilo-agent`, `GitHub Action(s)`, `Retype`, `Codex Action Probe`; l'indice dichiara l'assenza del `.mailmap` quando manca; `verify` conta come propri anche i commit fuori da `dev` della stessa persona; `detail` classifica i file compilati, le viste Blade sotto `pages/tests/` e le copie `.bak`/`.old` a parte, elenca i file cancellati e le righe tolte, e tratta come specchio solo i percorsi che un repo di modulo ha toccato (prima il 2026-09-27 contava 473 file contro 31.592); `collect-day.sh` aggiunge `#distinct` (patch distinti per `git patch-id --stable`, nessun totale esistente cambia).
3. Prompt `98-daily-report.md`: regola del `.mailmap` invertita (si crea e si aggiorna, solo con prove), Passo 7 per le docs di moduli e temi, righe nuove in «Errori già commessi».
4. Docs: `links --fix --by-name` su tutti i perimetri, poi `frontmatter --fix --default-status draft`. Non `dups --fix` (DCP-56 lo subordina alla classificazione DOC-004) e nessuno spostamento (DOC-006).

## Verifica
- Link: 13.857 riscritti; per ogni evento il nuovo bersaglio è stato risolto sul disco: 0 assenti su 13.857.
- Frontmatter: 15.295 file nel primo passaggio, 21.255 blocchi spuri tolti, 27.488 chiavi aggiunte. Il secondo passaggio «ne trova 2» **non e' una misura**: `docs-organizer` salta in silenzio i file toccati da meno di 10 minuti e in quel momento gli agenti ne stavano riscrivendo centinaia. Fa fede l'audit.
- Corpo dei documenti: su 16.741 `.md` modificati (14.946 nei repo dei moduli e dei temi con `.git`, 1.795 in Barthelemy che traccia la radice) il corpo, senza il frontmatter iniziale e con i bersagli dei link normalizzati, è identico a `HEAD` in 14.083 casi; in 2.656 differisce solo per righe di metadati YAML (blocchi duplicati incollati a metà documento); in 2 differisce per una riga aggiunta da un'altra sessione AI («BMAD + second brain note», `00-INDEX.md` di Cms e di Sixteen), non da questo lavoro. Nessuna riga di contenuto persa.
- Audit dopo: link rotti 12.602 -> 9.709; senza frontmatter 1.619 -> 215; blocchi spuri 3.342 -> 685; frontmatter completo 1.253 -> 16.550; numero di file `.md` invariato (20.356).
- Worklog: `verify` stampa OK per ogni giorno riscritto con il registro nuovo.

## Decisioni
- `status: draft` per i file senza stato: «active» afferma «verificato» e il corpo non è stato rivisto (lo dice lo stesso `docs-organizer.py`).
- Una persona, un report: unione per prova, non per somiglianza. `Malebestia` resta separata.
- Gli script di misura e di verifica (confronto dei corpi, eventi dei passaggi) sono restati nello scratchpad della sessione e non entrano nel repository; chi vuole ripetere la verifica la rifà con `git diff` e `git show HEAD:`.

## Aperto
- 685 blocchi spuri e 215 file senza frontmatter restano: sono i casi «manuali» dello strumento (frontmatter non chiuso, CRLF) o file toccati da meno di 10 minuti.
- 9.709 link rotti puntano a file che non esistono: servono `--annotate` o la riscrittura del testo (User ne ha ancora 7.064, UI 1.033).
- Duplicati (`dups --fix`, 3.061 corpi) e spostamenti dalla radice (1.538 file in User, 1.552 in Notify): richiedono la migration map di DOC-006.
- Il prompt 98 ha accodate altre versioni di prompt da una sessione parallela (1.472 righe contro le 390 di stamattina): va ripulito da chi ne è l'autore.
- Lo studio per modulo e per tema (verità degli ingressi contro il codice) è nelle story dei singoli moduli; l'esito è riassunto qui sotto quando gli agenti chiudono.
