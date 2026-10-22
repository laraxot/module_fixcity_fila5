---
id: dcp-200
title: "DCP-200: registro unico delle decisioni aperte (chi decide, opzioni, proposta, cosa sblocca)"
type: story
epic: dc-parity-v2
status: ready-for-decision
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "registro decisioni aperte ente titolare dpo fornitore prodotto proposta sblocca go-live DEC"
related: [dcp-154, dcp-155, dcp-178, dcp-186, dcp-201, dcp-202, dcp-203, dcp-204, dcp-205, dcp-206, dcp-207, dcp-210, dcp-211, dcp-212]
tags: [decisioni, go-live, governance, bmad]
---

# DCP-200: registro unico delle decisioni aperte

## Fase BMAD
Plan. Nessun codice. Questa story raccoglie in un solo posto le decisioni che oggi sono sparse in una ventina di story, matrici e note (DCP-154, 155, 165, 170, 178, 180, 181, 183, 186 e le matrici di conformita). Ogni riga ha un identificativo `DEC-nn`, **chi decide**, le opzioni, una **proposta** (il default che adotto se nessuno risponde, dove la scelta non e' legale ne' di contenuto) e **cosa sblocca**. Le story di lavoro (DCP-201 .. DCP-208) citano le decisioni per identificativo.

## Come si usa
1. Chi decide scrive nella colonna «Esito» la lettera dell'opzione (o il testo), la data e il proprio nome.
2. Lo sviluppo non anticipa una decisione che spetta ad altri. Dove la proposta e' marcata «tecnica», lo sviluppo puo' procedere e il decisore puo' solo ribaltarla.
3. Le proposte non sono pareri legali. Dove serve il DPO o un legale c'e' scritto.
4. Una riga decisa si porta nella story di lavoro e qui si lascia con l'esito (non si cancella).

## A. Ente: dati e testi (solo l'ente puo' fornirli)
| ID | Decisione | Opzioni | Proposta | Sblocca | Esito |
|---|---|---|---|---|---|
| DEC-01 | Denominazione, regione, sede, recapiti, codice fiscale, numero verde veri (oggi segnaposto «Il mio Comune», «Nome della Regione», «1234567»; 4 numeri diversi in pagina) | (a) l'ente li fornisce in un file unico; (b) si pubblica solo cio' che e' certo e si toglie il resto | (a): un solo file di configurazione del tema, un solo numero verde | LEG-02, DCP-186 gruppo A | |
| DEC-02 | Testi di informativa privacy, condizioni d'uso, note legali, media policy, dichiarazione di accessibilita' | (a) l'ente li scrive/approva; (b) bozza dello sviluppo da far rivedere al DPO | (b) solo come bozza, non pubblicata finche' non approvata (le guardie `PUBLICATION_GATE=1` restano rosse per costruzione) | LEG-04, LEG-01 | |
| DEC-03 | Account social (Facebook, Twitter/X, YouTube, Telegram, WhatsApp, RSS) | (a) l'ente fornisce gli indirizzi veri; (b) si tolgono le icone | (b) finche' non esistono i canali: oggi puntano a `https://facebook.com` e simili | DCP-203 (12 icone con `href="#"`) | |
| DEC-04 | Casella di feedback sull'accessibilita' e contatto per segnalazioni di sicurezza (`security.txt`) | indirizzi dell'ente | l'ente indica due caselle distinte | DCP-143, security.txt | |
| DEC-05 | Licenza dei contenuti (DCP-170) | licenza aperta (CC BY 4.0), tutti i diritti riservati | spetta all'ente; nessuna proposta | Linee guida design | |

## B. Titolare, DPO e legale (protezione dei dati)
| ID | Decisione | Opzioni | Proposta | Sblocca | Esito |
|---|---|---|---|---|---|
| DEC-06 | Nomina e contatto del DPO, raggiungibile da home e pagine legali | nome e recapito | l'ente fornisce | LG-02 | |
| DEC-07 | Base giuridica dei trattamenti (oggi l'informativa dice «consenso» anche per la segnalazione) | consenso per tutto; compito di interesse pubblico per la segnalazione e consenso solo per il facoltativo | seconda opzione, **da far confermare dal DPO** | informativa, DCP-178 | |
| DEC-08 | Consenso di marketing («pizza», C4/D14) e consenso di registrazione diverso per lingua (C5) | tenere e correggere; togliere il marketing | togliere il marketing finche' non esiste un trattamento dichiarato; unico testo di consenso tradotto | DCP-204 | |
| DEC-09 | Registro delle richieste dell'interessato: D1-D7 del blueprint (chi gestisce, chi registra, verifica identita', proroga, conservazione, dati del richiedente, esecuzione fuori piano) **e approvazione della nuova tabella `data_subject_requests`** | accettare le proposte D1-D7 del blueprint; modificarle | accettare D1-D7; la tabella e la migrazione si scrivono senza eseguirle sul database di sviluppo | DCP-204, DCP-140 | |
| DEC-10 | Cancellazione dell'account: anonimizzare o cancellare; destino delle segnalazioni (`tickets.owner_id` e' NOT NULL e senza chiave esterna) | (a) anonimizza l'utente e tiene le segnalazioni di interesse pubblico; (b) cancella tutto; (c) cancella solo cio' che non e' pubblico | (a), con conservazione dichiarata nell'informativa; **DPO** | DCP-204, DCP-178 punto 4 | |
| DEC-11 | Verifica dell'email in registrazione (oggi `email_verified_at = now()` senza verifica, mentre il modulo promette una conferma) | obbligatoria prima dell'uso; facoltativa; togliere la promessa | obbligatoria per ricevere notifiche, non per segnalare | DCP-204, C3 | |
| DEC-12 | Eta' minima (DCP-165) e conservazione dei log (DCP-141) | 14 anni (soglia italiana per il consenso digitale), 16; durata dei log | 14 anni; durata decisa dal DPO | DCP-165, DCP-141 | |
| DEC-13 | Segnalazione pubblica gia' in stato `in_review` (B14) e email di terzi non verificate | nascondere fino alla moderazione; lasciare | nascondere fino a «approvata» | DCP-203 | |
| DEC-14 | Dichiarare OpenStreetMap come terzo nell'informativa e mettere l'attribuzione sulle mappe | si; no | si (obbligo di licenza ODbL per l'attribuzione) | DCP-203, D-informativa | |
| DEC-15 | Regola password: la pagina dice «minimo 12 caratteri», il server ne accetta 9 (C6) | portare il server a 12; portare il testo a 9 | server a 12 (regola tecnica) | DCP-204 | |

## C. Prodotto (chi governa il progetto)
| ID | Decisione | Opzioni | Proposta | Sblocca | Esito |
|---|---|---|---|---|---|
| DEC-16 | Nome definitivo e dominio (DCP-155: `fixcity.it` e' di un servizio simile; `fixcity.eu` probabilmente del progetto; marchi e prezzi non verificati) | tenere FixCity su `.eu`; cambiare nome | decide il titolare dopo una verifica di marchio | ENV-04, ENV-05, sitemap, `Sitemap:` in robots.txt | |
| DEC-17 | `terms` e `note-legali`: tenere entrambe, fonderle; la registrazione impone di accettare una pagina `terms` che non esiste (D1, D2) | fondere in una; tenere due | fondere in «Termini e note legali» e collegare la registrazione | DCP-203, DCP-204 | |
| DEC-18 | Le 84 repliche `/tests` in produzione (E2): oggi pubbliche, senza `noindex`; `robots.txt` distingue le maiuscole (`/IT/tests` risponde 200) | 404 in produzione; `noindex`; lasciare | 404 in produzione per ambiente (tecnica) | DCP-197 | |
| DEC-19 | Otto servizi in home che il progetto non eroga (cambio di residenza, appuntamenti, CIE, tessera elettorale, tributi, voucher, ...) e 9 voci di menu senza pagina | togliere; «in arrivo» non cliccabile; creare le pagine | togliere dal template (nessun `href="#"`) | DCP-198 | |
| DEC-20 | Widget a stelle in elenco e conferma: non salva nulla ma dice «il tuo parere ci aiutera'» (B3/C8); valutazione della pagina (DCP-169) | togliere; implementare con nuova tabella | togliere ora; implementare solo dopo approvazione di una tabella | DCP-203 | |
| DEC-21 | Data di ultimo aggiornamento su ogni pagina (DCP-167): quale data | data del file di contenuto; data di pubblicazione; manuale | data di ultima modifica del contenuto nel JSON della pagina | DCP-203 | |
| DEC-22 | Ricerca interna (campo che non risponde, DCP-166) | collegarla alla ricerca ticket; toglierla | collegarla a `/tickets?search=` (esiste), poi estendere | DCP-196 | |
| DEC-23 | Contratto del passo «dati» del wizard: il test manda `latitude`/`longitude`, lo schema richiede `data.location` | il test si adegua allo schema; lo schema accetta anche lat/lng | schema = fonte di verita' (e' stato cambiato a ragione da DCP-184); test da riscrivere | DCP-205 | |
| DEC-24 | `/api/ticket-details/{id}` ricade su `public_html/data/tickets.json` (id 1 risponde 200 con dati finti); CORS `*` e nessun limite su `/tickets` e `/api/tickets/geojson` (B7, B19) | togliere il ripiego; limite 60/min per IP; CORS solo dall'origine del sito | tutte e tre (tecnica) | DCP-202 | |

## D. Fornitore e operativita'
| ID | Decisione | Opzioni | Proposta | Sblocca | Esito |
|---|---|---|---|---|---|
| DEC-25 | `.env` di produzione: `APP_ENV=production`, `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`, `SESSION_SECURE_COOKIE=true`, `APP_URL` con `https`, `trustHosts` | applicare | applicare (non negoziabile) | ENV-01..06, NET-01, E7 | |
| DEC-26 | Posta: SMTP vero, mittente sul dominio del servizio (oggi `sendmail`/Postfix locale) | SMTP dell'ente; servizio esterno | SMTP con SPF/DKIM sul dominio scelto in DEC-16 | ENV-05, ENV-08 | |
| DEC-27 | Backup con ripristino provato, worker delle code, scheduler, intestazioni di cache lunghe per `/themes/Sixteen/` | applicare | applicare; la prova di ripristino e' un'evidenza da allegare | gate go-live | |
| DEC-28 | **Credenziali nella storia di git**: password FTP del disco `photos` (rimossa dai 4 config ma presente nei commit), chiave Supermemory (`laravel/.mcp.json`), token GitHub e Context7 mai impostati nell'ambiente | ruotare; riscrivere la storia (vietato: git forward-only) | **ruotare subito** sul servizio; non toccare la storia | sicurezza | |

## E. Sviluppo e strumenti (decide il titolare tecnico)
| ID | Decisione | Opzioni | Proposta | Sblocca | Esito |
|---|---|---|---|---|---|
| DEC-29 | Messaggi di commit dello script di sincronizzazione (`merge_remote_repo_2.sh` fa `commit -am .` incondizionato) | messaggio descrittivo; lasciare | messaggio con data e elenco di repo/cartelle toccate; fermarsi se il merge ha conflitti | DCP-206 | |
| DEC-30 | 162 file `.merge_file_*` tracciati nel repo `Modules/Xot` (commit `3c9667641`, 6 giorni fa), nonostante `.gitignore` | `git rm --cached` con commit in avanti; lasciare | rimuoverli dall'indice con un commit in avanti | DCP-206 | |
| DEC-31 | Date e numeri nei nomi dei file `.md` (il prompt 55 le voleva, la policy delle story no) | con data; senza data | **DECISO dall'utente il 2026-10-15: mai date, timestamp o suffissi `-1.md`, `appunti-continuous-integration-tools.md`, `-3.md` nei nomi dei `.md`.** La data sta nella frontmatter (`created`, `updated`). Regola, gate e rinomina in DCP-201 | DCP-206, DCP-201 | Deciso 2026-10-15 (utente) |
| DEC-32 | Versioni fissate degli MCP: `ruflo` e `puppeteer` con versione esplicita non rispondono, senza si | fissare dopo aver trovato la causa; lasciare `@latest` | lasciare come sono, aprire un'indagine | DCP-206 | |
| DEC-33 | Tabella `notifications` legacy con `bigint` in `fixcity_data` (e `extras`, `profiles` con tipi diversi da `fixcity_user`) | rimuovere dopo verifica; lasciare | verificare che nessun codice usi la connessione dati, poi rimuovere con migrazione (richiede approvazione) | DCP-205 | |
| DEC-34 | Test `Geo` (515) rossi perche' al tema `Meetup` manca `resources/views/pages`; `TenantTest` (2) e `HasTeamsTest` (4) rossi preesistenti | ripristinare la cartella; togliere la dipendenza dal tema; marcare come saltati con motivo | togliere la dipendenza dal tema nel bootstrap di test Geo; aggiustare i due test di User | DCP-205 | |

## F. Aggiunte della sessione tracker, MCP e test (verificate il 2026-10-14 e 2026-10-15)
| ID | Decisione | Opzioni | Proposta | Sblocca | Esito |
|---|---|---|---|---|---|
| DEC-35 | `ticket_hours.user_id` e' `bigint unsigned` ma gli id utente sono UUID (`varchar(36)`): nessuna ora di lavoro si puo' registrare; tabella vuota, nessuna schermata la scrive; 20 dei 27 test rossi di Fixcity | (a) migrazione in avanti `user_id` -> `string(36)`; (b) togliere la funzione (model, factory, 3 punti d'uso, test) | (a) solo se esiste un caso d'uso scritto per i ticket DMO, altrimenti (b); cambia lo schema, serve approvazione | DCP-210, DCP-103 | |
| DEC-36 | Issue e Discussion GitHub per le 65 story attive del tracker che non ne hanno (story 1.3, criterio 2) | una coppia per story; collegarle tutte a #383 e #392 come epic; togliere il criterio | collegare a #383 e #392 (nessuna azione verso GitHub) | story 1.3 | |
| DEC-37 | Anteprima foto nel riepilogo: riquadro documento per i PDF (rinviato) e menzione nell'informativa che l'URL firmato dell'anteprima e' un link al portatore valido 30 minuti con cache pubblica | (a) riquadro e menzione; (b) solo menzione; (c) niente | (b): il riquadro e' cosmetico (prodotto), la menzione e' del DPO; non e' un parere legale | DCP-211 | |
| DEC-38 | `components/layouts/navigation.blade.php` non compila e nessun codice lo raggiunge (DCP-50); il test lo esclude | ripararlo; rimuoverlo con un commit in avanti | rimuoverlo (tecnica) | DCP-212 | |
| DEC-39 | MCP: `gsc` parte ma senza credenziali Google; `server-github` e `server-puppeteer` sono segnati «no longer supported» su npm; il comando `supermemory` non e' nel PATH | fornire credenziali o togliere `gsc`; sostituire o togliere github e puppeteer; installare o togliere `supermemory` | togliere cio' che non si usa; sostituire puppeteer con Playwright, gia' configurato (tecnica) | `bmad-output/ai-agent-tooling-governance.md` | |
| DEC-40 | Build completo del tema Sixteen (`npm run build`): attiva i font come asset di Vite (DCP-183) e la correzione del pulsante «Cerca» (DCP-195), ma consegna anche 46 regole CSS in piu' e 10 in meno rispetto al CSS vivo (lavoro di altre sessioni) | (a) una finestra concordata, a lavoro fermo; (b) senza build, correggendo a mano il CSS compilato come in DCP-137; (c) rinviare | (a), poi sonda di rete (zero richieste a Google, nessun 404 sui `woff2`) e gate di parity | DCP-183, DCP-195 | |
| DEC-41 | Passare a Vite 8 e a `laravel-vite-plugin` 3.x per usare l'opzione `fonts` ufficiale (misurato: la config attuale non compila con Vite 8, il CSS cresce del 20%, `local()` non copre il peso 900 con `unicode-range`) | passare; restare su Vite 7 con la strada nativa | restare su Vite 7 (strada nativa gia' pronta); riaprire solo con una prova delle mappe Leaflet e del peso 900 | DCP-183 | |
| DEC-42 | Nomi dei file `.md`: gli archivi `docs/raw/` (163 file, il timestamp nel nome E' il reperto) e le 1.103 collisioni che non si risolvono senza inventare un nome (DCP-201, `bmad-output/md-filenames-collisioni-residue.md`) | archivi: lasciare con eccezione scritta / spostare in una cartella datata; collisioni: risolvere gruppo per gruppo da chi conosce il contenuto | archivi: lasciare, con l'eccezione gia' nella regola e nell'hook; collisioni: a mano, mai un numero o una data come distinguo | DCP-201 | |
| DEC-43 | I file ritirati con il suffisso `.old` (regola `no-rm-no-archive-use-old-suffix`) **non entrano in git**: `.gitignore` riga 13 ignora `*.old`. La rinomina di DCP-201 ne ha ritirati ~708 (copie identiche e quasi identiche, >= 97% delle righe): su disco ci sono, nel repository risultano cancellati; il contenuto sta nel commit padre della rinomina | (a) accettare: la storia di git basta e i `.old` restano solo in locale; (b) tracciarli, con `!docs/**/*.md.old` in `.gitignore`; (c) un'unica cartella tracciata di ritiri | (a) per le 48 copie **byte per byte identiche** (nessuna informazione persa); (b) per i ritirati per somiglianza, finche' qualcuno non ha confrontato le differenze | DCP-201 | |
| DEC-44 | Archivi `docs/raw/` (163 file `.md` con timestamp nel nome, es. `phpmd_results_20251118_221520.md`): il timestamp e' l'identita' del reperto | lasciarli; rinominarli e spostare la data nella frontmatter | lasciarli, ma elencarli in un indice e dichiararli eccezione scritta della regola | DCP-201 | |
| DEC-45 | 216 file `.md` con nome che inizia o finisce con un trattino (`--eloquent.md`, `phpstan-fixes-.md`): resti di rinomine vecchie che hanno tolto solo la cifra | passata dedicata con nome dal titolo; lasciarli | passata dedicata, dopo aver deciso cosa fare dei duplicati | DCP-201 | |
| DEC-46 | `GeocodeTicketAddressJob` (Fixcity): serve la geocodifica inversa lato server degli indirizzi dei ticket? La classe e stata creata da un'altra sessione il 2026-10-15 alle 13:39 ma non ha chiamanti, non e una QueueableAction, duplica la chiamata Nominatim di `Geo\Actions\Nominatim\ReverseGeocodeAction`, e il suo test fallisce 7 su 7 a runtime (manca `uses(TestCase)`, verificato con `pest-scratch-db.sh`) pur con PHPStan a zero. Esistono due copie dello stesso test fuori modulo (`laravel/tests/Unit/Modules/Fixcity/Jobs/` e `laravel/tests/Feature/Feature/Jobs/`). `Ticket` non ha colonna `address`, l'indirizzo vive nel JSON `location` | riusare `ReverseGeocodeAction` in una QueueableAction con un solo punto di invio; togliere job e test (i test non si cancellano senza approvazione); tenere la classe come bozza | riusare l'Action di Geo se il front office ha bisogno dell'indirizzo testuale, altrimenti togliere con approvazione; intanto aggiungere `uses(Modules\Fixcity\Tests\TestCase::class)` al test, a cura di chi ne ha il lock | DCP-241 | aperta |
| DEC-47 | Geo: `GetMapStatsAction` e `GetMapMarkersAction` nel worktree sono stub che tornano `[]` (mtime 9 ottobre), mentre HEAD e MERGE_HEAD di `Modules/Geo` hanno `['total' => 0, 'categories' => 0]`; `ExportMapDataAction` differisce da HEAD. Il merge in `Modules/Geo` e aperto dalle 13:15 | ripristinare la versione di HEAD; tenere lo stub e adeguare i test | chi ha avviato il merge lo risolve; poi sostituire il `todo` di `MapActionsTest` con `assertSame(['total' => 0, 'categories' => 0], $stats)` | DCP-242 | aperta |
| DEC-48 | Comportamenti Geo che i test vecchi davano per scontati e il codice non ha: `ReverseGeocodeAction` con corpo `[]` solleva `Undefined array key` su `display_name`; `SearchPlacesAction` lancia `RuntimeException` se Nominatim non trova nulla (i test attendevano una collezione vuota); `GetGeocodingSuggestionsAction` e uno stub `[]` senza parametro `limit`; `resources/json/comuni.json` e una lista piatta mentre `LoadGeoDataAction` vuole `{"regions": [...]}`; `ReverseGeocodeAction::execute()` non ha `zoom` | per ciascuno: guardia nella produzione oppure errore voluto; implementare l'autocomplete oppure dichiarare lo stub definitivo | decidere uno alla volta; i 5 test `todo` restano finche non c'e la decisione, nessun test cancellato | DCP-242 | aperta |
| DEC-49 | `Modules\Geo\Tests\TestCase` forza `pub_theme = Meetup` ma `Themes/Meetup` non ha `resources/views`: dal secondo test del processo il boot di `FolioServiceProvider` lancia `realpath not find dir`. 55 file di test dichiarano quel TestCase; quanti falliscano non e stato misurato | correggere il TestCase (non forzare il tema, o usarne uno esistente); migrare i test a `LightTestCase` quando non servono DB e tema | misurare prima (i 55 file uno alla volta con il runner protetto), poi correggere il TestCase una volta sola | DCP-242 | risolta per Geo il 2026-10-15: tolto il forzamento di `pub_theme` dal TestCase, 12 file rimisurati verdi (DCP-240); resta aperto per `Seo/tests/TestCase.php` (forza `Meetup`, non misurato) |
| DEC-50 | Merge in corso (MERGE_HEAD presente) in 7 repo di modulo: Geo 13:15, Blog 13:15, Fixcity 13:15, Notify 13:24, Tenant 13:25, User 13:25, Xot 13:25 (UTC, 2026-10-15), con voci `DU` non risolte in Tenant e Blog | completare il merge; abortirlo (richiede approvazione: perde la risoluzione parziale); lasciarlo | lo decide chi ha lanciato la sincronizzazione; nessun agente lo chiude o lo abortisce, e nessuno committa a mano nei moduli finche e aperto | DCP-240 | aperta |
| DEC-51 | Gate go-live `REPO-02` (nomi `.md` senza date ne suffissi): oggi segna **519 file** su 33 controlli, ma 466 stanno in `docs/archive` e `docs/consolidated` di Notify (202), User (140), Xot (81+43), 23 in `Notify/.github/skills` (skill BMAD di terzi: i file si citano a vicenda per nome, es. `step-04-session-quick-start.md`), 15 in `Notify/docs/phpstan`, 1 in `.ecc/memory` (cartella scritta da ECC e ignorata da git, `.gitignore:386`), 2 creati oggi in `docs/project` e `docs/chat`, 2 `coverage-pest-80.md` (Blog, AI; `-80` e una percentuale, non un suffisso di serie); 347 su 519 hanno gia un file con il nome corretto (collisione) | (a) rinominare tutto con migration map (rompe le skill di terzi e fonde archivi distinti); (b) escludere dal gate `.github/skills`, `.ecc` e i file ignorati da git, e trattare `docs/archive`/`docs/consolidated` come archivi (il nome e il reperto, come DEC-44); (c) lasciare il gate rosso | (b) per `.github/skills`, `.ecc` e ignorati da git (non sono documenti del progetto); per gli archivi decide l'utente, come DEC-44; rinominare ora solo i 2 file di oggi, con `md-filenames.py plan`/`apply`; chiarire se `-80` e un falso positivo dello strumento | DCP-201, DCP-250 | aperta |

## Criteri di accettazione
- [ ] Ogni riga ha un esito datato oppure una scadenza dichiarata.
- [ ] Le righe decise sono riportate nella story di lavoro corrispondente.
- [ ] Il gate `go-live-gate.py` e' rimisurato dopo le decisioni A e B e l'esito scritto in DCP-154.

## Non verificato
Marchi e disponibilita' dei nomi (DEC-16); parere del DPO e di un legale su DEC-07, DEC-10, DEC-11, DEC-12; costi del fornitore.
