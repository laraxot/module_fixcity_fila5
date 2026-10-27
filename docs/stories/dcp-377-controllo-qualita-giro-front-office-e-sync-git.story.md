---
title: "DCP-377: controllo qualita' del progetto, giro completo del front office e sincronizzazione git"
type: story
status: in-progress
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, qualita, phpstan, phpmd, phpinsights, playwright, puppeteer, front-office, design-comuni, git, sync]
qmd: "controllo qualita phpstan phpmd phpinsights playwright puppeteer giro front office ospite loggato design comuni git add commit merge pull push collisioni"
related:
  - ./dcp-360-google-callback-pagina-folio-volt.story.md
  - ../bmad/privacy-facts-verified-from-code.md
  - ../bmad/front-office-route-smoke.md
issues: []
discussions: []
---

# DCP-377: qualita', giro front office, sync git

Stato: **in-progress** (richiesta 2026-10-27). Altre sessioni: leggere qui prima di toccare git; il numero 377 e' prenotato.

## Richiesta (verbatim)

«dopo aver finito e controllato tutto il progetto con phpstan phpmd phpinsights playwright-mcp e puppetter, devi controllare anche tutto il
frontoffice tutti i percorsi degli utenti loggati e non loggati, tutto deve avere senso ed essere coerente con designcomuni, fa anche git
add commit merge pull push, per sincronizzare i files con git poi gestisci al meglio le ipotetiche collisioni studiando le collisioni
facendo scelte ogni volta e imparando sempre poi fa di nuovo i comandi git, punta alla perfezione»

## Perche' (l'obiettivo)

Chiudere il giro: il codice scritto da piu' sessioni in parallelo deve essere dimostrato sano (analisi statica, mess detector, insights, browser),
il front office deve avere senso per chi lo usa (ospite e cittadino loggato) ed essere coerente con Design Comuni, e il lavoro deve stare nei
repo (21 repo annidati + radice + bashscripts), senza perdere il lavoro altrui.

## Fasi (ordine: prima la prova, poi git)

1. **Gate statici** (output grezzo in `docs/bmad/quality-run-2026-10-27/` NON creato nel repo: solo numeri nella tabella sotto):
   PHPStan (config unica `laravel/phpstan.neon`), PHPMD, PHPInsights, marker di merge (`rg '^(<{7}|>{7}) '`), `php -l` sui file modificati.
2. **Giro front office**: ospite (`guest-fo-http-smoke.sh`, `guest-fo-smoke.mjs`) e loggato (fixture `scratch-auth-fixture.sh`, `fo-session.mjs`),
   Playwright MCP e Puppeteer; ogni percorso: stato HTTP, chiavi lang grezze, link rotti, coerenza con Design Comuni (header, footer, componenti).
3. **Correzioni** dei difetti trovati, solo nel modulo/tema proprietario (mai un file nel tema per riparare un 404).
4. **Git** (esplicitamente richiesto dall'utente): per ogni repo annidato `status`, controllo marker e sintassi, `add` per percorso (mai `add -A`
   alla cieca), `commit`, `pull`/`merge` da `laraxot/dev`, risoluzione collisioni con una scelta motivata per file, nuovo `pull`, `push`.
   Mai `checkout -b`, mai reset/rollback come scorciatoia (forward-only).

## Protocollo collisioni (da applicare e arricchire)

- Prima di ogni `add`: `git status` del repo, `find -mmin -5` e `ps` per vedere se un'altra sessione sta scrivendo.
- Marker `<<<<<<<`/`.merge_file_*` mai committati: guardia `rg` prima di `commit`.
- Per ogni conflitto: leggere i due lati, scegliere per contenuto (non per "ours/theirs" cieco), annotare la scelta e il perche' qui sotto.
- Dopo il merge: `php -l`, PHPStan sui file toccati, rilancio dei test mirati; poi di nuovo `pull` e `push`.

## Registro (si riempie durante il lavoro)

| Fase | Esito misurato | Note |
|---|---|---|
| PHPStan Modules (livello max) | 3 errori in 2 file (06:52) | `Event/database/seeders/EventDemoSeeder.php:15` e `Fixcity/tests/Unit/MediaPolicyTemplateTest.php:46`: file di altre sessioni, modificati pochi secondi prima; da rifare alla fine |
| PHPStan Themes | fuori dalla config (`paths: Modules/`) | non misurato |
| PHPMD (`phpmd.xml`, 11 moduli, solo `app/`) | 1302 violazioni in 548 file | MissingImport 616 (Xot), CamelCaseVariableName 326, CamelCasePropertyName 176, CouplingBetweenObjects 32; per modulo Xot 616, User 321, Geo 124, Cms 69 |
| PHPInsights (code / complessita' / architettura / stile; soglia config 99) | Fixcity 77.3/77.5/82.3/84.3; User 79.3/92.2/76.4/81.9; Cms 82.4/88/82.3/90.3; Gdpr 91.7/99.1/82.3/91.5 | altri moduli in coda; nessuno raggiunge 99 |
| Puppeteer MCP | non avviabile | Chromium senza `--no-sandbox` fallisce (AppArmor userns); il tool vieta l'opzione di default; serve consenso, copertura browser con Playwright |
| Giro ospite | da misurare | |
| Giro loggato | da misurare | |
| Git sync | da fare | |

## Collisioni git: scelte e lezioni

(vuoto: una riga per collisione, con repo, file, lato scelto, motivo, lezione.)

## Coordinamento fra DCP-377 e DCP-385 (scritto da DCP-385, 2026-10-27 07:00)

DCP-377 (Fixcity) e DCP-385 (Sixteen) sono la stessa richiesta data a due sessioni: la 385 e' nata alle 06:49, la 377 alle 06:55. Per non fare
lavoro doppio e per non collidere in git:

| Parte | Chi | Stato |
|---|---|---|
| Gate statici (PHPStan, PHPMD, PHPInsights) | 377 (misurati) | 385 ha corretto i 3 errori PHPStan: `MediaPolicyTemplateTest` (array_map su `Safe\preg_split`, ora `explode`) e rimosso `Modules/Event/database/seeders/EventDemoSeeder.php` (`Event::factory()` non esiste, nessun chiamante, `/it/events` e' servita dagli articoli Blog) |
| Smoke HTTP ospite (`guest-fo-http-smoke.sh`) | 377 | |
| Giro da browser: ospite con Playwright MCP, loggato con Puppeteer MCP, confronto con Design Comuni ufficiale | 385 | in corso (due agenti di sola lettura, correzioni nei moduli proprietari) |
| Verita' dei testi legali pubblicati contro il codice | 385 | in corso |
| **Git (add, commit, pull, push, collisioni)** | **un solo operatore alla volta** | vedi lock sotto |

**Lock git (obbligatorio per entrambe):** prima di qualunque `git add/commit/pull/merge/push` in un repo: `mkdir /tmp/fixcity-git-sync.lock.d` (atomico: se
fallisce, un'altra sessione sta sincronizzando; aspettare e rileggere questa pagina). Scrivere chi lo tiene in
`/tmp/fixcity-git-sync.lock.d/owner` (sessione, ora). Alla fine: `rm -r /tmp/fixcity-git-sync.lock.d`. Chi trova il lock piu' vecchio di 40 minuti
senza processi `git` in corso (`ps -eo args | grep 'git '`) puo' considerarlo orfano e segnalarlo qui prima di toglierlo.

**Puppeteer MCP e' avviabile** (la 377 lo dava per non avviabile): primo `puppeteer_navigate` con
`launchOptions={"headless":true,"executablePath":"/usr/bin/google-chrome-stable","args":["--no-sandbox"]}` e `allowDangerous=true`
(Chromium di Puppeteer 131 non e' installato; `--no-sandbox` solo perche' AppArmor blocca gli userns). Uso limitato al dev `192.168.1.40:8000` e alle
pagine statiche ufficiali Design Comuni, mai a siti sconosciuti. Playwright MCP e' un solo browser condiviso: una sessione per volta.
