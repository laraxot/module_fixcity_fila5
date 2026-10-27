---
title: "Media policy: fonti, scelte, dati dell'ente e domande aperte"
type: source-register
status: draft-for-legal-review
story: dcp-231
owner: module:Fixcity
created: 2026-10-26
updated: 2026-10-26
tags: [compliance, media-policy, fonti-primarie, normattiva, eur-lex, garante, dsa, front-office]
related:
  - ./01-matrice-conformita-normativa.md
  - ../stories/dcp-231-guardie-di-pubblicazione-di-terms-e-media-policy-e-feedback-accessibilita.story.md
  - ../../../../Themes/Sixteen/docs/compliance/03-front-office-scheda-per-pagina.md
qmd: "media policy modello resources templates guardia publication gate dsa art 16 art 17 codice comportamento 11-ter stemma foto minori L 71 2017 segnaposto ente"
---

# Media policy: fonti, scelte, dati dell'ente e domande aperte

Bozza redatta il 2026-10-26 da un agente, non da un avvocato. Il modello e' pronto per la revisione del legale, del responsabile della comunicazione e del DPO dell'ente. Nessuna parte e' "conforme": nessun parere legale e' stato dato e il Comune non ha ancora fornito i dati.

## 1. Meccanismo (letto nel codice)

- Pagina: `config/local/fixcity/database/content/pages/media-policy.index.json` (identica a `Modules/Fixcity/config/database/content/pages/media-policy.index.json`). Blocco `legal_document`, `document: media_policy`, `markdown: media-policy.md`, **senza** `assess`.
- Titolo: `Themes/Sixteen/lang/<lingua>/legal.php`, chiave `documents.media_policy.title`.
- Vista: `Themes/Sixteen/resources/views/components/blocks/legal-document/design-comuni.blade.php`. Senza testo mostra l'avviso `legal-incomplete`; con testo mostra `legal-text`; l'elenco delle sezioni mancanti compare solo se `assess` e' valorizzato.
- Il tenant legge il testo in `config/local/fixcity/lang/<lingua>/media-policy.md`, poi `config/local/fixcity/media-policy.md` (`GetPublishedLegalDocumentAction.php:21-29` con `GetLocalizedMarkdownPathAction`). Oggi nessuno dei due file esiste, quindi la pagina risponde 200 con l'avviso in tutte e 4 le lingue (riscaricata il 2026-10-26).
- `BuildLegalDocumentAction.php:25-29`: `assess` ammette solo `true`/`gdpr-art13` e `legal-notes`. Per la media policy non c'e' ne' `assess` ne' una costante in `AssessPrivacyPolicyAction` ne' un file `lang/<lingua>/media_policy.php`.
- Il modello non e' servito da nessuna parte: `resources/templates/` e' un punto di partenza che l'ente copia e compila (come per `privacy-policy.*.md` e `legal-notes.*.md`).

### Guardia mancante (decisione richiesta, NON implementata)

Se qualcuno copia il modello nel tenant cosi' com'e', la pagina passa da "avviso" a "testo con `[[...]]` visibili" **senza alcun avviso** (D9 della story DCP-231). Per questo il modello non e' stato copiato nel tenant e il JSON non e' stato toccato. Per chiudere D9 servono: `assess: "media-policy"` nei JSON, un ramo in `BuildLegalDocumentAction`, le 14 sezioni del modello in `AssessPrivacyPolicyAction`, un `lang/<lingua>/media_policy.php` con titolo e `hint` per sezione (come `legal_notes.php`) e `MediaPolicyPublicationGateTest` nel gruppo `publication-gate`. Le 14 intestazioni del modello sono l'elenco naturale; il test `MediaPolicyTemplateTest` ne custodisce gia' numero e dati dell'ente.

## 2. Premessa non verificata: la «voce Media policy»

Nelle Linee guida di design (bozza `lg-design-servizi-web`, pagine `index.html` e `requisiti.html`) e nei documenti Design Comuni (`modello-sito-comunale.html`, `norme-e-requisiti-legali.html`, `allegati-tecnici.html`, `conformita/conformita-modello-sito.html`, `introduzione.html`) la stringa «media policy» **non compare**. Il Design Comuni nomina nel footer solo privacy policy, cookie policy e note legali. L'unica fonte AgID sul tema e' la «social media policy» (SMP) nelle Linee guida per la promozione dei servizi digitali: va adottata "interna ed esterna", deve dire come sono trattati i dati degli utenti (Reg. UE 2016/679), le regole di moderazione e le licenze dei contenuti e dei materiali multimediali, richiamare la privacy policy, trattare i dati di newsletter e messaggistica; le condizioni d'uso dei contenuti multimediali stanno nelle note legali; policy e note legali stanno nel footer. Il modello segue questi punti. Il modello statico `design-comuni-pagine-statiche/sito/media-policy.html` risponde 404.

## 3. Sezioni del modello

| # | Sezione (it) | Dati dell'ente (`[[...]]`) | Stato |
|---|---|---|---|
| 1 | Ente titolare e finalità della policy | 1 | dipende dall'ente |
| 2 | Canali ufficiali | 6 | dipende dall'ente |
| 3 | Gestione dei canali e tempi di risposta | 5 | dipende dall'ente |
| 4 | Linguaggio e tono | 1 | quasi completa |
| 5 | Partecipazione e contenuti pubblici | 0 | completa (fatti dell'app) |
| 6 | Regole di partecipazione e comportamenti vietati | 0 | completa |
| 7 | Moderazione e rimozione dei contenuti | 1 | quasi completa |
| 8 | Segnalare un contenuto o contestare una decisione | 3 | dipende dall'ente |
| 9 | Foto, immagini e dati di altre persone | 1 | quasi completa |
| 10 | Diritto d'autore, licenza dei contenuti e riuso | 3 | dipende dall'ente |
| 11 | Logo e stemma dell'ente | 2 | dipende dall'ente |
| 12 | Rapporti con la stampa e i media | 1 | dipende dall'ente |
| 13 | Dati personali, cookie, note legali e accessibilità | 1 | quasi completa |
| 14 | Aggiornamenti e contatti | 3 | dipende dall'ente |

Piu' la nota iniziale per l'ente (1, da eliminare a testo completo): **29 voci `[[...]]`** in tutto, uguali nelle 4 lingue.

### Dati che l'ente deve fornire (elenco esatto)

1. Denominazione dell'ente.
2. Indirizzo del sito ufficiale; PEC; recapito dell'URP.
3. Elenco dei profili social ufficiali con indirizzo di ciascuno (o "nessuno"); elenco di canali di messaggistica e newsletter (o "nessuno").
4. Decisione: un messaggio ricevuto sui social o in un canale di messaggistica e' trattato come segnalazione?
5. Struttura o ufficio responsabile della comunicazione; giorni e orari di presidio; tempo di risposta ai messaggi; lingue di risposta sui social; nome o sigla con cui si firmano le risposte.
6. Titolo e indirizzo del codice di comportamento dell'ente e, se esiste, della sua social media policy (D.P.R. 62/2013, art. 11-ter, comma 4).
7. Periodo di conservazione dei contenuti rimossi e dei registri di moderazione.
8. Indirizzo email o PEC per segnalare contenuti illeciti o lesivi; tempo entro cui l'ente decide; ufficio o persona che riesamina le decisioni di moderazione.
9. Come l'ente ottiene il consenso alla pubblicazione di immagini di persone ritratte e di minori, e quando usa le eccezioni di legge.
10. Altre finalita' e durata della licenza concessa dagli utenti; licenza dei contenuti e dei dati aperti dell'ente; formula di attribuzione.
11. Ufficio e indirizzo per chiedere l'uso di logo e stemma; regolamento o delibera sull'uso dello stemma e del logo.
12. Ufficio stampa o portavoce (nome e recapito), o chi tiene i rapporti con i media.
13. Collegamento all'informativa su newsletter e messaggistica (o "non presenti").
14. Data dell'ultimo aggiornamento; come si informano gli utenti registrati delle modifiche; recapito per domande sulla policy.

Le regole di moderazione (sezioni 5, 7, 8) descrivono come funziona il servizio oggi (vedi par. 4): l'ente le conferma o le cambia con decisione scritta, come dice la nota iniziale.

## 4. Fatti dell'applicazione verificati nel codice (percorsi relativi a `laravel/`)

| Fatto usato nel testo | Dove |
|---|---|
| Commenti su novità ed eventi: scrive solo chi ha un account, gli ospiti leggono | `Themes/Sixteen/resources/views/components/blocks/blog/detail.blade.php:88-102`, `.../events/detail.blade.php:85-99`; `config/local/fixcity/comments.php:29` (`allow_anonymous_comments` false) |
| Commenti pubblicati subito, controllo dopo | `config/local/fixcity/comments.php:60` (`automatically_approve_all_comments` true); `Modules/Comment/app/Actions/Comment/ShouldAutomaticallyApproveCommentAction.php` |
| Il nome del profilo compare accanto a ogni commento | `Modules/Comment/resources/views/filament/widgets/comment/comment.blade.php:20`, `.../components/comment-item.blade.php:6` (dedotto dal codice, non provato a runtime) |
| Moderazione dei commenti da pannello: approva e rifiuta; "rifiuta" esegue `$comment->delete()` | `Modules/Comment/app/Filament/Resources/CommentResource/Tables/CommentsTable.php:101-107`, `Modules/Comment/app/Actions/Comment/RejectCommentAction.php:22` |
| Limite di invii: 5 per 60 secondi per utente o IP (nel testo non compaiono i numeri) | `Modules/Comment/app/Actions/Comment/EnsureCommentRateLimitAction.php:22,25` |
| Nessun pulsante "segnala contenuto" nei commenti | `rg -i "report\|segnala\|flag\|abuse" Modules/Comment/app/Filament/Widgets/Commentable/CommentsWidget.php`: nessun risultato |
| Una nuova segnalazione parte in stato `pending` (non pubblico) | `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php:118`, `Modules/Fixcity/app/Actions/GetTicketFormDataForPersistAction.php:34` |
| Stati visibili a tutti: aperto, in revisione, in lavorazione, in sospeso, risolto, chiuso, riaperto; non pubblici: bozza, in attesa | `Modules/Fixcity/app/Enums/TicketStatusEnum.php:46-57` e `:64-70`; etichette it in `Modules/Fixcity/lang/it/ticket_status_enum.php` |
| Lo scarto di un moderatore richiede una motivazione, registrata con visibilità solo-autore, lo stato resta non pubblico | `Modules/Fixcity/app/Actions/RejectTicketAction.php:23-56` |
| Campi pubblici della segnalazione: titolo, tipo, stato, giorno (non l'ora), indirizzo, coordinate, descrizione, cronologia; mai proprietario, email, autore, immagini originali | `Modules/Fixcity/app/Actions/BuildPublicTicketDetailAction.php:26,80-100` |
| Commenti sotto una segnalazione pubblica: leggono tutti, scrivono proprietario e chi ha il permesso di modifica | `Themes/Sixteen/.../ticket-detail/design-comuni.blade.php:36-38,138`; `Modules/Fixcity/app/Policies/TicketPolicy.php:18-40` |
| Eliminare una segnalazione: solo admin o permesso `ticket.delete` | `Modules/Fixcity/app/Policies/TicketPolicy.php:42-52` |
| Allegati: JPEG, PNG, PDF, massimo 5 file, 10 MB ciascuno | `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php:335-339` |
| Le foto non compaiono nella pagina pubblica ne' nel geojson | assenza di `images`/`attachments` in `ticket-detail/design-comuni.blade.php`, `ticket-list/design-comuni.blade.php`, `BuildTicketsGeoJsonAction.php` (rg senza risultati) |
| Footer: link a `/media-policy`; `social` vuoto in tutte le lingue | `config/local/fixcity/database/content/sections/footer.json:105,122,278,295` |
| Lingue del sito: it, en, de, es | `media-policy.index.json` (blocchi per lingua) |
| Cookie banner sempre attivo; cancellazione account via `DeleteAccountAction` (soft delete) | `config/local/fixcity/gdpr.php:7`; `Modules/Gdpr/app/Actions/Account/DeleteAccountAction.php` |

## 5. Fonti consultate il 2026-10-26 (esito)

Legenda: V = testo letto sulla fonte; P = letto in parte o via fonte secondaria; N = non letto.

| Norma / documento | URL | Esito |
|---|---|---|
| Linee guida per la promozione dei servizi digitali (SMP) | https://docs.italia.it/italia/designers-italia/lg-comunicazione-docs/it/stabile/doc/comunicazione-digitale.html | V (nessuna data nella pagina, sorgente `doc/comunicazione-digitale.rst`) |
| Design Comuni docs (assenza di «media policy») | https://docs.italia.it/italia/designers-italia/design-comuni-docs/it/versione-corrente/modello-sito-comunale.html e pagine sorelle del par. 2 | V (ricerca testuale) |
| Linee guida di design servizi web, bozza | https://docs.italia.it/italia/design/lg-design-servizi-web/it/bozza/requisiti.html | V (ricerca testuale) |
| L. 150/2000 artt. 1, 2, 6, 9 | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2000-06-07;150 (con `~art1`, `~art2`, `~art6`, `~art9`) | V; ultimo aggiornamento atto 28/12/2024 |
| D.P.R. 62/2013 artt. 11, 11-ter, 12 | https://www.normattiva.it/uri-res/N2Ls?urn:nir:presidente.repubblica:decreto:2013-04-16;62 (con `~art11`, `~art11ter`, `~art12`) | V; "ultimo aggiornamento all'atto pubblicato il 29/06/2023" |
| D.P.R. 81/2023 | https://www.sns.it/sites/default/files/2023-10/dpr812023.pdf | N (PDF non estraibile); contenuto confermato su Normattiva |
| CAD (D.Lgs. 82/2005) artt. 2, 52, 53 | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2005-03-07;82 (con `~art2`, `~art52`, `~art53`) | V; ultimo aggiornamento atto 20/04/2026 |
| L. 633/1941 artt. 70, 88, 96, 97, 110 | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:1941-04-22;633 (con `~art70` ecc.) | V (art. 87 non mostrato) |
| D.Lgs. 30/2005 art. 10 | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2005-02-10;30~art10 | V |
| L. 71/2017 art. 2 | https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2017-05-29;71~art2 | V; art. 2 in vigore dal 18-6-2017, nessuna modifica visibile; ultimo aggiornamento atto 24/04/2026 |
| Codice civile art. 10 | https://www.brocardi.it/codice-civile/libro-primo/titolo-i/art10.html | P (fonte secondaria; Normattiva non mostra il corpo) |
| Codice penale art. 595 | https://www.brocardi.it/codice-penale/libro-secondo/titolo-xii/capo-ii/art595.html e Gazzetta Ufficiale (ricerca) | P |
| Codice penale art. 604-bis | https://www.brocardi.it/codice-penale/libro-secondo/titolo-xii/capo-iii/sezione-i-bis/art604bis.html | P |
| Reg. UE 2022/2065 (DSA): art. 3 lett. g) iii), h), i); art. 6 par. 1; art. 8; art. 12; art. 14; art. 16; art. 17; considerando 13 | https://publications.europa.eu/resource/celex/32022R2065 (header `Accept: application/xhtml+xml`, `Accept-Language: ita`) | V sul testo italiano scaricato. `eur-lex.europa.eu` risponde 202 vuoto agli strumenti usati |
| Reg. UE 2016/679 (GDPR) artt. 8, 12 par. 3, 17 par. 1 e 3 | https://publications.europa.eu/resource/celex/32016R0679 (stessi header) | V |
| Garante: minori e foto online | https://www.garanteprivacy.it/temi/minori (rimanda a https://www.garanteprivacy.it/garante/document?ID=3766930) | V sulla pagina; il vademecum non e' stato aperto |
| D.Lgs. 196/2003 art. 2-quinquies (14 anni) | pagina Garante sopra e Gazzetta Ufficiale (ricerca) | P; non citato nel testo, perché una proposta in Senato (DDL 1136) potrebbe portare la soglia a 16 anni e la vigenza non e' stata verificata |
| Art. 15 D.L. 123/2023 (AGCOM coordinatore dei servizi digitali) | https://www.agcom.it/node/1069/printable/print (ricerca) | P; non citato nel testo |

Discrepanza con l'incarico: nel D.P.R. 62/2013 l'art. 11 e' "Comportamento in servizio" e non tratta i rapporti con il pubblico. I rapporti con il pubblico sono all'**art. 12**; l'uso dei social e' all'**art. 11-ter**, inserito dal D.P.R. 81/2023, con 5 commi (letti nel riepilogo di Normattiva: c. 1 opinioni non riconducibili all'amministrazione, c. 2 astenersi da commenti dannosi, c. 3 comunicazioni di servizio non in conversazioni pubbliche, c. 4 facolta' di adottare una social media policy nei codici, c. 5 divieto di divulgare documenti e informazioni). Il testo cita 11-ter commi 1 e 3 e 12 commi 1, 4, 5; i commi vanno riletti sul testo ufficiale prima della pubblicazione.

## 6. Scelte fatte

- **DSA: "coerente con", non "soggetto a".** Il testo dice che l'ente segue procedure coerenti con gli artt. 14, 16 e 17. Non afferma che il Comune sia prestatore di servizi di "hosting" (art. 3 lett. g) iii)). Il considerando 13 considera la sezione commenti di un quotidiano una funzione accessoria; se il Comune sia un "servizio della società dell'informazione" (art. 3 lett. a), che rinvia alla dir. 2015/1535) e' una questione per il legale.
- **Nessun obbligo di sorveglianza** (art. 8 DSA): il testo lo ricorda perché i commenti sono pubblicati senza controllo preventivo (dato di app).
- **Cyberbullismo (L. 71/2017 art. 2):** inserito perché il "gestore del sito internet" ha termini di 24 e 48 ore; l'interpretazione che includa il sito di un Comune e' da confermare.
- **Foto:** il testo dice che la pagina pubblica non le mostra ma i file "possono essere accessibili a chi ne conosce l'indirizzo", in linea con le note legali, che oggi scrivono "anche le foto allegate possono essere accessibili al pubblico" (modello `legal-notes.it.md`, riga 51). Se l'ente decide di non pubblicarle mai, i due testi vanno riallineati.
- **Stemma:** citato solo l'art. 10 D.Lgs. 30/2005 (non registrabile come marchio senza autorizzazione). Le regole d'uso dello stemma comunale sono dello statuto o di un regolamento dell'ente: segnaposto, nessuna norma citata a memoria.
- **Nomi file e posizione:** `resources/templates/media-policy.<lingua>.md`, come `legal-notes` e `privacy-policy`; JSON e lang della pagina non toccati (nessuna modifica era indispensabile).

## 7. Domande aperte

1. L'ente ha profili social e una media policy? Se no, la story DCP-231 (D231-2, DEC-03) propone di togliere il link dal footer e dalla sitemap invece di pubblicare il testo.
2. Chi decide la guardia (par. 1)? Va fatta insieme a quella di `terms`.
3. Il Comune e' soggetto al DSA? Serve un meccanismo di segnalazione elettronico di facile accesso (art. 16 par. 1): oggi esiste solo l'indirizzo email del segnaposto, nessun pulsante nell'app.
4. Il nome del profilo compare accanto ai commenti anche per l'autore di una segnalazione: puo' annullare l'anonimato che la pagina della segnalazione promette. Serve una regola sui nomi (pseudonimo) o la scelta di mostrare solo "Autore". Dedotto dal codice, non provato a runtime.
5. Foto: i file hanno URL raggiungibili? Si tolgono i metadati EXIF (posizione) al caricamento? Non verificato; il testo si limita a un consiglio all'utente.
6. Strumenti di moderazione: per le segnalazioni pubbliche non e' stato trovato un comando "nascondi" diverso da modifica del testo, cambio di stato o eliminazione (solo admin). Per i commenti "rifiuta" li cancella: se e' soft delete e quanto si conserva e' da verificare (segnaposto della conservazione).
7. Chiusura o sospensione dell'account per violazioni: le note legali la prevedono; lo strumento non e' stato verificato.
8. Pagine social dell'ente e contitolarita' del trattamento con la piattaforma: tema per il DPO (giurisprudenza CJUE sulle pagine di fan, citata solo a memoria, NON verificata).
9. Forma scritta della licenza sui contenuti degli utenti (L. 633/1941 art. 110: la trasmissione dei diritti "deve essere provata per iscritto"): una licenza non esclusiva accettata online basta? Da allineare con la sezione sulla licenza delle note legali.
10. L'ente ha un codice di comportamento con social media policy (D.P.R. 62/2013 art. 11-ter c. 4) e un ufficio stampa (L. 150/2000 art. 9: facoltativo)?

## 8. Che cosa NON e' stato verificato

Parere di un legale o del DPO; rendering con un file reale nel tenant (il test usa una Action finta); esistenza e funzionamento delle caselle indicate; comportamento a runtime di nome sui commenti, moderazione dei commenti e flusso di scarto delle segnalazioni (letti solo nel codice); i commi dell'art. 11-ter D.P.R. 62/2013 sul testo ufficiale; Reg. UE 2022/2065 oltre agli articoli elencati e la sua attuazione nazionale; D.Lgs. 70/2003; qualita' delle traduzioni en, de, es da parte di un traduttore giuridico madrelingua (terminologia allineata ai modelli `legal-notes.*`).

## 9. Prova automatica

`Modules/Fixcity/tests/Unit/MediaPolicyTemplateTest.php` (24 casi): 14 sezioni con testo e gli stessi dati dell'ente per sezione in it, en, de, es; titolo e nota iniziale; rimandi a privacy, cookie, note legali, accessibilita'; rimando interno fra sezioni 9 e 8 per titolo; nessun trattino lungo ne' emoji; resa del modello nella pagina `/{lingua}/media-policy` con una Action finta. Prova di sensibilita': togliendo una sezione dal modello tedesco il test fallisce solo per `de`. Comando: `export DB_DATABASE_LIMESURVEY= APP_CONFIG_CACHE=/nonexistent/config.php && bash bashscripts/tools/heavy-slot.sh bash bashscripts/tools/pest-scratch-db.sh Modules/Fixcity/tests/Unit/MediaPolicyTemplateTest.php`.
