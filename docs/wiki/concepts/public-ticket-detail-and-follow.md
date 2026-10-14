---
title: "Dettaglio pubblico della segnalazione e funzione Segui"
type: concept
tags: [fixcity, ticket, public, privacy, follow, design-comuni, dcp-148]
created: 2026-10-13
updated: 2026-10-13
qmd: "dettaglio pubblico segnalazione tickets id segui follow BuildPublicTicketDetailAction FollowTicketAction occurred_date"
related:
  - ticket-citizen-timeline.md
  - ../../../../Themes/Sixteen/docs/bmad/stories/2026-10-13-dcp-148-dettaglio-pubblico-segnalazione-e-segui.story.md
status: active
---

# Dettaglio pubblico della segnalazione e "Segui"

`/{lang}/tickets/{id}` (id numerico) e' la pagina PUBBLICA di una segnalazione gia' presa in carico. Collegata dalle card di `/{lang}/tickets`.

## Una sola fonte dei campi esposti
`Actions/BuildPublicTicketDetailAction::execute(int $id, ?Authenticatable $viewer)` restituisce un array esplicito o `null` (-> 404). Chiavi: `id, title, type, type_label, status, status_label, created_date (Y-m-d), address, latitude, longitude, description, meta_description, timeline, is_following`. Mai `code`, `owner_id`, email, autore, immagini. Il test unitario confronta l'insieme ESATTO delle chiavi: aggiungere un campo deve rompere il test.
Visibilita' = `BuildPublicTicketsQueryAction::execute()` (stati `canViewByAll()` + le proprie segnalazioni se loggato): il proprietario apre il dettaglio della propria segnalazione ancora `pending` (test `CitizenMyTicketsDetailLinkTest`), un altro cittadino e l'ospite no.

## Cronologia
`BuildTicketTimelineAction` ha la chiave `occurred_date` (solo giorno, `j F Y`) accanto a `occurred_at` (con ora, invariata). Le pagine pubbliche mostrano solo il giorno.

## Segui
`POST /{lang}/tickets/{ticket}/follow` (`Actions/FollowTicketAction`, invocabile), registrata in `laravel/routes/web.php` (le route dei moduli non sono caricate), middleware `auth` + `throttle:30,1`, CSRF. Campo `following` (1 segui, 0 smetti): stato esplicito, quindi idempotente. Delega a `SetTicketSubscriptionAction` (autorizza `view`). Non pubblico = 404. Dopo il POST, redirect al dettaglio con flash `ticket_follow` (la CHIAVE `followed`/`unfollowed`, non il testo: la pagina lo traduce nella lingua del proprio URL, `role="status"`). Dopo ogni modifica alla route: `php artisan route:cache` da `laravel/`.

## Vista
Blocco `Themes/Sixteen/resources/views/components/blocks/ticket-detail/{design-comuni,timeline}.blade.php`, chrome `layouts.design-comuni` (stesso di `/it/tickets`), regole proprie in `resources/css/app/19-ticket-detail.css`. La mappa e' `<coordinate-picker-lit readonly>` con alternativa testuale, SENZA stampare le coordinate (`<x-geo::point-map>` le scrive in chiaro sotto la mappa: non ampliare l'esposizione dell'elenco). Testi: `fixcity::ticket_detail` (it, en, de, es).

## Aperto (decisioni dell'utente)
Civico e arrotondamento delle coordinate (PP-02 di DCP-104), miniatura, ritorno al dettaglio dopo il login dell'ospite, notifiche all'iscritto (la Notification esiste ma non e' stata provata a recapito).
