---
title: "Contratto — GeocodeTicketAddressAction"
type: contract
action: "Modules\\Fixcity\\Actions\\GeocodeTicketAddressAction"
module: Fixcity
status: accepted
tags: [contract, action, geocoding, nominatim, servizio-esterno, cache]
created: 2026-11-26
updated: 2026-11-26
---

# GeocodeTicketAddressAction

Sorgente: `app/Actions/GeocodeTicketAddressAction.php`. Riempie `location.address` di un ticket con il reverse geocode di Nominatim (OpenStreetMap) a partire da latitudine e longitudine. Chiama un servizio esterno **dal
server** (non dal browser: il proxy `/api/geo/...` del modulo Geo, [ADR osm-proxy-tile-nominatim](../../../Geo/docs/adr/0003-osm-proxy-tile-nominatim.md), vale per il browser).

**Stato attuale:** `rg GeocodeTicketAddress` sotto `app/`, `routes/` e `Modules/*/app` trova solo la definizione: nessun codice di produzione la chiama (la citano i test `tests/Unit/Jobs/GeocodeTicketAddressJobTest.php`, che
conserva il nome di un Job che non esiste piu'). `CreateTicketAction` non la invoca. Il modulo Geo ha un geocoder inverso proprio
(`Modules/Geo/app/Actions/Nominatim/ReverseGeocodeAction.php`) che punta allo stesso endpoint (`GeoApiEndpoints::NOMINATIM_REVERSE`): duplicazione da decidere (Backlog test / codice morto).

## Input

- `Ticket $ticket` con `latitude` e `longitude` (stringhe) e `location` (array o nullo).

## Output

- `void`. L'esito si vede in `location` del ticket.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-GEOCODE-INV-1 | Un ticket senza coordinate (nulle o stringa vuota) non viene modificato: si registra l'avviso `Ticket missing coordinates` nel log con l'id del ticket. | `TICKET-GEOCODE-INV-1` |
| TICKET-GEOCODE-INV-2 | Con una risposta valida l'indirizzo (`display_name`) e' salvato in `location['address']` del ticket. | `TICKET-GEOCODE-INV-2` |
| TICKET-GEOCODE-INV-3 | Il risultato e' in cache per coppia latitudine/longitudine: un secondo ticket con le stesse coordinate non genera una seconda richiesta a Nominatim (chiave `geocode:reverse:{lat}:{lon}` e durata di un giorno sono nel codice, non asserite dal test). | `TICKET-GEOCODE-INV-3` |
| TICKET-GEOCODE-INV-4 | In caso di errore HTTP la richiesta si ripete una volta (2 tentativi in tutto); al secondo errore si registra l'errore nel log (id ticket e status) e non si scrive nulla. | `TICKET-GEOCODE-INV-4` |
| TICKET-GEOCODE-INV-5 | Una risposta senza `display_name` o con un corpo che non e' un oggetto non scrive nulla. | `TICKET-GEOCODE-INV-5` |
| TICKET-GEOCODE-INV-6 | La richiesta a Nominatim contiene solo le coordinate del ticket e il formato (`lat`, `lon`, `format=json`) piu' un `User-Agent` che identifica l'applicazione: nessun dato del cittadino. | — (non dimostrato: il test controlla `lat` e `lon`, non l'assenza di altri parametri) |
| TICKET-GEOCODE-INV-7 | Le altre chiavi di `location` esistenti sono preservate; vengono scritte `address`, `display_name`, `provider = nominatim` e le coordinate (`latitude`, `longitude`, `lat`, `lng`). | — (non dimostrato) |

## Effetti collaterali

In quest'ordine:

1. Controllo delle coordinate (esce con `Log::warning` se mancano).
2. Lettura della cache; se presente, passa direttamente alla scrittura.
3. Fino a 2 `GET https://nominatim.openstreetmap.org/reverse` in sincrono (nessun timeout dichiarato, nessuna attesa fra i tentativi).
4. Scrittura della cache (`Cache::put`, 1 giorno) con il nome visualizzato.
5. `forceFill(['location' => ...])->save()` sul ticket (`UPDATE tickets`, un solo salvataggio).

## Errori

- Nessuna eccezione propria: errori HTTP, risposte malformate e coordinate mancanti diventano un ritorno a vuoto (avviso o errore nel log).
- Un'eccezione di trasporto di `Http` (connessione, timeout del client) non e' catturata e propaga a chi chiama.
- Limite noto: la politica d'uso di Nominatim chiede al massimo una richiesta al secondo e un `User-Agent` identificabile; l'Action non limita la frequenza ne' attende fra i tentativi. Usata su molti
  ticket di seguito potrebbe essere bloccata.
- Limite noto: le coordinate sono chiave di cache e vanno nell'URL dell'API esterna: sono dati di posizione, ma non identificano la persona da soli.
- Limite noto: `location['address']` viene sovrascritto con il nome restituito da Nominatim, anche se il ticket ne aveva gia' uno.

## Non fa

- Non e' invocata da nessun flusso di produzione (vedi Stato attuale).
- Non converte indirizzo in coordinate (geocoding diretto): solo reverse.
- Non passa dal proxy del modulo Geo e non riusa `ReverseGeocodeAction`.
- Non invia dati del cittadino o del ticket a Nominatim: solo coordinate.
- Non aggiorna la colonna `latitude`/`longitude` del ticket.
