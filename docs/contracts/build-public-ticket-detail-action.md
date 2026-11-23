---
title: "Contratto — BuildPublicTicketDetailAction"
type: contract
action: "Modules\\Fixcity\\Actions\\BuildPublicTicketDetailAction"
tags: [contract, action, ticket, privacy, front-office]
created: 2026-11-23
updated: 2026-11-23
---

# BuildPublicTicketDetailAction

Contratto del payload per `/{lang}/tickets/{id}`.

## Input

- ID numerico del ticket.
- Viewer autenticato opzionale; se assente viene usato l'utente corrente.

## Output

Restituisce `null` oppure un array con le sole chiavi pubbliche:
`id`, `title`, `type`, `type_label`, `status`, `status_label`,
`created_date`, `address`, `latitude`, `longitude`, `description`,
`meta_description`, `timeline`, `images`, `is_following`, `is_owner`,
`is_public`, `tracking_code`.

## Invarianti

- Un ospite vede soltanto ticket pubblici e visibili nella query pubblica.
- Il proprietario può vedere il proprio ticket secondo la policy, anche se non
  è ancora pubblico.
- Ticket inesistente o non autorizzato restituisce `null`, senza distinguere i
  due casi.
- `owner_id`, email, autore e dati interni del file non entrano nel payload.
- `tracking_code` è valorizzato solo per il proprietario.
- `meta_description` è normalizzata e lunga al massimo 160 caratteri.

## Effetti collaterali

- Esegue query di lettura, policy, timeline, immagini e stato di follow.
- Non modifica ticket, iscrizioni, media o sessione.

## Errori

- Record non visibile: `null`; il componente owner converte il risultato in 404.
- Coordinate mancanti o nulle: `latitude` e `longitude` diventano `null`.

## Non fa

- Non renderizza Blade o Design Comuni.
- Non decide il layout, la lingua o la rotta canonica.
- Non espone il modello Eloquent serializzato.
