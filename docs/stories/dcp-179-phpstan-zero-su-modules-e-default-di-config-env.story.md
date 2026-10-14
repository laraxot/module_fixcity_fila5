---
id: dcp-179
title: "DCP-179: PHPStan a zero su Modules e il default perso di config('env.K', default)"
type: story
epic: dc-parity-v2
status: done
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "phpstan zero modules typeCoverage constantTypeCoverage noEnvCallsOutsideOfConfig config/env.php default null config('env.K') ?? morph_map orbit comments"
related: [dcp-154, dcp-176, dcp-177]
---
# DCP-179: PHPStan a zero su Modules e il default perso di `config('env.K', default)`

Fase BMAD: Analyze, Implement, Verify. Ordine permanente dell'utente: PHPStan a zero, senza chiedere conferma.

## Stato misurato
Comando: `cd laravel && ./vendor/bin/phpstan analyse Modules` (level max, `phpstan.neon`, estensioni Larastan e type-coverage caricate dall'installer).
- Segnalazione incollata dall'utente: **144 errori**. I file citati risultano poi modificati tra le 14:39 e le 14:53 dell'orologio della macchina, da un'altra sessione.
- Dopo il lavoro dell'altra sessione e poi dopo le mie correzioni: **exit 0, nessuna segnalazione** (tre esecuzioni: formato JSON, formato raw, formato raw dopo la mia riscrittura).
- Non e' stato usato `ignoreErrors`, baseline o `@phpstan-ignore` (la baseline e' disattivata di proposito nel neon).

## Che cosa dicevano i 144 errori e a che cosa servivano i file
| Gruppo | Errori | Scopo del codice | Correzione |
|---|---|---|---|
| `typeCoverage.constantTypeCoverage` (184 su 203 costanti tipizzate, soglia 99%) | 19, in 9 file | costanti di classe (soglie, elenchi di sezioni, trattamenti) | `private const int`, `private const array` (PHP 8.4) |
| `larastan.noEnvCallsOutsideOfConfig` | ~100, in 13 file di `Modules/Fixcity/config/` | copie di configurazione dei pacchetti, caricate dal provider (`XotBaseServiceProvider::registerConfig`) come `fixcity.<file>` | `env('K')` sostituito da `config('env.K')`: `laravel/config/env.php` centralizza gli `env()` perche' con `config:cache` `env()` fuori da `config/` torna `null` |
| `array.duplicateKey` in `morph_map.php` | 12 | mappa tipo morph -> modello | tolto il blocco duplicato; i segnaposto `Modules\<nome progetto>\...` diventano `Modules\Fixcity\...` |
| `class.notFound` in `comments.php` | 1 | azione di notifica dei commenti approvati | `SendNotificationsForApprovedCommentAction` non esiste; punta a `QueueNotifyApprovedCommentAction` (esiste) |
| `class.notFound` in `orbit.php` | 3 | driver di Orbit | il pacchetto non e' installato: `drivers` vuoto con nota |
| `binaryOp.invalid` in `database.php` | 1 | nome della connessione utente | nome letto in variabile stringa |
| `argument.type` in `User/lang/{de,en,es}/validation.php` | 3 | catalogo base di validazione | controllo `is_array` prima di `array_replace_recursive` |

## Regressione trovata e corretta (questa sessione)
La riscrittura meccanica `env('K', default)` -> `config("env.K", default)` **perde il default**: `config()` restituisce il default solo se la chiave NON esiste, mentre in `config/env.php` ogni chiave esiste (`'K' => env('K')`) con valore `null` se la variabile non e' impostata. Prova: `config('env.TWITTER_CLIENT_ID', 'DEFAULT')` -> `null`, `env('TWITTER_CLIENT_ID', 'DEFAULT')` -> `DEFAULT`. Conseguenza: `fixcity.activitylog.enabled` passava da `true` a `null`, i driver di filesystem e le porte dei database perdevano il valore predefinito.
- Corretto: 71 chiamate in 13 file convertite in `(config('env.K') ?? default)` (`config('env.K')` quando il default era `null`); script con parser delle parentesi, copie originali nello scratchpad. Verifica: `config('fixcity.activitylog.enabled')` torna `true`, `fixcity.filesystems.default` torna `local`.
- Mancavano in `config/env.php` due chiavi usate da `filament-google-maps.php`: `FILAMENT_GOOGLE_MAPS_SERVER_API_KEY` e `FILAMENT_GOOGLE_MAPS_WEB_API_KEY`. Aggiunte in ordine alfabetico.
- Pint sui file di `Modules/Fixcity/config` (ha sistemato anche import e ordine in file non toccati).
- Nessun lettore: in tutto `Modules`, `Themes`, `app`, `routes` nessun file legge `config('fixcity.<file>')` per questi 13 file. Sono copie di configurazione non consumate: il rischio runtime era basso, ma il default perso era un errore latente.

## Regola per il futuro
Chi sostituisce `env()` in un file di configurazione scrive `(config('env.K') ?? default)`, mai `config('env.K', default)`; e aggiunge la chiave a `config/env.php` se manca. Un test o una ricerca (`rg "config\(['\"]env\.[A-Z0-9_]+['\"]\s*,"`) deve restituire 0.

## Non verificato
- Pest completo: non rieseguito dopo le modifiche alle configurazioni (nessun lettore noto).
- Le costanti tipizzate e le altre correzioni dell'altra sessione sono state viste solo nel `git diff`, non rieseguite una per una.
- Una nuova analisi da zero (senza cache dei risultati) non e' stata fatta: PHPStan ha usato `tmpDir /tmp/phpstan`.

## Criteri di accettazione
- [x] `phpstan analyse Modules`: exit 0, nessuna segnalazione.
- [x] Nessuna `ignoreErrors`, baseline o ignore inline aggiunti.
- [x] Default di `env()` preservati nelle configurazioni modificate.
- [ ] Un controllo automatico contro `config('env.K', default)` (oggi solo la ricerca a mano sopra).
