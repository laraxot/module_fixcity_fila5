---
title: "Registro delle lacune per il completamento del progetto"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, lacune, registro, fixcity, front-office]
qmd: "registro lacune completamento fixcity go-live front office bloccanti onde dcp-280 gap analysis"
related: ["completion-planning.md", "completion-architecture.md", "completion-brainstorming.md", "completion-front-office-audit.md", "completion-front-office-blueprint.md", "../stories/dcp-200-registro-unico-delle-decisioni-aperte.story.md"]
---

# Registro delle lacune per il completamento del progetto

## Fase BMAD

Analysis e Planning. Questo e' il registro unico: raccoglie le lacune trovate da due analisi complete, una sul progetto (sette report A-G: back office e mobile, temi, Cms/Lang/Seo/Tenant, User/Gdpr, contenuti e media, operativita', dati e deploy) e una sul front office (sei report FO-1..FO-6: pagine, flussi da ospite e da utente collegato, accessibilita'). Non contiene codice. Ogni riga rimanda alla storia che la chiude; il documento per proprietario e' in `docs/bmad/completion-gap-analysis.md` di ciascun modulo e tema.

## Come leggerlo

- VERIFICATO o MISURATO: l'analista ha aperto il file o eseguito il comando; il coordinatore ne ha ricontrollato un campione.
- RIPORTATO: preso da un altro documento e non ricontrollato. Non usarlo come prova.
- DEDOTTO: ragionamento dal codice letto, senza esecuzione.
- Due volte un'analisi ha indicato la causa sbagliata e una volta una misura e' stata fatta sull'istanza sbagliata: ogni correzione riparte da una riproduzione, non dal testo dell'audit.
- Bloccante impedisce la messa online. Qualita' degrada correttezza, sicurezza, accessibilita' o manutenzione. Rifinitura e' pulizia.
- Dove «Chi decide» nomina titolare, ente, DPO o fornitore, lo sviluppo non anticipa la decisione: la decisione e' nel registro DCP-200 (DEC-xx).

## Numeri

- Lacune registrate: 491 (276 dall'analisi di progetto, 215 dall'analisi del front office). Le righe dei due insiemi si sovrappongono in parte: vedi i gruppi di doppioni piu' sotto.
- Per gravita': 102 bloccanti, 290 di qualita', 99 di rifinitura.
- Chiuse e verificate al 2026-10-26: 21. Parziali o non confermate: 5. Il resto e' aperto o da decidere.
- Modifiche non ancora salvate in git: tutto il lavoro di questa sessione e' nel working tree, nessun commit, per scelta esplicita del progetto.

### Per proprietario (primo proprietario di ogni riga)

| Proprietario | Tipo | Lacune |
|---|---|---|
| Sixteen | tema | 224 |
| Fixcity | modulo | 112 |
| User | modulo | 23 |
| Geo | modulo | 19 |
| Cms | modulo | 13 |
| Gdpr | modulo | 12 |
| Xot | modulo | 12 |
| Tenant | modulo | 11 |
| Seo | modulo | 11 |
| Notify | modulo | 8 |
| Blog | modulo | 7 |
| Media | modulo | 6 |
| Lang | modulo | 5 |
| Rating | modulo | 5 |
| Comment | modulo | 4 |
| AI | modulo | 4 |
| Mobile | modulo | 3 |
| Activity | modulo | 3 |
| Job | modulo | 3 |
| UI | modulo | 2 |
| Meetup | tema | 1 |
| Barthelemy | tema | 1 |
| TwentyOne | tema | 1 |
| Event | modulo | 1 |

## Gruppi di doppioni tra i report

La stessa lacuna e' stata vista da piu' analisti. Si lavora una volta sola, sul primo ID; gli altri si chiudono insieme.

| Argomento | ID | Nota |
|---|---|---|
| Segreti tracciati in git | C-01, E-01, F-02, G-01 | Valori mai riportati nei documenti. Serve conferma dell'utente per `git rm --cached` e rotazione (DEC-28). |
| File di prova nella radice web | B-07, C-02, G-02 | Chiusa in DCP-282: otto file ritirati con `.old`, controllo REPO-03 nel gate. |
| Pagine di prova pubbliche (tests) | B-08, C-07, G-03 | Dato e non codice: si decide cosa resta pubblico. |
| Scheduler, worker, backup | A-07, F-05, G-06, G-07 | Servono decisioni di hosting. |
| DPO | B-03, D-10, G-10 | Nome e contatto li fornisce il titolare. |
| Cancellazione e portabilita' dei dati | A-05, D-01, D-09, D-13 | Una sola storia, dopo DEC-10. |
| Dati di esempio nel web | E-08, G-04 | `tickets.json` e simili. |
| Deploy | F-03, G-05 | Dipende dal fornitore. |
| Proxy fidati | F-09, G-09 | Chiusa in DCP-282; resta da decidere `TRUSTED_HOSTS` obbligatoria in produzione. |
| Difetti del controllo di go-live | F-04, F-07 | Parziale in DCP-282. |
| Posta | D-15, G-08 | Nessuna mail reale e' mai stata spedita e provata. |
| Ruoli | A-06, D-14 | Allineare i ruoli dei seeder a quelli usati dai pannelli. |
| Foto delle segnalazioni | A-10, E-02, E-03 | Compressione ed EXIF: scelte di privacy. |
| AI | F-08, F-10 | Il fornitore non e' deciso. |
| Valutazione della pagina | B-05, E-07, FO-5-17 | Il widget non salva nulla: o si salva (schema) o si nasconde. |

## Lacune chiuse e verificate

Una riga per lacuna, con la storia che l'ha chiusa. Le prove (comandi e output) sono nella sezione Esito di ciascuna storia.

| ID | Proprietario | Lacuna | Esito |
|---|---|---|---|
| A-01 | Fixcity | Il cittadino non riceve notifica per approvazione, chiusura, rifiuto, assegnazione, media; nessuna notifica al | chiusa nel perimetro in DCP-284 (2026-10-26): ricevuta, approvazione, rifiuto, assegnazione, chiusura via database e posta verificata; 21 test verdi; DA DECIDERE DD-1..DD-6 (filtro comune, assegnazione al cittadino, follower, riassegnazioni, lingua, posta reale e coda) |
| A-02 | Fixcity | L'attore dell'audit si perde per utenti con UUID | chiusa e verificata in DCP-281 (2026-10-26): AssignTicketAction e ChangeStatus registrano l'uuid dell'operatore; lock orfano di 30 giorni rimosso con il consenso del coordinatore |
| A-08 | Fixcity | Rotta orfana POST /livewire/form-create-ticket verso CreateTicketWizardWidget@submit senza middleware auth ne throttle | chiusa e verificata in DCP-282 (2026-10-26): rotta orfana rimossa, POST ora 405 |
| A-09 | Fixcity | Pagina Folio legacy tickets/[slug].blade.php nel modulo | chiusa e verificata in DCP-281 (2026-10-26): pagina legacy ritirata con .old2 |
| A-13 | Fixcity | Azione Filament ChangeStatus mostra tutti gli stati dell'enum, non solo i target consentiti; una transizione | chiusa e verificata in DCP-281 (2026-10-26): il cambio stato offre solo i passaggi consentiti |
| A-14 | Fixcity | Nel ViewTicket il campo "assegna a" elenca tutti gli utenti (nomi dei cittadini inclusi) e permette di assegnare un cittadino | chiusa e verificata in DCP-281 (2026-10-26): ListAssignableTicketOperatorsAction |
| A-37 | Fixcity | Pagina FO legacy tickets/[id].blade.php con testi italiani fissi ("Passaggi", "Passaggio", righe 59, 60, 64) | chiusa e verificata in DCP-281 (2026-10-26): pagina legacy ritirata con .old |
| A-38 | Fixcity | app/View/Components/Blocks/TicketList.php ha conteggi di categoria fissi inventati ("(21)", "(208)", " | chiusa e verificata in DCP-281 (2026-10-26): TicketList ritirato con .old |
| B-07 | Sixteen | public_html/info.php (phpinfo();) e due file di prova sono tracciati in git nella radice web | chiusa e verificata in DCP-282 (2026-10-26): 8 file di prova ritirati con .old, gate REPO-03 |
| C-02 | Fixcity | public_html/info.php (<?php phpinfo();) tracciato in git, nel docroot | chiusa e verificata in DCP-282 (2026-10-26): 8 file di prova ritirati con .old, gate REPO-03 |
| C-03 | Cms | PageSlugMiddleware fail-open | chiusa e verificata in DCP-283 (2026-10-26): PageSlugMiddleware fail-closed (503) con allowlist auth e throttle |
| C-04 | Cms | ResolvePageAction carica modelli arbitrari dai due segmenti dell'URL e li mette in una proprieta' pubblica Livewire | chiusa e verificata in DCP-283 (2026-10-26): ResolvePageAction non carica piu' modelli arbitrari; /profile/{uuid} ora 404; da verificare #[Locked] sul Volt del tema |
| C-05 | Lang | Auto-writer di Lang attivo in produzione | chiusa e verificata in DCP-282 (2026-10-26): auto-writer acceso solo in locale |
| C-08 | Notify | Notify: NotificationTemplate::compileString() fa Blade::render($template, $data) su subject/body_html/body_text letti dal DB | chiusa e verificata in DCP-283 (2026-10-26): NotificationTemplate non esegue piu' Blade; DA DECIDERE cosa fare dei costrutti nei modelli di produzione (oggi zero modelli) |
| D-04 | User | La rotta verification.verify non e' definita in nessun file caricato | chiusa e verificata in DCP-285 (2026-10-26): il reinvio non lancia piu' Verified; rotta verification.verify resta non definita (DEC-11) |
| D-05 | Notify | La mail di reset password non ha un template seminato | chiusa e verificata in DCP-285 (2026-10-26): template reset password in it/en/de/es; invio reale non provato (D-15) |
| D-06 | User | UserPolicy risponde true a view, create, update e delete (solo viewAny e' falso) | chiusa e verificata in DCP-285 (2026-10-26): policy registrata sulla classe reale, matrice 23 test verdi; DA DECIDERE capacita' per la cancellazione del proprio account (DEC-10) |
| F-01 | Fixcity | Provider di sviluppo (Boost, Mcp) registrati a mano in bootstrap/providers.php ma pacchetti require-dev | chiusa e verificata in DCP-282 (2026-10-26): provider di sviluppo dietro class_exists |
| F-09 | Fixcity | trustProxies limitato a 127.0.0.1 e nessun trustHosts | chiusa in DCP-282 (trustProxies da config, trustHosts opt-in); DA DECIDERE: TRUSTED_HOSTS obbligatoria in produzione |
| G-02 | Fixcity | public_html/info.php (phpinfo();) è tracciato e risponde 200 | chiusa e verificata in DCP-282 (2026-10-26): 8 file di prova ritirati con .old, gate REPO-03 |
| G-09 | Fixcity | Proxy e host fidati fissi per lo sviluppo | chiusa in DCP-282 (trustProxies da config, trustHosts opt-in); DA DECIDERE: TRUSTED_HOSTS obbligatoria in produzione |

## Lacune parziali o non confermate

| ID | Proprietario | Lacuna | Esito |
|---|---|---|---|
| A-23 | Fixcity | hasPermissionToOrCreate esegue Permission::findOrCreate a ogni controllo | non confermata in DCP-281: findOrCreate inserisce una sola volta |
| A-36 | Mobile | Mobile: app/Native/Screens/CreateTicketScreen.php:10 dichiara class TicketDetailScreen | parziale: classe rinominata in DCP-281; la schermata di creazione mobile manca ancora (DA DECIDERE) |
| B-02 | Sixteen | Dichiarazione di accessibilita' vuota e fuori dal gate | parziale: il gate ora conta le pagine di accessibilita' con avviso (LEG-04); i campi della dichiarazione restano al titolare |
| D-02 | Gdpr | La revoca dei consensi usa colonne che non esistono | parziale: la revoca ora riconosce l'utente (DCP-285); SCHEMA-DA-CHIEDERE per la colonna revoked_user_agent |
| D-08 | User | Superfici di accesso non protette o rischiose | parziale: throttle del login API chiuso in DCP-282; restano le altre superfici |

## Lacune bloccanti ancora aperte

Ordinate per proprietario. «Chi» e' chi deve agire o decidere. Le ricerche sul front office (FO-n-nn) e quelle di progetto (lettera-numero) sono nella stessa tabella.

| ID | Proprietario | Taglia | Lacuna | Chi | Stato |
|---|---|---|---|---|---|
| F-08 | AI | L | AI e privacy: l'azione invia titolo e descrizione delle segnalazioni (testo del cittadino) a api.openai.com | titolare (attivare o no, base giuridica, DPIA, responsabile  | aperta |
| C-07 | Cms | M | 83 pagine replica in pages/tests/ pubbliche, solo in italiano, e almeno una pagina di produzione dipende da un blocco di tests | titolare (DEC-18); sviluppo | aperta |
| FO-2-26 | Cms | S | Link morti e contenuto inventato | sviluppo; decisioni nei punti segnalati | aperta |
| FO-4-01 | Cms | L | L'informativa privacy mostra il riquadro "TESTO NON ANCORA COMPLETO" con 10 sezioni dell'art | sviluppo; decisioni nei punti segnalati | aperta |
| E-06 | Comment | M | Tutti i commenti sono auto-approvati e non esistono premoderazione, limite di frequenza, antispam o segnalazio | Ente/titolare (politica di moderazione, responsabile), svilu | aperta |
| A-03 | Fixcity | M | Il wizard rifiuta l'invio se non esiste un'informativa pubblicata (fail-closed) | titolare/ente (testo), sviluppo (gate) | aperta |
| A-04 | Fixcity | M | La prova del consenso non viene salvata | sviluppo, titolare (versioning dell'informativa) | aperta |
| A-05 | Fixcity | L | Nessuna conservazione limitata, nessun diritto dell'interessato, nessuna cancellazione | titolare (tempi e modalita), sviluppo | aperta |
| A-06 | Fixcity | M | Nessun seed di ruoli e permessi per la produzione | sviluppo, ente (chi sono gli operatori) | aperta |
| A-07 | Fixcity | M | Nessuno scheduler registrato per Fixcity e nessuna verifica che coda e worker siano attivi | sviluppo, fornitore (hosting/supervisor) | aperta |
| A-10 | Fixcity | M | API pubblica /api/tickets/list restituisce gli URL delle immagini originali (possibili EXIF/GPS), in contras | sviluppo, titolare (DEC-24) | aperta |
| F-02 | Fixcity | M | Credenziali tracciate: password FTP letterale in config/test/fixcity/filesystems.php:85 e in altri 7 filesy | titolare (ruotare le credenziali, decidere se riscrivere la  | aperta |
| F-03 | Fixcity | M | deploy.sh non adatto alla produzione | fornitore/titolare (ambiente di destinazione); sviluppo (scr | aperta |
| F-04 | Fixcity | M | La CI non blocca nulla e non esegue il gate | sviluppo; titolare decide quali controlli sono bloccanti | aperta |
| F-06 | Fixcity | L | CSP solo in report-only di default, con 'unsafe-inline' e 'unsafe-eval' negli script e senza report-uri/report-to | titolare (data di enforce); sviluppo (nonce al posto di inli | aperta |
| F-07 | Fixcity | M | Il go-live gate puo' dare esito verde con i blocchi sopra presenti | sviluppo; titolare approva l'elenco dei controlli | aperta |
| FO-2-25 | Fixcity | ? | Email di riepilogo promessa, non inviata | sviluppo; decisioni nei punti segnalati | aperta |
| FO-3-02 | Fixcity | S | Doppio clic su "Invia" crea due segnalazioni | sviluppo; decisioni nei punti segnalati | aperta |
| G-01 | Fixcity | M | Segreti nell'albero tracciato, non solo nella storia | sviluppo (`git rm --cached`, commit in avanti, `.gitignore`) | aperta |
| G-03 | Fixcity | M | Le 84 pagine tests.* raggiungibili come /<lingua>/tests/* sono pubbliche, senza noindex; /IT/tests | sviluppo | aperta |
| G-04 | Fixcity | M | La mappa e i filtri leggono public_html/data/tickets.json | sviluppo (DEC-24 ha la proposta) | aperta |
| G-05 | Fixcity | M | Il rilascio non è riproducibile e deploy.sh è pericoloso | sviluppo, fornitore | aperta |
| G-06 | Fixcity | M | Backup e ripristino non esistono nel progetto | fornitore (con prova di ripristino allegata) | aperta |
| G-07 | Fixcity | M | Code e scheduler non sono definiti | sviluppo (definire cosa si pianifica), fornitore (supervisor | aperta |
| G-08 | Fixcity | S | La posta di produzione non è provata e dipende dal dominio | fornitore, titolare | aperta |
| G-10 | Fixcity | M | Qualificazioni giuridiche mancanti dal registro e dal gate | titolare, DPO, legale; sviluppo apre le DEC | aperta |
| D-01 | Gdpr | L | La cancellazione dell'account non funziona e non anonimizza | titolare decide DEC-10 (anonimizzare o cancellare, destino d | aperta |
| D-02 | Gdpr | S | La revoca dei consensi usa colonne che non esistono | sviluppo | parziale: la revoca ora riconosce l'utente (DCP-285); SCHEMA-DA-CHIEDERE per la colonna revoked_user_agent |
| D-09 | Gdpr | L | Mancano registro delle richieste dell'interessato ed esportazione dei dati (accesso, portabilita') | titolare e DPO decidono; sviluppo esegue | aperta |
| D-10 | Gdpr | S codice, L documenti | Mancano DPO e documenti di responsabilita' | ente e titolare forniscono i dati; sviluppo mostra il contat | aperta |
| D-12 | Gdpr | S | Nessun controllo dell'eta' minima in registrazione | titolare e DPO decidono (14 o 16 anni); sviluppo | aperta |
| D-13 | Gdpr | M | La conservazione dei dati e' dichiarata ma non applicata | titolare e DPO decidono durate (DEC-12, dcp-141); sviluppo e | aperta |
| FO-2-29 | Gdpr | M | Diritti GDPR non esercitabili | sviluppo; decisioni nei punti segnalati | aperta |
| E-04 | Geo | S | Nessuna attribuzione OpenStreetMap sulla mappa delle segnalazioni e sui picker | Titolare (dove mostrare il credito), sviluppo | aperta |
| E-05 | Geo | L | Il browser di ogni visitatore parla direttamente con Nominatim | Titolare (provider: self-host, contratto, o OSM con limiti), | aperta |
| E-08 | Geo | M | La mappa pubblica legge il file statico public_html/data/tickets.json, rigenerato solo da un pulsante admin e da seeder | Sviluppo; titolare per la regola di ritiro | aperta |
| F-05 | Job | L | Nessuna definizione di worker di coda, scheduler e backup nel repository (DEC-27 senza esito) | titolare e fornitore (hosting, driver coda, backup); svilupp | aperta |
| C-06 | Lang | L | Traduzioni FO de/es mancanti con fallback it silenzioso | titolare (quali lingue al go-live); fornitore/traduttore (te | aperta |
| E-01 | Media | S (rimozione), M (pulizia storia) | Credenziali FTP del disco photos (host, utente, password) in chiaro in 7 file di config tracciati in git | Titolare (ruotare o chiudere l'account del servizio) poi svi | aperta |
| E-02 | Media | M | Le foto dei ticket stanno sul disco public con cartelle a id sequenziale | Titolare (le foto sono visibili prima della moderazione?), s | aperta |
| E-03 | Media | M | Il dettaglio pubblico espone gli originali con getFullUrl(); nessuna conversione/miniatura, nessuna rimozione di EXIF e GPS | Titolare (privacy), sviluppo | aperta |
| A-11 | Mobile | S (decisione) | Strategia mobile non decisa | ente/titolare | aperta |
| D-15 | Notify | M | La consegna reale della posta non e' dimostrata | ente e fornitore (SMTP, dominio); sviluppo | aperta |
| E-07 | Rating | S (nascondere), M (salvare) | I widget "stelle" di valutazione pagina dicono "Grazie, il tuo parere ci aiutera' a migliorare" ma, per quanto | Titolare (nascondere o salvare davvero) | aperta |
| B-01 | Sixteen | M | Testi legali non pubblicati | titolare scrive/approva (fornitore legale); sviluppo carica  | aperta |
| B-02 | Sixteen | M | Dichiarazione di accessibilita' vuota e fuori dal gate | titolare compila (obbligo AgID di pubblicazione); sviluppo e | parziale: il gate ora conta le pagine di accessibilita' con avviso (LEG-04); i campi della dichiarazione restano al titolare |
| B-03 | Sixteen | S | LG-02: nessun campo DPO in tutto il tema | titolare designa il DPO e fornisce i dati; sviluppo espone i | aperta |
| B-04 | Sixteen | S | LG-08: manca la licenza/attribuzione di terze parti | titolare sceglie la licenza del prodotto (DEC-05); sviluppo  | aperta |
| B-05 | Sixteen | M | LG-07: il widget "valuta questa pagina" non salva nulla | titolare decide se la valutazione e' richiesta e dove conser | aperta |
| B-06 | Sixteen | M | LG-06: "ultimo aggiornamento" mostrato solo dai blocchi articolo e dichiarazione accessibilita' | titolare decide la fonte della data (JSON, git, CMS); svilup | aperta |
| B-08 | Sixteen | S | Le ~83 repliche /it/tests/* sono servite senza controllo d'ambiente | titolare decide se esistono in produzione (DEC-18); sviluppo | aperta |
| B-09 | Sixteen | S | Segnaposto visibili: header.json ha regione name | titolare fornisce nome ente, URL, dominio; sviluppo sostitui | aperta |
| FO-1-01 | Sixteen | M | La ricerca non funziona. Il campo si chiama autocomplete-two, la lista legge q | sviluppo; decisioni nei punti segnalati | aperta |
| FO-1-02 | Sixteen | S | MISURATO. HTML di /it/tickets | sviluppo; decisioni nei punti segnalati | aperta |
| FO-1-03 | Sixteen | S | MISURATO. Presente in 68 delle 72 combinazioni pagina-lingua provate con curl | sviluppo; decisioni nei punti segnalati | aperta |
| FO-1-04 | Sixteen | M | Mancano i dati di contatto dell'ente | sviluppo; decisioni nei punti segnalati | aperta |
| FO-1-05 | Sixteen | M | Pagina obbligatoria per un sito PA, linkata dal footer di ogni pagina, ma resa senza stile e incompleta | sviluppo; decisioni nei punti segnalati | aperta |
| FO-3-03 | Sixteen | M | La conferma promette un'email che non parte | sviluppo; decisioni nei punti segnalati | aperta |
| FO-3-04 | Sixteen | L | Nessuna gestione dei propri dati | sviluppo; decisioni nei punti segnalati | aperta |
| FO-3-05 | Sixteen | M | Informativa incompleta e prova del consenso non salvata | sviluppo; decisioni nei punti segnalati | aperta |
| FO-3-06 | Sixteen | S | Invia con sessione scaduta | sviluppo; decisioni nei punti segnalati | aperta |
| FO-3-07 | Sixteen | S | "Cerca un luogo" non cerca | sviluppo; decisioni nei punti segnalati | aperta |
| FO-4-02 | Sixteen | M | Pagina con soli avviso e segnaposto | sviluppo; decisioni nei punti segnalati | aperta |
| FO-4-03 | Sixteen | M | MISURATO** shots/guest_1280_terms_it.png, guest_375_terms_it.png, guest_1280_media-policy_it.png; ricerc | sviluppo; decisioni nei punti segnalati | aperta |
| FO-4-04 | Sixteen | M | Mostra "DICHIARAZIONE NON ANCORA COMPLETA" | sviluppo; decisioni nei punti segnalati | aperta |
| FO-4-05 | Sixteen | M | Nessun recapito dell'ente in tutto il sito | sviluppo; decisioni nei punti segnalati | aperta |
| FO-4-06 | Sixteen | M | Click su 4 stelle: appare il passo 1/2, poi 2/2 con pulsante "Avanti" che non fa nulla | sviluppo; decisioni nei punti segnalati | aperta |
| FO-4-07 | Sixteen | S | Segnaposto del modello Design Comuni pubblicati come dati veri | sviluppo; decisioni nei punti segnalati | aperta |
| FO-4-08 | Sixteen | S | Sul sito pubblico: un evento con il testo di sviluppo "Sintesi di prova senza Lorem Ipsum | sviluppo; decisioni nei punti segnalati | aperta |
| FO-5-01 | Sixteen | S | Sotto 576 px il campo del codice e' largo 16 px su 49 di altezza (genitore 343 px) | sviluppo; decisioni nei punti segnalati | aperta |
| FO-5-03 | Sixteen | S | Il link porta a un 404, mentre "Traccia la segnalazione" per la stessa pratica | sviluppo; decisioni nei punti segnalati | aperta |
| FO-6-01 | Sixteen | M | Riquadro "Testo non ancora completo" con elenco di sezioni "assente o vuota" | sviluppo; decisioni nei punti segnalati | aperta |
| FO-6-02 | Sixteen | M | Segnaposto in produzione: ente "Demo" (it, 31 pagine) e "Name of the Region" / "Name der Region" / "Nombre de | sviluppo; decisioni nei punti segnalati | aperta |
| FO-6-33 | Sixteen | S | Un ospite (senza cookie) ottiene la dashboard Laravel Pulse completa | sviluppo; decisioni nei punti segnalati | aperta |
| FO-6-34 | Sixteen | S | APP_DEBUG=true, APP_ENV=local e Laravel Boost attivi in questo ambiente | sviluppo; decisioni nei punti segnalati | aperta |
| FO-6-43 | Sixteen | M | Il link alla propria pratica (l'unico ticket personale del fixture) punta a /{lg}/tickets/13, che risponde 4 | sviluppo; decisioni nei punti segnalati | aperta |
| C-01 | Tenant | M | Segreti di altri clienti nel working tree tracciato | titolare (rotazione, riscrittura storia); sviluppo (spostare | aperta |
| C-09 | Tenant | L | Promessa "piattaforma multi-comune" senza isolamento dei dati | titolare (modello: un'istanza per comune o istanza condivisa | aperta |
| D-03 | User | L | La verifica dell'email non esiste nel flusso reale | titolare decide DEC-11 e DEC-13; sviluppo esegue | aperta |
| D-07 | User | M | Google OAuth non e' pronto per la produzione | fornitore/ente (credenziali OAuth); titolare (consenso, base | aperta |
| D-08 | User | M | Superfici di accesso non protette o rischiose | sviluppo (per i file morti: ritiro con suffisso `.old`) | parziale: throttle del login API chiuso in DCP-282; restano le altre superfici |
| D-11 | User | S | La regola password e' incoerente in tre punti | titolare decide DEC-15; sviluppo esegue | aperta |
| D-14 | User | M | Ruoli PA non pronti per la produzione | ente definisce i ruoli reali; sviluppo | aperta |
| D-16 | User | L | SPID e CIE: la story dcp-144 e' in needs-decision con priorita' P1 e cita l'obbligo di accesso con identita' d | ente decide; sviluppo/fornitore integra | aperta |
| FO-3-01 | User | M | Recupero password: pagina non a tema, in inglese, chiavi grezze | sviluppo; decisioni nei punti segnalati | aperta |
| FO-5-02 | User | M | Pagina stock di Laravel dentro un guscio vuoto | sviluppo; decisioni nei punti segnalati | aperta |

## Dove sono i dettagli

- Per ogni modulo e tema: `docs/bmad/completion-gap-analysis.md` (e, se le lacune sono molte, i file `-bloccanti`, `-qualita`, `-rifiniture`). Sono la vista per proprietario di questo stesso registro.
- Piano di lavoro a onde, percorso critico e calendario delle decisioni: [completion-planning.md](completion-planning.md).
- Contratti tra proprietari e regole di progetto: [completion-architecture.md](completion-architecture.md).
- Analisi delle idee e dei rischi: [completion-brainstorming.md](completion-brainstorming.md).
- Audit del front office, pagina per pagina: [completion-front-office-audit.md](completion-front-office-audit.md); come si costruiscono le funzioni mancanti alla maniera Filament: [completion-front-office-blueprint.md](completion-front-office-blueprint.md).
- Rapporti grezzi delle tredici analisi (sette di progetto, sei sul front office), conservati per intero: cartella `completion-audit/` accanto a questo file.
- Decisioni che spettano al titolare: [registro DCP-200](../stories/dcp-200-registro-unico-delle-decisioni-aperte.story.md).
- I documenti generici del 2026-10-26 (`docs/completion-backlog.md`, `docs/bmad-remaining-work.md`, `docs/bmad/completion.md`, `plans/fixcity-completion-*`) sono sostituiti da questo registro: restano come cronologia e puntano qui.

