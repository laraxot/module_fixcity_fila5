---
title: "Contratto — BuildPublicTicketDetailAction"
type: contract
action: "Modules\\Fixcity\\Actions\\BuildPublicTicketDetailAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, privacy, front-office]
created: 2026-11-23
updated: 2026-11-26
---

# BuildPublicTicketDetailAction

Contratto del payload per `/{lang}/tickets/{id}` (DCP-148). E' l'UNICA fonte dei campi che il dettaglio pubblico espone: un
array esplicito, mai il modello serializzato. Il perche' della catena pagina -> Action sta nell'ADR
[public-ticket-detail-route-ownership](../adr/0003-public-ticket-detail-route-ownership.md).

## Input

- `int $ticketId`: id numerico del ticket.
- `?Authenticatable $viewer`: visitatore; se assente si usa `auth()->user()`.

## Output

`null` oppure un array con le sole chiavi (nell'ordine del codice): `id`, `title`, `type`, `type_label`, `status`,
`status_label`, `created_date`, `address`, `latitude`, `longitude`, `description`, `meta_description`, `images`, `timeline`,
`is_following`, `is_owner`, `is_public`, `tracking_code`. `created_date` e' `Y-m-d`, mai l'ora.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-DETAIL-INV-1 | Il payload ha esattamente le 18 chiavi sopra, nient'altro: `owner_id`, email, autore, dati interni del file e il modello Eloquent non escono mai. Aggiungere un campo rompe il test dell'insieme esatto di chiavi (guardia contro le fughe). | `TICKET-DETAIL-INV-1` |
| TICKET-DETAIL-INV-2 | Un visitatore qualunque (ospite o altro utente) apre solo le segnalazioni pubbliche: stato in `TicketStatusEnum::canViewByAll()`, come mappa ed elenco. Ticket inesistente e ticket non visibile (`pending`, `draft`) restituiscono entrambi `null`, indistinguibili. | `TICKET-DETAIL-INV-2` |
| TICKET-DETAIL-INV-3 | Il proprietario apre la propria segnalazione in qualunque stato e anche senza posizione, se `TicketPolicy::view` lo consente; `is_public` dice se e' gia' pubblica. Un altro cittadino non la apre finche' non e' pubblica. | `TICKET-DETAIL-INV-3` |
| TICKET-DETAIL-INV-4 | `tracking_code` (capability token) e' valorizzato solo per il proprietario; per ospiti, altri cittadini e follower e' `null`, e il codice non compare altrove nel payload. Ticket senza codice: `null` anche per il proprietario. | `TICKET-DETAIL-INV-4` |
| TICKET-DETAIL-INV-5 | `images` contiene solo URL di immagini (`image/*`) e solo per ticket pubblici o per il proprietario; PDF e altri allegati non escono. | `TICKET-DETAIL-INV-5` |
| TICKET-DETAIL-INV-6 | `meta_description` e' il contenuto con spazi normalizzati, lungo al massimo 160 caratteri (troncato con `...`). | `TICKET-DETAIL-INV-6` |
| TICKET-DETAIL-INV-7 | Coordinate assenti o a zero (`0,0`) danno `latitude` e `longitude` `null`; l'indirizzo resta quello salvato (o stringa vuota). | `TICKET-DETAIL-INV-7` |
| TICKET-DETAIL-INV-8 | `is_following` e' vero solo se il visitatore ha una riga in `TicketSubscriber` per quel ticket; per l'ospite e' sempre falso. | `TICKET-DETAIL-INV-8` |

## Effetti collaterali

Solo letture, in quest'ordine:

1. `BuildPublicTicketsQueryAction` (ricerca pubblica sulle colonne ammesse); se non trova nulla, ricerca per proprieta' e
   `Gate::forUser($viewer)->allows('view')`.
2. `BuildTicketPublicImageUrlsAction` (immagini) e `BuildTicketTimelineAction` (cronologia filtrata per visibilita' e per visitatore).
3. Una query su `TicketSubscriber` per `is_following`.

Non modifica ticket, iscrizioni, media ne' sessione.

## Errori

- Record non visibile o inesistente: `null`; la pagina che consuma il risultato lo traduce in 404 (guardia lato tema:
  `Themes/Sixteen/tests/Feature/Pages/DetailNotFoundTest.php`).
- Nessuna eccezione propria; errori del DB risalgono al chiamante.
- Limite noto: la query pubblica esclude anche i ticket con `location` nulla (`BuildPublicTicketsQueryAction`, riga `whereNotNull('location')`): nessun test lo isola da un ticket non pubblico, quindi non e' coperto da un gruppo.
- Limite noto: `description` e' il testo libero scritto dal cittadino (`content`), pubblicato per intero sui ticket pubblici;
  puo' contenere dati personali che il cittadino ha scritto. L'Action non lo filtra.
- Limite noto: l'indirizzo e' quello che l'elenco mostra oggi, con civico e senza arrotondamento (decisione aperta PP-02 della
  story DCP-104).

## Non fa

- Non renderizza Blade o Design Comuni e non decide layout, lingua o rotta canonica.
- Non espone il modello Eloquent serializzato.
- Non sostituisce `GetPublicTicketByCodeAction`: l'accesso con codice di tracciamento ha un altro contratto (`get-public-ticket-by-code-action.md`).
- Non dichiara la pagina raggiungibile da ospiti o da autenticati: l'accesso alla rotta e' del contenitore CMS.
