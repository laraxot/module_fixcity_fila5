---
title: "Epic: niente Services, mixed ultima spiaggia, const in tipi adatti"
type: epic
status: in-progress
created: 2026-10-08
updated: 2026-10-08
tags: [bmad, epic, services, queueable-actions, mixed, enum, phpstan, quality]
qmd: "epic services to queueable actions mixed ultima spiaggia const enum phpstan cluster perimetri"
related:
  - ../bashscripts/ai/wiki/rules/no-services-rule.md
  - ../bashscripts/ai/wiki/memories/phpstan-quality-docs-bmad-standing-order.md
  - docs-organization-audit.md
---

# Epic: standard di codice (Services, mixed, const)

Richiesta dell'utente (2026-10-08): tutti i Services diventano Spatie Queueable Actions; `mixed` solo come ultima
spiaggia; le `const` diventano qualcosa di piu' adeguato quando possibile; tutto con BMAD + second brain.

## Criteri di completamento

1. Nessun `app/Services` ne' classe `*Service` con logica applicativa nei moduli (`laravel/Modules/*`) e nei temi.
   Ogni use case e' una Action (`app/Actions/**`, `QueueableAction`, `execute()`), con tutti i chiamanti aggiornati.
2. `mixed` solo dove non esiste un tipo migliore (shape array, generics, Data, Contracts, union precise).
   Si applica al codice toccato e ai nuovi file; il backlog storico (3.347 occorrenze, quasi tutte shape `array<string, mixed>`)
   si affronta per modulo, partendo dai `mixed` nudi in parametri e ritorni.
3. Le `const` di classe che rappresentano insiemi di stati/tipi/ruoli/chiavi diventano enum backed (riuso degli 46 enum
   esistenti prima di crearne); configurazione in `config/` o proprieta' tipizzata. Restano `const` tipizzate solo le
   costanti locali vere.
4. Per ogni modulo/tema toccato: PHPStan pulito sui file e sui chiamanti toccati, story in `docs/stories/`, second brain.

## Inventario (2026-10-08, esclusi test)

73 file Services in 12 moduli/temi; 295 `const` di classe; 3.347 occorrenze di `mixed`.

Esclusi dalla conversione (non sono il pattern Service, motivazione nella story del proprietario):
`User/Http/Livewire/TermsOfService` (componente Livewire dei termini di servizio), `Sixteen/Models/Service`
e `Sixteen/Models/Municipal/MunicipalService` (entita' di dominio "servizio comunale"), i contratti
(`ConfigResolverInterface`, `CommandHandlerInterface`) che seguono i loro implementatori.

## Cluster (perimetri disgiunti, ordine di esecuzione random, onde)

| Onda | Cluster | Perimetro | Rischio |
|---|---|---|---|
| 2 | AI | `AIService`, `AIChatCompletionClient`, `AIServiceJsonDecoder`, `AIServicePromptBuilder`, `AIServicePromptTemplates` + const AI | basso (0-1 chiamanti) |
| 2 | Tenant | `TenantService` (13 chiamanti) e famiglia `Services/Config/**` + const | medio |
| 2 | Moduli piccoli | UI (5), Media (`VideoStream`, `SubtitleService` doppio), Job, Lang, Seo (`MetatagService`, 16 metodi) + const | basso-medio |
| 2 | Notify | `PushNotificationService` (facade residua), `NotificationManager`, `SmsService`, `MailEngines/*` | medio (test da riscrivere) |
| 2 | Sixteen (tema) | `CieAuthService`, `SpidAuthService`, `ThemeService`, `MenuBuilder` + const (39) | medio |
| 3 | Xot-small | `ConfigService`, `HtmlService`, `UrlService`, `XotService`, `ModuleService`, `ThemeService`, `Trend/*`, `Translators/*` | medio-alto (API usata da tutti) |
| 3 | Xot-artisan | `ArtisanService` (16 chiamanti) + `Services/Artisan/**` (esistono gia' copie in `Actions/`) | alto |
| 3 | Xot-route | `RouteService`, `RouteDynService` (la Action sostitutiva esiste gia') | alto |
| 4 | Geo | 8 Services + 85 const | medio, peer attivo sul modulo |
| 4 | Const Fixcity/Activity/Mobile/User | 53 + 11 + 8 + 16 const | medio, peer attivo |

Le onde 3 e 4 partono quando gli agenti PHPStan dell'onda 1 hanno finito (stessi file in Xot) e quando l'altro agente
(Codex) lascia fermi i file di Geo/User/Fixcity/Activity.

## Regole di esecuzione

- Un Service per volta: crea Action/Actions per caso d'uso (non un'Action "contenitore" che replica i metodi del Service),
  aggiorna TUTTI i chiamanti (moduli, temi, provider, config, container bindings, stringhe di classe, test, docs), poi
  elimina il Service solo con zero riferimenti residui provati (`rg`), secondo `bashscripts/docs/prompts/42-delete-obsolete-files-safely.md`.
- Mai Actions che fanno da proxy a relazioni Eloquent; le relazioni restano su model/trait.
- Niente `@phpstan-ignore`, baseline, `assert()`/`@var` per forzare tipi; `mixed` ultima spiaggia.
- Test: riscrivere quelli che usano il Service in test sulle Action. Pest su DB di test non raggiungibile da questo host:
  si dichiara il limite e si verifica con `php -l`, PHPStan e probe senza DB.
- Rischio noto: in passato i Services sono "tornati" dopo la migrazione del 2026-07-13 (merge da `laraxot/dev`). Finche'
  il remoto non e' allineato un merge puo' riesumarli: le modifiche vanno committate forward-only dal proprietario del repo
  del modulo quando l'utente lo decide; in sessione si lascia il working tree.

## Stato

| Cluster | Stato | Story |
|---|---|---|
| Notify (push + Services) | fatto: onda 1 (2 copie del recapito + `SendPushNotificationAction`) e onda 2: `app/Services` eliminata (`PushNotificationService`, `NotificationManager`, `SmsService`, `MailEngines/MailtrapEngine`, 4 file non PHP) piu' `Facades/NotificationFacade` morta; successori gia' presenti (`Actions/Push/*`, `Actions\NotificationManager`, `SendSmsAction`, `SendMailtrapMailAction`), MailEngines non era una strategy da config. Test riscritti sulle Action. 7 const `NotificationLog::STATUS_*` -> `NotificationLogStatusEnum` (+ case `processing`). Log dei push simulati collegato, rami di `getTokensByCriteria` distinti. PHPStan pulito; probe senza DB 14/14; Pest non risolutivo (harness non boota l'app). Aperto: transport APNs/WebPush (prodotto), store device token (User `DeviceUser` vs Mobile `mobile_push_tokens`), `Actions\NotificationManager` multi-metodo, `SendSmsAction` morta | `Notify/docs/stories/phpstan-notify-push-delivery.story.md`, `Notify/docs/stories/services-to-actions-notify.story.md` |
| AI | fatto: 5 Services eliminati (codice morto, equivalenti vivi gia' presenti, nessuna nuova Action); 3 copie di `AIPromptTemplates` -> `private const` in `BuildAIPromptAction`; 11 const di stato/ruolo (models) ripristinate a enum (merge le aveva riesumate). PHPStan pulito | `AI/docs/stories/services-to-actions-ai.story.md` |
| Tenant | fatto: `TenantService` (facade 1:1 su 11 Action esistenti, 1 solo chiamante vivo in Geo) e `TenantAction` (clone facade, 0 chiamanti) eliminati; catena `Services/Config/**` eliminata invece che convertita (mai collegata, semantica diversa dal path vivo: con `database` perde le connessioni). Probe tenant finto prima/dopo identico (72 righe). PHPStan pulito | `Tenant/docs/stories/services-to-actions-tenant.story.md` |
| Xot-small | fatto: 7 Services (`Config`, `Html`, `Url`, `Xot`, `Module`, `Theme`, `ProfileTest`) + `Trend/Adapters` (3 copie, il vendor sceglie il driver) + 6 stub `Translators` eliminati, 0 chiamanti, Actions equivalenti gia' presenti; hex PA -> `PaDesignColorEnum` (3 copie -> 1); 7 const restano locali, 4 const handler Artisan lasciate all'agente Artisan. PHPStan pulito sui file toccati | `Xot/docs/stories/services-to-actions-xot-small.story.md` |
| Moduli piccoli (UI, Media, Job, Lang, Seo) | fatto: 11 Services/classi eliminati (UI 5, Media 3 con `Actions/Stream/SubtitleService` duplicato, Job 1, Lang 1, Seo 1) piu' i `.bak` tracciati e le directory `app/Services`; erano residui riapparsi (Action/Adapter equivalenti gia' presenti, 0 chiamanti di produzione), nessuna Action nuova. Difetti corretti nelle Action successore: `ConvertSrtToVttAction` (`exit()`, handle), shape di `ParseSubtitleXmlAction`. Test riscritti sulle Action. PHPStan pulito. Aperto: duplicati `Job/Actions` vs `Actions/Schedule`, `Lang/Actions/TranslatorAction`, tre stati metatag in Seo | `<Modulo>/docs/stories/2026-10-08-services-to-actions-<modulo>.story.md` (UI, Media, Job, Lang, Seo) |
| Xot-artisan | fatto: `ArtisanService` + `Services/Artisan/**` (12 file) + `Actions/ArtisanAction` (copia piatta) + 9 handler Strategy e `CommandRegistry`/`CommandHandlerInterface` in `Actions/Artisan` eliminati (0 chiamanti vivi, solo 4 test; registry mai collegato a `act()`); resta `Actions/Artisan/HandleArtisanActRequestAction` + 6 Action. Mappe const comando->metodo (4 x 2 alberi) -> `ArtisanActEnum` (18 act, valori storici invariati). Ritrovata logica persa: guard `STDIN` per artisan da SAPI web (in `RunArtisanCommandAction`) e `database.default` nel migrate. Test riscritti, eseguiva `act('clear')`/`migrate` reali. PHPStan pulito; probe senza DB 18/18; Pest non eseguibile (DB test). Aperto: `key:generate` dentro `act=clear` | `Xot/docs/stories/services-to-actions-xot-artisan.story.md` |
| Xot-route | fatto: `RouteService` + `RouteDynService` + terza copia `Actions/RouteDynAction` eliminati (0 chiamanti di produzione; Action gia' presenti: `Actions/Route/*`, `RegisterDynamicRoutesAction`); parita' provata con sonda (21 divergenze -> 8 volute): `BuildActionUrlAction` allineata (`row` opzionale, ultimo segmento), nuova `GetCurrentRouteHandlerAction` (closure rifiutata); test riscritti sulle Action; aperto: `inAdmin()` helper (segment 2) vs `IsAdminRouteAction` (segment 1) | `Xot/docs/stories/services-to-actions-xot-route.story.md` |
| Altri | da fare | vedi tabella sopra |
