---
title: "FixCity: norme, conformità e lacune (matrice normativa DCP-54)"
type: concept
tags: [compliance, norme, gdpr, privacy, cookie, accessibilita, wcag, cad, agid, spid, cie, sicurezza, bmad]
created: 2026-10-13
updated: 2026-10-13
status: draft-for-dpo-legal-review
story: dcp-54
related:
  - ../../stories/dcp-54-scopo-del-progetto-e-conformita-normativa.story.md
  - ../../stories/dcp-49-audit-sicurezza-viste-wizard.story.md
  - ../../../../../../bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md
  - ./design-comuni-service-workflow-scope.md
qmd: "FixCity norme conformità matrice CAD GDPR WCAG AgID cookie SPID CIE informativa privacy dichiarazione accessibilità lacune DCP-54"
---

# FixCity: norme, conformità e lacune

> **Avvertenza. Questo documento NON è un parere legale.** È uno studio tecnico che mette in relazione il comportamento verificabile del software con le norme che un servizio digitale di un Comune italiano deve rispettare. Gli articoli citati sono quelli che l'autore conosce con ragionevole sicurezza; dove un numero di articolo o di comma non è certo, la cella lo dice ("da verificare") e non lo inventa. Ogni interpretazione giuridica (per esempio: la base giuridica del trattamento, se una segnalazione sia un "procedimento" ai sensi della L. 241/1990, se serva lo SPID) spetta al Titolare (il Comune), al suo Responsabile della protezione dei dati (RPD o DPO) e al Responsabile per la transizione digitale (RTD). Nessuna riga di questa matrice autorizza a dichiarare "conforme" il servizio verso terzi.

Data delle verifiche: 2026-10-13. Ambiente: app di sviluppo `http://192.168.1.40:8000` (APP_ENV=local, HTTP senza TLS, tenant `local/fixcity`, contenuti segnaposto). I percorsi dei file sono relativi a `laravel/` salvo dove indicato; `bmad-output/` e `bashscripts/` sono relativi alla radice del repository.

## Scopo e perché

### Che cosa è FixCity e per chi

FixCity è una piattaforma civic-tech per Comuni italiani: il cittadino segnala un disservizio urbano, l'operatore comunale lo prende in carico dal back office, e l'amministrazione offre un canale digitale accessibile e allineato alle linee guida della PA (`bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md:22`, `:25-27`). Il dominio centrale non è l'inserimento di dati ma il ciclo di vita del ticket: creazione, assegnazione, risoluzione, feedback (`fixcity-scopo-e-target.md:28`).

Il problema che risolve, nelle parole del brief di prodotto: le segnalazioni oggi arrivano da telefono, email, sportelli e fogli di calcolo; il cittadino non sa se la segnalazione è stata ricevuta né che cosa accade dopo; il personale non ha geografia strutturata, responsabile, evidenza dei tempi e traccia storica (`bmad-output/product-brief.md:16-20`). La promessa è rendere la segnalazione osservabile dall'inizio alla fine, con proprietario, stato corrente, attore responsabile, confine pubblico/privato e storia recuperabile (`product-brief.md:28-33`).

Gli utenti previsti sono tre: il cittadino (mobile-first, multilingua, percorso in pochi passi), l'operatore (Filament: liste, filtri, mappa, azioni di workflow) e lo sviluppatore/agente (`fixcity-scopo-e-target.md:44-46`; `product-brief.md:22-26`).

I confini sono dichiarati: non è un CMS generico, non è un ERP comunale, **non sostituisce protocollo o PEC** (`fixcity-scopo-e-target.md:48-50`). Il PRD esclude esplicitamente chatbot, gamification, notifiche di marketing, riscrittura nativa e "certificazione legale senza evidenza del titolare" (`bmad-output/prd.md:126-129`; `product-brief.md:59-62`).

### Perché è fatto in questo modo

| Scelta | Motivo dichiarato | Fonte |
|---|---|---|
| Design Comuni / Bootstrap Italia come riferimento visivo | Il Comune deve offrire un servizio coerente con il modello AgID; parity HTML/CSS sulle pagine critiche (elenco, dettaglio, wizard) | `fixcity-scopo-e-target.md:31-33` |
| Monolite modulare Laravel 13 / PHP 8.4 con moduli nwidart | Transazioni semplici su ticket, attività e notifiche; riuso tra Comuni; scartati microservizi e un secondo modello di ticket per il mobile | `bmad-output/architecture.md:18-31` |
| Folio + Actions, niente controller HTTP né Services | Confine UI/dominio fisso; la logica di business sta nelle Actions del modulo proprietario | `architecture.md:47-50`; `fixcity-scopo-e-target.md:37-38` |
| Wizard Filament a passi | Il riferimento è un flusso a 3 passi più conferma; lo schema `TicketForm::getSteps()` è fonte unica per front office e back office, con validazione per passo, upload e stato Livewire | `Modules/Fixcity/docs/stories/dcp-35-tickets-create-privacy-dati-coordinamento.story.md:34` |
| Mappa e coordinate | Ogni segnalazione ha una posizione; elenco e mappa pubblici mostrano solo ciò che è pubblicabile | `prd.md:38-39` (FR-001); `product-brief.md:37` |
| Tracciamento pubblico con codice | Il codice è una "capability" non enumerabile: chi lo possiede vede lo stato, gli altri no; il codice completo compare solo al proprietario | `prd.md:42-43` (FR-003); `architecture.md:60-64`; `Modules/Fixcity/app/Actions/BuildTicketPublicDetailsPayloadAction.php:48-58` |
| Back office Filament per operatori | Triage, assegnazione, transizioni di stato con attore, motivo e orario, SLA | `prd.md:50-56` (FR-006..FR-010); `architecture.md:66-69` (ADR-004) |
| Valutazione del servizio | Il proprietario di un ticket risolto può dare un voto | `prd.md:46` (FR-005) |
| Privacy e accessibilità come requisiti, con decisione al titolare | Il codice non deve decidere se la casella privacy sia consenso o presa visione; WCAG 2.1 AA sui flussi critici | `bmad-output/security-plan.md:31-35`; `prd.md:94-95` (NFR-003), `:102-103` (NFR-007) |
| Non promettere SPID/CIE, pagoPA ecc. copiandoli dai modelli | "Do not infer support for SPID/CIE, pagoPA ..." finché non esiste un flusso reale | `Modules/Fixcity/docs/wiki/concepts/design-comuni-service-workflow-scope.md:27-31` |

Nota di coerenza: la story DCP-54 cita come SSoT `Modules/Fixcity/docs/wiki/concepts/fixcity-scopo-e-target.md`, ma quel file non esiste nel modulo; il documento esiste in `bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md` (verificato con `find`). Qui si usa quello.

## Come leggere la matrice

Ogni riga ha un identificativo `N-xx`. Le colonne sono: norma e articolo, che cosa richiede (in una frase), come il progetto la rispetta, la prova, la lacuna, la gravità.

**Livelli di prova** (sempre indicati all'inizio della cella "Prova"):

| Sigla | Significato |
|---|---|
| V | Verificato da me il 2026-10-13: lettura del codice (file:riga) oppure risposta HTTP, DOM, Playwright, `artisan tinker` in sola lettura sull'app di sviluppo |
| T | Esiste un test Pest che copre il fatto. **Non l'ho rieseguito**: la regola di questo studio è sola lettura e i test toccano il database |
| D | Dedotto dalla norma o dal codice, senza prova diretta |
| L | Da verificare con DPO, RTD o legale |

**Stato**: Provato (solo per fatti stretti e dimostrati), Parziale, Non conforme (lacuna dimostrata), Non verificabile (dipende da fatti fuori dal repository), Non pertinente (con motivo).

**Gravità**: Bloccante (non si mette in produzione senza chiuderla), Alta, Media, Bassa. La gravità riguarda il rischio per l'ente, non la difficoltà tecnica.

Limiti dello studio, da tenere presenti: l'ambiente è di sviluppo; il Comune destinatario non è definito (il footer dice "Comune di Nome Comune"); il proxy HTTPS di sviluppo (`192.168.1.40:8080`) non rispondeva durante lo studio, quindi TLS e HSTS non sono verificati; non ho potuto autenticarmi (nessun account di prova, DCP-98), quindi il wizard autenticato è letto nel codice e non osservato nel browser; i test Pest non sono stati rieseguiti.

## Matrice normativa

### A. Codice dell'Amministrazione Digitale (D.Lgs. 82/2005, "CAD") e servizi in rete

| ID | Norma e articolo | Che cosa richiede | Come lo rispetta il progetto | Prova | Lacuna | Gravità |
|---|---|---|---|---|---|---|
| N-01 | CAD artt. 1-3 (art. 3: diritto di usare in modo accessibile ed efficace gli strumenti digitali nei rapporti con la PA, fermi i diritti delle minoranze linguistiche; formulazione da verificare) | Il cittadino deve poter usare il servizio digitale in modo accessibile ed efficace | Servizio web mobile-first, wizard a passi, mappa, tracciamento con codice (`fixcity-scopo-e-target.md:44`; `prd.md:38-46`) | V: le pagine pubbliche rispondono 200 su `/it` e `/en`; axe-core 4.13.0 (WCAG 2.1 A e AA) non trova violazioni nel DOM del prodotto su 8 pagine (sezione "Verifiche", V11). V: però i 10 link del footer danno 404 (V2), compreso "Segnalazione disservizio" | "Efficace" non è dimostrato oltre i controlli automatici; link critici rotti; nessun collaudo con utenti reali | Media. Stato: Parziale |
| N-02 | CAD art. 7 (diritto a servizi online semplici e integrati; qualità; rilevazione della soddisfazione degli utenti; commi da verificare) e art. 63 (progettare i servizi in rete per la migliore soddisfazione, accertando il grado di soddisfazione; comma da verificare) | Misurare la soddisfazione di chi usa il servizio | Esiste una valutazione persistente: `citizen_rating` su `Ticket` (`Modules/Fixcity/app/Models/Ticket.php:200-201`), widget `TicketCitizenRatingPromptWidget` per il proprietario del ticket risolto e statistiche in back office `CitizenRatingOverviewWidget` | T: 6 test in `Modules/Fixcity/tests/Feature/Filament/TicketCitizenRatingPromptWidgetTest.php:18-103`. V: la pagina FO di conferma mostra invece le stelle del modello Design Comuni ("Quanto è stato facile usare questo servizio?") che **non inviano nulla**: `dcRating` (`Themes/Sixteen/resources/js/theme/design-comuni-home.js:64-108`) cambia solo la visibilità e lancia un evento DOM `feedback-submit` senza alcun ascoltatore nel repository; Playwright: dopo il clic su 5 stelle, 0 richieste di rete (V10) | Il voto dato in conferma non viene salvato ma la pagina risponde "Grazie, il tuo parere ci aiuterà a migliorare": affermazione non vera. La valutazione che si salva vive nel contesto Filament, non nella conferma FO | Media. Stato: Parziale |
| N-03 | CAD art. 12 (uso delle ICT nell'azione amministrativa) e art. 17 (Responsabile per la transizione al digitale, RTD, e ufficio per il digitale) | L'ente organizza l'uso del digitale e nomina l'RTD | Fuori dal software: è un ruolo dell'ente. Nel codice dei moduli e del tema non ci sono riferimenti all'RTD (grep di `RTD` e `transizione digitale`: 0 risultati) | D | Nessun recapito RTD nel sito; non verificabile nel repository | Bassa. Stato: Non verificabile. L |
| N-04 | CAD art. 50-ter (Piattaforma Digitale Nazionale Dati, PDND) e Linee guida AgID sull'interoperabilità tecnica | Scambiare dati con altre PA tramite e-service PDND | Nessuna integrazione; il PRD non la richiede | V: grep -i `pdnd` in `Modules/{Fixcity,User,Tenant,Xot}/app`: 0 risultati | Nessuna, finché FixCity non consuma dati di altre PA (per esempio anagrafe per verificare la residenza) | Bassa. Stato: Non pertinente oggi |
| N-05 | CAD art. 53 (caratteristiche dei siti: accessibilità, usabilità, reperibilità, completezza, chiarezza, omogeneità) e art. 54 (contenuti dei siti: contatti, PEC, atti e servizi; commi da verificare) e Linee guida AgID | Il sito istituzionale ha i contenuti e i collegamenti previsti, ed è omogeneo al modello | Il footer replica il modello Design Comuni con i link obbligatori (`config/local/fixcity/database/content/sections/footer.json:136-177`) | V: nel browser **tutti i 10 link del footer finiscono in 404** (V2) perché gli URL sono scritti senza prefisso di lingua (`/privacy`, `/note-legali`, `/accessibilita`, `/tickets/create`: `footer.json:136,155,159,163,167,173,177`). V: contatti segnaposto ("Comune di Nome Comune", "Via Roma 123", "P. IVA 00123456789", `serviziopubblico@comune.it`), voce "Posta Elettronica Certificata" senza indirizzo (`footer.json:120-139`) | Link legali non funzionanti; identità e recapiti dell'ente non configurati | Alta. Stato: Non conforme |
| N-06 | CAD art. 64 (accesso ai servizi in rete con SPID e CIE; commi 2-quater e 3-bis da verificare) e D.L. 76/2020 conv. L. 120/2020, art. 24 (da verificare) | I servizi in rete che richiedono identificazione informatica si accedono con SPID e CIE; la PA non può rilasciare nuove credenziali proprie | L'accesso è con email e password proprie: login `Modules/User/app/Filament/Widgets/Auth/LoginWidget.php:52` (`Auth::attempt`), registrazione con nome, cognome, email, password (V8). Il progetto dichiara di non supportare SPID/CIE (`design-comuni-service-workflow-scope.md:27-31`; DCP-48 §3) | V: DOM di `/it/auth/login` e `/it/auth/register` senza SPID/CIE; grep `spid\|cie\|saml` solo nei tipi generici di `SsoProvider` (`Modules/User/app/Models/SsoProvider.php`), tabelle `sso_providers` e `socialite_users` vuote (DCP-48 §3) | Nessuna identità digitale a norma; il servizio **richiede il login** per creare la segnalazione (`/it/tickets/create` risponde 302 a `/it/auth/login`, V1) | Alta. Diventa bloccante se il Comune lo eroga come canale ufficiale che richiede identificazione. Stato: Non conforme. L |
| N-07 | CAD art. 64-bis (punto di accesso telematico, app IO; commi da verificare) | I servizi sono fruibili anche tramite l'app IO | Nessuna integrazione. Le notifiche di stato vanno per email e database (`Modules/Fixcity/app/Notifications/TicketStatusChangedNotification.php:34-41`) | V: grep -i `app io` in Fixcity, Notify, User e viste attive di Sixteen: 0 risultati; `pagopa` compare solo in blocchi UI dimostrativi del tema (`Themes/Sixteen/resources/views/components/utilities/pagopa-button.blade.php`), non in un flusso FixCity | Servizio non esposto su IO; messaggi di stato non inviabili su IO | Media. Stato: Non conforme. L |
| N-08 | CAD art. 65 (istanze e dichiarazioni presentate per via telematica: validità se l'autore è identificato con SPID/CIE, o firma, o domicilio digitale; commi da verificare) | Un'istanza telematica è valida solo se l'autore è identificato nei modi di legge | Il ticket ha `owner_id` ma l'identità è autodichiarata (email e password) | D: dipende dalla qualificazione giuridica della "segnalazione" (istanza o semplice segnalazione) | Decisione non presa; se è istanza serve identificazione a norma | Media. Stato: Non verificabile. L |
| N-09 | CAD artt. 40-44, D.P.R. 445/2000 (art. 53: registrazione di protocollo) e Linee guida AgID sulla formazione, gestione e conservazione dei documenti informatici (determinazione e data da verificare) | Se il ticket è un documento amministrativo va protocollato, fascicolato e conservato | Il progetto dichiara di non sostituire protocollo e PEC (`fixcity-scopo-e-target.md:50`); nessuna integrazione di protocollo | V: grep -i `protocoll` in Fixcity, User, Tenant, Xot e Sixteen: 4 occorrenze, tutte estranee (protocollo del wizard, indirizzi di esempio, schema degli URL); nessun flusso di protocollo | Nessuna decisione scritta su protocollo e piano di conservazione | Media. Stato: Non verificabile. L |

### B. Procedimento amministrativo, comunicazione pubblica, trasparenza

| ID | Norma e articolo | Che cosa richiede | Come lo rispetta il progetto | Prova | Lacuna | Gravità |
|---|---|---|---|---|---|---|
| N-10 | L. 241/1990: art. 2 (dovere di concludere il procedimento; termine di 30 giorni se non diverso), artt. 4-5 (unità organizzativa e responsabile del procedimento), artt. 7-8 (comunicazione di avvio: amministrazione, oggetto, ufficio e responsabile, termine e rimedi in caso di inerzia, data di presentazione, ufficio dove vedere gli atti), art. 3 (motivazione), art. 10-bis (preavviso di rigetto); potere sostitutivo (art. 2 c. 9-bis, da verificare) | Chi presenta una domanda riceve gli elementi dell'avvio e una conclusione motivata nei tempi | Il ticket ha un responsabile (`responsible_id`, `Ticket.php:184`); le transizioni di stato registrano attore, motivo e orario (`architecture.md:66-69`); il proprietario riceve notifica a ogni cambio di stato | T: `Modules/Fixcity/tests/Feature/Actions/TicketStatusNotificationTest.php:18-37`. V: la conferma (`/it/tickets/confirmation?code=TCK-CLIQUERY`) dice "sarà visibile ... una volta presa in carico" e non indica ufficio, responsabile, termine, rimedi. V: `GetTicketSlaMetricsAction.php` calcola solo tempi medi di risoluzione; nessun termine per singolo ticket | Mancano i contenuti dell'art. 8 e un termine per ticket. Se la segnalazione non è un procedimento ad istanza di parte la L. 241 può non applicarsi: lo decide l'ente | Media. Stato: Parziale. L |
| N-11 | L. 150/2000 (comunicazione e informazione delle PA; URP; articoli da verificare) | Canali di informazione al cittadino, tra cui l'ufficio relazioni con il pubblico | Il footer elenca URP, numero verde, FAQ, assistenza (`footer.json:121-139`); il tracciamento mostra stato e aggiornamenti pubblici | V: contatti URP segnaposto (V2); le pagine `/it/assistenza` e `/it/contatti` danno 404 | Contatti reali e pagine di assistenza assenti | Bassa. Stato: Parziale. L |
| N-12 | D.Lgs. 33/2013: art. 5 (accesso civico), art. 9 (sezione "Amministrazione trasparente"), art. 32 (servizi erogati: carta dei servizi, standard di qualità, tempi medi) e art. 35 (procedimenti); numeri da verificare | Pubblicare dati sui servizi e sui procedimenti nella sezione dedicata del sito istituzionale | Il footer ha la voce "Amministrazione trasparente" (`footer.json:155`); l'SLA medio è calcolato dal back office | V: `/it/amministrazione-trasparente` e `/amministrazione-trasparente` danno 404; il dato `avg_resolution_hours` (`GetTicketSlaMetricsAction.php`) non è pubblicato | La sezione spetta al sito istituzionale del Comune, non a FixCity: va deciso se FixCity ne è parte o se il link punta altrove; il tempo medio potrebbe alimentare l'art. 32 | Bassa. Stato: Non pertinente finché FixCity è un servizio e non il sito. L |

### C. Accessibilità

| ID | Norma e articolo | Che cosa richiede | Come lo rispetta il progetto | Prova | Lacuna | Gravità |
|---|---|---|---|---|---|---|
| N-13 | L. 4/2004 "Stanca" come modificata da D.Lgs. 106/2018 e D.L. 76/2020 art. 29 (numeri di articolo da verificare); Direttiva (UE) 2016/2102 art. 7 (dichiarazione di accessibilità e meccanismo di feedback); Decisione di esecuzione (UE) 2018/1523 (modello di dichiarazione); D.L. 179/2012 art. 9 c. 7 (obiettivi di accessibilità entro il 31 marzo, da verificare); Linee guida AgID sull'accessibilità degli strumenti informatici | La PA pubblica la dichiarazione di accessibilità (con stato di conformità, contenuti non accessibili, data, contatto), offre un modo per segnalare problemi di accessibilità e pubblica ogni anno gli obiettivi | Il tema Sixteen ha come obiettivo WCAG 2.1 AA (`Themes/Sixteen/docs/accessibility.md:15`); il footer prevede la voce "Dichiarazione di accessibilità" (`footer.json:167`) | V: `/accessibilita`, `/it/accessibilita`, `/it/dichiarazione-di-accessibilita`, `/it/accessibility` danno tutti 404 (V1). V: nel testo della home non c'è alcun meccanismo di feedback (grep `feedback`: 0 occorrenze) | **Dichiarazione non pubblicata, feedback assente, obiettivi annuali non verificabili**; la documentazione del tema dichiara l'obiettivo ma non contiene un audit | Bloccante. Stato: Non conforme |
| N-14 | EN 301 549 (norma armonizzata, Decisione di esecuzione (UE) 2018/2048, versione 2.1.2) e WCAG 2.1 livello AA | I siti e le app rispettano i requisiti di accessibilità della norma (che incorpora WCAG 2.1 AA) | NFR-003: flussi critici WCAG 2.1 AA, tastiera, focus, errori semantici, movimento ridotto (`prd.md:94-95`). Strumento `@axe-core/playwright` tra le dipendenze del tema (`Themes/Sixteen/package.json:36`) | V: axe-core 4.13.0, tag wcag2a, wcag2aa, wcag21a, wcag21aa, su 8 pagine: 0 violazioni nel DOM del prodotto; l'unica violazione (contrasto) è nella barra Debugbar di sviluppo. V: `lang` corretto, collegamento "salta al contenuto", landmark `main`, 0 immagini senza `alt` (V11). T: `Themes/Sixteen/tests/Unit/HeaderGuestCtaAccessibleNameTest.php` | Un controllo automatico copre una parte dei criteri; non risultano prove su tastiera, lettore di schermo, zoom 200 percento, wizard autenticato. Aperti ACC-01..ACC-05 di DCP-49 (media/bassa) | Alta. Stato: Parziale (non dimostrato) |
| N-15 | Direttiva (UE) 2019/882 (European Accessibility Act), recepita dal D.Lgs. 82/2022 (da verificare) | Requisiti di accessibilità per prodotti e servizi di operatori economici privati dal 28 giugno 2025 | I siti degli enti pubblici ricadono nella Direttiva 2016/2102 (riga N-13), non nella 2019/882 | D | Nessuna, salvo che il fornitore commerciale di FixCity offra servizi nelle categorie dell'EAA (commercio elettronico, servizi bancari, trasporto...): non è il caso di una piattaforma di segnalazioni | Bassa. Stato: Non pertinente direttamente. L |
| N-16 | Linee guida AgID di design per i siti e i servizi digitali della PA (modello Comuni / Design Comuni; estremi da verificare) | Aspetto, struttura e componenti omogenei; font e asset del kit | Parity con le pagine statiche di Design Comuni, footer e header del modello | V: l'interfaccia usa Bootstrap Italia e i blocchi del modello. V: il font Titillium Web è caricato da `fonts.googleapis.com` (`Themes/Sixteen/resources/css/app.css:9`) benché il kit abbia i file locali (`Themes/Sixteen/public/design-comuni/assets/fonts/Titillium_Web/`) | Dipendenza esterna evitabile (vedi N-23) | Media (privacy). Stato: Parziale |

### D. Protezione dei dati personali (GDPR e Codice privacy)

Glossario per il lettore non giurista: il **titolare** è chi decide perché e come si trattano i dati (qui il Comune, non FixCity, che è il software); il **responsabile** è chi tratta dati per conto del titolare (hosting, posta); l'**RPD/DPO** è la figura di controllo che ogni ente pubblico deve nominare; la **base giuridica** è la ragione che rende lecito il trattamento (per un ente pubblico, di norma, il compito di interesse pubblico, art. 6.1.e, e non il consenso).

| ID | Norma e articolo | Che cosa richiede | Come lo rispetta il progetto | Prova | Lacuna | Gravità |
|---|---|---|---|---|---|---|
| N-17 | GDPR (Reg. UE 2016/679) artt. 12-14 (informativa), art. 37 par. 7 (contatti RPD pubblicati), art. 77 (reclamo al Garante); D.Lgs. 196/2003 come modificato dal D.Lgs. 101/2018 | Prima di raccogliere i dati, dire in modo chiaro: chi è il titolare e come contattarlo, i contatti dell'RPD, finalità e base giuridica, destinatari, eventuali trasferimenti extra UE, durata, diritti, diritto di reclamo, se il conferimento è obbligatorio | Passo 1 del wizard con testo da `policy.md` del tenant, reso come markdown sanitizzato (`Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php:202-210`; `Modules/Fixcity/resources/views/components/gdpr-notice.blade.php:6-8`). Se il testo è un segnaposto il passo è disabilitato e `save()` rifiuta (fail-closed, `CreateTicketWizardWidget.php:69-70`) | V: testi in `config/local/fixcity/lang/{it,en,de,es}/policy.md` di 713, 625, 713, 692 byte (V7). Contengono finalità generiche e categorie di dati; **mancano** titolare, RPD, base giuridica, destinatari e responsabili, trasferimenti, durata precisa, elenco dei diritti, reclamo al Garante, natura del conferimento, cookie. V: `/it/privacy` risponde 404 e così il link del wizard (`TicketForm.php:205`, `gdpr-notice.blade.php:8,12`) e del footer (`footer.json:159`). V: il footer non ha titolare né RPD (0 occorrenze di "Titolare", "DPO", "RPD", "protezione dei dati" nel testo della home, V2). T: `Modules/Fixcity/tests/Feature/Pages/TicketPrivacyPublicationTest.php:15-50` si aspetta che `/it/privacy` risponda 503 o 200, ma l'app di sviluppo risponde 404: o il test è rosso o dipende da una condizione dell'ambiente di test (non rieseguito, da riesaminare) | **Informativa incompleta e pagina dedicata inesistente.** Il testo parla di "FixCity" come se fosse il titolare | Bloccante. Stato: Non conforme |
| N-18 | GDPR art. 6 par. 1 e 3 (basi giuridiche; per i compiti di interesse pubblico serve una norma di legge o regolamento), art. 7 (condizioni del consenso: dimostrabile, distinguibile, revocabile), Considerando 43 (il consenso difficilmente è libero quando il titolare è un'autorità pubblica); D.Lgs. 196/2003 art. 2-ter (base giuridica per compiti di interesse pubblico; testo vigente da verificare) | Individuare la base giuridica corretta e non chiedere un "consenso" dove la base è un'altra | La casella del passo 1 ha etichetta "Ho letto e compreso l'informativa sulla privacy" (presa visione: `Modules/Fixcity/lang/it/ticket.php:580`). In registrazione ci sono tre caselle: privacy, termini, marketing facoltativo. Il PRD e il piano di sicurezza rimandano la scelta al titolare (`prd.md:102-103`; `security-plan.md:31-35`) | V: il testo di `policy.md` conclude "Proseguendo, dichiari di aver letto questa informativa **e acconsenti** al trattamento dei dati" (V7): confonde presa visione e consenso e contraddice l'etichetta della casella. V: nella registrazione il testo "Ho letto e accetto l'Informativa Privacy" non contiene alcun link (V8) | Semantica non decisa e testo ambiguo. Se la base è il compito di interesse pubblico il "consenso" è improprio e la casella dovrebbe essere una presa visione | Alta. Stato: Non conforme. L |
| N-19 | GDPR art. 5 par. 1 lett. a-c (liceità, correttezza, trasparenza, minimizzazione) e par. 2 (responsabilizzazione), art. 25 (privacy by design e by default) | Raccogliere e mostrare solo i dati necessari; impostazioni predefinite protettive | Il payload pubblico è una allowlist: `id`, `title`, `description`, `images`, `status`, `slug`, e il `code` solo al proprietario (`Modules/Fixcity/app/Actions/BuildTicketPublicDetailsPayloadAction.php:48-58`). L'email è nascosta agli ospiti nella conferma (DCP-49 "Verificato senza finding"; stessa evidenza dal mio GET della conferma da ospite). Il codice fiscale non è raccolto (DCP-48, decisione aperta) | V: GET da ospite di `/it/tickets/confirmation?code=TCK-CLIQUERY`: nessun indirizzo email nel testo. V: `/api/tickets/geojson` risponde 200 con 0 feature (i 4 ticket di sviluppo non sono pubblici). Aperti in DCP-49: SEC-05 (EXIF con GPS nelle foto, ipotesi), SEC-08 (la pagina di tracciamento mostra titolo e testo anche di ticket non pubblicati a chi ha il codice). V: la registrazione offre una casella "Voglio ricevere aggiornamenti e inviti (facoltativo)" (`Modules/Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php:36`) mentre il PRD esclude le notifiche di marketing (`prd.md:128`); IP e user agent vengono salvati con il consenso (`Modules/Gdpr/app/Actions/SaveGdprConsentsAction.php:44-58`) e l'IP è scritto nel log di debug (`:59-63`) | Casella di marketing fuori scopo; metadati delle foto; testo libero pubblico senza moderazione dichiarata | Media. Stato: Parziale |
| N-20 | GDPR artt. 15-22 (accesso, rettifica, cancellazione con i limiti dell'art. 17 par. 3, limitazione, portabilità solo per consenso o contratto, opposizione) | L'interessato può esercitare i diritti e ricevere risposta | Esiste `DeleteAccountWidget` nel pannello utente (`Modules/User/app/Filament/Widgets/Profile/DeleteAccountWidget.php`) che chiama `DeleteUserAction` | V: `DeleteUserAction` esegue solo `$user->delete()` dopo il controllo della password (`Modules/User/app/Actions/User/DeleteUserAction.php:29-38`); `tickets.owner_id` non ha chiave esterna (`Modules/Fixcity/database/migrations/2026_06_10_140000_create_tickets_table.php:24`), quindi i ticket con testo, posizione e foto dell'utente restano orfani. T: un solo test, sulla password errata (`Modules/User/tests/Unit/Actions/User/DeleteUserActionTest.php:12`). V: nessuna funzione di esportazione dei dati personali nel codice (grep `PersonalDataExport`, `SelectsPersonalData`: 0), benché esista `config/personal-data-export.php`. Nessuna vista del front office incorpora il widget (grep di `DeleteAccount` in `Themes/Sixteen/resources`, `Modules/Fixcity/resources`, `Modules/User/resources`: 0 risultati); `/it/profile` e `/it/area-personale` rispondono 404 da ospite | Nessun flusso per l'esercizio dei diritti; cancellazione che lascia i dati nei ticket; nessun canale dichiarato | Alta. Stato: Non conforme |
| N-21 | GDPR art. 5 par. 1 lett. e (limitazione della conservazione) e art. 13 par. 2 lett. a (indicare la durata) | Fissare per quanto tempo si conservano i dati e cancellarli alla scadenza | Il modulo Gdpr ha una configurazione di conservazione (`Modules/Gdpr/config/config.php:58-75`) | V: quella configurazione è copiata da un altro progetto (contiene `insurance_data`, polizze, sinistri, `insurance_consents` con dati sanitari: `config.php:64-110`) e **nessun codice la legge** (grep `retention`, `processing_register` in `Modules/*/app`: solo il modulo Job con un'altra chiave). Nessun job di eliminazione per ticket, media, attività | Nessuna politica di conservazione; il piano di sicurezza la rimanda a Media/Gdpr senza implementarla (`security-plan.md:24-25`) | Alta. Stato: Non conforme. L (tempi decisi dall'ente, massimario di scarto) |
| N-22 | GDPR art. 7 par. 1 e art. 5 par. 2 (dimostrare chi ha preso visione o consentito, quando e a quale versione del testo) | Conservare una prova del consenso o della presa visione | In registrazione, `SaveGdprConsentsAction` scrive una riga `Consent` per privacy, termini e marketing con `accepted_at`, IP e user agent. Nel wizard, `privacyAccepted` viene validato (`accepted()`) ma non salvato | V: `SaveGdprConsentsAction.php:41-43` salta in silenzio (`if ($treatment)`) se il `Treatment` non esiste. V (sola lettura, `tinker`): `{"db":"mariadb","treatments":[],"consents":0,"tickets":4,"users":9}`: tabella dei trattamenti vuota e nessun consenso registrato per i 9 utenti (V9). V: nel wizard nessuna scrittura della presa visione (`grep privacy` in `CreateTicketAction.php`: 0; `CreateTicketWizardWidget.php:69-70` solo valida). È il finding SEC-12 di DCP-49, ancora aperto. T: `Modules/Gdpr/tests/Unit/Actions/SaveGdprConsentsActionTest.php` esiste (non rieseguito) | Prova assente nel wizard; in registrazione la prova dipende da dati iniziali (seed) che nel database di sviluppo mancano; nessuna versione del testo salvata | Media (Alta se il titolare sceglie il consenso). Stato: Non conforme |
| N-23 | GDPR art. 13 par. 1 lett. e-f (destinatari e trasferimenti), art. 28 (responsabili), artt. 44-49 (trasferimenti verso Paesi terzi), art. 5 par. 1 lett. a-c | Sapere a chi vanno i dati, regolare i fornitori con contratto, evitare invii non necessari a terzi | Leaflet e asset sono serviti dal dominio proprio. La geocodifica degli indirizzi passa dal server (`Modules/Geo/app/Filament/Forms/Components/Traits/HasCoordinatePicker.php:222-231`, `:274-285`) | V (Playwright, V4): ogni pagina pubblica contatta `fonts.googleapis.com` (CSS del font, `Themes/Sixteen/resources/css/app.css:9`) e le pagine con mappa (`/it`, `/en`, `/it/tickets`) contattano `a/b/c.tile.openstreetmap.org` (tessere) e `fonts.gstatic.com`: l'IP e l'user agent del cittadino arrivano a Google e alla Fondazione OpenStreetMap. V: il server invia a `nominatim.openstreetmap.org` il testo digitato nel campo indirizzo e le coordinate (`HasCoordinatePicker.php:226`, `:277`; senza limitazione di frequenza, SEC-07 di DCP-49). Esiste anche una chiamata diretta dal browser in `Modules/Fixcity/resources/js/components/map-picker-lit.js:285` (uso non osservato). V: il font esiste già in locale (`Themes/Sixteen/public/design-comuni/assets/fonts/Titillium_Web/`). Nessuno di questi fornitori è nominato nell'informativa (V7) | Terze parti non dichiarate e in parte evitabili: il font si risolve con una riga di CSS. Per mappa e geocodifica serve una decisione (tessere ospitate o fornitore con accordo; informativa; politica d'uso OSM per la produzione). Contesto esterno da verificare con il legale: sentenza del Tribunale di Monaco del 20 gennaio 2022 (3 O 17493/20) sul trasferimento dell'IP a Google tramite Google Fonts | Alta. Stato: Non conforme. L |
| N-24 | GDPR art. 30 (registro dei trattamenti), art. 35 (valutazione d'impatto, solo se il rischio è elevato), artt. 33-34 (violazioni: notifica al Garante entro 72 ore), art. 37 (RPD obbligatorio per gli enti pubblici); D.Lgs. 196/2003 art. 2-quaterdecies (designati) | L'ente tiene il registro, valuta la DPIA, ha una procedura per le violazioni e un RPD | Il modulo Gdpr ha i modelli `Treatment`, `Consent`, `Event`, `Profile` e le risorse Filament per gestirli | V: la tabella `treatments` è vuota nel database di sviluppo (V9); nessuna procedura di violazione nel codice (grep `breach`, `violazione dei dati` in Fixcity, Gdpr, User, Activity: 0). DPIA: dalla lettura la geolocalizzazione riguarda il luogo del disservizio e non il monitoraggio della persona, quindi un obbligo non è evidente, ma la valutazione spetta all'RPD | Documenti organizzativi assenti dal repository | Media. Stato: Non verificabile. L |

### E. Cookie e strumenti di tracciamento

| ID | Norma e articolo | Che cosa richiede | Come lo rispetta il progetto | Prova | Lacuna | Gravità |
|---|---|---|---|---|---|---|
| N-25 | D.Lgs. 196/2003 art. 122; Direttiva 2002/58/CE art. 5 par. 3; Linee guida cookie e altri strumenti di tracciamento del Garante (provv. 10 giugno 2021, numero da verificare) | Cookie tecnici: nessun consenso ma informativa. Analitici di terzi, profilazione, altri tracciatori: consenso preventivo con banner | Il banner è spento per scelta di configurazione: `cookie_banner_on => false` (`Modules/Fixcity/config/gdpr.php:6`, copia in `config/local/fixcity/gdpr.php:6`; letto da `Modules/Gdpr/app/Providers/GdprServiceProvider.php:40`). Il foglio di stile del banner è comunque caricato (`vendor/cookie-consent/css/cookie-consent.css`) | V (Playwright, V3): alla prima visita i cookie sono `fixcity_session` (HttpOnly, SameSite Lax, 120 minuti), `XSRF-TOKEN` (SameSite Lax, 120 minuti) e `PHPDEBUGBAR_STACK_DATA` (solo sviluppo); nessun cookie di terze parti e nessun `__cookie_consent`. V: nessuno script di analisi o marketing nel DOM della home (grep `gtag`, `analytics`, `matomo`, `google`: 0; le 4 occorrenze di `facebook` sono il link social del footer). Quindi nessun consenso è richiesto da questi cookie | Manca l'informativa sui cookie tecnici (la privacy page non esiste). Il giorno in cui si aggiunge un contatore o un widget esterno il banner va riattivato: nessun test lo impedisce. I font Google e le tessere OSM non impostano cookie osservati, ma sono tracciatori di fatto dell'IP (riga N-23) | Media. Stato: Parziale |

### F. Identità elettronica e sportello unico europeo

| ID | Norma e articolo | Che cosa richiede | Come lo rispetta il progetto | Prova | Lacuna | Gravità |
|---|---|---|---|---|---|---|
| N-26 | Reg. (UE) 910/2014 eIDAS (mutuo riconoscimento delle identità notificate, artt. 6-9 da verificare) come modificato dal Reg. (UE) 2024/1183 (portafoglio europeo di identità digitale, EUDI Wallet; obblighi di accettazione per il settore pubblico e scadenze da verificare) | Se un servizio pubblico richiede identificazione elettronica, accettare le identità notificate di altri Stati membri e, a tendere, l'EUDI Wallet | Nessuna identità elettronica oltre email e password | V: come N-06 | Nessuna accettazione di SPID/CIE né di identità di altri Stati; il portafoglio europeo è un orizzonte 2026-2027 da monitorare | Bassa (cresce con N-06). Stato: Non conforme. L |
| N-27 | Reg. (UE) 2018/1724 Single Digital Gateway (art. 4 informazioni; art. 6 e Allegato II procedure online; art. 25 strumento di feedback; articoli da verificare) | Le procedure dell'Allegato II (per esempio residenza, certificati) sono online e accessibili agli utenti UE | FixCity gestisce segnalazioni di disservizi, che non sono tra le procedure dell'Allegato II | D | Nessuna, salvo estensioni future a procedure dell'Allegato II | Bassa. Stato: Non pertinente. L |

### G. Sicurezza informatica

| ID | Norma e articolo | Che cosa richiede | Come lo rispetta il progetto | Prova | Lacuna | Gravità |
|---|---|---|---|---|---|---|
| N-28 | GDPR art. 32 (misure tecniche e organizzative adeguate) e art. 25 | Sicurezza proporzionata al rischio: autenticazione, controllo accessi, limitazione dei tentativi, intestazioni di sicurezza, cifratura | Piano di sicurezza (`bmad-output/security-plan.md:18-29`); correzioni di DCP-49 con test: SEC-01 (stato e priorità imposti dal server) e SEC-10 (proprietà `#[Locked]`), `Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardTamperingTest.php`; password di almeno 12 caratteri con maiuscole, numeri e simboli (testo di `/it/auth/register`; `Modules/User/app/Datas/PasswordData.php:59-71`); sessione cifrata (`config/session.php:55`), HttpOnly (`:191`), SameSite lax (`:208`), `Secure` in produzione (`:178`) | V: **nessuna limitazione dei tentativi di login**: `LoginWidget::login()` chiama `Auth::attempt` senza `RateLimiter` (`Modules/User/app/Filament/Widgets/Auth/LoginWidget.php:36-62`) e la sezione `rate_limit` di `config/security.php` non è letta da nessun codice (grep `security.rate_limit`: 0). V: 30 GET consecutive su `/it/tickets/track?code=...` rispondono tutte 200, nessuna intestazione `X-RateLimit` (V12). V: **nessuna intestazione di sicurezza**: nella risposta di `/it` ci sono solo `cache-control, connection, content-type, date, expires, host, pragma, x-powered-by, phpdebugbar-id`: niente CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy (V5). V: ambiente di sviluppo con `APP_DEBUG=true` e `DEBUGBAR_ENABLED=true`; `/_debugbar/open?op=get&id=...` risponde 200 (V6). Il proxy TLS non rispondeva: HSTS non verificabile. DCP-49: SEC-01 e SEC-10 corrette dal lead; gli altri finding (SEC-02, 03, 05, 07, 08, 09, 11, 12, 13, 14) risultano aperti o in corso, alcuni di gravità media | Login senza freno ai tentativi; intestazioni di sicurezza assenti (in produzione dipendono anche da vhost e proxy); debug da spegnere nel rilascio | Alta (login); Media (intestazioni, debug). Stato: Parziale |
| N-29 | Misure minime di sicurezza ICT per le PA (AgID, Circolare 18 aprile 2017, n. 2/2017); criteri di qualificazione cloud per la PA (circolari AgID 2/2018 e 3/2018 e regolamento ACN successivo, da verificare) | Inventari, configurazioni sicure, gestione vulnerabilità, privilegi amministrativi, copie di sicurezza; infrastruttura cloud qualificata | Piano DevOps: backup cifrato giornaliero con ripristino provato, RPO/RTO da approvare (`bmad-output/devops-plan.md:28-32`), monitoraggio e runbook | D: sono misure dell'ente e dell'hosting, non del codice | Non verificabile dal repository: dove gira l'applicazione, chi la gestisce, quale livello ABSC si dichiara | Media. Stato: Non verificabile. L |
| N-30 | Direttiva (UE) 2022/2555 NIS2 e D.Lgs. 4 settembre 2024, n. 138 (applicabilità ai Comuni secondo soglie di popolazione, da verificare) | Gestione del rischio informatico e notifica degli incidenti, se l'ente rientra nel perimetro | Nessuna funzione specifica | D: l'applicabilità dipende dalla dimensione del Comune, non determinabile | Da verificare quando il Comune destinatario sarà noto | Bassa. Stato: Non verificabile. L |

### H. Lingua

| ID | Norma e articolo | Che cosa richiede | Come lo rispetta il progetto | Prova | Lacuna | Gravità |
|---|---|---|---|---|---|---|
| N-31 | L. 482/1999 (tutela delle minoranze linguistiche storiche; uso della lingua negli uffici pubblici, art. 9 da verificare) e CAD art. 3 (fermi i diritti delle minoranze); regimi speciali per Alto Adige e Valle d'Aosta | Nei Comuni delle aree di minoranza, uso della lingua minoritaria con la PA | Il servizio è in italiano, inglese, tedesco e spagnolo (`LaravelLocalization::getSupportedLocales()` letto con `tinker`: `["de","en","es","it"]`); nessun obbligo generale oltre l'italiano | V: lingue supportate come sopra; `<html lang>` corretto su `/it` e `/en` | Pertinente solo se il Comune destinatario è in un'area di minoranza (francese, ladino, friulano, sloveno, sardo e altre non sono supportate) | Bassa. Stato: Non pertinente finché il Comune non è noto. L |

### Non esaminato in questo studio

Minori (art. 8 GDPR, art. 2-quinquies del Codice privacy), riuso dei dati pubblici e open data (D.Lgs. 36/2006 e successive modifiche), pagamenti pagoPA (FixCity non incassa), firma e valore probatorio delle ricevute, contratti con i responsabili (art. 28) e licenze/attribuzione OpenStreetMap, contenuti dei documenti allegati, applicazione mobile.

## Verifiche sul campo con output

Tutti i comandi sono stati eseguiti il 2026-10-13 in sola lettura. Gli script Playwright (Chromium) stanno nella cartella temporanea della sessione e usano il Playwright già installato in `Themes/Sixteen/node_modules`; nessun POST, nessuna scrittura sul database, nessuna build.

### V1. Stato HTTP delle pagine legali e di servizio

```
$ curl -s -o /dev/null -w "%{http_code} %{redirect_url}\n" http://192.168.1.40:8000<percorso>
/it 200                              /en 200
/it/privacy 404                      /en/privacy 404                 /privacy 404
/it/informativa-privacy 404          /it/privacy-policy 404          /it/cookie 404          /it/cookie-policy 404
/it/note-legali 404                  /it/legal 404
/it/accessibilita 404                /it/accessibility 404           /it/dichiarazione-di-accessibilita 404
/it/contatti 404                     /it/assistenza 404              /it/mappa-del-sito 404
/it/amministrazione-trasparente 404  /it/login 404                   /it/profile 404         /it/area-personale 404
/it/tickets/create 302 -> /it/auth/login
/it/tickets 200   /it/tickets/track 200   /it/domande-frequenti 200   /it/auth/login 200   /it/auth/register 200
/api/tickets/geojson 200 (application/json, 0 feature)
/robots.txt 200      /sitemap.xml 404      /.well-known/security.txt 404
```

Lettura: **nessuna** pagina con informativa privacy, cookie, note legali o dichiarazione di accessibilità risponde 200 tra i nomi provati. Il login vive in `/it/auth/login`, non in `/it/login`.

### V2. Link del footer cliccati in un browser reale

Per ogni voce del footer ho cliccato il collegamento in Chromium e registrato l'URL finale e lo stato del documento:

```
Informativa privacy            href=/privacy                       -> /privacy                     404
Note legali                    href=/note-legali                   -> /note-legali                 404
Dichiarazione di accessibilità href=/accessibilita                 -> /accessibilita               404
Segnalazione disservizio       href=/tickets/create                -> /tickets/create              404
Amministrazione trasparente    href=/amministrazione-trasparente   -> /amministrazione-trasparente 404
Media policy                   href=/media-policy                  -> /media-policy                404
Mappa del sito                 href=/mappa-del-sito                -> /mappa-del-sito              404
Richiesta d'assistenza         href=/assistenza                    -> /assistenza                  404
Leggi le FAQ                   href=/domande-frequenti             -> /domande-frequenti           404
Prenotazione appuntamento      href=/prenota-appuntamento          -> /prenota-appuntamento        404
```

Causa: gli URL del footer sono scritti senza prefisso di lingua in `config/local/fixcity/database/content/sections/footer.json` (righe 136, 155, 159, 163, 167, 173, 177) e il sito risponde solo sotto `/it/...` o `/en/...`. Anche i collegamenti che esisterebbero (`/it/domande-frequenti` risponde 200, `/it/tickets/create` porta al login) sono irraggiungibili dal footer.

Testo del footer (DOM della home, dopo il blocco "Contatti"): `Comune di Nome Comune`, `Via Roma 123 - 00100 Comune`, `Codice fiscale / P. IVA: 00123456789`, `Ufficio Relazioni con il Pubblico`, `Numero verde: 800 016 123`, `Posta Elettronica Certificata` (senza indirizzo), `Centralino unico: 012 3456`. Ricerca nel testo della pagina: `Titolare` 0, `DPO` 0, `RPD` 0, `Responsabile` 0, `protezione dei dati` 0, `cookie` 0, `feedback` 0, `accessibil` 1 (la sola voce del footer).

### V3. Cookie e storage alla prima visita (contesto del browser pulito)

```
cookie                   HttpOnly  Secure  SameSite  durata
fixcity_session          sì        no      Lax       120 minuti
XSRF-TOKEN               no        no      Lax       120 minuti
PHPDEBUGBAR_STACK_DATA   sì        no      Lax       sessione   (solo Debugbar di sviluppo)
localStorage: phpdebugbar-height, phpdebugbar-visible          (solo Debugbar di sviluppo)
sessionStorage: vuoto
Banner di consenso nel DOM: nessuno. L'unico role=dialog è la modale di ricerca (display: none).
```

Gli stessi tre cookie compaiono su `/it`, `/it/tickets`, `/it/tickets/track`, `/it/auth/login`, `/it/auth/register`, `/it/domande-frequenti`, `/it/tests`. `Secure=no` è atteso in sviluppo: `config/session.php:178` lo imposta a `true` quando `APP_ENV=production` (non osservato).

### V4. Host contattati da ogni pagina (Playwright, `page.on('request')`)

```
pagina                    fonts.googleapis.com  fonts.gstatic.com  *.tile.openstreetmap.org
/it                       1                     1                   18 (a, b, c: 6 ciascuno)
/en                       1                     1                   18
/it/tickets               1                     1                   16
/it/tickets/track         1                     0                   0
/it/auth/login            1                     0                   0
/it/auth/register         1                     0                   0
/it/domande-frequenti     1                     0                   0
/it/tests                 1                     0                   0
(tutto il resto va a 192.168.1.40:8000)
```

Nessun altro host: Leaflet, Livewire, Bootstrap Italia e le icone sono serviti in locale. La richiesta di font è `https://fonts.googleapis.com/css2?family=Titillium+Web:...` e proviene da `Themes/Sixteen/resources/css/app.css:9`. Nominatim non compare perché la geocodifica parte solo dal wizard autenticato (non osservato; vedi il codice in N-23).

### V5. Intestazioni di sicurezza della risposta

```
$ Response headers di GET /it (Playwright)
cache-control, connection, content-type, date, expires, host, pragma, x-powered-by, phpdebugbar-id
Content-Security-Policy: assente    Strict-Transport-Security: assente   X-Frame-Options: assente
X-Content-Type-Options: assente     Referrer-Policy: assente             Permissions-Policy: assente
```

Il proxy TLS di sviluppo (`http://192.168.1.40:8080` e `https://192.168.1.40:8080`) non ha risposto entro 6 secondi: HSTS e certificato non sono verificabili. `X-Powered-By: PHP/8.4.26` rivela il runtime.

### V6. Ambiente e strumenti di debug

```
.env: APP_ENV=local  APP_DEBUG=true  DEBUGBAR_ENABLED=true  APP_URL=http://fixcity.local
      SESSION_DRIVER=file  SESSION_LIFETIME=120  QUEUE_CONNECTION=sync  MAIL_MAILER=sendmail
GET /_debugbar/open?op=get&id=<id della risposta>  ->  200
```

Corretto per lo sviluppo; è un punto di controllo obbligatorio del rilascio (N-28).

### V7. Testo dell'informativa e confronto con l'art. 13 GDPR

Il testo italiano (`config/local/fixcity/lang/it/policy.md`, 713 byte) dice in sostanza: FixCity tratta i dati inviati con la segnalazione per riceverla, valutarla e comunicarne gli aggiornamenti; sono trattati dati di contatto, posizione, descrizione e allegati; i dati sono accessibili agli uffici competenti e conservati per il tempo necessario e per gli obblighi di legge; per i diritti si contatta l'ufficio responsabile con i recapiti del portale; conclude "Proseguendo, dichiari di aver letto questa informativa e acconsenti al trattamento". Il file `policy.md` alla radice del tenant è il segnaposto "Edit this file ..." (84 byte), così come `terms.md` (88 byte) e `lang/it/terms.md`. `GetPublishedPrivacyPolicyAction` restituisce il testo di `lang/<lingua>/policy.md` in tutte e quattro le lingue (`tinker`: it 713, en 625, de 713, es 692 byte).

| Elemento richiesto dall'art. 13 | Presente nel testo |
|---|---|
| Identità e contatti del titolare (par. 1 lett. a) | No. Cita "FixCity", che è il software |
| Contatti dell'RPD (par. 1 lett. b) | No |
| Finalità e base giuridica (par. 1 lett. c) | Finalità sì, generica. Base giuridica no (il testo parla di consenso) |
| Destinatari o categorie (par. 1 lett. e) | Solo "uffici competenti" |
| Trasferimenti verso Paesi terzi (par. 1 lett. f) | No (Google Fonts, OpenStreetMap non citati) |
| Periodo di conservazione o criteri (par. 2 lett. a) | Solo "tempo necessario" e "obblighi di legge" |
| Diritti dell'interessato (par. 2 lett. b-c) | Rinvio generico, nessun elenco |
| Diritto di reclamo all'autorità (par. 2 lett. d) | No |
| Obbligatorietà del conferimento (par. 2 lett. e) | No |

### V8. Registrazione (`/it/auth/register`)

Campi: nome, cognome, email, password, conferma password (testo guida: "Minimo 12 caratteri, maiuscola, minuscola, numero e simbolo"). Tre caselle: "Ho letto e accetto l'Informativa Privacy", "Ho letto e accetto i Termini e Condizioni", "Voglio ricevere aggiornamenti e inviti (facoltativo)". L'unico collegamento `<a>` nel contenuto è "Accedi". Quindi né l'informativa né i termini sono collegati, e i termini sono comunque un segnaposto (V7). Il modulo Gdpr salva una riga `Consent` per ciascuna casella, con IP e user agent, solo se esiste il `Treatment` corrispondente (`SaveGdprConsentsAction.php:41-44`).

### V9. Consensi e trattamenti nel database (sola lettura, `artisan tinker`)

```
{"db":"mariadb","treatments":[],"consents":0,"tickets":4,"users":9}
```

### V10. La valutazione nella pagina di conferma non invia nulla

Script Playwright su `/it/tickets/confirmation?code=TCK-CLIQUERY` con `page.route` che abortisce ogni richiesta non GET (garanzia di non scrivere). Clic su "Valuta 5 stelle su 5", poi su "Avanti", con registrazione delle richieste non statiche:

```
{"visibleSteps":["1"],"thanks":{"fs":0},"blocked":0,"requestsAfterClick":[]}
```

Nessuna richiesta di rete, nessuna richiesta non GET bloccata, nessun ascoltatore di `feedback-submit` (`fs` = 0). Il codice lo conferma (`Themes/Sixteen/resources/js/theme/design-comuni-home.js:64-108`).

### V11. Accessibilità automatica (axe-core 4.13.0) e basi del DOM

axe-core iniettato con Playwright, tag `wcag2a`, `wcag2aa`, `wcag21a`, `wcag21aa`:

```
/it  /it/tickets  /it/tickets/track  /it/tickets/track?code=TCK-CLIQUERY
/it/tickets/confirmation?code=TCK-CLIQUERY  /it/auth/login  /it/auth/register  /it/domande-frequenti
-> violazioni nel DOM del prodotto: 0 su tutte e 8.
   Unica regola violata (color-contrast, serious): elementi `.phpdebugbar-tab` della barra di debug di sviluppo.
Controlli di base su ogni pagina: <html lang> = it o en; collegamento "salta al contenuto" presente (a[href="#main-container"]);
landmark <main>; immagini senza alt = 0.
```

Limite: un controllo automatico individua solo una parte dei criteri WCAG. Non sono stati provati tastiera, lettore di schermo, zoom, wizard autenticato.

### V12. Freno ai tentativi

```
$ 30 GET di /it/tickets/track?code=TCK-ZZZZZZZZZZZZ0001 ... 0030
200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200 200
(nessuna intestazione RateLimit / Retry-After)
```

Il login non è stato sondato con tentativi reali (regola di sola lettura: un tentativo fallito può lasciare tracce in log e database): la lacuna è letta nel codice (`LoginWidget.php:36-62`, nessun `RateLimiter`; `config/security.php` ha `rate_limit.max_attempts = 5` ma nessuna riga di codice la usa).

### V13. SPID, CIE, identità

`grep -rIiE 'spid|cie|saml'` su Modules User, Fixcity, Tenant, Xot e viste del tema: i riscontri sono i tipi generici `saml/oidc/oauth` di `SsoProvider`, pacchetti `socialiteproviders/auth0` e `microsoft`, e testi di FAQ ("Come posso richiedere SPID"). Nessun flusso di login SPID o CIE; la pagina di login ha solo email, password e "Ricordami".

### V14. Modulo Gdpr: che cosa fa davvero

Fa: modelli `Consent`, `Treatment`, `Event`, `Profile`; widget di registrazione con tre consensi; risorse Filament di consultazione; middleware del banner cookie (spento dal tenant). **Non fa**: esportazione dei dati, cancellazione guidata, retention, registro dei trattamenti compilato, procedura per violazioni. La sua configurazione (`Modules/Gdpr/config/config.php`) descrive un'assicurazione (polizze, sinistri, consensi sanitari, 10 anni di conservazione) e non è letta da nessun codice. Le rotte del modulo (`routes/web.php`, `routes/api.php`) sono vuote. Test presenti: `tests/Unit/Actions/` (6 file, tra cui `SaveGdprConsentsActionTest.php`) e `tests/Feature/` (registrazione); nessun test di esportazione o cancellazione.

## Lacune per gravità

### Che cosa regge (fatti stretti, con prova)

| Fatto | Prova |
|---|---|
| Alla prima visita il sito imposta solo cookie tecnici di sessione e CSRF; nessun cookie di terze parti, nessuno script di analisi o marketing: il banner di consenso non è richiesto da questi strumenti | V3, V4 (N-25) |
| Il payload pubblico di un ticket è una allowlist e non contiene i dati del proprietario; l'email non compare a un ospite nella conferma | `BuildTicketPublicDetailsPayloadAction.php:48-58`; GET della conferma da ospite (N-19) |
| Se l'informativa è un segnaposto, il passo privacy è disabilitato e il salvataggio rifiuta (comportamento fail-closed); il markdown dell'informativa è sanitizzato | `CreateTicketWizardWidget.php:69-70`; `TicketForm.php:202-210`; `TicketPrivacyPublicationTest.php` (T) (N-17) |
| Stato e priorità del ticket non sono più decisi dal client; le proprietà che governano il reindirizzamento sono bloccate | DCP-49, `CreateTicketWizardTamperingTest.php` (T) (N-28) |
| Politica password di 12 caratteri; sessione cifrata, HttpOnly, SameSite Lax | `PasswordData.php:59-71`; `config/session.php:55,191,208`; V3 (N-28) |
| Pagine pubbliche: `lang` corretto, collegamento "salta al contenuto", landmark `main`, immagini con `alt`, 0 violazioni axe nel DOM del prodotto su 8 pagine | V11 (N-14), con il limite dei controlli automatici |
| Notifiche di cambio stato al proprietario, in coda dopo il commit, con test | `TicketStatusChangedNotification.php`, `TicketStatusNotificationTest.php:18-37` (N-10) |
| Valutazione di soddisfazione persistente, riservata al proprietario di un ticket risolto, con 6 test | `TicketCitizenRatingPromptWidgetTest.php:18-103` (N-02) |

Nessuna di queste righe dice che il servizio è "conforme": sono i soli punti in cui la prova c'è.

### Elenco ordinato

| ID | Gravità | Lacuna | Norme | Prova | Story |
|---|---|---|---|---|---|
| G-01 | **Bloccante** | Informativa privacy incompleta rispetto all'art. 13 e pagina dedicata inesistente (404); il testo indica FixCity come titolare e non nomina RPD, base giuridica, destinatari, trasferimenti, durata, diritti, reclamo | GDPR artt. 12-14, 37 par. 7, 77; Codice privacy | V1, V7, N-17 | DCP-54.1 |
| G-02 | **Bloccante** | Dichiarazione di accessibilità non pubblicata (404), nessun meccanismo di feedback, obiettivi annuali non verificabili | Dir. (UE) 2016/2102 art. 7; L. 4/2004; D.L. 179/2012 art. 9 | V1, V2, N-13 | DCP-54.2 |
| G-03 | Alta | Tutti i 10 link del footer danno 404 (URL senza lingua), compreso "Segnalazione disservizio"; contatti, P. IVA e PEC segnaposto; nessun RPD né RTD | CAD artt. 53-54; L. 150/2000 | V2, N-05, N-11 | DCP-54.3 |
| G-04 | Alta | Base giuridica non decisa; il testo dell'informativa dice "acconsenti" mentre la casella dice "ho letto"; la registrazione non collega informativa e termini, e i termini sono un segnaposto | GDPR artt. 6, 7, Cons. 43; Codice privacy art. 2-ter | V7, V8, N-18 | DCP-54.4 |
| G-05 | Alta (bloccante per un canale ufficiale che richiede identificazione) | Accesso solo con email e password proprie; nessun SPID/CIE, nessuna app IO, nessun EUDI Wallet | CAD artt. 64, 64-bis, 65; D.L. 76/2020; eIDAS 910/2014 e 2024/1183 | V1, V13, N-06..N-08, N-26 | DCP-54.5 |
| G-06 | Alta | Google Fonts e tessere OpenStreetMap contattati da ogni pagina; Nominatim chiamato dal server con il testo digitato; nessuno dichiarato nell'informativa | GDPR artt. 5, 13, 28, 44-49 | V4, N-23 | DCP-54.6 |
| G-07 | Alta | Login e tracciamento senza limite di tentativi; la sezione `rate_limit` di `config/security.php` non è usata | GDPR art. 32; Misure minime AgID | V12, N-28 | DCP-54.7 |
| G-08 | Alta | Diritti e conservazione: cancellazione dell'utente che lascia i dati nei ticket, nessuna esportazione, nessuna politica né job di conservazione, configurazione Gdpr copiata da un progetto assicurativo | GDPR artt. 5 par. 1 lett. e, 13 par. 2, 15-22 | V14, N-20, N-21 | DCP-54.8 |
| G-09 | Alta | Accessibilità non dimostrata oltre i controlli automatici: nessun audit su tastiera, lettore di schermo, zoom, wizard autenticato; ACC-01..ACC-05 aperti | EN 301 549 / WCAG 2.1 AA; L. 4/2004 | V11, N-14 | DCP-54.9 |
| G-10 | Media (Alta se si sceglie il consenso) | Nessuna prova di presa visione nel wizard (SEC-12); in registrazione i consensi non vengono scritti se manca il `Treatment` (database di sviluppo: 0 trattamenti, 0 consensi, 9 utenti) | GDPR artt. 5 par. 2, 7 par. 1 | V9, N-22 | DCP-54.10 |
| G-11 | Media | La valutazione in conferma risponde "Grazie" ma non salva nulla | CAD artt. 7, 63 | V10, N-02 | DCP-54.11 |
| G-12 | Media | Nessun termine per singolo ticket, nessun ufficio/responsabile/rimedi nella conferma; qualificazione della segnalazione (procedimento o no) non decisa | L. 241/1990 artt. 2-8, 10-bis; CAD art. 65 | V1, N-08, N-10 | DCP-54.12 |
| G-13 | Media | Nessuna intestazione di sicurezza; `APP_DEBUG`, Debugbar e `X-Powered-By` da spegnere nel rilascio; `TrustHosts` non attivo (SEC-13) | GDPR art. 32 | V5, V6, N-28 | DCP-54.13 |
| G-14 | Media | Informativa sui cookie assente; nessuna guardia automatica contro tracciatori futuri | D.Lgs. 196/2003 art. 122; Linee guida Garante 2021 | V3, V4, N-25 | DCP-54.1, DCP-54.14 |
| G-15 | Media | Nessuna decisione su protocollo, fascicolo e conservazione dei ticket come documenti | CAD artt. 40-44; D.P.R. 445/2000 | N-09 | DCP-54.15 |
| G-16 | Media | Registro dei trattamenti vuoto, nessuna procedura di violazione, DPIA non valutata, responsabili e hosting non documentati | GDPR artt. 28, 30, 33-35, 37; Misure minime AgID | V9, N-24, N-29 | DCP-54.16 |
| G-17 | Media | Minimizzazione: casella marketing in registrazione contro il PRD, metadati EXIF nelle foto (ipotesi), IP nel log di debug | GDPR art. 5 par. 1 lett. c, art. 25 | N-19 | DCP-54.17 |
| G-18 | Bassa | Pagine e collegamenti istituzionali che spettano al sito del Comune (Amministrazione trasparente, RTD, URP): da collegare, non da costruire in FixCity | D.Lgs. 33/2013; CAD art. 17; L. 150/2000 | V2, N-03, N-11, N-12 | DCP-54.3 |
| G-19 | Bassa | Da monitorare: NIS2 (soglie del Comune), EUDI Wallet, Single Digital Gateway, European Accessibility Act (non pertinente), lingue di minoranza (dipende dal Comune) | D.Lgs. 138/2024; Reg. 2024/1183; Reg. 2018/1724; Dir. 2019/882; L. 482/1999 | N-15, N-26, N-27, N-30, N-31 | nessuna story: nota nel decision log |

Per la messa in produzione sono prerequisiti G-01 e G-02 (bloccanti) e, in pratica, G-03, G-04 e G-09, che li rendono possibili o credibili. G-05 va decisa dall'ente prima del collaudo: se il Comune sceglie di accettare segnalazioni senza identificazione forte, la decisione va scritta e il login diventa facoltativo; se resta obbligatorio, SPID/CIE diventa un requisito bloccante.

## Story proposte

Numerazione provvisoria `DCP-54.n` (sotto-story di DCP-54); il numero definitivo lo assegna il coordinatore. Ogni story va aperta nel modulo o tema proprietario già esistente, secondo la regola BMAD del repository.

| Story | Titolo | Proprietario | Criteri di accettazione verificabili | Dipendenze |
|---|---|---|---|---|
| DCP-54.1 | Pagina informativa privacy e cookie | Fixcity (contenuto tenant), Cms, Sixteen | `GET /{it,en,de,es}/privacy` risponde 200; il testo contiene le nove voci dell'art. 13 più i cookie tecnici; wizard, registrazione e footer puntano alla pagina; versione e data visibili. Test Pest HTTP e di struttura del testo | Testo approvato dal DPO |
| DCP-54.2 | Dichiarazione di accessibilità e meccanismo di feedback | Cms, Sixteen | Pagina `/{lingua}/accessibilita` secondo il modello della Decisione (UE) 2018/1523 (stato di conformità, contenuti non accessibili, data, contatto/feedback, procedura di attuazione); obiettivi annuali pubblicati; link nel footer funzionante | 54.9 per lo stato di conformità |
| DCP-54.3 | Footer localizzato e pagine istituzionali | Sixteen, Cms | URL del footer localizzati; test che percorre i link del footer su `/it` e `/en` con 0 risposte 404; note legali, media policy, mappa del sito, assistenza e contatti dal tenant (`config/comune.php`), collegamento ad Amministrazione trasparente e RTD del Comune; niente segnaposto | Dati dell'ente |
| DCP-54.4 | Decisione su base giuridica e semantica della casella | Fixcity, Gdpr, con il DPO | Decisione scritta (compito di interesse pubblico o consenso); `policy.md` in 4 lingue senza "acconsenti" se la base non è il consenso; etichetta della casella coerente; termini reali o casella rimossa; link ai testi in registrazione | DPO |
| DCP-54.5 | Piano identità digitale | User, con l'RTD | ADR con opzioni (SPID e CIE tramite intermediario o federazione, oppure segnalazione senza identificazione forte), scelta dell'ente, piano per app IO ed EUDI Wallet; nessun codice prima della decisione | RTD, legale |
| DCP-54.6 | Terze parti: font, mappa, geocodifica | Sixteen, Geo, Fixcity | Titillium Web servito dal tema (eliminare gli `@import` verso `fonts.googleapis.com`); decisione su tessere e Nominatim con relativo contratto o ospitamento; throttle su `searchAddress` e `reverseGeocode` (SEC-07); informativa aggiornata. Test Playwright: 0 richieste a Google | 54.1 |
| DCP-54.7 | Freno ai tentativi | User, Fixcity | `RateLimiter` su login, registrazione, reset password e tracciamento per codice; risposta 429 localizzata; `config/security.php` collegata o ripulita. Test Pest: dopo N tentativi il login è rifiutato anche con la password giusta | nessuna |
| DCP-54.8 | Diritti dell'interessato e conservazione | Gdpr, User, Fixcity, Media | Politica di conservazione approvata (ticket, foto, attività, utenti); job di eliminazione o anonimizzazione; `DeleteUserAction` gestisce i ticket dell'utente; esportazione dei dati per l'operatore; rimosse le chiavi assicurative da `Modules/Gdpr/config/config.php`; test per ciascun flusso | Tempi decisi dall'ente |
| DCP-54.9 | Audit WCAG 2.1 AA manuale e correzioni | Sixteen | Checklist datata su 8 flussi (home, elenco, dettaglio, tracciamento, login, registrazione, wizard a 4 passi, conferma) con tastiera, lettore di schermo, zoom 200 percento, contrasto, movimento ridotto; correzioni ACC-01..ACC-05; test axe (`@axe-core/playwright`) sui flussi in CI | Account di prova (DCP-98) |
| DCP-54.10 | Prova della presa visione | Fixcity, Gdpr | Chiude SEC-12: `TicketActivity` con `privacy_accepted_at`, hash del testo e lingua; `SaveGdprConsentsAction` fallisce in modo esplicito se manca il `Treatment`; seeder dei trattamenti FixCity. Test Pest | 54.4 |
| DCP-54.11 | Valutazione in conferma collegata o rimossa | Fixcity, Rating, Sixteen | Il voto delle stelle è inviato a un'Action e salvato (o il widget è nascosto finché non lo è); nessun messaggio di ringraziamento senza salvataggio. Test Pest più Playwright con richiesta osservata | nessuna |
| DCP-54.12 | Termine e responsabile del procedimento | Fixcity, con l'ente | ADR sulla qualificazione (segnalazione o istanza); se applicabile: termine per ticket configurabile per tenant, ufficio e responsabile in conferma e tracciamento, scadenze in back office. Test Pest | Legale |
| DCP-54.13 | Gate di rilascio della sicurezza | Xot, Ops | CSP (prima in sola segnalazione), HSTS, X-Frame-Options o `frame-ancestors`, X-Content-Type-Options, Referrer-Policy, Permissions-Policy; `X-Powered-By` rimosso; `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`, `trustHosts`; verifica HTTP nel pacchetto di rilascio | Ambiente di staging |
| DCP-54.14 | Guardia automatica su host e cookie | Sixteen, Fixcity | Test Playwright che fallisce se alla prima visita compare un host fuori allowlist o un cookie non tecnico; la decisione `cookie_banner_on=false` documentata nel decision log | 54.6 |
| DCP-54.15 | Decisione su protocollo e conservazione documentale | Fixcity, Tenant, con l'ente | ADR: i ticket sono atti da protocollare o no; collegamento al piano di conservazione; integrazione o esclusione documentata | Ente |
| DCP-54.16 | Pacchetto per il DPO | Gdpr | Registro dei trattamenti FixCity (seed `Treatment`), valutazione di necessità della DPIA, elenco dei responsabili e dei fornitori terzi, procedura di violazione (72 ore), contatti RPD nel tenant, hosting dichiarato | DPO |
| DCP-54.17 | Minimizzazione dei dati | Fixcity, Media, Gdpr | Casella marketing rimossa o motivata; metadati EXIF eliminati in `addMedia` (SEC-05); IP tolto dal log di debug. Test Pest | 54.4 |

## Fonti

**Repository** (percorsi relativi alla radice del repository salvo `laravel/`):

- `bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md` (scopo, target, confini)
- `bmad-output/product-brief.md`, `bmad-output/prd.md`, `bmad-output/architecture.md`, `bmad-output/security-plan.md`, `bmad-output/devops-plan.md`
- `laravel/Modules/Fixcity/docs/stories/dcp-49-audit-sicurezza-viste-wizard.story.md` (SEC-01..SEC-14, ACC-01..ACC-05)
- `laravel/Modules/Fixcity/docs/stories/dcp-48-codice-fiscale-autore-decisione.story.md` (identità, SPID/CIE, dati dell'autore)
- `laravel/Modules/Fixcity/docs/stories/dcp-35-tickets-create-privacy-dati-coordinamento.story.md`
- `laravel/Modules/Fixcity/docs/wiki/concepts/design-comuni-service-workflow-scope.md`
- `laravel/Modules/Fixcity/{app,lang,config,tests}` (wizard, privacy, notifiche, valutazione, SLA)
- `laravel/Modules/Gdpr/{app,config,tests}`, `laravel/Modules/User/app/{Filament/Widgets,Actions}`
- `laravel/Themes/Sixteen/{resources/css/app.css,resources/js/theme/design-comuni-home.js,docs/accessibility.md}`
- `laravel/config/{session.php,security.php,cookie-consent.php,local/fixcity/gdpr.php,local/fixcity/lang/*/policy.md,local/fixcity/database/content/sections/footer.json}`

**Norme** (riferimenti noti all'autore; gli indirizzi sono quelli canonici dei portali ufficiali e **non sono stati aperti in questa sessione**: verificare testo vigente e numeri di articolo su Normattiva ed EUR-Lex prima di citarli in atti):

- D.Lgs. 7 marzo 2005, n. 82 (CAD), artt. 1-3, 7, 12, 17, 40-44, 50-ter, 53, 54, 63, 64, 64-bis, 65; D.L. 16 luglio 2020, n. 76, conv. L. 11 settembre 2020, n. 120. Normattiva: https://www.normattiva.it
- L. 7 agosto 1990, n. 241; L. 7 giugno 2000, n. 150; D.Lgs. 14 marzo 2013, n. 33; D.P.R. 28 dicembre 2000, n. 445
- L. 9 gennaio 2004, n. 4 (Stanca) e D.Lgs. 10 agosto 2018, n. 106; D.L. 18 ottobre 2012, n. 179, art. 9 c. 7; D.Lgs. 27 maggio 2022, n. 82
- Direttiva (UE) 2016/2102: https://eur-lex.europa.eu/eli/dir/2016/2102/oj; Decisioni di esecuzione (UE) 2018/1523 e 2018/2048; Direttiva (UE) 2019/882: https://eur-lex.europa.eu/eli/dir/2019/882/oj
- Regolamento (UE) 2016/679 (GDPR): https://eur-lex.europa.eu/eli/reg/2016/679/oj; D.Lgs. 30 giugno 2003, n. 196 come modificato dal D.Lgs. 10 agosto 2018, n. 101 (artt. 2-ter, 2-quaterdecies, 122); Direttiva 2002/58/CE: https://eur-lex.europa.eu/eli/dir/2002/58/oj
- Garante per la protezione dei dati personali, Linee guida cookie e altri strumenti di tracciamento, 10 giugno 2021 (numero del provvedimento da verificare)
- Regolamento (UE) 910/2014: https://eur-lex.europa.eu/eli/reg/2014/910/oj e Regolamento (UE) 2024/1183: https://eur-lex.europa.eu/eli/reg/2024/1183/oj; Regolamento (UE) 2018/1724: https://eur-lex.europa.eu/eli/reg/2018/1724/oj
- AgID: Circolare 18 aprile 2017, n. 2/2017 (misure minime ICT); Linee guida sull'accessibilità degli strumenti informatici; Linee guida di design per i siti e i servizi digitali della PA; Linee guida sulla formazione, gestione e conservazione dei documenti informatici (estremi delle determinazioni da verificare)
- Direttiva (UE) 2022/2555 (NIS2) e D.Lgs. 4 settembre 2024, n. 138; L. 15 dicembre 1999, n. 482
- Contesto giurisprudenziale non vincolante in Italia: Landgericht München I, 20 gennaio 2022, 3 O 17493/20 (Google Fonts)

*Chiusura: il documento è aggiornato al 2026-10-13. Ogni riga con la sigla L attende la validazione del DPO, del RTD o del legale dell'ente; nessuna di esse è una conclusione giuridica.*
