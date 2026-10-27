---
tags: [documentation]
qmd: "second brain"
issues: []
discussions: []
title: "Second Brain — indice architetturale Fixcity"
type: index
module: Fixcity
created: 2026-09-26
updated: 2026-10-14
status: draft
---

# Second Brain Fixcity

La fonte canonica per le decisioni architetturali del modulo è
[`FixCity — contratto architetturale canonico`](wiki/concepts/fixcity-architecture-contract-2026-09-26.md).
Aggiorna quella fonte quando cambia una regola; non duplicare qui il contenuto.

Per il contesto di processo e il recupero on-demand consulta
[`second-brain-local-discipline`](wiki/concepts/second-brain-local-discipline.md).
Le decisioni di prodotto, i flussi attore e i criteri di accettazione vivono invece nei documenti
[`docs/bmad/`](bmad/README.md).

I workflow eseguibili sono indicizzati in [`docs/bmad/workflows/`](bmad/workflows/README.md)
(process 00–10 + attori) e nel [`workflow-catalog`](bmad/workflow-catalog.md).
Non creare una seconda sequenza equivalente in questo indice.

### Demo FO (seed + guest)

- Seed: [`demo-tickets-presentation-seed`](wiki/concepts/demo-tickets-presentation-seed.md) via `FixcityDatabaseSeeder`
- Story demo guest: [`STORY-513`](bmad/stories/STORY-513-public-guest-visual-demo.md)
- Log: [`wiki/log.md`](wiki/log.md)


## Documentazione Architetturale

### Dati sacri — regola permanente

I dati persistenti non sono sacrificabili. È vietato usare `migrate:fresh`,
`migrate:refresh`, `migrate:reset`, `db:wipe`, `RefreshDatabase` o
`php artisan migrate --force`, anche nei test. Le migrazioni devono essere
forward-only; per isolare i test usare `DatabaseTransactions`, transazioni
manuali o un database esplicitamente dedicato senza cancellare dati condivisi.

Questa regola prevale su snippet legacy nella documentazione e sulle
impostazioni standard di Laravel. Prima di eseguire un comando database,
verificare sempre che sia non distruttivo.

### Migrazioni e ownership

Per `TicketSubscriber` la regola R3, la scelta UUID cross-connection e la
checklist preventiva sono registrate nel dossier BMAD
[migration-ticket-subscribers-audit](bmad/migration-ticket-subscribers-audit.md).
La cartella `database/migrations/README.md` indica inoltre le migration
storiche archiviate come `*.php.old`.

Il model `Modules\Fixcity\Models\User` eredita intenzionalmente la connessione
`user` da `BaseUser`: il model Fixcity estende il comportamento, non la
proprietà del database. Vedi [filament-user-uuid-connection-audit](bmad/filament-user-uuid-connection-audit.md).

`authentication_logs` non è dominio Fixcity: vive su `fixcity_user` (modulo
User). Su prod, se `Connection: user` punta a `fixcity_data`, è un errore di
env/migrate — non una migrazione da creare in Fixcity. Canonico User:
[authentication-logs-connection-ownership](../../User/docs/bmad/authentication-logs-connection-ownership.md).

`permissions` (Spatie) non è dominio Fixcity: vive su `fixcity_user` (modulo
User). Su prod, se `Connection: user` punta a `fixcity_data`, è un errore di
env/migrate — non una migrazione da creare in Fixcity. Canonico User:
[permissions-connection-ownership](../../User/docs/bmad/permissions-connection-ownership.md).
La timeline pubblica (`BuildTicketTimelineAction`) soft-guarda la tabella
assente per non trasformare un gap RBAC in HTTP 500 sul dettaglio ticket.


`ticket_subscribers` segue la stessa regola: il pivot vive su `fixcity_user`,
mentre i ticket vivono su `fixcity_data`. Non usare `belongsToMany()` dal
modello Ticket: `ticketSubscriberQuery()` legge il pivot sulla connessione
`user`, poi le action risolvono gli utenti con una seconda query. Questo evita
che Eloquent cerchi silenziosamente `fixcity_data.ticket_subscribers`.

### 5. PHPStan generics
Le relazioni Eloquent devono dichiarare entrambi i tipi del generic (`RelatedModel` e
`DeclaringModel`). I docblock vanno verificati dopo aver pulito la result cache; non si
accettano falsi positivi documentati né `@phpstan-ignore` come sostituto del fix.

**Fonte**: `app/Models/Concerns/HasTicketRelations.php`

### 6. Costanti tipizzate
Le costanti array di dominio devono dichiarare `array` quando il gate `typeCoverage`
richiede la copertura della dichiarazione. Il gate si corregge nel codice, mai nel neon.

### 7. UserContract pattern (non User / BaseUser diretti)
Sempre usare `UserContract` per identità e autorizzazione: mai
`Modules\User\Models\User` e mai `instanceof BaseUser` / type-hint `BaseUser`.
`Modules\Fixcity\Models\User` estende `BaseUser` e implementa il contratto;
i consumer (listener Auth, action, policy, tema) devono vedere solo il contratto.
Capability opzionali (es. `authentications()`) → `HasAuthentications`, non
ricadere sull'astratta. SSoT: `Modules/User/docs/wiki/concepts/usercontract-boundary.md`.

---

### 8. Aggiornamento parallelo — marcatori mappa + wizard (2026-10-14)

- **STORY-054**: marker non visibile (`nero piccolo con ?`) → SVG Design Comuni `#007A52`, `36×48`, `aria-label`, `role="img"`, nessun `?` overlay.
- **STORY-XXX**: pulsanti `ctrl-btn` mappa (`bg-transparent` invisibile) → `#007a52` background, hover `#005c3d`, shadow, `rounded-lg`, transizione.
- **STORY-530**: geolocalizzazione non funzionante (`isSecureContext` false su HTTP, `navigator.geolocation` disattivato su HTTP) → `fallBackToApproximate` attivato, documentato requisito HTTPS (`FORCE_HTTPS=true`) per produzione.

**Cross-riferimenti BMAD** (`docs/bmad/stories/`): `STORY-054-ticket-marker-visibility.md`, `STORY-XXX-wizard-map-buttons.md`, `STORY-530-geolocation-not-working.md`.
**Cross-riferimenti design**: `design-comuni-pagine-statiche` v2.4.0 marker (green `#007A52`), `button` componente con `bg-primary-500`.
**Second brain**: questi tre problemi sono collegati alla stessa area (`map` + `geolocation` dentro il wizard ticket); non duplicare le storie ma collegare tramite `related:` nel frontmatter.
