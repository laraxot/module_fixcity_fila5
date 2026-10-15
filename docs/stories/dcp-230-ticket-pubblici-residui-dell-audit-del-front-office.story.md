---
id: dcp-230
title: "DCP-230: ticket pubblici, residui dell'audit del front office (geocoding, foto, conteggi, date, noindex, codice nell'URL, viste morte)"
type: story
epic: dc-parity-v2
status: open
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-15
updated: 2026-10-15
qmd: "ticket pubblici elenco dettaglio tracciamento conferma nominatim geocoding foto exif conteggio marker ticket_id date localizzate plurale stelle noindex codice nell url throttle viste morte ticket-list audit front office gruppo b"
related: [dcp-186, dcp-200, dcp-203, dcp-149, dcp-150, dcp-148]
tags: [front-office, ticket, bmad, audit]
---

# DCP-230: ticket pubblici, i residui

## Fase BMAD
Plan. Dal gruppo B dell'audit [DCP-186](../../../../Themes/Sixteen/docs/bmad/stories/dcp-186-front-office-scopo-e-senso-di-ogni-pagina-ed-elemento.story.md), scheda in `Themes/Sixteen/docs/compliance/03-front-office-scheda-per-pagina.md`. Le voci B1 (pulsante «Cerca»), B2 (layout di `tickets/track`) e B18 (modale filtri) sono in DCP-195; B3, B4, B5, B7, B10 e B19 in DCP-203 e nel registro DCP-200 (DEC-20, DEC-24). **Qui stanno le altre.** MISURATO = osservato dall'agente del gruppo B; DEDOTTO = letto nel codice.

## Scopo
Chi guarda le segnalazioni pubbliche (ospite o cittadino) deve vedere dati veri, coerenti e senza esporre piu' del necessario: l'elenco e la mappa, il dettaglio, il tracciamento con codice e la conferma.

## Cose da fare
- [ ] **B6 geocoding (MISURATO + DEDOTTO).** La ricerca di indirizzo della mappa manda ogni testo digitato a Nominatim con autocompletamento e mostra risultati di altri Comuni. Proxy lato server che non inoltra altro, limite per IP, risultati limitati al territorio del Comune; dichiarato nell'informativa (DEC-14 e DCP-137). Test del proxy con Nominatim simulato.
- [ ] **B8 foto ed EXIF (DEDOTTO, non provato: nessun ticket di prova ha foto).** `/api/ticket-details/{id}` restituisce `images` con gli originali, contro la decisione di non mostrare mai foto senza miniatura e senza togliere l'EXIF (posizione GPS e data dello scatto). Provare con un ticket con foto e un JPEG con EXIF reale; servire solo miniature ri-codificate.
- [ ] **B9 conteggi (MISURATO).** Per l'utente loggato la pagina elenco dice «12 Risultati» e la mappa ha 21 marker: due sorgenti diverse per lo stesso insieme. Una sola query, usata da elenco, mappa e contatore; test con un utente che ha segnalazioni proprie non pubbliche.
- [ ] **B13 sottotitolo «risolte negli ultimi 12 mesi» (MISURATO).** Conta solo `RESOLVED` e usa `updated_at`; la definizione va decisa (D230-3).
- [ ] **B14 pubblico gia' in `in_review` (DEDOTTO dal modello).** Un ticket diventa visibile a tutti prima che l'operatore abbia finito la revisione del testo (`TicketStatusEnum::canViewByAll()`). E' la decisione DEC-13 del registro: va decisa prima di toccare il codice.
- [ ] **B15 ramo `?ticket_id=` (MISURATO + DEDOTTO).** Non e' generato da nessuna interfaccia e per un utente loggato risponde 404 per un ticket inesistente e 403 per uno non pubblico: rivela l'esistenza dei ticket privati. Togliere il ramo o uniformare a 404 (D230-2).
- [ ] **B16 date non localizzate (MISURATO).** In de e es compaiono «28 September 2026» e «28 septiembre 2026»; usare la localizzazione della data (Carbon `isoFormat` con la lingua attiva) in tutte le viste, con test per le 4 lingue.
- [ ] **B17 plurale (MISURATO).** «Valuta 1 stelle su 5» in it, en, es: plurale a una stella (`trans_choice`). Si risolve da solo se il widget viene tolto (DEC-20), altrimenti correggere.
- [ ] **B20 `noindex` (MISURATO).** Nessun `noindex` su `/tickets/track?code=...` e su `/tickets/confirmation`; `/tickets/{id}` non e' in sitemap. Il codice di tracciamento e' un capability token: la risposta con `?code=` non deve mai essere indicizzata ne' finire in una cache condivisa (`X-Robots-Tag: noindex, nofollow` e `Cache-Control: private, no-store`). Test sugli header.
- [ ] **B21 codice nell'URL e throttle (DEDOTTO, l'undicesima richiesta ha dato 429 sulla fixture).** Il codice sta nella query (va nei log di accesso, nell'header `Referer`, nella cronologia); la ricevuta consuma 2 delle 10 richieste al minuto per IP. Decidere se il codice passa in POST e se la pagina di conferma usa un token di sessione e non il codice (D230-4).
- [ ] **B22 viste morte (DEDOTTO per grep).** 11 partial in `ticket-list/*` e `tickets/index.blade.php` con numeri fissi non sono usati da nessuna pagina; `Modules/Fixcity/resources/views/tickets/show.blade.php` stampa `{!! $ticket->content !!}` ma non e' servita da nessuna rotta. Cancellare e' vietato: rinominare con `.old` (regola del progetto) dopo aver verificato con `route:list` e grep, in un commit a parte.
- [ ] **B12 numeri verdi diversi** (`tel:050505` nel blocco elenco e 800 016 123 nel footer): in DCP-198 (recapito unico, D198-1).
- [ ] Aggiornare la scheda per pagina con il verdetto di ogni voce chiusa.

## Decisioni da prendere
| ID | Domanda | Opzioni | Proposta | Chi decide | Blocca |
|---|---|---|---|---|---|
| D230-1 | Geocoding: chi e' il fornitore e cosa parte | Nominatim pubblico via proxy; istanza propria; Photon/pelias del fornitore | proxy lato server verso un servizio con accordo; Nominatim pubblico solo in sviluppo (la sua policy vieta l'uso intensivo) | titolare, fornitore | B6, informativa |
| D230-2 | `?ticket_id=` | togliere; tenere con 404 uniforme | togliere: nessuna interfaccia lo usa | sviluppo | B15 |
| D230-3 | Sottotitolo «risolte negli ultimi 12 mesi» | solo `RESOLVED`; `RESOLVED` e `CLOSED`; data di chiusura invece di `updated_at` | `RESOLVED` e `CLOSED` con la data in cui hanno cambiato stato (la timeline ce l'ha) | prodotto | B13 |
| D230-4 | Codice di tracciamento | resta in query; passa a POST con redirect a una pagina con token di sessione | POST e token di sessione per la conferma; la query `?code=` resta solo per il link ricevuto per email, con `noindex` e `no-store` | prodotto, DPO | B21 |
| D230-5 | Foto pubbliche | mai; solo miniatura senza EXIF; originale al proprietario | solo miniatura ri-codificata senza EXIF, originale solo al proprietario e agli operatori | titolare, DPO | B8 |

## Criteri di accettazione
- [ ] Per ogni voce un test rosso prima e verde dopo, oppure una misura in browser prima e dopo quando il test non e' possibile (B6, B16 sul sito).
- [ ] `FollowedTicketsPageTest`, `TicketPublicDetailPageTest`, `PublicPagesStructureTest` e la suite Filament di Fixcity restano verdi; PHPStan su `Modules` a zero.
- [ ] Nessun ticket non pubblico visibile a un ospite o a un altro utente (test con tre utenti).

## Non verificato
Foto ed EXIF reali; la pagina 429 con corpo; la timeline con voci e `reason` (nessun ticket di prova ha attivita'); il flusso dopo l'invio con la sessione reale.
