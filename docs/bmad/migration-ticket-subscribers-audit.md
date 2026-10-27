---
title: "Migration audit — ticket_subscribers UUID and ownership"
type: story-audit
module: Fixcity
status: done
created: 2026-10-18
updated: 2026-10-18
tags: [bmad, second-brain, migrations, r3, uuid]
qmd: "ticket subscribers migration owner uuid cross connection"
related:
  - ../r3-one-migration-per-model.md
  - ../second-brain.md
  - ./decision-log.md
---

# Audit BMAD

## Causa radice

`ticket_subscribers` aveva già una migration owner, ma le migration
`update_*` e `align_*` duplicavano la responsabilità. Inoltre `user_id` è un
UUID su una connessione diversa: `foreignIdFor()` poteva reintrodurre un
bigint o un vincolo fisico non valido. L'`align_*` mescolava anche DDL di
`TicketSubscriber` e `Ticket`.

## Correzione applicata

- `2023_01_01_000000_create_ticket_subscribers_table.php` è l'unica owner,
  dichiara `$model_class` e mantiene schema/evoluzioni idempotenti.
- `user_id` è `string(36)` indicizzata, senza FK cross-connection; `ticket_id`
  è `unsignedBigInteger` indicizzato.
- Unique e colonne comuni sono centralizzate nell'owner tramite
  `updateTimestamps(table: ..., hasSoftDeletes: true)`.
- Le migration storiche restano archiviate come `*.php.old`: non vengono
  caricate da Laravel e non vengono cancellate dalla storia forward-only.
- Le coordinate sono responsabilità della migration owner di `Ticket`.

## Checklist preventiva

- [ ] Una sola `create_*_table.php` per modello/tabella.
- [ ] `XotBaseMigration` e `$model_class` esplicito.
- [ ] Riferimenti User cross-connection: `string(..., 36)` + indice, senza FK.
- [ ] `foreignIdFor()` solo con tipo/connessione compatibili.
- [ ] Nessuna migration modifica la tabella di un altro modello.
- [ ] Timestamps/audit/soft delete solo tramite `updateTimestamps()`.
- [ ] Verificare `migrate`, `migrate:status` e `git diff --check` prima del merge.

## Evidenza

La tabella esistente `user.ticket_subscribers` è già compatibile: UUID,
bigint, indici, unique composta e colonne audit. La correzione non converte né
cancella dati esistenti.

## Ripristino produzione

La connessione `user` deve risolvere a `DB_DATABASE_USER=fixcity_user`, distinta
da `DB_DATABASE=fixcity_data`. Dopo il deploy, senza comandi distruttivi:

```bash
php artisan config:clear
php artisan migrate --database=user
php artisan tinker --execute='dump(config("database.connections.user.database"));'
```

Il valore stampato deve essere `fixcity_user`; altrimenti il deploy sta
caricando un `.env` o una cache diversa dal repository.

## Incident 2026-10-18 — installazioni con migration registrata ma tabella assente

L'area personale usava `TicketSubscriber` sulla connessione `user`, ma alcune
installazioni avevano registrato la migration owner senza avere la tabella in
`fixcity_user`. Il fallback non è spostare la query su `fixcity_data`: sarebbe
una violazione del confine dati. È stata aggiunta la repair migration
`2026_10_18_180002_repair_ticket_subscribers_table.php`, che crea la tabella
solo se manca e lascia intatti i dati presenti.

Prima di verificare il deploy: `config:clear`, `migrate --no-interaction` e
`migrate:status` sulla connessione reale; mai `migrate:fresh`, `refresh`,
`reset` o `db:wipe`.
