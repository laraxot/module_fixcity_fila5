---
title: "Second Brain — indice architetturale Fixcity"
type: index
module: Fixcity
created: 2026-09-26
updated: 2026-09-26
---

# Second Brain Fixcity

La fonte canonica per le decisioni architetturali del modulo è
[`FixCity — contratto architetturale canonico`](wiki/concepts/fixcity-architecture-contract-2026-09-26.md).
Aggiorna quella fonte quando cambia una regola; non duplicare qui il contenuto.

Per il contesto di processo e il recupero on-demand consulta
[`second-brain-local-discipline`](wiki/concepts/second-brain-local-discipline.md).
Le decisioni di prodotto e i criteri di accettazione vivono invece nei documenti
[`docs/bmad/`](bmad/README.md).

## Documentazione Architetturale

### 5. PHPStan generics: falsi positivi noti
I generics `HasMany<TicketComment, $this>` e `BelongsTo<User, $this>` generano falsi positivi `missingType.generics` quando il trait viene usato in contesti diversi. 

**Fix**: Usare `static` invece di `$this` quando possibile, accettare i falsi positivi documentati.

**Fonte**: `Modules/Fixcity/app/Models/Concerns/HasTicketRelations.php`

### 6. PHPStan constantTypeCoverage: falsi positivi noti
`PA_ROLES` (3 elementi) e `ProfileSeeder::DEMO_PROFILES` (2 elementi) generano `typeCoverage.constantTypeCoverage` perché PHPStan calcola la copertura su 180 possibili tipi costanti e richiede \u003e99%.\n\n**Realt\u00e0**: 3/180 = 1.7% \u00e8 perfettamente normale per array piccoli e semantici. Non \u00e8 un bug \u2014 \u00e8 una soglia irragionevole per costanti di dominio.\n\n**Fonte**: `Modules/Fixcity/app/Policies/BasePolicy.php:38`, `Modules/Fixcity/database/seeders/ProfileSeeder.php:22`\n\n**Documentazione aggiuntiva**:\n- `PA_ROLES` copre ruoli operativi critici (operator, supervisor, admin) per l'autorizzazione PA\n- La copertura teorica \u00e8 98.8% ma in pratica \u00e8 sufficiente per l'uso operativo\n- Documentata in `docs/second-brain.md` come eccezione architetturale legittima

### 7. UserContract pattern (non User model direct usage)
Sempre usare `UserContract` per l'autorizzazione, mai `Modules\User\Models\User` direttamente. Questo garantisce l'astrazione del modello utente e permette implementazioni diverse.