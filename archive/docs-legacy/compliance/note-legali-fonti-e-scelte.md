---
title: "Note legali e condizioni di utilizzo: fonti, fatti dell'applicazione e scelte di redazione"
type: source-register
status: draft-for-dpo-legal-review
story: dcp-162
owner: module:Fixcity
created: 2026-10-26
updated: 2026-10-26
tags: [compliance, note-legali, condizioni-di-utilizzo, dsa, cad, diritto-d-autore, licenze, bmad]
related:
  - ./01-matrice-conformita-normativa.md
  - ./03-fonti-cad-accessibilita-verificate.md
  - ../stories/dcp-162-note-legali-disclaimer-e-condizioni-di-utilizzo.story.md
  - ../stories/dcp-231-guardie-di-pubblicazione-di-terms-e-media-policy-e-feedback-accessibilita.story.md
  - ../../../Gdpr/docs/terms-conditions.md
  - ../../../Gdpr/docs/stories/dcp-178-registrazione-e-disiscrizione-a-norma.story.md
qmd: "note legali condizioni di utilizzo terms dsa 2022/2065 art 14 16 17 cad art 52 licenza contenuti ccby c.si.3.4 l 633 1941 art 11 96 97 codice civile 1229 1341 foro odr dismessa codice del consumo"
---

# Note legali e condizioni di utilizzo: fonti, fatti dell'applicazione e scelte di redazione

Bozza di alta qualita' per il legale dell'ente. **Non e' un parere legale e nulla qui dice che il servizio sia «conforme»**: le norme sono state lette sul testo (verifica del 2026-10-26, vigenza a quella data), l'applicazione e' stata letta nel codice, il giudizio giuridico spetta al legale e al DPO dell'ente. Percorsi relativi a `laravel/`.

## 1. Che cosa e' stato fatto

- `Modules/Fixcity/resources/templates/legal-notes.{it,en,de,es}.md`: da modello con 8 sezioni su 11 da compilare a testo completo; restano segnaposto `[[...]]` solo i dati dell'ente (sezione 3). Stesse 11 sezioni, stessi titoli di `lang/<lingua>/legal_notes.php`, sottotitoli di livello 3 dove serve (restano nel corpo della sezione per `AssessPrivacyPolicyAction`).
- `Modules/Fixcity/lang/{it,en,de,es}/legal_notes.php`: solo i suggerimenti (`hint`) di `ip`, `law`, `contacts`, per dire all'ente che cosa fornire. Titoli e chiavi invariati.
- `Modules/Fixcity/tests/Unit/AssessLegalNotesActionTest.php`: la costante `NOTES_WITH_ENTITY_DATA` passa da 8 a 5 sezioni (`owner`, `ip`, `changes`, `law`, `contacts`), perche' `purpose`, `contributions` e `liability` non hanno piu' segnaposto. E' l'unica modifica fuori dall'area dichiarata, necessaria per non lasciare rosso il test del modello.
- Nessun codice di produzione, routing o configurazione del tenant e' stato toccato.

## 2. Dove il tenant legge i testi veri e come si arriva a /terms

| Pagina | Dato JSON | File letto | Stato nel tenant `local/fixcity` |
|---|---|---|---|
| `/{lang}/note-legali` | `config/local/fixcity/database/content/pages/note-legali.index.json`: `document: legal_notes`, `markdown: legal-notes.md`, `assess: legal-notes` | `GetPublishedLegalDocumentAction` -> `GetLocalizedMarkdownPathAction`: prima `config/local/fixcity/lang/<lingua>/legal-notes.md`, poi `config/local/fixcity/legal-notes.md` | **il file non esiste in nessuna lingua**: la pagina risponde 200 con l'avviso «Testo non ancora completo» |
| `/{lang}/terms` | `terms.index.json`: `document: terms_disclaimer`, `markdown: terms.md`, nessun `assess` | `config/local/fixcity/terms.md`, `lang/it/terms.md`, `lang/en/terms.md`: 88 byte, «Edit this file...» (trattato come assente); de ed es senza file | avviso «Testo non ancora completo» |
| Collegamento dalla registrazione | `Themes/Sixteen/resources/views/filament/widgets/auth/register.blade.php:35-38` (`FrontofficeUrl::fromStoredUrl('/terms')`); obbligo server in `Modules/Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php:70` | | difetto D1 della scheda per pagina: si accetta un testo che non esiste |

Il testo vero lo scrive l'ente nel tenant partendo dal modello: copiare `legal-notes.<lingua>.md` in `config/local/fixcity/lang/<lingua>/legal-notes.md`, sostituire i `[[...]]`, togliere la nota in testa. Il gate `PUBLICATION_GATE=1 pest --group=publication-gate` resta rosso finche' manca una sezione o resta un segnaposto.

**Collegamento minimo proposto per /terms (non applicato, nessuna duplicazione di testo, nessun codice di routing):**
1. Preferibile: in `terms.index.json` (e nelle copie `eu` e del modulo) usare per i 4 blocchi `markdown: legal-notes.md` e `assess: legal-notes`, lasciando `document: terms_disclaimer` (titolo «Termini e disclaimer»). `/terms` mostra cosi' lo stesso file di `/note-legali`, con la stessa guardia. Richiede di aggiornare `TermsAndDisclaimerPageTest.php`, che oggi simula il file `terms.md`.
2. Alternativa, solo nel tema: far puntare il link della registrazione (`register.blade.php:37`) a `/note-legali`. Chiude D1 senza toccare le pagine, ma `terms` resta una pagina vuota raggiungibile.
3. Decisione di prodotto D231-1 / DEC-17: se `terms` diventa un reindirizzamento 301 a `note-legali`, serve routing (fuori dal mio perimetro). Con la 1 o la 2 il 301 si puo' fare dopo senza rifare i testi.

Se la registrazione fa accettare le condizioni come contratto, vedi la domanda aperta Q3 (art. 1341 c.c.): il testo e' strutturato in modo che le clausole critiche siano in sezioni distinte e riconoscibili.

## 3. Sezioni: complete o dipendenti dall'ente

Verifica con `AssessPrivacyPolicyAction` (documento `legal_notes`), test `AssessLegalNotesActionTest`: sul modello cosi' com'e', in tutte e 4 le lingue, l'esito e' esattamente `owner`, `ip`, `changes`, `law`, `contacts` = `placeholder`; nessuna sezione `missing`; dopo aver sostituito i segnaposto dei dati e tolto la nota in testa, l'esito e' vuoto (pubblicabile).

| Sezione | Stato | Che cosa deve fornire l'ente (elenco esatto) |
|---|---|---|
| `owner` Titolare e gestore | dipende dall'ente | denominazione; indirizzo della sede; codice fiscale o partita IVA; PEC; recapito dell'URP; nome e recapito del responsabile per la transizione digitale (CAD art. 17) |
| `purpose` Finalita' | **completa** | nulla (vedi Q1 per la qualificazione come istanza) |
| `use` Condizioni di utilizzo | **completa** | nulla |
| `contributions` Segnalazioni e contenuti | **completa** | nulla (moderazione, pubblicazione, commenti, licenza all'ente, motivazione delle decisioni) |
| `liability` Limitazione di responsabilita' | **completa** | nulla (da far rivedere: Q3, Q4) |
| `links` Collegamenti di terzi | **completa** | nulla |
| `ip` Proprieta' intellettuale e licenza | dipende dall'ente | la licenza dei contenuti scelta (il modello Design Comuni C.SI.3.4 indica CC-BY 4.0) |
| `privacy` Dati personali, cookie, accessibilita' | **completa** | nulla (solo tre rimandi relativi: `privacy`, `cookie-policy`, `accessibilita`) |
| `changes` Modifiche | dipende dall'ente | data dell'ultimo aggiornamento; mezzo con cui si informano gli utenti registrati delle modifiche rilevanti |
| `law` Legge applicabile e foro | dipende dall'ente | il foro scelto, oppure la conferma che valgono i criteri di legge |
| `contacts` Contatti | dipende dall'ente | l'indirizzo email o PEC che riceve le richieste di rimozione e le segnalazioni sul sito |

Il documento ha in piu' la nota in testa (`> [[Nota per l'ente: ...]]`, riga unica): va tolta alla fine, finche' c'e' il documento non e' pubblicabile. Le sezioni «complete» restano **testo da far rivedere al legale**: la guardia controlla solo l'assenza di segnaposto, non la correttezza.

## 4. Fatti dell'applicazione ricavati dal codice

| Fatto scritto nel testo | Prova (file:riga) |
|---|---|
| Per inviare una segnalazione serve un account; senza sessione l'invio si ferma | `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php:89-93` |
| Si leggono elenco e mappa pubblici senza account; i commenti si scrivono solo da loggati | `Themes/Sixteen/resources/views/components/blocks/ticket-detail/design-comuni.blade.php:36-39` (scrittura), `:132-146` |
| La segnalazione nasce `pending` e il client non puo' cambiare lo stato | `CreateTicketWizardWidget.php:118`; `Modules/Fixcity/app/Models/Ticket.php:338-339` |
| E' pubblica solo negli stati `open, in_review, in_progress, on_hold, resolved, closed, reopened`; `draft` e `pending` no | `Modules/Fixcity/app/Enums/TicketStatusEnum.php:46-56` (pubblici), `:64-70` (interni); `BuildPublicTicketsQueryAction.php` |
| Un operatore approva (`pending` -> `in_review`, evento pubblico) o scarta; il motivo dello scarto lo vede solo l'autore | `ApproveTicketAction.php:36,51`; `RejectTicketAction.php:35,56` |
| Campi pubblici: titolo, descrizione, tipo, stato, data (senza ora), indirizzo, coordinate, cronologia; mai proprietario, email, autore | `BuildPublicTicketDetailAction.php:26,84-95` |
| Le foto di una segnalazione pubblica sono raggiungibili come URL nell'API `api/ticket-details/{id}` | `BuildTicketPublicDetailsPayloadAction.php:38-52`; `Modules/Fixcity/resources/views/pages/api/ticket-details/[ticket].blade.php:17-25` |
| Chi conosce il codice di tracciamento vede la segnalazione anche prima della pubblicazione (nessun filtro sullo stato, 10 tentativi falliti al minuto) | `GetPublicTicketByCodeAction.php:49-51`, `:26,29` |
| Campi del modulo: tipo, titolo, descrizione, luogo, allegati JPEG, PNG, PDF | `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php:320-339`; `Ticket.php:423-430` |
| I commenti sono approvati subito (moderazione solo successiva) e mostrano il nome dell'account | `config/local/fixcity/comments.php:60`; `Modules/Comment/app/Actions/Comment/ShouldAutomaticallyApproveCommentAction.php`; `Modules/Comment/resources/views/components/comment-item.blade.php:6` |
| Limite ai commenti (5 al minuto) | `Modules/Comment/app/Actions/Comment/EnsureCommentRateLimitAction.php:22,25` |
| Nessuna moderazione automatica: la pipeline dei commenti non ha filtri e la ricerca di parole come profanity, spam, openai, llm, toxic in `Modules/Fixcity/app` e `Modules/Comment/app` non trova nulla | verifica per assenza (grep, 2026-10-26); da confermare col fornitore |
| Nessun tempo di risposta fissato | `Modules/Fixcity/lang/it/ticket_detail.php:33` («I tempi dipendono dall'ufficio competente...») |
| Il servizio e' gratuito | verifica per assenza: nessun codice di pagamento in `Modules/Fixcity/app` e `Modules/Gdpr/app` |
| Il cittadino non puo' cancellare i dati in autonomia (la richiesta va all'ente) | `Modules/Fixcity/lang/it/personal_area_settings.php:73` |
| La registrazione impone l'accettazione di condizioni e informativa | `RegisterWidget.php:70`; `Modules/Gdpr/app/Actions/SaveGdprConsentsAction.php:26,42,71` (prova con `document_version` provvisoria `1.0`) |
| L'account puo' essere disattivato (colonna `is_active`) | `Modules/User/app/Models/BaseUser.php:175,205`; **non verificato** che il login la rispetti |
| Mappe: tessere OpenStreetMap scaricate dal browser; satellite (Esri) e topografica (OpenTopoMap) definite come livelli | `Modules/Fixcity/resources/js/components/coordinate-picker-lit.js:287-289` |
| Ricerca indirizzo e conversione punto-indirizzo: Nominatim di OpenStreetMap, chiamato dal server | `Modules/Geo/app/Filament/Forms/Components/CoordinatePickerHelpers.php:55,101`; `Modules/Geo/app/Forms/Components/CoordinatePicker.php:133` |

Non e' codice, ma testo mio da confermare: «ogni segnalazione viene esaminata da un operatore» (e' il flusso del codice, non un impegno sui tempi) e «le decisioni sono prese da persone, non da sistemi automatici» (coerente col grep sopra).

## 5. Fonti e vigenza al 2026-10-26

Testi scaricati il 2026-10-26: Normattiva (`caricaAKN`, dataVigenza 20261026) e Cellar dell'Ufficio delle pubblicazioni UE (`Accept: application/xhtml+xml`, `Accept-Language: ita`), perche' EUR-Lex risponde 202 con sfida anti-bot (stessa esperienza del registro 02).

| Norma o fonte | Articoli usati | Esito | URL |
|---|---|---|---|
| Reg. (UE) 2022/2065 (DSA) | art. 2 par. 1 e 2 (solo servizi intermediari); art. 3 lett. a, g(iii) hosting, i piattaforma online, t moderazione, u condizioni generali; art. 11 e 12 punti di contatto; art. 14 condizioni generali; art. 15 par. 2; art. 16 segnalazione e azione; art. 17 motivazione; art. 19 | letto sul testo italiano | https://publications.europa.eu/resource/celex/32022R2065 |
| Dir. (UE) 2015/1535, art. 1 par. 1 lett. b | «servizio prestato normalmente dietro retribuzione» | letto | https://publications.europa.eu/resource/celex/32015L1535 |
| CGUE C-291/13 Papasavvas, punti 26-30 (cita il considerando 18 dir. 2000/31: servizi non remunerati dal destinatario sono servizi della societa' dell'informazione «nella misura in cui costituiscono un'attivita' economica») | | letto | https://publications.europa.eu/resource/celex/62013CJ0291 |
| CGUE C-109/92 Wirth, punti 15-17 (istruzione finanziata con fondi pubblici non e' servizio retribuito) | | **non letto direttamente**, ricavato da una guida alla giurisprudenza e da altre fonti secondarie | https://eur-lex.europa.eu/legal-content/EN/TXT/HTML/?uri=CELEX:61992CJ0109 |
| Reg. (UE) 2024/3228 (abroga il Reg. 524/2013, piattaforma ODR) | art. 1: abrogato dal 20 luglio 2025; art. 2: piattaforma dismessa, reclami interrotti il 20 marzo 2025 | letto: **la piattaforma ODR non e' citata nel testo** | https://publications.europa.eu/resource/celex/32024R3228 |
| D.Lgs. 82/2005 (CAD) | art. 1 c. 1 lett. l-bis e l-ter (dati aperti); art. 17 (RTD; c. 1-quater difensore civico per il digitale); art. 52 c. 2 (open by default); art. 53 e 54 (siti PA) | letto, testo vigente | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2005-03-07;82 |
| D.Lgs. 36/2006 (come riscritto dal D.Lgs. 200/2021) | art. 2 c. 1 lett. h (licenza standard); art. 3 c. 1 lett. h, h-ter, h-quater (esclusi documenti con diritti di terzi, logotipi e stemmi, dati personali); art. 7; art. 8 c. 1 (le PA adottano licenze standard) | letto | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2006-01-24;36 |
| L. 633/1941 | art. 11 (diritto d'autore dei Comuni sulle opere create e pubblicate a loro nome e spese); art. 87 e 88 (fotografie); art. 96 e 97 (ritratto); art. 110 (trasmissione dei diritti provata per iscritto) | letto | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:1941-04-22;633 |
| L. 241/1990 | art. 2 c. 1 (provvedimento espresso se il procedimento consegue a istanza o e' d'ufficio); art. 3 (motivazione; c. 4 termine e autorita' di ricorso negli atti notificati); art. 9 | letto | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:1990-08-07;241 |
| Codice civile | art. 1229 (nullo l'esonero preventivo per dolo o colpa grave); art. 1341 c. 2 (limitazioni di responsabilita', facolta' di sospendere, deroghe alla competenza: inefficaci se non specificamente approvate per iscritto) | letto | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:regio.decreto:1942-03-16;262 |
| D.Lgs. 206/2005 (Codice del consumo), art. 3 c. 1 lett. a e c | consumatore e professionista (attivita' imprenditoriale, commerciale, artigianale o professionale) | letto | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2005-09-06;206 |
| D.Lgs. 70/2003, artt. 14-17 | abrogati dall'art. 3 c. 4 del D.Lgs. 25 marzo 2024, n. 50 (le regole sull'hosting sono ora negli artt. 4-6 e 8 DSA) | letto su Normattiva | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2003-04-09;70 |
| Design Comuni, criterio C.SI.3.4 «Licenza e attribuzione» | la pagina «note legali» e' raggiungibile dal footer e contiene la sezione «Licenza dei contenuti» con la dicitura open by default (CAD art. 52) e CC-BY 4.0; la verifica cerca `data-element="legal-notes"` sul link del footer, `legal-notes-section` sul titolo e `legal-notes-body` sui paragrafi | letto (riassunto di WebFetch; testo della dicitura completa non riprodotto qui) | https://docs.italia.it/italia/designers-italia/design-comuni-docs/it/versione-corrente/conformita/conformita-modello-sito.html e https://docs.italia.it/italia/designers-italia/app-valutazione-modelli-docs/it/versione-attuale/requisiti-e-modalita-verifica-comuni.html |
| AgID, Linee guida open data (Determinazione 183/2023) | licenze: CC0 e CC-BY 4.0 (o equivalenti aperte) | **solo da risultati di ricerca**, PDF non scaricato | https://www.agid.gov.it/sites/agid/files/2024-05/lg-open-data_v.1.0_1.pdf |
| Coordinatore dei servizi digitali: AGCOM (art. 15 D.L. 123/2023, conv. L. 159/2023) | non serve nel testo | **non verificato sul testo di legge**, solo da delibere AGCOM e dossier parlamentare | |
| Numero unico di emergenza 112 | citato nel testo | non verificato su fonte primaria (e' il numero unico di emergenza) | |
| OpenStreetMap, Open Database License | attribuzione «© contributori di OpenStreetMap» e link a openstreetmap.org/copyright | pagina non letta | https://www.openstreetmap.org/copyright |

## 6. Scelte di redazione e perche'

1. **DSA, formulazione prudente.** Il DSA si applica ai «servizi intermediari» (art. 2 par. 1 e 2), cioe' ai servizi della societa' dell'informazione prestati «normalmente dietro retribuzione» (dir. 2015/1535). Un servizio comunale gratuito, finanziato dalla fiscalita' generale, potrebbe non essere un'attivita' economica (considerando 18 dir. 2000/31 come citato in Papasavvas; per analogia Wirth), quindi il DSA potrebbe non applicarsi: **non e' stabilito**. Se si applicasse, il servizio che ospita e diffonde al pubblico segnalazioni e commenti sarebbe un servizio di hosting (art. 3 lett. g iii) e, perche' diffonde al pubblico su richiesta dell'utente, probabilmente una piattaforma online (art. 3 lett. i, k). L'art. 19 non aiuta: esenta dalla sola sezione 3 le microimprese e piccole imprese, lasciando fermi artt. 12, 14, 16 e 17, e un Comune non e' un'impresa (la raccomandazione 2003/361/CE non e' stata riletta qui). Scelta: non dichiarare l'ente «prestatore», ma scrivere le condizioni in modo che le regole sostanziali ci siano comunque, con la riserva «per quanto applicabili»: punto di contatto elettronico (art. 12), condizioni in linguaggio chiaro con regole di moderazione, controllo umano e riesame (art. 14), elementi della segnalazione (art. 16 par. 2, lett. a-d), conferma di ricevimento e decisione (art. 16 par. 4-6), motivazione della restrizione con fatti, base, rimedi (art. 17 par. 3), nessuna decisione automatica. Sono compatibili con la L. 241/1990 art. 3 anche se il DSA non si applicasse.
2. **ODR.** Non citata: la piattaforma europea e' dismessa (Reg. 2024/3228).
3. **Codice del consumo.** L'art. 3 lett. c richiede un «professionista» che agisce nell'esercizio di un'attivita' imprenditoriale, commerciale, artigianale o professionale; un servizio civico gratuito dell'ente nell'esercizio dei compiti istituzionali non lo e'. Quindi non applicabile, ma il testo non lo afferma: salva «le norme che tutelano gli utenti che sono consumatori, se applicabili» e l'art. 1229 c.c.
4. **Contenuti di terzi e licenza aperta.** L'art. 52 c. 2 CAD fa «dati di tipo aperto» i dati e documenti pubblicati senza licenza espressa; il D.Lgs. 36/2006 esclude i documenti su cui terzi hanno diritti ai sensi della L. 633/1941 (art. 3 c. 1 lett. h), logotipi e stemmi (h-ter) e i dati personali (h-quater). Per questo segnalazioni, foto e commenti sono **esclusi** dalla licenza aperta dell'ente e disciplinati dalla licenza non esclusiva e gratuita concessa dall'utente (nessuna cessione, art. 110 L. 633/1941 non richiesto).
5. **Foto.** Il fotografo ha il diritto sulla fotografia (art. 88 L. 633/1941); se ritrae persone, serve il consenso salvo le eccezioni dell'art. 97. Il testo chiede di non includere volti, targhe e dati di terzi e consente all'ente di oscurare, non di «modificare» il contenuto.
6. **Pubblicazione e responsabilita'.** Il testo dice la verita' del flusso: c'e' un esame umano prima della pubblicazione delle segnalazioni, nessun esame prima dei commenti. Il modello precedente («senza obbligo di controllo preventivo») era in contrasto con `ApproveTicketAction`. L'esame non garantisce la veridicita'. La responsabilita' per i contenuti degli utenti e' limitata sul modello dell'art. 6 DSA (conoscenza e rimozione tempestiva), sempre «nei limiti previsti dalla legge».
7. **Nessun dato dell'ente inventato.** Denominazione, sede, CF, PEC, recapiti, RTD, licenza, data, mezzo di informazione, foro e casella delle richieste restano `[[...]]`. Tolte le formule che nel modello chiedevano all'ente di «dichiarare come istanze» le segnalazioni, le «altre finalita'» e i termini di risposta: la prima e' la domanda Q1; le altre non appartengono alle condizioni d'uso (le finalita' sono dell'informativa, i termini non sono fissati nel codice).
8. **Anti-lorem.** Il testo non contiene le parole che fanno scattare la guardia (`TODO`, `TBD`, `FIXME`, `xxxx`, `example.*`, `lorem ipsum`). Vedi D-3 sotto: in spagnolo la parola «todo» farebbe scattare il segnaposto.

## 7. Domande aperte per il legale dell'ente

- **Q1.** Le segnalazioni sono istanze ai sensi della L. 241/1990 art. 2 c. 1? Il testo non lo dice (dice solo che non sostituiscono le istanze formali). Se l'ente le tratta come istanze, serve un termine e un provvedimento, cosa che l'applicazione oggi non prevede (nessun termine configurato).
- **Q2.** Il DSA si applica al servizio? Se si', l'ente dovrebbe garantire davvero: punto di contatto, conferma di ricevimento, decisione e motivazione per ogni restrizione (art. 16-17), reclami interni se il servizio e' una piattaforma online (art. 20, non scritto nel testo), relazioni di trasparenza (art. 15). Il testo promette solo cio' che il legale conferma che l'ufficio puo' fare.
- **Q3.** Art. 1341 c. 2 c.c.: con una sola casella «accetto i Termini e Condizioni» le clausole di limitazione di responsabilita', di sospensione dell'account e l'eventuale deroga al foro non hanno effetto se il rapporto e' un contratto e non sono approvate specificamente per iscritto. Il rapporto con un servizio pubblico gratuito e' un contratto? Se si', serve una seconda approvazione specifica delle sezioni `liability`, `use` (sospensione) e `law` (foro): richiede una modifica al modulo di registrazione (Gdpr/tema), non fatta. In alternativa non prevedere la deroga al foro e lasciare i criteri di legge.
- **Q4.** La moderazione preventiva delle segnalazioni rende l'ente editore dei contenuti che approva? Il testo limita la responsabilita' ma non la esclude (art. 1229 c.c.; art. 28 Cost. e art. 2043 c.c. per gli atti dell'ente, non riletti qui).
- **Q5.** Le decisioni di sospendere un account o rimuovere un contenuto sono provvedimenti? In tal caso L. 241/1990 art. 3 c. 4 impone di indicare termine e autorita' di ricorso; il testo indica solo il riesame e la via giudiziaria o amministrativa «nei casi previsti».
- **Q6.** Licenza: CC-BY 4.0 (richiesta dal modello Design Comuni C.SI.3.4) o altra? Il testo presuppone una licenza con obbligo di citazione della fonte (non vale per CC0).
- **Q7.** Licenza concessa dall'utente: la durata «finche' il contenuto e' pubblicato o conservato» e' coerente con i tempi di conservazione dell'informativa e con gli obblighi di archivio dell'ente?
- **Q8.** Lo stemma e i loghi hanno un regolamento d'uso dell'ente da citare?
- **Q9.** Il foro: ente sceglie una sede o conferma i criteri di legge? Il consumatore resta tutelato dalle norme inderogabili.
- **Q10.** Quando il testo cambia, l'ente chiedera' una nuova accettazione? Oggi la registrazione salva solo `document_version` provvisoria `1.0` (DCP-178 punto 5).

## 8. Difetti trovati fuori dal mio perimetro (nessuno corretto)

- **D-1. Attribuzione OSM nascosta.** La mappa del modulo di segnalazione crea Leaflet con `attributionControl: false` (`coordinate-picker-lit.js:284`) e usa le tessere di `tile.openstreetmap.org`: la licenza ODbL e la politica d'uso delle tessere chiedono l'attribuzione visibile vicino alla mappa. Il testo delle note la contiene, ma non basta come sola attribuzione. Per l'immagine statica (`staticmap.openstreetmap.de`, `Modules/Fixcity/resources/views/components/ticket-location-map.blade.php:11`) non ho verificato se il componente sia usato nel front office.
- **D-2. Attributi di verifica Design Comuni mancanti nel tema.** Per C.SI.3.4 l'app di valutazione cerca `data-element="legal-notes-section"` sul titolo «Licenza dei contenuti» e `legal-notes-body` sui paragrafi; il blocco `legal-document/design-comuni.blade.php` rende il Markdown in un `div` con solo `data-element="legal-text"`. Da risolvere nel tema (non e' possibile da Markdown). Non verificato che il link del footer abbia `data-element="legal-notes"`.
- **D-3. Falso positivo della guardia in spagnolo.** `AssessPrivacyPolicyAction::PLACEHOLDER_PATTERN` ha il flag `i` e contiene `\bTODO\b`: la parola spagnola «todo» (tutto) fa risultare `placeholder` una sezione. Provato con `preg_match` sulla stringa «Actualice todo el sitio» (esito 1). Il mio testo evita la parola; il tenant spagnolo e le altre guardie (informativa, accessibilita') ne sono esposti. Correzione suggerita: rendere `TODO|TBD|FIXME` sensibili alle maiuscole.
- **D-4. Troppo ampia la regola della policy sui ticket.** `TicketPolicy::view`, `update` e `delete` restituiscono `true` per qualunque utente (`Modules/Fixcity/app/Policies/TicketPolicy.php:27-45`) e il blocco del dettaglio usa `update` per decidere chi puo' commentare (`ticket-detail/design-comuni.blade.php:38-39`): oggi chi e' loggato puo' commentare qualunque segnalazione. Il testo dice «accedi con il tuo account», che e' vero; la policy e' da rivedere.
- **D-5. Codice di tracciamento come capability.** Chi lo conosce vede anche segnalazioni non pubbliche e le foto (`GetPublicTicketByCodeAction.php:49-51`); il testo lo dice all'utente («non diffonderlo») ma e' una scelta di prodotto gia' nota (DCP-149).
- **D-6. Story DCP-162 e docs.** La story dice «8 sezioni con dati dell'ente»; ora sono 5. Aggiornare la story e `Modules/Gdpr/docs/terms-conditions.md` (rimanda al segnaposto di 88 byte) quando l'ente decide D231-1.

## 9. Che cosa non ho potuto verificare

- Il parere di un legale (questa e' una bozza), la lettura di Wirth (C-109/92) e della raccomandazione 2003/361/CE, il testo di legge sull'AGCOM, le Linee guida AgID del 2023, la pagina di OpenStreetMap sull'attribuzione, l'esistenza della dicitura completa C.SI.3.4 parola per parola.
- Che il login rispetti `is_active`, che esistano davvero la schermata di moderazione dei commenti e il flusso di conferma di ricevimento per le richieste di rimozione (e' un processo dell'ufficio, non del codice).
- Il comportamento nel browser (nessuna prova visiva delle pagine con il nuovo testo; verificato solo il rendering via test e la struttura delle sezioni).
- Il contenuto effettivo dei file del tenant al momento della pubblicazione: `legal-notes.md` non esiste ancora.

## 10. Verifica eseguita (2026-10-26, DB di prova)

- `Modules/Fixcity/tests/Unit/AssessLegalNotesActionTest.php`: 25 test, 178 asserzioni, verdi. Prova che il modello in it, en, de, es ha esattamente `owner`, `ip`, `changes`, `law`, `contacts` in `placeholder`, nessuna `missing`, e che con i dati compilati e senza la nota in testa l'esito e' vuoto.
- `Modules/Fixcity/tests/Feature/Pages/LegalNotesPageTest.php`: 13 test, 119 asserzioni, verdi (testo completo reso e ripulito, avviso con i titoli delle note legali, modello pubblicato com'e' elenca solo le sezioni da completare).
- La guardia `LegalNotesPublicationGateTest` resta rossa per costruzione finche' l'ente non scrive `legal-notes.md` nel tenant (non eseguita: richiede `PUBLICATION_GATE=1` e legge il tenant).
