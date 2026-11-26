---
title: "Contratto — PurgeDeletedAccountsAction"
type: contract
action: "Modules\\Fixcity\\Actions\\Privacy\\PurgeDeletedAccountsAction"
module: Fixcity
status: accepted
tags: [contract, action, privacy, gdpr, retention, cancellazione, scheduler]
created: 2026-11-26
updated: 2026-11-26
---

# PurgeDeletedAccountsAction

Sorgente: `app/Actions/Privacy/PurgeDeletedAccountsAction.php`. Cancella **per sempre** gli account che i cittadini hanno chiesto di
eliminare (il modulo Gdpr, `DeleteAccountAction`, imposta `deleted_at`), come promette l'informativa: conservati solo finche' servono a
chiudere le segnalazioni aperte, poi cancellati. Schedulata ogni giorno in `routes/console.php` (`purge-deleted-accounts`, `->daily()->withoutOverlapping()`).
E' l'unica Action del pacchetto che cancella dati personali in modo irreversibile.

## Input

- Nessun parametro. Legge `gdpr.retention.deleted_account_days` (default nel codice e in `config/gdpr.php`: 30).

## Output

- `int`: numero di account cancellati in questa esecuzione.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| PRIVACY-PURGE-INV-1 | Un account con `deleted_at` piu' vecchio della retention e senza segnalazioni aperte viene cancellato, insieme al suo profilo. | `PRIVACY-PURGE-INV-1` |
| PRIVACY-PURGE-INV-2 | Le segnalazioni dell'account cancellato restano (la riga di `tickets` non si tocca): l'informativa le conserva secondo il piano di conservazione del Comune. | `PRIVACY-PURGE-INV-2` |
| PRIVACY-PURGE-INV-3 | Un account cancellato da meno della retention non viene toccato. | `PRIVACY-PURGE-INV-3` |
| PRIVACY-PURGE-INV-4 | Un account con almeno una segnalazione "aperta" (qualunque stato tranne `draft`, `resolved`, `closed`) non viene cancellato, per vecchia che sia la richiesta. | `PRIVACY-PURGE-INV-4` |
| PRIVACY-PURGE-INV-5 | Un account senza `deleted_at` non viene mai toccato. | `PRIVACY-PURGE-INV-5` |
| PRIVACY-PURGE-INV-6 | La Action e' agganciata allo scheduler con cadenza giornaliera (`purge-deleted-accounts`). | `PRIVACY-PURGE-INV-6` |
| PRIVACY-PURGE-INV-7 | Insieme all'account spariscono le sue notifiche e i collegamenti Google (`socialiteUsers`). | — (non dimostrato: i test controllano utente e profilo, non notifiche ne' collegamenti) |

## Effetti collaterali

Per ogni blocco di 100 account candidati (`chunkById`):

1. Una sola `SELECT` sulle segnalazioni dei candidati (`tickets` sta su un altro database di `users`: niente `whereDoesntHave` fra database).
2. Per ogni account non bloccato, in sequenza e **senza transazione**: elimina il profilo, le notifiche e i collegamenti social, poi l'utente (`delete()`).

Non scrive nel registro delle attivita' del ticket.

## Errori

- Nessuna eccezione gestita: un errore su un account interrompe l'esecuzione e propaga; gli account gia' cancellati prima restano cancellati.
- Limite noto: i quattro passi di cancellazione di un account non sono atomici. Un errore a meta' lascia un utente con `deleted_at` ma senza profilo
  o notifiche; alla prossima esecuzione viene ripreso (profilo assente = nessuna azione).
- `BaseUser` non usa `SoftDeletes` (`deleted_at` e' una colonna con cast datetime): `delete()` e' una `DELETE` vera e non si annulla.
- Limite noto: lo stato "aperto" e' definito qui dentro (`NOT_OPEN`), non in `TicketStatusEnum`; un nuovo stato terminale va aggiunto a mano.
- Limite noto: `withoutOverlapping` e' dichiarato nello scheduler ma nessun test lo verifica ne' prova l'esecuzione concorrente di due istanze.

## Non fa

- Non cancella segnalazioni, commenti, valutazioni ne' media (l'informativa li conserva).
- Non cancella i consensi ne' il registro degli accessi: invecchiano con le loro retention (`activitylog:clean`, `model:prune` su `AuthenticationLog`).
- Non avvisa il cittadino della cancellazione definitiva.
- Non decide la richiesta di cancellazione (`deleted_at` lo imposta il modulo Gdpr).
- Non gestisce account che non sono `BaseUser`: vengono ignorati senza segnalazione.
