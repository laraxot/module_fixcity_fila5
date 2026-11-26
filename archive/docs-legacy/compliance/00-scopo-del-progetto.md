---
title: "FixCity: scopo del progetto e ragioni delle scelte"
type: concept
status: draft-for-dpo-legal-review
story: dcp-112
owner: module:Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [scopo, civic-tech, design-comuni, compliance, bmad]
related:
  - ./01-matrice-conformita-normativa.md
  - ../../../../Themes/Sixteen/docs/compliance/01-matrice-conformita-interfaccia.md
  - ../../../../Themes/Sixteen/docs/bmad/stories/dcp-112-scopo-e-conformita-normativa.story.md
  - ../stories/dcp-54-scopo-del-progetto-e-conformita-normativa.story.md
  - ../../../../../bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md
qmd: "scopo progetto fixcity perche design comuni login segnalazione cittadino operatore ciclo ticket trasparenza multilingua mobile costo scelta"
---

# FixCity: scopo del progetto e ragioni delle scelte

Questo documento non e' un parere legale. Descrive che cosa il progetto vuole essere, perche' e' costruito cosi' e quanto costa ogni scelta, citando i file che lo provano. Le norme e il loro stato sono nella [matrice di conformita](01-matrice-conformita-normativa.md). Verifiche eseguite il 2026-10-13 sull'ambiente di sviluppo `http://192.168.1.40:8000` (APP_ENV=local, tenant `local/fixcity`, contenuti segnaposto). Percorsi relativi a `laravel/` salvo dove indicato.

## 1. Che cos'e' FixCity

FixCity e' una piattaforma civic-tech per Comuni italiani: il cittadino segnala un disservizio urbano (buca, lampione spento, rifiuti) indicando il punto su una mappa, il Comune lo prende in carico e il cittadino vede che cosa accade dopo. Il documento canonico dello scopo e' `bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md` (sezione "Scopo"); il brief approvato e' `bmad-output/product-brief.md`; i requisiti sono in `bmad-output/prd.md` (FR-001..FR-018, NFR-001..NFR-007).

Nota di coerenza: la story DCP-54 indica come fonte `Modules/Fixcity/docs/wiki/concepts/fixcity-scopo-e-target.md`; quel file non esiste (verificato con `find`). Il file reale e' quello in `bashscripts/`.

## 2. Il problema pubblico

Dal brief (`bmad-output/product-brief.md`, sezione "Problem"): le segnalazioni arrivano oggi da telefono, email, sportelli e fogli di calcolo; il cittadino non sa se la segnalazione e' stata ricevuta ne' che cosa succede dopo; il personale non ha geografia strutturata, responsabile, evidenza dei tempi e traccia storica. La promessa e' rendere la segnalazione osservabile dall'inizio alla fine: un responsabile, uno stato corrente, un confine chiaro tra pubblico e privato, una storia recuperabile.

Il valore per l'ente e' duplice: un canale unico e tracciabile al posto di canali sparsi, e una base di dati geografica sui disservizi. Il valore per il cittadino e' sapere a che punto e' la propria richiesta.

## 3. Gli attori

| Attore | Che cosa fa | Fonte |
|---|---|---|
| Ospite (non identificato) | Legge: elenco e mappa pubblici, dettaglio pubblicato, tracciamento con codice. Non scrive. | `prd.md` FR-001, FR-003; `Themes/Sixteen/docs/decision-log/D17-tickets-create-richiede-auth.md` ("La lettura resta aperta") |
| Cittadino (autenticato) | Crea la segnalazione (privacy, luogo, dettagli, foto, riepilogo), vede solo le proprie, segue quelle altrui, riceve notifiche, valuta una segnalazione risolta | `prd.md` FR-002, FR-004, FR-005, FR-012 |
| Operatore | Triage, assegnazione, cambio di stato con attore e motivo, commenti, allegati (pannello Filament) | `prd.md` FR-006..FR-009 |
| Supervisore e amministratore | Carico di lavoro e SLA, ruoli, tenant, contenuti, integrazioni | `prd.md` FR-010, FR-011, FR-014 |

I ruoli sono nel modulo User/Tenant; il confine di proprieta' e' `tickets.owner_id` (`bmad-output/security-plan.md`: "Treat `owner_id` as the only citizen ownership authority").

## 4. Il ciclo: segnalazione, presa in carico, intervento, chiusura, trasparenza

| Fase | Che cosa avviene nel codice | Dove si vede |
|---|---|---|
| Segnalazione | Wizard a 3 passi (privacy, dati con mappa, riepilogo) poi `CreateTicketAction`; stato iniziale `pending`; codice di tracciamento allocato da `AllocateTicketCodeAction` | `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`; `/it/tickets/create` (richiede login) |
| Presa in carico | L'operatore sposta il ticket da `pending` a `in_review`/`in_progress` e lo assegna (`AssignTicketAction`, `ChangeStatus`) | `Modules/Fixcity/app/Enums/TicketStatusEnum.php` (draft, pending, in_review, in_progress, on_hold, resolved, closed, reopened, open) |
| Intervento | Stati `in_progress`, `on_hold`; timeline con attore e motivo (`RecordTicketActivityAction`, `BuildTicketTimelineAction`) | pagina di tracciamento `/it/tickets/track?code=...` |
| Chiusura | Stati `resolved`, `closed`; il proprietario puo' valutare (`GetCitizenRatingAggregateAction`); `reopened` consente la riapertura | `prd.md` FR-005 |
| Trasparenza | Un ticket e' pubblico solo se lo stato e' in `TicketStatusEnum::canViewByAll()` (open, in_review, in_progress, on_hold, resolved, closed, reopened); `pending` e `draft` restano privati, salvo per il proprietario | `Modules/Fixcity/app/Models/Ticket.php::isVisibleOnPublicFrontoffice()`; test `tests/Feature/TicketTest.php` e `tests/Unit/Policies/TicketPolicyTest.php` (non rieseguiti da me) |

Conseguenza di progetto: la pubblicazione avviene dopo una presa in carico dell'operatore, quindi esiste un filtro umano prima che il testo libero di un cittadino diventi visibile a tutti. E' una scelta di privacy by design (vedi la matrice, riga sul GDPR art. 25), non un obbligo che il codice dichiari come tale.

## 5. Perche' e' costruita cosi'

Ogni riga riporta la motivazione dichiarata nel repository, il costo che la scelta impone (misurato o dedotto, e dichiarato come tale) e la fonte.

| Scelta | Motivazione dichiarata | Costo | Fonte |
|---|---|---|---|
| Interfaccia secondo Design Comuni (kit di AgID e Dipartimento per la trasformazione digitale basato su Bootstrap Italia) | Il Comune deve offrire un servizio coerente con il modello della PA; parity HTML e visiva con le pagine statiche di riferimento sulle pagine critiche | Il kit e' una fotografia: il riferimento ha dati segnaposto ("Nome del Comune", "Numero verde: 800 016 123", "Via Roma 123 - 00100 Comune") che oggi compaiono nel footer del sito (verificato con `curl /it`). Inseguire la parity ha assorbito molto lavoro: l'epic DCP-00 misura percentuali di HTML e di pixel | `bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md`; `Themes/Sixteen/docs/bmad/stories/dcp-00-epic-design-comuni-parity-v2.story.md`; `config/local/fixcity/database/content/sections/footer.json` |
| Pagine guidate da JSON (CMS): Folio generico, `<x-page>`, blocchi Blade, `tests.<slug>.json` | Riuso tra Comuni e tenant: il contenuto e' dato, non codice; stesso motore per decine di pagine | (a) Una URL senza JSON risponde 200 con `<main>` vuoto (soft 404): provato su `/it/tests/privacy`, 200 con 0 caratteri di testo. (b) Il controllo di accesso della pagina vive nel JSON (`"middleware": ["auth"]`) e `PageSlugMiddleware` e' fail-open su errore di lettura (DCP-49, SEC-02, non corretto). (c) Le copie del JSON per tenant possono divergere (DCP-42). (d) I corpi pagina sono tradotti solo in it ed en: `/de` e `/es` mostrano il testo italiano (vedi sezione 7) | `Themes/Sixteen/docs/bmad/stories/dcp-02-tests-pages-are-data-contract.story.md`; `Modules/Fixcity/docs/stories/dcp-49-audit-sicurezza-viste-wizard.story.md` |
| Laravel 13, PHP 8.4, moduli nwidart, Filament per il back office | Monolite modulare: transazioni semplici su ticket, attivita' e notifiche; riuso tra Comuni; scartati i microservizi | 22 moduli attivi in `modules_statuses.json`; piu' connessioni DB (`user`, `fixcity`); superficie ampia da mettere in sicurezza e da documentare. Il back office Filament non e' stato misurato per accessibilita' in questo studio | `bmad-output/architecture.md`; `laravel/modules_statuses.json` |
| Tailwind, Alpine e Lit invece del pacchetto Bootstrap Italia a runtime | "Stiamo RIFACENDO Bootstrap Italia con Tailwind @apply + Alpine.js": stessi nomi di classe del riferimento AgID (parity HTML), CSS purgato e piu' leggero, nessun conflitto con Filament e Tailwind | (1) Non e' vero che nel progetto "non c'e' Bootstrap Italia": `resources/css/design-comuni/components.css` e' generato estraendo le regole usate da `bootstrap-italia.min.css` (versione 2.9.0) e l'intestazione dichiara licenza BSD-3-Clause, (c) Presidenza del Consiglio dei Ministri. Le regole sono quindi opera derivata di un software con licenza: servono gli obblighi di ridistribuzione (vedi matrice, riga sulle licenze). (2) I comportamenti accessibili del JS di Bootstrap Italia (focus nei modali, tastiera nei menu) vanno riscritti in Alpine e provati: la regressione e' possibile. (3) Debito CSS: 143 regole `!important` legacy isolate con un plugin PostCSS (DCP-03). (4) Deriva da upstream: la copia e' ferma alla 2.9.0 mentre i documenti citano anche la 2.8.8 | `Themes/Sixteen/docs/NO_BOOTSTRAP_ITALIA.md`; `Themes/Sixteen/docs/design-comuni/bootstrap-italia-tailwind-apply.md` ("Benefits"); `Themes/Sixteen/resources/css/design-comuni/components.css` righe 1-8; `Modules/Fixcity/docs/TECH-STACK-NO-BOOTSTRAP.md` |
| La lettura e' pubblica, la creazione richiede il login (DCP-42, D17) | Il Comune risponde di cio' che prende in carico (SLA, notifiche, valutazione); un ticket anonimo era comunque rifiutato a tre livelli del codice (widget, `CreateTicketAction`, colonna `owner_id` non nulla); meglio dirlo all'ingresso che all'ultimo clic. Il riferimento Design Comuni presuppone un'identita' (card autore, "Consulta la richiesta nella tua area riservata") | Barriera d'accesso reale. Il login e' con email e password: nessun SPID o CIE (`grep` su `Modules/*/app` e `config`, DCP-48). L'identificazione richiede piu' dati del minimo (nome, cognome, email, password), quindi pesa sulla minimizzazione. L'alternativa "ospiti ammessi" richiederebbe un altro prodotto (D17, sezione "Alternativa") | `Themes/Sixteen/docs/decision-log/D17-tickets-create-richiede-auth.md`; `Modules/Fixcity/docs/stories/dcp-42-tickets-create-auth-regressione.story.md` |
| Tracciamento pubblico con codice (capability) | Chi ha il codice vede lo stato e la timeline pubblica; il codice e' casuale (circa 82 bit, DCP-49 SEC-08) | Il codice e' un segreto: serve un limite di tentativi. Oggi non c'e': 14 richieste ravvicinate a `/it/tickets/track?code=...` danno 14 risposte 200 (prova di questo studio; DCP-102 aperta) | `bmad-output/security-plan.md`; `Themes/Sixteen/docs/bmad/stories/dcp-102-tracking-pubblico-senza-rate-limit-oscurato-dal-tema.story.md` |
| Mappa con coordinate e Leaflet su tessere OpenStreetMap | Ogni segnalazione ha una posizione; riuso del componente del modulo Geo | Le tessere sono richieste dal browser a `*.tile.openstreetmap.org` (verificato con Playwright su `/it` e `/it/tickets`): un terzo riceve l'IP del visitatore. Il geocoding lato server usa Nominatim (DCP-49, SEC-07) | `Modules/Geo/resources/js/widgets/geo-map-widget.js`; sonda di questo studio |
| Multilingua (it, en, de, es) | Il cittadino straniero deve capire come segnalare; il brief chiede "localized service discovery" | Il chrome (header, footer, menu) e' tradotto in quattro lingue, ma il corpo delle pagine JSON solo in it ed en. Sotto `<html lang="de">` e `<html lang="es">` la home e' interamente in italiano (27 righe su 27 uguali all'italiano). Lo scopo dichiarato prevede "IT/EN minimo" | `config/laravellocalization.php` (de, en, es, it attive); `bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md` ("Utenti target") |
| Mobile first | Il flusso del cittadino e' quasi sempre da telefono, sul posto del disservizio; obiettivo "completamento sotto due minuti" | La mappa e il geolocalizzatore dipendono dal browser: la geolocalizzazione richiede un contesto sicuro (HTTPS), che in sviluppo manca. Sulle pagine pubbliche misurate non c'e' scorrimento orizzontale a 375 e 768 px (vedi la matrice di interfaccia) | `bmad-output/product-brief.md` ("Success"); sonda di questo studio |

## 6. Che cosa FixCity non e'

Dai non-obiettivi dichiarati: non e' un CMS generico, non e' un ERP comunale, non sostituisce protocollo o PEC, non ha chatbot, gamification, notifiche di marketing, riscrittura nativa o microservizi speculativi, e non dichiara conformita' legale senza evidenza del titolare (`bmad-output/product-brief.md` "Non-goals"; `bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md` "Confini prodotto"). Questo studio rispetta lo stesso confine.

## 7. Dove lo scopo non e' ancora raggiunto

Lo scopo ("osservabile dall'inizio alla fine, per un Comune reale") dipende da fatti che il codice non puo' fornire e da lacune che il codice puo' colmare. Le principali, con prova, sono nella matrice; qui il quadro:

- Il titolare non e' definito: il footer dice "Nome del Comune", `config/comune.php` ha valori di esempio, il testo dell'informativa dice "FixCity tratta i dati" senza indicare un ente (file `config/local/fixcity/lang/it/policy.md`). Senza titolare non esiste informativa corretta.
- I link legali del footer (`/it/privacy`, `/it/note-legali`, `/it/accessibilita`, `/it/amministrazione-trasparente`, `/it/media-policy`) rispondono tutti 404 (alle 17:01 puntavano a percorsi senza lingua, localizzati da DCP-123 alle 18:12, ma le pagine mancano). La pagina `pages/privacy/index.blade.php` del tema e' stata cancellata dal commit `30d521c` (2026-10-11, "."), ma i documenti che la dichiarano attiva non sono stati aggiornati.
- L'area personale (DCP-106, 110, 111) e' in costruzione: i diritti dell'interessato e le preferenze non hanno ancora una pagina servita.

Per l'elenco ordinato delle lacune e le story di chiusura (DCP-113 e successive) si veda la story [DCP-112](../../../../Themes/Sixteen/docs/bmad/stories/dcp-112-scopo-e-conformita-normativa.story.md).

## 8. Relazione con DCP-54

La story DCP-54 (modulo Fixcity, avviata alle 16:51) ha lo stesso titolo di lavoro e un documento in corso `wiki/concepts/norme-e-conformita-fixcity.md`. Questo documento e la matrice DCP-112 sono stati prodotti in parallelo, con metodo diverso: DCP-112 verifica ogni norma su fonte primaria scaricata e misura le pagine con browser e `curl`. Le due matrici vanno riconciliate dal coordinatore (vedi la sezione "Relazione con DCP-54" della story DCP-112). Nessun file di DCP-54 e' stato modificato.
