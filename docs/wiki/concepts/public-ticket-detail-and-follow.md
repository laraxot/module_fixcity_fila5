---
title: "Dettaglio pubblico della segnalazione e funzione Segui"
type: concept
tags: [fixcity, ticket, public, privacy, follow, design-comuni, dcp-148, dcp-152]
created: 2026-10-13
updated: 2026-10-14
qmd: "dettaglio pubblico segnalazione tickets id segui follow BuildPublicTicketDetailAction FollowTicketAction occurred_date marker-icon-url"
related:
  - ticket-citizen-timeline.md
  - ../../../../../Themes/Sixteen/docs/bmad/stories/dcp-148-dettaglio-pubblico-segnalazione-e-segui.story.md
  - ../../../../Geo/docs/stories/dcp-152-marker-dettaglio-ticket-e-pulsanti-mappa-su-pagine-dc.story.md
status: active
---

# Dettaglio pubblico della segnalazione e "Segui"

`/{lang}/tickets/{id}` (id numerico) e' la pagina PUBBLICA di una segnalazione gia' presa in carico. Che le card di `/{lang}/tickets` la colleghino non e' stato verificato in questa revisione.

Nota di revisione 2026-10-14: ogni affermazione sotto e' stata riletta sul codice (Action, route, blocco del tema, test). Dove c'e' scritto `(non verificato)` il codice non basta a provarla e nessun test e' stato eseguito.

## Chi serve la pagina
Il percorso e' `Themes/Sixteen/resources/views/pages/[container0]/[slug0]/index.blade.php`: con `$container0 === 'tickets'` e `$slug0` numerico la pagina speciale e' `ticket-detail`. Chiama `BuildPublicTicketDetailAction::execute((int) $slug0, auth()->user())`, risponde 404 se il risultato e' `null`, usa `title` e `meta_description` per titolo e meta, la avvolge nel componente `layouts.design-comuni` (`components/layouts/design-comuni.blade.php`) e include `components/pages/ticket-detail.blade.php`, che a sua volta include il blocco. Il modulo ha anche una pagina Folio nativa Tailwind, `Modules/Fixcity/resources/views/pages/tickets/[id].blade.php` (nome `tickets.detail`, usa `BuildPublicTicketsQueryAction` e non l'Action del dettaglio): quale delle due risponda a runtime col tema Sixteen attivo (non verificato).

## Una sola fonte dei campi esposti
`Actions/BuildPublicTicketDetailAction::execute(int $ticketId, ?Authenticatable $viewer = null)` restituisce un array esplicito o `null` (-> 404). Chiavi: `id, title, type, type_label, status, status_label, created_date (Y-m-d), address, latitude, longitude, description, meta_description, timeline, is_following`. La query seleziona solo `id, name, content, type, status, location, latitude, longitude, created_at`: mai `code`, `owner_id`, email, autore, immagini.
- `meta_description`: descrizione con spazi compattati, al massimo 160 caratteri (157 piu' `...`).
- `latitude` e `longitude` sono `null` se mancano o sono entrambe zero: senza punto il blocco non disegna la mappa.
- `address` viene da `location.address`, poi `location.display_name`, altrimenti stringa vuota.
- `is_following`: esiste una riga `TicketSubscriber` per ticket e utente (sempre `false` per l'ospite).
- Il test `BuildPublicTicketDetailActionTest` («exposes exactly the public fields and nothing else») confronta le chiavi con `array_keys`: aggiungere un campo deve romperlo.

Visibilita' = `BuildPublicTicketsQueryAction::execute()`: richiede `location` non nulla; l'ospite vede gli stati di `TicketStatusEnum::canViewByAll()`, chi e' loggato vede quelli piu' le proprie segnalazioni (`owner_id`). Conseguenza: una segnalazione senza `location` risponde 404 anche in uno stato pubblico. Il proprietario apre la propria segnalazione non ancora pubblica (test unitario «lets the owner open the own report even when it is not public yet»; `CitizenMyTicketsDetailLinkTest.php` esiste, il suo contenuto non e' stato riletto).

## Cronologia
`BuildTicketTimelineAction` restituisce per ogni voce `occurred_at` (`d F Y H:i`, con ora), `occurred_date` (`j F Y`, solo giorno), `occurred_on` (`Y-m-d`, per `<time datetime>` nel markup), `from_label`, `to_label`, `reason` (ordine fissato dal test). Le pagine pubbliche mostrano solo il giorno, in un elemento `<time>`.

## Segui
`POST /{lang}/tickets/{ticket}/follow` e' registrata in `laravel/routes/web.php` (le route dei moduli non sono caricate): `lang` `[a-z]{2}`, `ticket` numerico, middleware `auth` e `throttle:30,1`, nome `tickets.follow`. Il form del blocco ha `@csrf`; che il gruppo `web` applichi il controllo CSRF (non verificato).
`Actions/FollowTicketAction` e' invocabile (non closure, per `route:cache`): 403 se l'utente non e' un `UserContract`, 404 se la segnalazione non e' visibile con la regola sopra, valida `following` (obbligatorio, booleano: 1 segui, 0 smetti), poi chiama `SetTicketSubscriptionAction`, che autorizza `view` con il Gate, lavora in transazione ed e' idempotente (stato esplicito). Redirect a `/{lang}/tickets/{ticket}` con flash `ticket_follow` uguale a `followed` o `unfollowed`: la CHIAVE, non il testo, perche' la pagina lo traduce nella lingua del proprio URL (`fixcity::ticket_detail.follow.*`, `role="status"`). Dopo `followed` il blocco aggiunge un link a `/{lang}/area-personale/seguite` (pagina di destinazione non verificata).
Nel blocco: il cittadino loggato vede un form che invia il valore opposto allo stato corrente; l'ospite vede un link a `/{lang}/auth/login` senza parametri di ritorno (se il login riporti al dettaglio non e' verificato). Dopo ogni modifica alla route: `php artisan route:cache` da `laravel/` (non verificato in questa revisione).

## Vista
Blocco `Themes/Sixteen/resources/views/components/blocks/ticket-detail/{design-comuni,timeline}.blade.php`, regole proprie in `Themes/Sixteen/resources/css/app/19-ticket-detail.css`. Testi: `fixcity::ticket_detail` (it, en, de, es: i quattro file esistono).

Mappa: `<coordinate-picker-lit readonly zoom="16">`, solo se c'e' il punto. Gli attributi del marker vengono dalle stesse Action che costruiscono il GeoJSON (DCP-152):
- `marker-icon-url` = `iconUrl` di `ResolveTicketTypeMarkerPropertiesAction::execute($detail['type'])` (file `fixcity::svg/{tipo-kebab}.svg`, ripiego `other.svg`);
- `marker-color` = `color` di `ResolveTicketStatusMarkerPropertiesAction::execute($detail['status'])` (hex; i token Filament sono mappati, ripiego `#607d8b`);
- `marker-label` = `type_label`, il nome del tipo.
Le due Action sono richiamate anche da `BuildTicketsGeoJsonAction`, `Ticket`, `BuildSegnalazioniFilterAggregateAction` e `BuildsTicketLayoutFilters` (quale delle due in ciascuno, non verificato).
Il Lit dichiara i tre attributi in `Modules/Geo/resources/js/components/coordinate-picker-lit.js` e `map/events.js` costruisce l'icona con `createGeoMapLeafletIcon(L, ctx.markerColor, ctx.markerIconUrl, ctx.markerLabel)`. Che il Lit giri quei valori fino a `ctx` e che il browser disegni l'icona giusta (non verificato; copertura prevista: `Themes/Sixteen/tests/browser/ticket-detail-marker.spec.mjs`, non eseguito qui).
Alternativa testuale: contenitore `role="region"` con `aria-label` (`where.map_at` con l'indirizzo, `where.map` senza) e riga «Indirizzo» sempre visibile. Le coordinate non sono stampate come testo, ma restano nell'attributo `state` del Lit: sono le stesse gia' pubbliche nel GeoJSON (docblock dell'Action). `<x-geo::point-map>` invece scrive latitudine e longitudine come testo sotto la mappa (`Modules/Geo/resources/views/components/point-map.blade.php`): non usarlo qui, per non ampliare l'esposizione dell'elenco.

## Test
- Unitari: `Modules/Fixcity/tests/Unit/Actions/BuildPublicTicketDetailActionTest.php` (chiavi esatte, `null` se non visibile, proprietario, `is_following`, meta di 160 caratteri, nessun punto con coordinate assenti o zero, `occurred_date`).
- Pagina: `Modules/Fixcity/tests/Feature/Pages/TicketPublicDetailPageTest.php`, gruppi «guest access and languages», «visibility», «privacy and escaping», «public timeline», «follow», «map marker» (legge `marker-icon-url`, `marker-color` e `marker-label` dal markup servito).

## Aperto (decisioni dell'utente)
Civico e arrotondamento delle coordinate (PP-02 di DCP-104, citato anche in DCP-148), miniatura (non verificato), ritorno al dettaglio dopo il login dell'ospite (non verificato), notifiche all'iscritto: `TicketAssignedNotification` e `TicketStatusChangedNotification` esistono in `Modules/Fixcity/app/Notifications/`, il recapito agli iscritti non e' stato provato.
