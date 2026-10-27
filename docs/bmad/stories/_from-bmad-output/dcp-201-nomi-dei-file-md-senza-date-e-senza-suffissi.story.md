---
id: dcp-201
title: "DCP-201: i nomi dei file .md non hanno date, timestamp o suffissi -1/-2/-3 (regola, rinomina, blocco automatico)"
type: story
epic: dc-parity-v2
status: done
priority: P0
owner: repo
created: 2026-10-15
updated: 2026-10-15
qmd: "nomi file md senza date timestamp suffisso -1 -2 -3 rinomina collisioni hook gate regola dimenticata md-filenames"
related: [dcp-200, dcp-186]
tags: [docs, naming, convenzioni, gate, bmad]
---

# DCP-201: niente date e niente -1/-2/-3 nei nomi dei file .md

## Fase BMAD
Analyze, Plan, Implement. Owner `repo` perche' la regola vale per tutti i moduli, i temi e `bashscripts`; la story sta in `bmad-output/` perche' nessun modulo la possiede.

## Richiesta
Utente, 2026-10-15: «nei nomi dei file .md non vanno date o timestamp o finire per -1.md -2.md -3.md, fai tutto quello che puoi per non dimenticarlo mai e sistema anche i file .md che non rispettano questa convenzione». Con BMAD e second brain. Lo stesso messaggio e' arrivato a **due sessioni**: questa story divide il lavoro.

## Perche' si dimenticava (misurato 2026-10-15)
La regola **esisteva gia' in quattro file** (`markdown-filename-conventions.md`, `no-numbered-filenames.md`, `docs-chat-filename-no-dates.md`, `020-markdown-filename-convention.md`) ma:
1. nessuno la applicava a macchina: si poteva creare `2026-10-14-dcp-154-...story.md` senza che nulla dicesse di no (e il tool `docs-organizer.py` e la policy delle story dicevano addirittura di **non** rinominare le story);
2. non era nel bootstrap: ne' il `CLAUDE.md` di radice ne' la trigger map la richiamavano al momento di creare un file;
3. quattro copie della stessa regola non sono una regola: nessuno sa quale leggere.
Risultato: 2190 file `.md` fuori regola (su ~37 mila), tra cui 430 story datate, 960 circa con suffisso `-N`.

## Cosa significa «fuori regola»
- data nel nome: `2026-10-14-...`, `...-20260408-162846`, `...-2026-09`, `2025-q4-...`;
- timestamp: numero di 10-13 cifre (epoch);
- suffisso numerico di copia: `foo-1.md`, `foo-2-1.md`, `foo_3.md`;
- anno in coda (`excellence-2025.md`).
La data vive nella frontmatter (`created`, `updated`). Il nome dice **che cosa contiene**.

## Casi limite da decidere (dati di questa sessione)
| Caso | Esempi | Proposta |
|---|---|---|
| Numero che e' parte del significato | `level-4.md`, `lang-phpstan-level-10.md`, `coverage-implementation-phase-1.md`, `iso-27001.md` | **tenere**: numero dopo `level`, `livello`, `phase`, `fase`, `step`, `part`, `v`, `wcag`, `php`, `iso`, `art`... |
| Serie di identificativi in maiuscolo | `STORY-DEFECT-01.md` ... `STORY-DEFECT-10.md` | **tenere**: sono ID, non copie (il piano le trasformava in duplicati di `STORY-DEFECT-01.md`) |
| Anno che fa parte del titolo di un contratto | `cci-2026-art-12-13-progressione-economica.md`, `agid-linee-guida-2022.md` | **tenere**, l'anno non e' una data di creazione |
| Copie con contenuto diverso (`foo.md`, `foo-1.md`, `foo-2.md`) | circa 625 in `Modules/User/docs`, 68 in Notify, 40 in Cms | non rinominare a caso: se il corpo e' un sottoinsieme, `superseded` e unione; altrimenti nome dal titolo (H1) quando e' diverso; il resto a mano. **Mai** un numero come disambiguatore |
| Copie identiche | 60 | si toglie la copia (resta nella storia di git) e i riferimenti vanno al sopravvissuto |
| Archivi immutabili (`docs/raw/`) | 160 circa, nomi con timestamp del reperto | **non toccare**, elencare |
| Story datate (430) | `2026-10-14-dcp-154-...story.md` | rinominare togliendo la data: l'ID `dcp-NNN` resta, quindi i riferimenti per ID non cambiano. Supera la regola «non rinominare le story» solo per questo difetto |

## Divisione del lavoro (chi fa cosa, per non pestarsi)
| Parte | Chi | Stato |
|---|---|---|
| Strumento `bashscripts/tools/docs/md-filenames.py` (`check`, `name`, `plan`, `apply`, `links`, `selftest`) e rinomina | sessione A (autrice dello strumento, 05:46) | in corso: piano di 753 rinomine, 924 collisioni, 163 archivi, 3 senza nome |
| Analisi dei casi limite, eccezioni semantiche, collisioni (tabella sopra) | sessione B (DCP-179/181/182) | consegnata qui |
| Una sola regola canonica, richiamata dal bootstrap | sessione B | vedi criteri |
| Blocco automatico (hook sulla creazione di file) e gate (`REPO-02` nel go-live gate) | sessione B | vedi criteri |
| Esecuzione della rinomina e riscrittura dei riferimenti | **solo la sessione A**, con `apply` | non lanciarla in due |

## Criteri di accettazione
- [x] **Una** regola canonica (`bashscripts/ai/wiki/rules/markdown-filename-conventions.md`): le altre tre diventano puntatori; stato `superseded` con link, nessuna cancellazione.
- [x] `CLAUDE.md` di radice (ordini permanenti) e trigger map richiamano la regola **prima di creare un file**.
- [x] Hook `PreToolUse` su `Write` che rifiuta (exit 2) un `.md` con data/timestamp/suffisso, e dice il nome corretto; apre in caso di errore dello strumento (non blocca mai le sessioni per un guasto).
- [x] Gate `REPO-02` nel go-live gate: rosso se `md-filenames.py check` ha violazioni.
- [x] Eccezioni semantiche nello strumento (`level-4`, `phase-1`, `STORY-DEFECT-NN`, `iso-27001`) con test in `selftest`.
- [x] `apply` eseguito: `check` a 0 (archivi `raw/` esclusi e dichiarati), `links` non peggiora (rotti dopo <= rotti prima).
- [x] Memoria e `tasks/lessons.md` aggiornate; prompt 55 senza l'eccezione per le story.

## Non verificato
Se `apply` conserva i riferimenti scritti senza estensione (`[[wikilink]]`) e quelli dentro il QMD; l'effetto sugli indici QMD (da rigenerare con `QMD_INDEX=fixcity-fila5` a lavoro fermo).

## Esito dell'esecuzione (sessione A, 2026-10-15, strumento `md-filenames.py`)
**Fatto, con prove:** 1.008 file rinominati (742 dirette, 266 con nome derivato dal titolo), 48 copie identiche ritirate come `.old` (regola `no-rm-no-archive-use-old-suffix`, nessuna cancellazione), 6.389 riferimenti riscritti in 2.521 file, 0 file di altre sessioni toccati (lo strumento salta cio' che e' sporco in git o modificato da meno di 10 minuti). Verifica: 1.056 spostamenti su 1.056 coerenti, **link `.md` relativi rotti 44.125 -> 43.853** (non peggiora), `audit-prompts.sh` senza guasti bloccanti e `SLUG-MISMATCH` a 0 (i `slug:` dei prompt seguono il nome), `php -l`, `bash -n` e JSON validi sui file non-md toccati.

**Errori miei trovati e corretti nello stesso giro (da non ripetere):**
1. Un file di log storico (`Modules/Geo/docs/chat/md-rename-remove-dates-log.json`: coppie `oldRel` -> `newRel`) e' stato riscritto: le coppie vecchio->nuovo sono diventate nuovo->nuovo. Ripristinato da git. Lezione: i documenti che citano i vecchi nomi come DATO (mappe e registri di migrazione) o come ESEMPIO (le regole sui nomi, questa story, lo strumento) non si riscrivono: `NO_REWRITE` nello strumento; per le mappe di migrazione serve controllare a mano.
2. Dodici file chiamati `-2.md` ... `-21.md` (frammenti di tabelle spezzate da una shell, in `Themes/Barthelemy/docs/shared-components/`) sono diventati `2.md` ... `21.md`: nome fatto di soli numeri e riferimenti nel testo alterati (`-8.md` -> `8.md`). Riportati al nome originale; lo strumento ora non rinomina nomi senza lettere.
3. Nelle righe di `Modules/Activity/docs/index.md` gli esempi di copia (`-2.md`) erano diventati `2.md`: corretto a mano.

**Cosa resta (non e' una dimenticanza):** 1.103 file in collisione + 15 senza nome ricavabile, piu' 163 archivi `docs/raw/` esclusi (D194-1, `raw/` tiene il timestamp perche' E' il reperto). Rinominarli avrebbe voluto dire inventare nomi; motivi, aree e come risolverli in [md-filenames-collisioni-residue.md](../md-filenames-collisioni-residue.md). Decisione D194-2 da prendere: gruppo per gruppo, da chi conosce il contenuto. **Mai** un numero o una data come distinguo.

**Per le altre sessioni:** tre cose da sapere. (a) Le story che avete scritto con la data nel nome (`2026-10-15-*.story.md`, 8 file) sono state rinominate: se un vostro file punta ancora al vecchio nome, `python3 -I bashscripts/tools/docs/md-filenames.py fixrefs --map <applied.json>` lo corregge (la mappa e' nello scratchpad della sessione A; in alternativa rigenerate con `plan`). (b) I file sporchi non sono stati toccati: quando li avrete committati, `apply` rinominera' anche quelli. (c) Il test di unicita' dei numeri delle story (`StoryNumbersAreUniqueTest`) legge i nomi: vedi l'esito in coda a questa story.

## Aggiornamento sessione B, 2026-10-15 06:10: fase delle collisioni
Misura dopo l'`apply` della sessione A: restano **1119** file fuori regola: 1076 con un `foo.md` che esiste gia' (copie con contenuto diverso), 28 con bersaglio libero ma in conflitto fra loro (`struct1_<timestamp>.md`), 15 senza nome ricavabile (`-19.md`, `2026-03.md`), piu' 163 archivi `raw/` che non si toccano. Le eccezioni semantiche (`level-4`, `phase-1`, `STORY-DEFECT-NN`) non risultano piu' tra i rimasti.
**Questa sessione prende SOLO le collisioni**, con `bashscripts/tools/docs/md-collisions.py` (usa `apply_plan` di `md-filenames.py`, quindi stesse protezioni: file sporchi e recenti saltati, riferimenti riscritti, mai due file sullo stesso nome). Regola: copia il cui contenuto e' gia' in un'altra = ritirata (`.old`, nulla si cancella); contenuto diverso = nome `<base>-<parole del titolo che distinguono>`; mai un numero o una data come disambiguatore. La sessione A non rilanci `apply` finche' questa fase non e' scritta qui come chiusa.

## Verifica dopo il commit (sessione A, 2026-10-15 06:08): i `.old` non sono in git
Commit unico di tutte le sessioni: `bdf0ee651` nella radice (e i commit gemelli nei repository dei moduli e del tema), gia' pubblicato (0 avanti, 0 indietro rispetto a `laraxot/dev`). Controllo fatto sui 448 file che il commit riporta come cancellati: 49 sono sorgenti di rinomine dello strumento, **399 sono ritiri come `.old`** (copie quasi identiche, `md-collisions.py`). **Nessuno e' stato cancellato davvero**: i `.old` esistono su disco (708 in tutto, 48 miei e il resto dell'altra sessione), ma `.gitignore` riga 13 ignora `*.old`, quindi **non sono in nessun commit**. Per git sono cancellazioni.
- Cosa si perde se il disco si perde: i `.old` (la storia di git conserva comunque la versione prima della rinomina).
- Recuperare un file: `git show bdf0ee651~1:<percorso-vecchio>` (radice), oppure `git log --diff-filter=D --follow -- <percorso-vecchio>`.
- Solo per 20 dei 399 il contenuto e' identico a un altro file ancora in HEAD; gli altri 379 hanno differenze (fino al 3% delle righe): il criterio «>= 97% delle righe gia' presenti» ritira anche file con qualche riga propria. Va confrontato prima di darli per ridondanti: decisione **DEC-43** del registro.
- Lo strumento non riscrive piu' i file `*-log.json` e `*rename*.json`: il registro `Modules/Geo/docs/chat/md-rename-remove-dates-log.json` (coppie `oldRel` -> `newRel` del 2026-05-27) e' stato alterato due volte da riscritture, ripristinato da git entrambe.
- **Verifiche finali:** `phpstan analyse Modules` exit 0, 0 errori; `md-filenames.py selftest` 29 su 29; link `.md` rotti 44.125 -> 43.853; `audit-prompts.sh` senza guasti bloccanti. Suite Xot (docs) non eseguibile in questo ambiente (database `fixcity_user_test` assente): i tetti che dipendono dai nomi sono stati ricalcolati a parte (`docs/chat`: 0 file con data) e `InstructionReferencesExistTest` non scambia piu' gli esempi `-1.md` per file.

- **Report e mappe come dati (aperto).** Il report `md-filenames-collisioni-residue.md` era stato riscritto dallo strumento (esempi `2026-03-13.md` diventati `daily-progress-report.md`, elenco di collisioni con i nomi gia' risolti): ripristinato da git e aggiunto a `NO_REWRITE`. Resta `laravel/Modules/Geo/docs/DOCS-MIGRATION-MAP.md`: la colonna dei nomi vecchi e' stata riscritta (riga 347 punta a un file che esiste solo come `.old`, riga 353 ha colonna 2 non allineata) e l'altra sessione la sta modificando nello stesso verso. Da decidere insieme: dato storico (ripristino da `2cf47da6` + `NO_REWRITE`) oppure elenco di riferimenti vivi (allineare le righe 347 e 353).
- **Pubblicazione.** I commit della sessione B (`23f8ad566` radice e gemelli) contenevano gia' le mie ultime modifiche; verificati senza segreti ne' marcatori di conflitto e pubblicati in avanti su `laraxot/dev` in tutti e otto i repository.
- **Nomi che finiscono con `-` (155 file tracciati, es. `docs/roadmap-.md`, `dry-kiss-analysis-.md`).** Non violano la regola (nessuna data, nessun suffisso numerico) ma sono il residuo di una rimozione delle date fatta prima di questo lavoro (commit `cd32814db` e simili), non dello strumento: `clean_name` toglie sempre il trattino finale. Il controllo `check` oggi non li segnala. Decisione per il proprietario: aggiungere il caso a `check` e rinominarli (con la risoluzione delle collisioni, perche' quasi tutti hanno gia' un omonimo senza trattino), oppure lasciarli.

## Esito, 2026-10-15 (sessione B, dopo la fase di A e la fase delle collisioni)
Misurato con `python3 -I bashscripts/tools/docs/md-filenames.py check`, `links` e con il gate:
- **File `.md` fuori regola: da 2190 a 0.** Restano fuori dal conteggio, e non toccati, 163 archivi `docs/raw/` (il timestamp nel nome e' l'identita' del reperto): vanno elencati e decisi dall'utente (DEC-44).
- **Rinominati 456 file con collisione** (nome dal titolo, mai un numero), **ritirate 663 copie** il cui contenuto era gia' (>= 97% delle righe) in un'altra: restano sul disco come `.md.old`, fuori da git, e nella storia di git. In piu', le rinomine dirette fatte dalla sessione A.
- **Riferimenti riscritti: oltre 9000**, in oltre 2000 file. Mappa delle mie mosse: `bashscripts/docs/md-collisions-migration-map.json` (percorso vecchio verso nuovo; per le copie ritirate, il sopravvissuto).
- **Link `.md` relativi rotti: da 43853 a 43144** (non peggiora, anzi). I rimanenti 43 mila sono preesistenti: vanno trattati in una story propria, non qui.
- Gate `REPO-02`: **PASS**. Hook `md-filename-guard.sh` attivo (prova: nomi vietati rifiutati con exit 2, nomi corretti e file esistenti ammessi). `selftest` dello strumento verde.
- Commit e push in tutti i 23 repo annidati e nella radice (messaggio «Rename .md files without dates or numeric suffixes...»).

### Non risolto, da sapere
- **216 file con nome che inizia o finisce con un trattino** (`--eloquent.md`, `phpstan-fixes-.md`, `-competitors.md`): sono i resti di vecchie rinomine che hanno tolto solo la cifra. Non violano il divieto di date e `-N`, ma sono lo stesso difetto: da correggere con una passata dedicata (nome dal titolo): DEC-45.
- **I nomi derivati dal titolo sono a volte poveri** (`cms-link-collegamento-al.md`, `analisiettagliata-finali-parte.md`): sono distinti e semantici, non belli. Rivederli a mano nei moduli User e Notify quando si lavora su quei documenti; la mappa dice da dove vengono.
- **Le 663 copie ritirate non sono in git** (`*.old` e' ignorato): se un contenuto serve, si recupera con `git show <commit prima del 2026-10-15>:<percorso>`.
- L'indice QMD non e' stato rigenerato (a lavoro fermo: `QMD_INDEX=fixcity-fila5`).
- Collegamento: la sessione A ha la story `dcp-194-nomi-file-md-senza-date-e-suffissi-numerici.story.md`; questa e' la story di coordinamento.
