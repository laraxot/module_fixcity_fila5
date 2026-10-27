---
title: Informativa privacy art. 13 GDPR, fonti, scelte e domande aperte
date: 2026-10-26
status: bozza di alta qualita, richiede revisione DPO e legale
scope: Modules/Fixcity/resources/templates/privacy-policy.{it,en,de,es}.md
---

# Informativa privacy art. 13 GDPR: fonti, scelte e domande aperte

Questo documento accompagna i quattro template `resources/templates/privacy-policy.{it,en,de,es}.md`. Non attesta la conformita dell'ente: elenca cosa e stato scritto, perche, su quali fonti, cosa l'ente deve ancora fornire e cosa non e stato possibile verificare. Il testo e una bozza da far rivedere al DPO e al legale dell'ente.

## Come funziona la pagina

- La pagina `/it/privacy` legge `policy.md` del tenant (`config/local/fixcity/lang/<lingua>/policy.md`, poi `config/local/fixcity/policy.md`) tramite `GetPublishedLegalDocumentAction`. Il template del modulo e solo la base da cui partire.
- `AssessPrivacyPolicyAction` valuta 10 sezioni (controller, dpo, purposes, recipients, transfers, retention, rights, complaint, mandatory, automated). Una sezione e `missing` se manca il titolo, `placeholder` se contiene `[[...]]`, `Edit this file`, `lorem ipsum`, TODO/TBD/FIXME, `example.com|org|it`, `xxxx`.
- Il wizard di segnalazione (`TicketForm::getGdprHtml`) mostra l'intero `policy.md` al passo 1.
- Passaggio di messa in linea (non eseguito da me, fuori dalla mia area): copiare il template completato in `laravel/config/local/fixcity/lang/<lingua>/policy.md` dopo che l'ente ha sostituito tutti i campi segnati.

## Stato per sezione

| Sezione | Stato | Cosa deve fornire l'ente |
|---|---|---|
| Titolare del trattamento | dipende dall'ente | denominazione, indirizzo della sede, email, PEC |
| Responsabile della protezione dei dati | dipende dall'ente | nome (persona o societa) e email del DPO |
| Finalita e base giuridica | completa | nulla (verificare gli atti da citare, domanda 1) |
| Destinatari dei dati | dipende dall'ente | elenco dei fornitori nominati responsabili (art. 28): hosting, assistenza software, invio email |
| Trasferimenti verso paesi terzi | parzialmente completa | sede di server, hosting e posta; eventuali trasferimenti extra SEE e relative garanzie. La parte sui fornitori di mappe e gia scritta |
| Periodo di conservazione | dipende dall'ente | tempi per: account, segnalazioni e allegati, registri di sicurezza e prove di accettazione, trattamento dopo la cancellazione dell'account |
| Diritti dell'interessato | completa | nulla |
| Reclamo all'autorita di controllo | completa | nulla |
| Natura del conferimento dei dati | completa | nulla |
| Processi decisionali automatizzati | completa | nulla |

I campi da compilare sono 12 per lingua, nel formato `[[DA COMPILARE: ...]]` (it), `[[TO BE COMPLETED: ...]]` (en), `[[AUSZUFÜLLEN: ...]]` (de), `[[A COMPLETAR: ...]]` (es). Le sezioni ricavabili dall'applicazione non contengono segnaposto: l'ente vi e indicato come "il Comune" o "il Titolare".

Il pulsante di pubblicazione resta rosso (guard) finche restano segnaposto in controller, dpo, recipients, transfers, retention.

## Scelte giuridiche

1. **Base giuridica generale: art. 6.1.e GDPR**, con base nell'ordinamento ai sensi dell'art. 2-ter D.Lgs. 196/2003 (comma 1 e comma 1-bis per i compiti della PA). Il legittimo interesse non e invocabile dall'ente nello svolgimento dei compiti pubblici (art. 6.1, ultimo periodo). Riferimenti funzionali: art. 13 TUEL (D.Lgs. 267/2000) e art. 8, comma 2, L. 150/2000 (URP).
2. **Sicurezza e prove di accettazione**: art. 6.1.c con art. 32 GDPR (obbligo di adottare misure adeguate), IP e user agent conservati nei registri.
3. **Modulo di segnalazione accessibilita**: art. 6.1.c (L. 4/2004 art. 3-quater, Dir. (UE) 2016/2102 art. 7).
4. **Aggiornamenti e inviti facoltativi**: unica finalita su consenso (art. 6.1.a), revocabile; soglia 14 anni (art. 2-quinquies D.Lgs. 196/2003).
5. **Cookie**: solo tecnici (art. 122 D.Lgs. 196/2003, nessun consenso richiesto per i soli cookie tecnici).
6. **Casella "presa visione"** dell'informativa: nel testo e qualificata come presa visione, non come consenso (il consenso non e la base del trattamento principale).
7. **Portabilita (art. 20)**: esclusa per i trattamenti basati sull'art. 6.1.e (art. 20(3)); cancellazione (art. 17) limitata dall'art. 17(3)(b); opposizione (art. 21) esposta.
8. **Reclamo**: Garante (art. 77) e ricorso giudiziario (art. 79), come vie alternative (art. 140-bis D.Lgs. 196/2003).
9. **Nessun processo decisionale automatizzato** (art. 22): nessun uso di IA nei flussi del cittadino (verificato sul codice, vedi sotto).
10. **Trasferimenti**: citate le decisioni di adeguatezza per UK (decisione (UE) 2021/1772, rinnovata il 19.12.2025) e per gli USA (Data Privacy Framework, decisione (UE) 2023/1795) per OSMF/Nominatim, OSM France, Esri e Google.
11. **Lingue**: italiano e il testo fonte; en/de/es usano la terminologia ufficiale del GDPR (DSGVO in tedesco, RGPD in spagnolo). Tedesco con "Sie", spagnolo con "tu", inglese con "you". Stessi titoli di sezione dei file `lang/*/privacy.php`.

## Fatti dell'applicazione verificati nel codice

Percorsi relativi a `laravel/Modules/` salvo diversa indicazione.

- Creazione segnalazione solo con login: `Fixcity/app/Actions/CreateTicketAction.php:24-29`.
- Campi e allegati del wizard: `Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php` (stato `pending` nascosto :258, priorita :257, geolocalizzazione e geocodifica inversa :272-275, massimo 5 file :335, tipi jpeg/png/pdf :338, 10 MB :339, casella presa visione :171 non persistita).
- Visibilita pubblica: `Fixcity/app/Enums/TicketStatusEnum.php:46-57` (`canViewByAll`); `ApproveTicketAction` (da pending a in_review, moderazione), `RejectTicketAction` (resta non pubblica, motivo visibile solo all'autore); `BuildPublicTicketDetailAction` (mai owner_id, email, autore; codice di tracciamento solo al proprietario; solo il giorno di creazione).
- Immagini esposte: `BuildTicketPublicDetailsPayloadAction.php:38-46` e API `Fixcity/resources/views/pages/api/ticket-details/[ticket].blade.php`. Nessuna rimozione EXIF trovata.
- Notifiche: `DeliversByDatabaseAndVerifiedMail.php:30` (database sempre, email solo se verificata e preferenza attiva), `CanUserReceiveTicketMailAction.php:25`, `GetFixcityEmailNotificationPreferenceAction.php:31` (default attivo), `RecordTicketCommunicationAction.php:33`, valutazione `SubmitCitizenTicketRatingAction`, segui `FollowTicketAction`.
- Esercizio dei diritti: `Fixcity/lang/it/personal_area_settings.php:73` (l'area personale non permette oggi download o cancellazione autonomi, richiesta all'ente).
- Registrazione: `Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php` (nome, cognome, email, password, privacy, termini, marketing :40; limite per IP :124; log IP e user agent :174); password con hash `User/app/Models/BaseUser.php:476`; registro dei consensi `Gdpr/app/Actions/SaveGdprConsentsAction.php:67-73` (ip_address, user_agent, locale, document_version); `User/app/Actions/Activity/LogRegistrationAction.php:24`.
- Cancellazione account solo soft delete: `Gdpr/app/Actions/Account/DeleteAccountAction.php:75`. Nessun job di pulizia o conservazione trovato. `config/local/fixcity/activitylog.php:16`: 365 giorni (default del pacchetto, nessuna pianificazione verificata). `Gdpr/config/config.php`: soglie generiche (5/1/10 anni) non applicate dal codice.
- Login Google: `User/app/Actions/Socialite/CreateSocialiteUserAction.php:32-36` (provider_id, nome, email, avatar); pulsante visibile solo se configurato (`laravel/Themes/Sixteen/resources/views/components/utilities/google-button.blade.php:9`).
- Mappe e geocodifica: `Geo/resources/js/components/map/layers.js:3-6` (tile OSM, OSM France HOT, Esri immagini e topo), `map/controls/search.js:7,219` e `map/reverse-geocode.js:5` (Nominatim dal browser), `Geo/app/Filament/Forms/Components/Traits/HasCoordinatePicker.php:250,301` (Nominatim lato server), geolocalizzazione `map/events.js:221-227` e `map/controls/geolocation.js:43-57`; mappa statica `staticmap.openstreetmap.de` in `ticket-location-map.blade.php:11` (back office).
- Cookie: solo tecnici (`Gdpr/app/Actions/Cookie/GetCookieInventoryAction.php:37-39`, `Gdpr/config/config.php:35` extra vuoto); nessun analytics o pubblicita trovato; nessun import di Google Fonts in `laravel/Themes/Sixteen/resources/css/app.css:9`.
- Modulo accessibilita: `Fixcity/app/Livewire/AccessibilityFeedbackForm.php:38-57` (problema, pagina, email facoltativa, throttle per IP).
- Ricerca per codice di tracciamento con throttle per IP: `GetPublicTicketByCodeAction.php:42`.
- Nessun uso di IA nel codice di Fixcity, User, Notify, Geo, Media, Comment (il modulo AI e abilitato ma non richiamato).

## Fonti consultate

- GDPR, testo consolidato nelle quattro lingue (Cellar): https://publications.europa.eu/resource/celex/02016R0679-20160504 (artt. 6, 7, 9, 12-14, 15-22, 28, 29, 32, 37, 38, 77, 79; terminologia ufficiale).
- D.Lgs. 196/2003, art. 2-ter (in vigore dall'8.12.2021, D.L. 139/2021 conv. L. 205/2021, comma 1-bis): https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2003-06-30;196~art2ter
- D.Lgs. 196/2003, art. 2-quinquies (14 anni) e art. 140-bis (forme alternative di tutela), stesso schema di URN su Normattiva.
- TUEL art. 13: https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:decreto.legislativo:2000-08-18;267~art13
- L. 150/2000 art. 8: https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2000-06-07;150~art8
- Garante, come presentare un reclamo (PEC protocollo@pec.gpdp.it accettata solo da PEC, Piazza Venezia 11, 00187 Roma, artt. 140-bis e seguenti): https://www.garanteprivacy.it/diritti/come-agire-per-tutelare-i-tuoi-dati-personali/reclamo
- Commissione europea, decisioni di adeguatezza: https://commission.europa.eu/law/law-topic/data-protection/international-dimension-data-protection/adequacy-decisions_en (UK rinnovata il 19.12.2025; DPF del 10.7.2023; relazione di riesame 9.10.2024).
- Decisioni (UE) 2023/1795 e (UE) 2021/1772 (titoli verificati sul Cellar nelle quattro lingue).
- Causa Latombe c. Commissione, T-553/23: Tribunale UE ha respinto il ricorso il 3.9.2025; impugnazione C-703/25 P pendente a ottobre 2026.
- Esri, adesione al DPF dichiarata dal fornitore: https://trust.arcgis.com/en/compliance/dpf.htm
- Google LLC nel DPF: https://www.dataprivacyframework.gov/participant/5780 ; https://policies.google.com/privacy/frameworks ; Google Ireland come titolare: https://business.safety.google/controllerterms/
- OpenStreetMap Foundation, privacy policy: https://osmfoundation.org/wiki/Privacy_Policy ; policy Nominatim: https://operations.osmfoundation.org/policies/nominatim/
- EDPB, Linee guida 05/2021 e Raccomandazioni 01/2020: solo tramite sintesi di ricerca, non lette nel testo.
- Registri fonti gia presenti: `01-matrice-conformita-normativa.md`, `02-fonti-privacy-sicurezza-verificate.md`, `03-fonti-cad-accessibilita-verificate.md` (stessa cartella).
- Digital Omnibus: le modifiche al GDPR non risultano adottate a ottobre 2026 (mandato del Consiglio 21.10.2026, Parlamento senza voto); il testo si basa sul GDPR vigente.

## Cosa non ho potuto verificare

- Elenco vivo del Data Privacy Framework per Esri (la pagina e solo JavaScript): l'adesione e quella dichiarata dal fornitore.
- Testo primario della decisione di rinnovo UK del 19.12.2025 e scadenza 27.12.2031: ricavate da fonti secondarie.
- Nota informativa EDPB sul DPF: non trovata.
- Art. 2-quaterdecies e art. 152 del Codice privacy: non letti.
- Hosting, provider di posta e fornitori in produzione: non determinabili dal repository.
- Se il login Google sia attivo in produzione e se esistano comunicazioni di marketing reali.
- Dove l'utente inserisce il numero di telefono nel profilo (il testo dice che non e mai pubblicato).
- Localizzazione effettiva dei server CDN di OSMF e di Esri.
- Nessuna prova di "conformita": il testo e una bozza.

## Domande aperte per DPO e legale

1. Atto del Comune che istituisce il servizio o norma specifica da citare accanto all'art. 6.1.e (regolamento comunale, delibera).
2. Base e limiti della pubblicazione di testo e foto delle segnalazioni: l'EXIF non viene rimosso, le immagini sono servite dall'API pubblica, la moderazione non verifica i dati personali. Valutare sfocatura automatica o rimozione dei metadati.
3. Foto con persone riconoscibili e categorie particolari (art. 9): serve una regola operativa per l'operatore.
4. La registrazione dice "accetto" mentre l'informativa dice che non e consenso; la revoca del consenso di marketing non ha una funzione self service (art. 7(3)). Il testo di registrazione in en/de/es e riempitivo (`Gdpr/lang/en/register.php:102` "pizza tips", de:99, es:99; titolo "Pizza Revolution").
5. Conservazione: scegliere i tempi, e dotarsi di un job di pulizia (non esiste). La cancellazione account e solo logica. Il log attivita ha 365 giorni (default del pacchetto).
6. Necessita di DPIA (geolocalizzazione, foto), registro dei trattamenti (art. 30), nomine art. 28.
7. Contitolari o altri enti destinatari delle segnalazioni (es. gestori di servizi pubblici).
8. Soglia di eta del servizio e minori.
9. Fornitori di mappe: la policy Nominatim vieta l'autocompletamento lato client; valutare tile e geocodifica self-hosted o proxy per non trasmettere l'IP dei cittadini a terzi.
10. Il passo 1 del wizard mostra l'intera informativa (lunga): valutare una informativa a livelli (sintesi + testo completo).
11. Chiave Google Maps scritta nel codice in `laravel/config/local/fixcity/google.php` e `Fixcity/config/google.php` (non riportata qui): ruotare e spostare in `.env`.
12. Commento obsoleto nel blade sulla "posizione approssimativa da IP": il codice usa il fuso orario del browser.
13. Regex del guard (`AssessPrivacyPolicyAction`) case-insensitive: la parola spagnola "todo" attiverebbe il segnaposto (evitata nel testo); valutare `\b` con maiuscole.
14. Tedesco con "Sie" nell'informativa e "du" nelle stringhe dell'app: scegliere un registro unico.
15. Versionamento: allineare la versione del testo a `Treatment.documentVersion` ('1.0').

## Verifica

- `Modules/Fixcity/tests/Feature/Pages/PrivacyPolicyPageTest.php`: 17 passati (120 asserzioni).
- `Modules/Fixcity/tests/Feature/Pages/TicketPrivacyPublicationTest.php`: 3 passati (17 asserzioni).
- `Modules/Fixcity/tests/Unit/AssessPrivacyPolicyActionTest.php`: 13 passati, 4 falliti. Il caso "ships a template per locale with every section and only placeholders as content" (riga 90) si aspetta `placeholder` per tutte e 10 le sezioni: vale per lo scheletro precedente, non per il nuovo testo. Correzione suggerita al proprietario del test: attendere `placeholder` solo per controller, dpo, recipients, transfers, retention, e l'assenza delle altre cinque.
- Sonda in sola lettura (13 test, scartata): per it/en/de/es la valutazione restituisce esattamente controller, dpo, recipients, transfers, retention come `placeholder`; sostituendo i `[[...]]` con un valore la valutazione restituisce `[]`. Parita tra le lingue: 12 segnaposto, 10 titoli H2, 9 finalita numerate; nessuna emoji e nessun trattino lungo.
