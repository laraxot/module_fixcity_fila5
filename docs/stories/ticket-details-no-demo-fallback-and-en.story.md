# API ticket-details senza fallback demo + inglese di fixcity::ticket

- Stato: done (2026-10-07)
- Fase BMAD: Quick Flow
- Owner: Modules/Fixcity

## Problemi

1. `GET /api/ticket-details/{id}`: se il ticket esiste ma non e visibile a tutti (stato `pending`, solo il proprietario lo vede),
   la pagina Folio ricadeva sul file demo `public_html/data/tickets.json` e rispondeva con il ticket demo che ha lo stesso id
   ("Buca profonda in via Morandi" al posto di "buche"). Il popup mostrava dati di un altro ticket.
2. `fixcity::ticket` aveva 160 chiavi italiane senza traduzione inglese (57 testi veri, il resto rumore di chiavi backoffice).

## Modifiche

- `resources/views/pages/api/ticket-details/[ticket].blade.php`: `abort_if(Ticket::whereKey($ticket)->exists(), 404)` prima del fallback demo.
- `lang/en/ticket_fo.php` (nuovo, unito da `en/ticket.php`): stati, priorita, contatti, modal dettaglio, informativa privacy.

## Verifica

- Ospite: `/api/ticket-details/1` (ticket reale pending) -> 404; `/api/ticket-details/87` (solo demo) -> 200.
- Audit it/en con il traduttore Laravel: `fixcity::ticket` 160 -> 104 mancanti (resto: chiavi backoffice senza testo reale).
- `/en/tickets` senza stringhe italiane (confronto automatico del testo it/en).

## Aperto

- `/api/tickets/geojson` per ospiti non include i ticket `pending` (voluto: moderazione). Il proprietario li vede.
- Fallback demo ancora attivo per id non presenti nel DB: da rimuovere quando il dataset demo non serve piu.
