---
title: "Audit di sicurezza Filament: gli 11 pannelli diversi da fixcity"
type: security-audit
status: active
created: 2026-10-14
updated: 2026-10-14
module: Fixcity
tags: [security, filament, audit, blueprint, dcp-181]
qmd: "audit sicurezza filament blueprint filament-security-audit pannelli user notify xot cms blog job rating comment lang tenant ui superadmin preserveFilenames xss bulk delete dcp-181"
issues: []
discussions: []
related:
  - ../stories/dcp-181-filament-blueprint-audit-degli-altri-pannelli-e-piano-richieste-interessato.story.md
  - ../stories/dcp-180-audit-sicurezza-filament-blueprint.story.md
---

# Audit di sicurezza Filament: gli 11 pannelli diversi da fixcity

Eseguito il 2026-10-14 con la skill `filament-security-audit` di `filament/blueprint` 2.4.0 (Filament 5.10.1, Livewire 4.4.7, Laravel 13.35.0). Complementare a DCP-180, che audita il pannello `fixcity/admin`. Perimetro: `Modules/*/app`, `Themes/*/app`, viste Blade dei moduli e del tema, provider dei pannelli, `config/`.

## 1. Sintesi
12 finding in 4 categorie, piu' E1 (dipendenze) senza avvisi. **Metodo:** quattro agenti in parallelo (A, B, C, D) con le ricerche del catalogo; io ho rifatto la lettura del codice per i sei finding piu' gravi (A-1, A-3, B-1, C-2, D-1, D-3) prima di correggerli, e ho eseguito `composer audit`. Gli altri sono riportati cosi' come li hanno trovati gli agenti e **non sono stati ricontrollati da me**: nessun finding e' stato provato eseguendo il sito o un exploit.

| Categoria | Finding | Verificati da me | Corretti |
|---|---|---|---|
| A. Accessi | 3 (A-1, A-2, A-3) | A-1, A-2, A-3 | A-1 (globale), A-2, A-3 |
| B. Upload / RCE | 2 (B-1, B-2) | B-1 | B-1 |
| C. XSS / injection | 4 (C-1 ... C-4) | C-2 | C-2, C-4 |
| D. Dati | 3 (D-1, D-2, D-3) | D-1, D-3 | D-1, D-3 |
| E. Dipendenze | 0 | `composer audit`: «No security vulnerability advisories found» | n/a |

**I tre da leggere per primi:** A-3 (qualunque utente di un pannello puo' diventare super-admin), B-1 (caricamento di un `.php` eseguibile nella document root dal pannello Cms), C-2 (XSS persistente sulla mappa). I primi due sono corretti.

**Fuori categoria, segnalati dagli agenti, non verificati ne' corretti:**
- `JobStatusWidget::saveEnv()` (`Modules/Job`, righe 73-95) scrive nel `.env` un valore preso da una proprieta' Livewire modificabile dal client.
- `EnvWidget::submit` salva `$this->data` interamente; `EditUserWidget::updateUser` non chiama `canEdit()`.
- Passport: l'azione di gruppo `revoke_all_for_user` non ha `->authorize()`.
- **Corretto in parte** (2026-10-14): il disco `photos` (driver FTP verso un hosting gratuito di prova, credenziali in chiaro) era definito due volte nei 4 file di configurazione (`config/local/fixcity`, `config/eu/fixcity`, `config/net/futurely`, `Modules/Fixcity/config`): la seconda definizione sovrascriveva quella locale. Nessun codice usa il disco `photos`: ho tolto il blocco FTP (14 righe per file) e resta la definizione locale. **La password resta nella storia di git e va considerata compromessa: va cambiata sul servizio** (non posso farlo io).

## 2. Findings
## A. Controllo degli accessi

### [A-1] Bulk delete senza `deleteAny()`: la policy per-record e' aggirabile

**Stato (2026-10-14):** Verificato nel codice. **Corretto alla radice con una sola riga globale**: `DeleteBulkAction::configureUsing()` in `XotServiceProvider::registerBulkDeleteAuthorization()` applica a OGNI azione di eliminazione di gruppo (default di `HasXotTable`, `XotBaseRelationManager`, i 15 siti espliciti e quelli futuri) la regola `HasXotTable::bulkDeleteRecordAuthorization()`: con una policy decide `delete()` su ogni record, **senza policy il record resta consentito come in Filament**. Prima versione (mia, stesso giorno, per-sito con `authorizeIndividualRecords('delete')` come stringa) **era sbagliata**: `Gate::inspect` senza policy NEGA, e 10 modelli su 73 con una risorsa non hanno policy (Attachment, Article, Banner, Category, Profile, TextWidget, User, SsoProvider, AiActionProposal, NotificationLog): il bulk delete si sarebbe bloccato. Test: `Modules/Fixcity/tests/Unit/Filament/BulkDeleteRecordAuthorizationTest.php` (3 casi, compreso un'azione creata senza la base Xot). Da non fare: aggiungere `deleteAny()` a 35 policy.

Check: A1
Component: `Filament\Actions\DeleteBulkAction` (default di `Modules\Xot\Filament\Traits\HasXotTable::getTableBulkActions()` e di `Modules\Xot\Filament\Resources\RelationManagers\XotBaseRelationManager::getTableBulkActions()`)
Docs: https://filamentphp.com/docs/5.x/resources/deleting-records#authorization e https://filamentphp.com/docs/5.x/actions/delete#improving-the-performance-of-delete-bulk-actions

Location (sorgente del default):
- `Modules/Xot/app/Filament/Traits/HasXotTable.php:392-399` (`'delete' => DeleteBulkAction::make()`, riga 395; ereditato da ogni `XotBaseResourceTable`)
- `Modules/Xot/app/Filament/Resources/RelationManagers/XotBaseRelationManager.php:243` (`delete_bulk`; `canDeleteBulk()` a riga 292 ritorna sempre `true`)

Location (siti con `DeleteBulkAction::make()` esplicito, stesso difetto):
- `Modules/Job/app/Filament/Resources/ScheduleResource/Tables/SchedulesTable.php:68`
- `Modules/Media/app/Filament/Resources/MediaConvertResource/Tables/MediaConvertsTable.php:83`
- `Modules/Media/app/Filament/Resources/TemporaryUploadResource/Tables/TemporaryUploadsTable.php:61`
- `Modules/Rating/app/Filament/Resources/RatingResource/Tables/RatingTable.php:69`
- `Modules/Xot/app/Filament/Resources/ExtraResource/Tables/ExtrasTable.php:56`
- `Modules/Xot/app/Filament/Resources/LogResource/Tables/LogsTable.php:66`
- `Modules/Rating/app/Filament/Resources/HasRatingResource/RelationManagers/RatingsRelationManager.php:64`
- `Modules/Rating/app/Filament/RelationManagers/RatingsRelationManager.php:64`
- `Modules/Blog/app/Filament/Resources/ArticleResource/RelationManagers/RatingsRelationManager.php:62`
- `Modules/Blog/app/Filament/Resources/ProfileResource/RelationManagers/RatingMorphsRelationManager.php:64`
- `Modules/User/app/Filament/Clusters/Passport/Resources/OauthAccessTokenResource.php:151` e `:299`
- `Modules/User/app/Filament/Clusters/Passport/Resources/OauthRefreshTokenResource.php:73`
- `Modules/User/app/Filament/Clusters/Passport/Resources/OauthPersonalAccessClientResource.php:97`
- `Modules/User/app/Filament/Resources/OauthAuthCodeResource.php:91`
- `Modules/User/app/Filament/Resources/OauthAccessTokenResource.php:128`

Location (policy con `delete()` reale e nessuna `deleteAny()`, e tabelle che le espongono; il numero e' la riga di `delete()`):

- `Modules/Activity/app/Models/Policies/ActivityPolicy.php:47` (delete) - modello Activity; tabelle: `Modules/Activity/app/Filament/Resources/ActivityResource/Tables/ActivitysTable.php`; `Modules/Activity/app/Filament/Resources/ActivityResource/Tables/ActivitiesTable.php`
- `Modules/Activity/app/Models/Policies/SnapshotPolicy.php:47` (delete) - modello Snapshot; tabelle: `Modules/Activity/app/Filament/Resources/SnapshotResource/Tables/SnapshotsTable.php`
- `Modules/Activity/app/Models/Policies/StoredEventPolicy.php:47` (delete) - modello StoredEvent; tabelle: `Modules/Activity/app/Filament/Resources/StoredEventResource/Tables/StoredEventsTable.php`
- `Modules/Cms/app/Models/Policies/MenuPolicy.php:47` (delete) - modello Menu; tabelle: `Modules/Cms/app/Filament/Resources/MenuResource/Tables/MenusTable.php`
- `Modules/Cms/app/Models/Policies/PageContentPolicy.php:47` (delete) - modello PageContent; tabelle: `Modules/Cms/app/Filament/Resources/PageContentResource/Tables/PageContentsTable.php`
- `Modules/Cms/app/Models/Policies/PagePolicy.php:47` (delete) - modello Page; tabelle: `Modules/Cms/app/Filament/Resources/PageResource/Tables/PagesTable.php`
- `Modules/Cms/app/Models/Policies/SectionPolicy.php:47` (delete) - modello Section; tabelle: `Modules/Cms/app/Filament/Resources/SectionResource/Tables/SectionsTable.php`
- `Modules/Gdpr/app/Models/Policies/ConsentPolicy.php:47` (delete) - modello Consent; tabelle: `Modules/Gdpr/app/Filament/Clusters/Profile/Resources/ConsentResource/Tables/ConsentsTable.php`; `Modules/Gdpr/app/Filament/Resources/ConsentResource/Tables/ConsentsTable.php`
- `Modules/Gdpr/app/Models/Policies/EventPolicy.php:47` (delete) - modello Event; tabelle: `Modules/Gdpr/app/Filament/Resources/EventResource/Tables/EventsTable.php`
- `Modules/Gdpr/app/Models/Policies/ProfilePolicy.php:47` (delete) - modello Profile; tabelle: `Modules/Gdpr/app/Filament/Clusters/Profile/Resources/ProfileResource/Tables/ProfilesTable.php`; `Modules/Gdpr/app/Filament/Resources/ProfileResource/Tables/ProfilesTable.php`
- `Modules/Gdpr/app/Models/Policies/TreatmentPolicy.php:47` (delete) - modello Treatment; tabelle: `Modules/Gdpr/app/Filament/Resources/TreatmentResource/Tables/TreatmentsTable.php`
- `Modules/Geo/app/Models/Policies/AddressPolicy.php:47` (delete) - modello Address; tabelle: `Modules/Geo/app/Filament/Resources/AddressResource/Tables/AddresssTable.php`; `Modules/Geo/app/Filament/Resources/AddressResource/Tables/AddressesTable.php`
- `Modules/Geo/app/Models/Policies/LocationPolicy.php:47` (delete) - modello Location; tabelle: `Modules/Geo/app/Filament/Resources/LocationResource/Tables/LocationsTable.php`
- `Modules/Job/app/Models/Policies/SchedulePolicy.php:47` (delete) - modello Schedule; tabelle: `Modules/Job/app/Filament/Resources/ScheduleResource/Tables/SchedulesTable.php:68` (DeleteBulkAction esplicito)
- `Modules/Lang/app/Models/Policies/TranslationFilePolicy.php:51` (delete) - modello TranslationFile; tabelle: `Modules/Lang/app/Filament/Resources/TranslationFileResource/Tables/TranslationFilesTable.php`
- `Modules/Media/app/Models/Policies/MediaConvertPolicy.php:47` (delete) - modello MediaConvert; tabelle: `Modules/Media/app/Filament/Resources/MediaConvertResource/Tables/MediaConvertsTable.php:83` (DeleteBulkAction esplicito)
- `Modules/Media/app/Models/Policies/MediaPolicy.php:47` (delete) - modello Media; tabelle: `Modules/Media/app/Filament/Resources/MediaResource/Tables/MediasTable.php`; `Modules/Media/app/Filament/Resources/MediaResource/Tables/MediaTable.php`
- `Modules/Media/app/Models/Policies/TemporaryUploadPolicy.php:47` (delete) - modello TemporaryUpload; tabelle: `Modules/Media/app/Filament/Resources/TemporaryUploadResource/Tables/TemporaryUploadsTable.php:61` (DeleteBulkAction esplicito)
- `Modules/Rating/app/Models/Policies/RatingMorphPolicy.php:52` (delete) - modello RatingMorph; tabelle: `Modules/Rating/app/Filament/Resources/RatingMorphResource/Tables/RatingMorphTable.php`; `Modules/Rating/app/Filament/Resources/RatingMorphResource/Tables/RatingMorphsTable.php`
- `Modules/Rating/app/Models/Policies/RatingPolicy.php:48` (delete) - modello Rating; tabelle: `Modules/Rating/app/Filament/Resources/RatingResource/Tables/BaseRatingsTable.php`; `Modules/Rating/app/Filament/Resources/RatingResource/Tables/RatingTable.php:69` (DeleteBulkAction esplicito); `Modules/Rating/app/Filament/Resources/RatingResource/Tables/RatingsTable.php`
- `Modules/Tenant/app/Models/Policies/DomainPolicy.php:47` (delete) - modello Domain; tabelle: `Modules/Tenant/app/Filament/Resources/DomainResource/Tables/DomainsTable.php`
- `Modules/User/app/Models/Policies/AuthenticationLogPolicy.php:36` (delete) - modello AuthenticationLog; tabelle: `Modules/User/app/Filament/Resources/AuthenticationLogResource/Tables/AuthenticationLogsTable.php`
- `Modules/User/app/Models/Policies/FeaturePolicy.php:48` (delete) - modello Feature; tabelle: `Modules/User/app/Filament/Resources/FeatureResource/Tables/FeaturesTable.php`
- `Modules/User/app/Models/Policies/OauthAccessTokenPolicy.php:51` (delete) - modello OauthAccessToken; tabelle: `Modules/User/app/Filament/Clusters/Passport/Resources/OauthAccessTokenResource/Tables/OauthAccessTokensTable.php`; `Modules/User/app/Filament/Resources/OauthAccessTokenResource/Tables/OauthAccessTokensTable.php`; `Modules/User/app/Filament/Resources/PersonalAccessTokenResource/Tables/OauthAccessTokensTable.php`; `Modules/User/app/Filament/Resources/PersonalAccessTokenResource/Tables/PersonalAccessTokensTable.php`
- `Modules/User/app/Models/Policies/OauthAuthCodePolicy.php:47` (delete) - modello OauthAuthCode; tabelle: `Modules/User/app/Filament/Clusters/Passport/Resources/OauthAuthCodeResource/Tables/OauthAuthCodesTable.php`; `Modules/User/app/Filament/Resources/OauthAuthCodeResource/Tables/OauthAuthCodesTable.php`
- `Modules/User/app/Models/Policies/OauthDeviceCodePolicy.php:47` (delete) - modello OauthDeviceCode; tabelle: `Modules/User/app/Filament/Clusters/Passport/Resources/OauthDeviceCodeResource/Tables/OauthDeviceCodesTable.php`
- `Modules/User/app/Models/Policies/OauthPersonalAccessClientPolicy.php:47` (delete) - modello OauthPersonalAccessClient; tabelle: `Modules/User/app/Filament/Clusters/Passport/Resources/OauthPersonalAccessClientResource/Tables/OauthPersonalAccessClientsTable.php`; `Modules/User/app/Filament/Resources/OauthPersonalAccessClientResource/Tables/OauthPersonalAccessClientsTable.php`
- `Modules/User/app/Models/Policies/OauthRefreshTokenPolicy.php:47` (delete) - modello OauthRefreshToken; tabelle: `Modules/User/app/Filament/Clusters/Passport/Resources/OauthRefreshTokenResource/Tables/OauthRefreshTokensTable.php`
- `Modules/User/app/Models/Policies/SocialProviderPolicy.php:48` (delete) - modello SocialProvider; tabelle: `Modules/User/app/Filament/Clusters/Socialite/Resources/SocialProviderResource/Tables/SocialProvidersTable.php`; `Modules/User/app/Filament/Resources/SocialProviderResource/Tables/SocialProvidersTable.php`
- `Modules/Xot/app/Models/Policies/CacheLockPolicy.php:48` (delete) - modello CacheLock; tabelle: `Modules/Xot/app/Filament/Resources/CacheLockResource/Tables/CacheLocksTable.php`
- `Modules/Xot/app/Models/Policies/CachePolicy.php:48` (delete) - modello Cache; tabelle: `Modules/Xot/app/Filament/Resources/CacheResource/Tables/CachesTable.php`
- `Modules/Xot/app/Models/Policies/ExtraPolicy.php:48` (delete) - modello Extra; tabelle: `Modules/Xot/app/Filament/Resources/ExtraResource/Tables/ExtrasTable.php:56` (DeleteBulkAction esplicito)
- `Modules/Xot/app/Models/Policies/LogPolicy.php:48` (delete) - modello Log; tabelle: `Modules/Xot/app/Filament/Resources/LogResource/Tables/LogsTable.php:66` (DeleteBulkAction esplicito)
- `Modules/Xot/app/Models/Policies/ModulePolicy.php:48` (delete) - modello Module; tabelle: `Modules/Xot/app/Filament/Resources/ModuleResource/Tables/ModulesTable.php`
- `Modules/Xot/app/Models/Policies/SessionPolicy.php:48` (delete) - modello Session; tabelle: `Modules/Xot/app/Filament/Resources/SessionResource/Tables/SessionsTable.php`

Issue: le 35 policy qui sopra proteggono `delete()` con un permesso o un ruolo (`hasPermissionTo('page.delete')`, `hasRole([...])`, `hasPermissionToOrCreate(...)`), ma nessuna ha `deleteAny()`. Filament autorizza `DeleteBulkAction` una sola volta con `deleteAny` e non per record; con la policy presente ma il metodo assente, `get_authorization_response()` (`vendor/filament/filament/src/helpers.php:60-90`) ripiega su `Response::allow()` perche' non c'e' alcun `Gate::before` globale (verificato: nessun `Gate::before`, `strictAuthorization` o `shouldSkipAuthorization` nel progetto) e il `before` di Spatie ritorna `null`, mai `false`. Un utente che ha `*.viewAny` ma non `*.delete` (per esempio un ruolo di sola lettura) puo' quindi selezionare righe e cancellarle in blocco. `RelationManager` e tabelle di risorsa ricadono nello stesso ramo (`vendor/filament/filament/src/Resources/Pages/Page.php:424` e `vendor/filament/filament/src/Resources/RelationManagers/RelationManager.php:362`).

Fix: aggiungere la ri-verifica per record con la policy `delete` esistente, una volta nella base e in ogni sito esplicito elencato sopra:
```php
DeleteBulkAction::make()->authorizeIndividualRecords('delete')
```
Nei due default (`HasXotTable.php:395` e `XotBaseRelationManager.php:243`) basta aggiungere la chiamata alla catena esistente. Alternativa equivalente (se si preferisce nascondere il pulsante): copiare in ogni policy elencata `public function deleteAny(UserContract $user): bool { return <stessa condizione di delete()>; }`, come gia' fatto in `TicketPolicy.php:45`.

Verify: test del Pest sotto, sezione 4 (T-1).

### [A-2] Upload RPC di Livewire esposto su componenti raggiungibili da ospiti

**Stato (2026-10-14):** **Corretto** in `XotBaseWidget` con `use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents` (62 sottoclassi dirette). Provato con un test che discrimina: senza il trait un upload Livewire verso `data.name` passa, con il trait viene rifiutato; i caricamenti legittimi (`data.images.<chiave>`) passano: roundtrip Pest e prova nel browser reale. Non applicato alle pagine di accesso e registrazione dei pannelli (non sono `XotBaseWidget`).

Check: A5
Component: `Filament\Schemas\Concerns\InteractsWithSchemas` (anche via `Filament\Forms\Concerns\InteractsWithForms`) senza `Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents`
Docs: https://filamentphp.com/docs/5.x/advanced/security#restricting-livewire-file-uploads-to-schema-components

Location (classi base: il fix qui copre tutte le sottoclassi):
- `Modules/Xot/app/Filament/Widgets/XotBaseWidget.php:36-37` (`InteractsWithActions` + `InteractsWithForms`; base di `XotBaseSchemaWidget.php:37-39`, `XotBaseInfolistWidget.php:23`, `XotBaseWizardWidget.php:54`)

Location (componenti standalone):
- `Modules/User/app/Http/Livewire/Auth/Login.php:31-32` (montato su pagina pubblica: `Themes/Sixteen/resources/views/components/sections/auth/login.blade.php:4`, `components/blocks/forms/login-card.blade.php:5`)
- `Modules/User/app/Http/Livewire/Auth/Register.php:30` (`InteractsWithSchemas`)

Prova di raggiungibilita' da non autenticati (viste che montano sottoclassi di `XotBaseWidget`/`XotBaseSchemaWidget`):
- `Modules/User/app/Providers/Filament/AdminPanelProvider.php:27`: `SocialLoginWidget` sulla pagina di login di ogni pannello (`AUTH_LOGIN_FORM_AFTER`)
- `Themes/Sixteen/resources/views/components/pages/auth-register.blade.php:11` (`Modules\Gdpr\Filament\Widgets\Auth\RegisterWidget`)
- `Themes/Sixteen/resources/views/components/pages/auth-password-reset.blade.php:6` (`ForgotPasswordWidget`), `auth-password-token.blade.php:6` e `pages/[container0]/[slug0]/[container1]/[token].blade.php:30` (`ResetPasswordWidget`)
- `Modules/UI/resources/views/components/headernav/simple.blade.php:59` (`LanguageSwitcherWidget`) e `Modules/UI/resources/views/components/layouts/main.blade.php:27` (`ToastWidget`): in ogni pagina pubblica

Issue: tutti questi componenti compongono `WithFileUploads` tramite `InteractsWithSchemas`, quindi espongono `_startUpload`, `_finishUpload`, `_uploadErrored`, `_removeUpload` a chiunque abbia lo snapshot del componente, anche se il loro schema non ha alcun campo di upload. Il gancio di difesa (`vendor/filament/schemas/src/SchemasServiceProvider.php:62-77`) scatta solo se il componente espone `shouldRestrictFileUploadsToSchemaComponents()`; `grep` su tutto il progetto non trova `RestrictsFileUploadsToSchemaComponents`. Un ospite puo' quindi caricare file arbitrari in `livewire-tmp` e agganciarli a proprieta' pubbliche di widget di login, registrazione, cambio lingua e toast.

Fix: aggiungere il trait alla base e ai due componenti standalone (le sottoclassi lo ereditano):
```php
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;

abstract class XotBaseWidget extends FilamentWidget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use RestrictsFileUploadsToSchemaComponents;
```
Stessa riga `use RestrictsFileUploadsToSchemaComponents;` in `Login.php` e `Register.php`. Il campo `images` del wizard ticket (`Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php:307`, `SpatieMediaLibraryFileUpload` con vista custom) resta un componente dello schema, quindi continua a funzionare.

Verify: test T-2, sezione 4.

### [A-3] `toggleSuperAdmin()` senza autorizzazione: qualsiasi utente di pannello diventa super-admin

**Stato (2026-10-14):** **Verificato** (widget reso in `USER_MENU_BEFORE`, metodo pubblico senza controllo). **Corretto** in `IsProfileTrait::toggleSuperAdmin()` (si inverte solo un ruolo che si ha gia'); test `Modules/User/tests/Unit/Models/ProfileToggleSuperAdminAuthorizationTest.php` rosso prima, verde dopo.

Check: A6 (esteso: azione Livewire pubblica raggiungibile senza guardia; il gancio `mount()` di questa classe e' innocuo, il difetto e' il metodo pubblico)
Component: `Modules\User\Filament\Widgets\Profile\SuperAdminWidget` (estende `Modules\Xot\Filament\Widgets\XotBaseWidget`)
Docs: https://filamentphp.com/docs/5.x/advanced/security#authorization-and-the-livewire-request-lifecycle

Location:
- `Modules/User/app/Filament/Widgets/Profile/SuperAdminWidget.php:36-41` (azione pubblica `toggleSuperAdmin()`)
- `Modules/User/app/Models/Traits/IsProfileTrait.php:186-216` (sink: `assignRole('super-admin')`, con fallback `Role::updateOrCreate` se il ruolo non esiste)
- `Modules/User/app/Providers/Filament/AdminPanelProvider.php:35-38` (montaggio nel render hook globale `USER_MENU_BEFORE`: compare nel menu utente di ogni pannello)
- `Modules/User/app/Http/Livewire/Profile/SuperAdmin.php:26-31` (stesso difetto nel componente gemello; oggi non e' montato in nessuna vista trovata)

Issue: la vista `Modules/User/resources/views/filament/widgets/profile/super-admin.blade.php` mostra il pulsante solo se `isSuperAdmin()` o `isNegateSuperAdmin()`, ma e' solo presentazione: il metodo pubblico `toggleSuperAdmin()` non controlla nulla e il widget non ha alcun controllo sulle chiamate (`XotBaseWidget` non ha `abort`/`Gate`; nessun hook `on('call')` nel progetto). Il `<span>` radice con lo snapshot viene reso comunque per ogni utente autenticato di un pannello (`canAccessPanel` richiede solo un ruolo del pannello, `BaseUser.php:319-336`): chi ha, per esempio, solo il ruolo `notify::admin` o `admin` puo' inviare a `/livewire/update` una chiamata a `toggleSuperAdmin` con lo snapshot letto dalla pagina. Il metodo assegna `super-admin` all'utente corrente (`getProfileModel()` risolve `authId()`), e `XotBasePolicy::before()` (`Modules/Xot/app/Models/Policies/XotBasePolicy.php:16`) concede da quel momento ogni abilita'. E' una escalation verticale a super-admin.

Fix: guardia nel sink, cosi' copre sia il widget sia il componente gemello. In `IsProfileTrait::toggleSuperAdmin()` come prima istruzione:
```php
abort_unless($this->isSuperAdmin() || $this->isNegateSuperAdmin(), 403);
```
(l'utente deve gia' possedere `super-admin` o `negate-super-admin`, che e' la condizione con cui la vista mostra il pulsante).

Verify: test T-3, sezione 4.

## B. Upload di file e esecuzione di codice

### [B-1] `preserveFilenames()` su dischi serviti dal web: upload di `.php` eseguibile

**Stato (2026-10-14):** **Verificato** (`public_html` e' un caso di `AttachmentDiskEnum`; il nome predefinito di Filament e' `$file->hashName()` con estensione da contenuto). **Corretto**: tolto `->preserveFilenames()` nei tre punti.

Check: B3 (con B2: nessun allowlist, oppure allowlist `image/*` aggirabile da poliglotta)
Location:
- `Modules/UI/app/Filament/Blocks/Hero.php:21-25` (campo `background`, disco default `public`, directory `blocks`, nessuna restrizione di tipo; blocco disponibile nel builder Cms via `GetAllBlocksAction`)
- `Modules/Cms/app/Filament/Resources/AttachmentResource/Schemas/AttachmentForm.php:28-35` (campo `attachment`, disco scelto dall'utente con `->disk(fn (Get $get) => $get('disk'))` tra `public_html` = document root, `videos`, `local`; nessuna restrizione di tipo)
- `Modules/Media/app/Filament/Resources/TemporaryUploadResource/Schemas/TemporaryUploadForm.php:21-25` (campo `file`, disco default `public`, allowlist `image/*`, `application/pdf`, `application/msword`)
Component: `Filament\Forms\Components\FileUpload`
Docs: https://filamentphp.com/docs/5.x/forms/file-upload#security-implications-of-controlling-file-names

Issue: `->preserveFilenames()` salva il file con il nome scelto dal client, estensione compresa. Un utente del back office con accesso a queste risorse carica `shell.php` e il file finisce eseguibile su un percorso web: `/attachments/shell.php` (disco `public_html`, document root), `/storage/blocks/shell.php` (Hero), `/storage/shell.php` (TemporaryUpload). Nel terzo caso l'allowlist `image/*` non protegge: un file `GIF89a<?php ...` chiamato `shell.php` sniffa come `image/gif` (provato in locale) e passa la regola `mimetypes`, ma conserva l'estensione `.php`. Nessuna di queste posizioni passa da Media Library, quindi non c'e' il blocco delle estensioni eseguibili.

Fix: eliminare `->preserveFilenames()` nelle tre posizioni, cioe' le righe `Hero.php:25`, `AttachmentForm.php:30`, `TemporaryUploadForm.php:23` (il nome di default `hashName()` deriva l'estensione dal MIME sniffato e non produce `.php`). Se serve il nome originale, `->storeFileNamesIn('original_file_name')` su una colonna esistente, mai sul disco. La restrizione di tipo di queste tre posizioni e' coperta da B-2.

Verify: test in §4 (T-1, T-2).

### [B-2] Campi upload senza allowlist di tipo su dischi serviti dal web

**Stato (2026-10-14):** Non verificato in esecuzione. **Non corretto**: allowlist globale con `FileUpload::configureUsing()` da decidere insieme ai tipi realmente usati dai 28 campi.

Check: B2
Component: `Filament\Forms\Components\FileUpload` e `Filament\Forms\Components\SpatieMediaLibraryFileUpload`
Docs: https://filamentphp.com/docs/5.x/forms/file-upload#file-type-validation

Issue: nessuno di questi campi ha `->acceptedFileTypes([...])`, `->image()` o `->avatar()` e tutti scrivono su dischi serviti dal web (`public`, `uploads`). Con il naming di default non ho potuto produrre una RCE (vedi Summary: PHP puro perde l'estensione, i campi Spatie rifiutano le estensioni eseguibili), quindi l'impatto dimostrato e' un altro: HTML e SVG con script vengono salvati con estensione `.html`/`.svg` e serviti dalla stessa origine dell'applicazione (stored XSS contro gli amministratori), e restano esposti a qualsiasi tipo futuro o a una libmagic diversa in produzione. Le due posizioni in B-1 sono le sole con RCE diretta.

Location (campi vivi):

Plain `FileUpload`, disco `public`:
- `Modules/Blog/app/Filament/Resources/BannerResource/Pages/ListBanners.php:31` (import JSON, `acceptedFileTypes` commentato)
- `Modules/Blog/app/Filament/Resources/ArticleResource/Pages/Support/ArticleImportActionFactory.php:20` (import JSON)
- `Modules/User/app/Filament/Clusters/Appearance/Pages/Alignment.php:63`
- `Modules/User/app/Filament/Clusters/Appearance/Pages/Background.php:56`
- `Modules/User/app/Filament/Clusters/Appearance/Pages/CustomCss.php:48`
- `Modules/User/app/Filament/Clusters/Appearance/Pages/Favicon.php:48`
- `Modules/User/app/Filament/Clusters/Appearance/Pages/Logo.php:49` e `:50`
- `Modules/Cms/app/Filament/Clusters/Appearance/Pages/Footer.php:64`
- `Modules/Cms/app/Filament/Clusters/Appearance/Pages/Headernav.php:72`
- `Modules/UI/app/Filament/Blocks/Image.php:20` (blocco `image` del builder; `Image.php:62` ha `->image()` ma e' in `getFormSchema()`, non e' il campo registrato da `make()`)
- `Modules/AI/app/Filament/Pages/FineTuning.php:110` (dataset)
- piu' le due di B-1: `Hero.php:21`, `AttachmentForm.php:28`

`SpatieMediaLibraryFileUpload`, disco `uploads`:
- `Modules/Blog/app/Filament/Resources/CategoryResource/Schemas/CategoryFormSchema.php:42`
- `Modules/Blog/app/Filament/Resources/CategoryResource/Schemas/CategoryForm.php:45`
- `Modules/Blog/app/Filament/Resources/BannerResource/Schemas/BannerForm.php:55`
- `Modules/Blog/app/Filament/Resources/ArticleResource/Schemas/ArticleFormSectionsSchema.php:46`
- `Modules/Blog/app/Filament/Resources/TextWidgetResource/Schemas/TextWidgetForm.php:26`
- `Modules/User/app/Filament/Resources/BaseProfileResource/Schemas/BaseProfileForm.php:23`
- `Modules/User/app/Filament/Resources/ProfileResource/Schemas/ProfileForm.php:32`
- `Modules/UI/app/Filament/Blocks/ImagesGallery.php:21`
- `Modules/Notify/app/Filament/Resources/NotifyThemeResource/Schemas/NotifyThemeForm.php:30`

`SpatieMediaLibraryFileUpload`, disco `public` (default Media Library):
- `Modules/Rating/app/Filament/Blocks/Rating.php:82`
- `Modules/Cms/app/Filament/Resources/MenuResource/Schemas/MenuForm.php:39`

Codice non referenziato, stessa correzione se riattivato: `Modules/Blog/app/Filament/Resources/BannerResource.php:65` e `Modules/Blog/app/Filament/Resources/TextWidgetResource.php:37` (dentro `getFormFields()`, nessun chiamante).

Fix: un default globale che richiede un allowlist su ogni upload, da mettere accanto agli altri `configureUsing` in `Modules/Xot/app/Providers/XotServiceProvider.php` (righe 123-128):

```php
use Filament\Forms\Components\FileUpload;

FileUpload::configureUsing(
    fn (FileUpload $component): FileUpload => $component
        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
);
```

Il default si applica anche a `SpatieMediaLibraryFileUpload` (sottoclasse) e un `->acceptedFileTypes([...])` per campo lo sostituisce (le regole leggono la stessa proprieta'). Sostituzioni necessarie, perche' questi campi non sono immagini: `ListBanners.php:31` e `ArticleImportActionFactory.php:20` con `->acceptedFileTypes(['application/json', 'text/plain'])`; `FineTuning.php:110` con il tipo del dataset (`['application/json', 'text/plain']` per JSONL); `Favicon.php:48` con `['image/x-icon', 'image/vnd.microsoft.icon', 'image/png']`; `Modules/UI/app/Filament/Blocks/VideoSpatie.php:30` (disco `local`, non servito, ma il default lo toccherebbe) con `['video/mp4', 'video/webm']`. SVG resta fuori dall'allowlist di proposito.

Verify: test in §4 (T-3).

## C. XSS e injection

### [C-1] Testo RichEditor dei blocchi CMS stampato in Blade con `{!! !!}` senza `sanitizeHtml()`

**Stato (2026-10-14):** Non verificato. **Non corretto**: serve sapere quali pagine CMS usano i blocchi.

Check: C1
Location:
- `Modules/UI/resources/views/components/blocks/paragraph/simple.blade.php:7` (`{!! $text !!}`)
- `Modules/UI/resources/views/components/blocks/hero/simple_centered_with_background_image.blade.php:44` (`{!! $text !!}` dopo uno `str_replace` su `<p>`)

Component: `Filament\Forms\Components\RichEditor` in `Modules\UI\Filament\Blocks\Paragraph` (`Modules/UI/app/Filament/Blocks/Paragraph.php:25`, campo `text`) e in `Modules\UI\Filament\Blocks\Hero` (`Modules/UI/app/Filament/Blocks/Hero.php:20`, campo `text`). I blocchi sono offerti dal builder `Modules\Cms\Filament\Fields\PageContentBuilder` (usato da `PageForm.php:37-43` e `SectionForm.php:26`); `GetAllBlocksAction` scopre ogni classe in `Modules/*/app/Filament/Blocks`.
Docs: https://filamentphp.com/docs/5.x/forms/rich-editor#security

Issue: il valore del RichEditor e' HTML grezzo scelto da chi ha il permesso di modificare Pagine o Sezioni (basta alterare il payload Livewire per salvare `<img src=x onerror=...>`). Le due viste lo stampano senza sanificazione e il renderer pubblico le include nelle pagine del sito (e nelle anteprime del pannello), quindi lo script gira nel browser di ogni visitatore e di ogni amministratore che apre la pagina. Il rischio e' un'escalation da ruolo redazionale a esecuzione di JS nella sessione di un admin; nel repository non esiste nessun `sanitizeHtml()` o purifier. La vista `hero` e' accoppiata al blocco Hero dalle props (`title`, `text`, `background`) ma non ho verificato una pagina reale che la selezioni.

Fix: sanificare all'uscita in entrambe le viste.
- `paragraph/simple.blade.php:7`: `{!! str($text)->sanitizeHtml() !!}`
- `hero/simple_centered_with_background_image.blade.php:44`: `{!! str_replace('<p>', '<p class="mt-6 leading-normal text-white">', str($text)->sanitizeHtml()->toString()) !!}` (e rimuovere il blocco `@php ... str_replace ... @endphp` alle righe 41-43)

Verify: test Pest che renderizza la vista con `text` = `<img src=x onerror=alert(1)><p>ok</p>` e controlla che l'HTML non contenga `onerror` e contenga `<p`.

### [C-2] XSS persistente sulla mappa pubblica `/{lingua}/mobile/map`: titolo, indirizzo e citta' del cittadino nel popup Leaflet

**Stato (2026-10-14):** **Verificato** (concatenazione di input del cittadino in HTML). **Corretto** con `esc()` e controllo dello schema dell'URL. Oggi `/it/mobile/map` risponde 404 sul server di sviluppo (probabile oscuramento da parte del catch-all del tema), quindi la correzione e' preventiva.

Check: C2 (variante lato client: HTML costruito per concatenazione e passato a un sink che lo interpreta)
Location: `Modules/Mobile/resources/views/pages/mobile/map.blade.php:98-106` (variabile `popupContent`, sink `layer.bindPopup(popupContent)` alla riga 106). Origine dati: `Modules/Fixcity/app/Actions/BuildTicketsGeoJsonAction.php:94-99` (`title` = `$ticket->name`, `address` e `city` da `location`) scritti in `public_html/data/tickets.json` da `Modules/Fixcity/app/Actions/GenerateTicketsJsonAction.php`.
Component: `L.Popup` di Leaflet (`bindPopup(string)` assegna la stringa a `innerHTML`); pagina Folio del modulo `Modules\Mobile` registrata da `Modules\Cms\Providers\FolioVoltServiceProvider` con middleware `web` e senza `auth`.
Docs: https://filamentphp.com/docs/5.x/tables/columns/text#rendering-raw-html-without-sanitization (stesso principio: HTML grezzo senza sanificatore a valle)

Issue: la pagina scarica `/data/tickets.json` e costruisce il popup con `'<h3>' + props.title + '</h3>' + ... + props.city + ... + props.address + ...`. Il campo `name` della segnalazione accetta 3-255 caratteri qualsiasi (`TicketForm.php:299-302`, nessun `strip_tags`), quindi un cittadino registrato che crea una segnalazione con titolo `<img src=x onerror=...>` fa eseguire JS a chiunque apra la mappa e clicchi il marker (le segnalazioni pubbliche finiscono nel GeoJSON). La CSP e' in modalita' `report-only` (`config/security.php:126`) e comunque ammette `'unsafe-inline'` negli script, quindi non mitiga. Il popup del sito reale (`Modules/Geo/resources/js/components/map/popup-ticket.js`) invece usa `escapeHtml` ed e' corretto.

Fix: scrivere un helper di escape e usarlo su ogni valore del popup (sostituire le righe 98-106 di `map.blade.php`):
```js
var esc = function (v) {
    var d = document.createElement('div');
    d.textContent = String(v == null ? '' : v);
    return d.innerHTML;
};
// dentro onEachFeature:
var props = feature.properties || {};
var statusClass = 'status-' + String(props.status?.value || 'open').replace(/[^a-z_]/g, '');
var detailUrl = /^\/(?!\/)/.test(String(props.detail_url || '')) ? props.detail_url : '#';
var popupContent = '<h3>' + esc(props.title || 'Segnalazione') + '</h3>' +
    '<p><strong>Tipo:</strong> ' + esc(props.type?.label || '-') + '</p>' +
    '<p><strong>Citta:</strong> ' + esc(props.city || '-') + '</p>' +
    '<p><strong>Indirizzo:</strong> ' + esc(props.address || '-') + '</p>' +
    '<p><strong>Stato:</strong> <span class="' + statusClass + '">' + esc(props.status?.label || '-') + '</span></p>' +
    '<p><a href="' + esc(detailUrl).replace(/"/g, '&quot;') + '">Dettaglio</a></p>';
layer.bindPopup(popupContent);
```

Verify: sonda Playwright (`bashscripts/tools/visual`) che serve un GeoJSON con `title` = `<img src=x onerror="window.__xss=1">`, apre `/it/mobile/map`, clicca il marker e verifica `window.__xss === undefined`; in piu' un test Pest sulla pagina che fallisce se la sorgente contiene `props.title ||` concatenato senza `esc(`.

### [C-3] `Blade::render()` su qualunque stringa di contenuto CMS che contiene `{{` e `}}`

**Stato (2026-10-14):** Non corretto: `Blade::render` sul contenuto CMS e' una scelta di progetto (i blocchi usano Blade); serve una decisione e una allowlist di direttive.

Check: C2 (variante non catalogata: sink di iniezione lato server su dato di contenuto)
Location: `Modules/Cms/app/Models/Traits/HasBlocks.php:97-99` (`compile()`, chiamato da `getBlocks()` alla riga 57; usato da `Modules\Cms\Models\Page` e `Section` tramite i componenti `Modules/Cms/app/View/Components/Page.php:57` e `Section.php:54`, cioe' dal tag `<x-page>` di ogni pagina Folio del tema).
Component: `Modules\Cms\Models\Traits\HasBlocks` / `Illuminate\Support\Facades\Blade::render`.
Docs: https://filamentphp.com/docs/5.x/schemas/primes#text-component (principio: niente HTML/codice dinamico non sanificato da campi modificabili)

Issue: ogni valore stringa dei blocchi (anche quelli scritti dal builder del pannello: titoli, testi RichEditor, TextInput) che contiene `{{ ... }}` viene compilato come template Blade e quindi eseguito come PHP (`{{ system('id') }}`, `@php`). Chi puo' creare o modificare una Pagina o una Sezione nel back office ottiene esecuzione di codice sul server, non solo XSS. L'uso legittimo nei JSON e' limitato a `{{ route('...') }}` e `{{ LaravelLocalization::localizeURL('...') }}` (verificato su `Modules/Fixcity/config/database/content/pages/*.json`, 13 forme distinte). Nota: `Modules/Notify/app/Models/NotificationTemplate.php:321` ha la stessa struttura per i template email, ma e' il linguaggio dei template dello staff Notify (i dati entrano come variabili con escape) e non e' raggiungibile dal front office; non e' contato come finding.

Fix: sostituire `Blade::render($value)` in `HasBlocks::compile()` con un valutatore chiuso che accetta solo le due forme in uso (se serve la sintassi Blade completa, la scelta alternativa e' limitare il permesso di modifica di Page/Section ai soli super admin):
```php
$result[$key] = (string) preg_replace_callback(
    "/\{\{\s*(route|LaravelLocalization::localizeURL)\(\s*'([^']+)'\s*(?:,\s*\[([^\]]*)\])?\s*\)\s*\}\}/",
    static function (array $m): string {
        preg_match_all("/'(\w+)'\s*=>\s*'([^']*)'/", $m[3] ?? '', $kv);

        return e($m[1] === 'route'
            ? route($m[2], array_combine($kv[1], $kv[2]))
            : \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeURL($m[2]));
    },
    $value,
);
```
Verify: test Pest unitario con una classe anonima che usa il trait: `compile(['t' => "{{ system('id') }}"])` non deve eseguire nulla (il valore resta invariato o vuoto) e `compile(['u' => "{{ route('home') }}"])` deve restituire l'URL di `home`.

### [C-4] `documentUrl` del trattamento GDPR usato come `href` senza `Str::sanitizeUrl()`

**Stato (2026-10-14):** **Corretto**: `TreatmentsTable` usa `Str::sanitizeUrl()` (macro di Filament: `javascript:` diventa nessun link, `https://...` resta). Non verificato in un pannello renderizzato.

Check: C3
Location: `Modules/Gdpr/app/Filament/Resources/TreatmentResource/Tables/TreatmentsTable.php:30` (`->url(fn (Treatment $record): ?string => $record->documentUrl)->openUrlInNewTab()`); campo di input senza `->url()` in `Modules/Gdpr/app/Filament/Resources/TreatmentResource.php:34`.
Component: `Filament\Tables\Columns\TextColumn`
Docs: https://filamentphp.com/docs/5.x/advanced/security#validating-user-input

Issue: la colonna emette il valore grezzo del modello come `href`. Il campo `documentUrl` e' un `TextInput` libero (`maxLength(191)`), quindi un utente con permesso di modifica dei trattamenti puo' salvare `javascript:...` che esegue JS quando un altro amministratore clicca il link. La sorgente e' dello staff, non del cittadino; la colonna e' nascosta di default ma attivabile.

Fix: in `TreatmentsTable.php:30` usare `->url(fn (Treatment $record): ?string => \Illuminate\Support\Str::sanitizeUrl($record->documentUrl))` e in `TreatmentResource.php:34` aggiungere `->url()` al `TextInput` (la sanificazione in lettura resta necessaria per i dati gia' presenti).

Verify: test Filament che crea un `Treatment` con `documentUrl = 'javascript:alert(1)'` e verifica che l'URL della colonna sia `null`, mentre `https://example.org/doc.pdf` resta invariato.

## D. Query, ambito dei dati ed esposizione

### [D-1] L'export JSON della mappa scrive nel file pubblico anche le segnalazioni non pubbliche dell'utente che lo lancia

**Stato (2026-10-14):** **Verificato** (valore predefinito `includeOwnTickets: true`). **Corretto**: `execute(includeOwnTickets: false)`. Oggi nessuna fuga: i 27 ticket del file sono in stati pubblici.

Check: D1
Location:
- `Modules/Fixcity/app/Actions/GenerateTicketsJsonAction.php:26` (chiama `BuildPublicTicketsQueryAction` con il default)
- `Modules/Fixcity/app/Actions/BuildPublicTicketsQueryAction.php:25-39` (con `includeOwnTickets = true` aggiunge `orWhere('owner_id', auth()->id())`)
- `Modules/Fixcity/app/Filament/Resources/TicketResource/Pages/ListTickets.php:54` (azione `export_map_json`, unico chiamante)
- Destinazione pubblica: `Modules/Fixcity/app/Actions/LoadPublicTicketsGeoJsonAction.php:18,20` (`../public_html/data/tickets.json`, servito come `/data/tickets.json`), letto dalla mappa pubblica in `Modules/Fixcity/resources/views/filament/infolist/ticket-location-map.blade.php:27` e come fallback in `Modules/Fixcity/resources/views/pages/api/ticket-details/[ticket].blade.php`
Component: `Modules\Fixcity\Actions\GenerateTicketsJsonAction` (invocata dall'azione Filament `Filament\Actions\Action` `export_map_json` in `Modules\Fixcity\Filament\Resources\TicketResource\Pages\ListTickets`)
Docs: https://filamentphp.com/docs/5.x/advanced/security#scoping-queries

Issue: `BuildPublicTicketsQueryAction` e' pensata per essere dipendente dall'utente ("per un utente autenticato la mappa include anche le sue segnalazioni non ancora pubbliche"), tanto che l'elenco pubblico passa `includeOwnTickets: false` per avere "a tutti lo stesso insieme". `GenerateTicketsJsonAction` invece usa il default e scrive il risultato in un file statico condiviso da tutti i visitatori. Se l'operatore o l'admin che preme "Esporta JSON mappa" e' proprietario di segnalazioni in stato non pubblico (`PENDING` e gli altri stati fuori da `TicketStatusEnum::canViewByAll()`), titolo, indirizzo, citta' e coordinate di quelle segnalazioni finiscono in `/data/tickets.json`, leggibile senza autenticazione. Stato attuale del file: 27 feature, tutte in stati pubblici, quindi non c'e' ancora fuga in corso; il difetto e' nel percorso di codice.

Fix: in `GenerateTicketsJsonAction::execute()` sostituire la riga 26 con `app(BuildPublicTicketsQueryAction::class)->execute(includeOwnTickets: false),`

Verify: test Pest con utente proprietario di un ticket `PENDING` con `location`, `actingAs($utente)`, esecuzione di `GenerateTicketsJsonAction` e verifica che l'id del ticket non compaia nel JSON scritto (vedi sezione 4; il test deve salvare e ripristinare il file reale o ridefinire il percorso).

### [D-2] La lista Filament di `RatingMorph` non applica la regola "l'evaluator vede solo le proprie valutazioni" che la policy impone sul singolo record

**Stato (2026-10-14):** Non corretto: dipende dai ruoli reali nel DB (nessun seeder assegna `evaluator`).

Check: D1
Location:
- `Modules/Rating/app/Models/Policies/RatingMorphPolicy.php:16-28` (`viewAny` concesso a `evaluator`; `view` limitato a `$user->id === $model->user_id`); stesso schema in `Modules/Rating/app/Models/Policies/BaseRatingMorphPolicy.php:16-28`
- `Modules/Rating/app/Filament/Resources/RatingMorphResource.php:9-12` e `Modules/Rating/app/Filament/Resources/BaseRatingMorphResource.php:9-12` (nessun `getEloquentQuery()`)
- `Modules/Rating/app/Filament/Resources/RatingMorphResource/Tables/RatingMorphsTable.php:17-33` (colonne `user_id`, `model_type`, `model_id`, `value`, `note`)
- Le righe contengono anche le valutazioni dei cittadini sui ticket: `Modules/Fixcity/app/Actions/SubmitCitizenTicketRatingAction.php` crea `RatingMorph` con `user_id` del cittadino.
Component: `Modules\Rating\Filament\Resources\RatingMorphResource` (e `BaseRatingMorphResource`)
Docs: https://filamentphp.com/docs/5.x/advanced/security#scoping-queries

Issue: la policy dichiara che un utente con solo il ruolo `evaluator` puo' vedere e modificare soltanto le righe in cui `user_id` e' il suo, ma gli concede `viewAny`. La risorsa usa la query di default, quindi la tabella mostra le valutazioni di tutti gli utenti (id utente, modello valutato, voto, nota), comprese quelle dei cittadini sui ticket. Precondizione: l'utente deve avere anche l'accesso al pannello `rating::admin` (`BaseUser::canAccessPanel` richiede `hasRole('rating::admin')`); nessun seeder o migrazione del repository assegna il ruolo `evaluator`, quindi l'effettiva raggiungibilita' dipende dai dati del DB (non verificabile).

Fix: aggiungere a `Modules\Rating\Filament\Resources\BaseRatingMorphResource`
```php
public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if ($user instanceof \Modules\Xot\Contracts\UserContract
        && $user->hasRole('evaluator')
        && ! $user->hasRole(['super-admin', 'admin', 'hr-manager'])) {
        $query->where('user_id', (string) $user->getAuthIdentifier());
    }

    return $query;
}
```

Verify: test con `livewire(ListRatingMorphs::class)` come utente `evaluator`: `assertCanSeeTableRecords($proprie)` e `assertCanNotSeeTableRecords($altrui)` (vedi sezione 4).

### [D-3] Segreti in chiaro non in `$hidden` su modelli modificati da pagine Edit/View Filament

**Stato (2026-10-14):** **Verificato** (nessun `$hidden`). **Corretto**: `SsoProvider::$hidden = ['client_secret']`, `SocialiteUser::$hidden = ['token']`.

Check: D2
Location:
- `Modules/User/app/Models/SsoProvider.php:80` (`client_secret`, fillable, nessun cast `encrypted`, nessun `$hidden`); pagine: `Modules/User/app/Filament/Resources/SsoProviderResource/Pages/EditSsoProvider.php:10`, `.../ViewSsoProvider.php` e le copie in `Modules/User/app/Filament/Clusters/Socialite/Resources/SsoProviderResource/Pages/{Edit,View}SsoProvider.php`; il campo e' anche nel form: `Modules/User/app/Filament/Resources/SsoProviderResource/Schemas/SsoProviderForm.php:38`, `Modules/User/app/Filament/Clusters/Socialite/Resources/SsoProviderResource/Schemas/SsoProviderForm.php:39`
- `Modules/User/app/Models/SocialiteUser.php:71` (`token`, token OAuth del provider salvato da `Modules/User/app/Actions/Socialite/RetrieveSocialiteUserAction.php:84`, colonna `text` in `Modules/User/database/migrations/2023_01_01_000003_create_socialite_user_table.php:24`, nessun `$hidden`); pagine: `Modules/User/app/Filament/Resources/SocialiteUserResource/Pages/{Edit,View}SocialiteUser.php` e `Modules/User/app/Filament/Clusters/Socialite/Resources/SocialiteUserResource/Pages/{Edit,View}SocialiteUser.php`
- Nessun `mutateFormDataBeforeFill()` e nessun `mutateRecordDataUsing()` nel repository per questi modelli (ricerca: 0 occorrenze rilevanti). `Modules/Xot/app/Models/XotBaseModel.php:46` e `Modules/User/app/Models/BaseUuidModel.php:46` hanno `$hidden` vuoto.
Component: `Modules\User\Models\SsoProvider`, `Modules\User\Models\SocialiteUser`; pagine `Modules\User\Filament\Resources\SsoProviderResource\Pages\EditSsoProvider` / `ViewSsoProvider` e `Modules\User\Filament\Resources\SocialiteUserResource\Pages\EditSocialiteUser` / `ViewSocialiteUser` (e le copie sotto `Clusters\Socialite`), che estendono `Modules\Xot\Filament\Resources\Pages\XotBaseEditRecord` / `XotBaseViewRecord` (Filament `EditRecord` / `ViewRecord`)
Docs: https://filamentphp.com/docs/5.x/resources/overview#protecting-model-attributes

Issue: `EditRecord` e `ViewRecord` riempiono lo stato Livewire con `$record->attributesToArray()` (verificato in `vendor/filament/filament/src/Resources/Pages/EditRecord.php:124,139` e `ViewRecord.php:109,124`), quindi `client_secret` del provider SSO e `token` OAuth degli utenti finiscono nel payload JavaScript della pagina anche dove nessun campo li mostra (`SocialiteUserForm` non ha il campo `token`). Chiunque apra queste pagine, ad esempio un admin con permesso `update` ma non `read-secrets`, o un XSS nel back office, ottiene segreti usabili presso il provider. Gli altri modelli con colonne segrete sono stati valutati e non sono finding: `OauthClient` ha `secret` in `$hidden` (Passport), `PersonalAccessToken.token` e' un hash Sanctum, `PasswordReset` non e' la tabella usata dal broker (`config/auth.php` usa `password_reset_tokens`), `DatabaseConfig.password`, `TenantDomain.verification_token`, `Contact.token`, `DeviceUser.push_notifications_token` e `WaiterSession.token` non hanno Resource con pagina Edit/View.

Fix: aggiungere ai due modelli `protected $hidden = ['client_secret'];` in `Modules\User\Models\SsoProvider` e `protected $hidden = ['token'];` in `Modules\User\Models\SocialiteUser`; nei due `SsoProviderForm` (righe 38 e 39) aggiungere `->dehydrated(fn (?string $state): bool => filled($state))` al campo `client_secret`, cosi' un campo lasciato vuoto non azzera il segreto salvato.

Verify: `expect((new SsoProvider(['client_secret' => 'x']))->attributesToArray())->not->toHaveKey('client_secret')` e `livewire(EditSsoProvider::class, ['record' => $provider->getKey()])->assertSet('data.client_secret', null)`; stesso schema per `token` su `EditSocialiteUser`.

## 3. Checks performed

Risultati = righe restituite dalla ricerca del catalogo, eseguita su tutte le radici sorgente (non solo `app/`).

| Check | Esito | Motivo | Risultati ricerca |
|---|---|---|---|
| A1 Bulk delete/restore senza `*Any()` | Finding (A-1) | 35 policy con `delete()` reale, 1 sola `deleteAny()` (Ticket, corretta); `DeleteBulkAction` di default in `HasXotTable`. Nessun `ForceDeleteBulkAction`/`RestoreBulkAction` nel progetto, quindi `forceDeleteAny`/`restoreAny` non sono raggiungibili | `function (delete\|forceDelete\|restore)(` in 88 file policy; `*BulkAction` 40 righe; `*Any()` 1 |
| A2 Import aggira `create()`/`update()` | N/A | Nessun `ImportAction::make` ne' `extends Importer`. Esiste un `Action::make('import')` custom per gli articoli (`Modules/Blog/app/Filament/Resources/ArticleResource/Pages/Support/ArticleImportActionFactory.php:16`), ma `Article` non ha policy, quindi non c'e' regola da aggirare | `ImportAction::make` 0; `extends Importer` 0; `ImportAction` 1 file rilevante |
| A3 `can*()` al posto di `get*AuthorizationResponse()` | Pass | 14 override trovati: `XotBaseEditRecord::canDelete/canForceDelete/canRestore` (`Modules/Xot/app/Filament/Resources/Pages/XotBaseEditRecord.php:29,38,47`) delegano a `$resource::can*()`, che e' gia' la policy; 10 `canView()` di widget sono costanti o `! Auth::check()` (visibilita' per ospiti, non una regola di accesso); `EditUserWidget::canEdit()` e' un metodo di widget non di risorsa e il widget non e' montato da nessuna parte | 14 |
| A4 Colonne inline modificabili | N/A | Nessuna istanza di `(Select\|Toggle\|TextInput\|Checkbox)Column::make` ne' `new`; esistono solo i wrapper non usati `SingleRoleSelectColumn` e `SelectStateColumn` (estendono `XotBaseSelectColumn`) e un riferimento in docblock | 1 (docblock); `extends *Column` 2 wrapper, 0 usi |
| A5 Upload RPC su componenti senza campo upload | Finding (A-2) | `RestrictsFileUploadsToSchemaComponents` non e' usato da nessuna parte; widget e Login/Register montati su pagine pubbliche | 33 righe in 16 file (piu' 62 sottoclassi dirette di `XotBaseWidget`/`XotBaseSchemaWidget`/`XotBaseWizardWidget`/`XotBaseInfolistWidget`); esclusi Resource/Page/RelationManager di pannello |
| A6 Lavoro prima dell'autorizzazione | Finding (A-3, esteso) | I `mount()`/`boot()`/`hydrate()` delle pagine custom di pannello (Activity, AI, Cms Appearance, Media, Notify Test, User Appearance/Passport/Password/MyProfile, Xot) hanno solo riempimento form o letture, nessuna scrittura ne' chiamata esterna. `JobStatusWidget::mount()` esegue `Artisan::call` ma e' reso solo dopo l'autorizzazione della pagina `JobMonitor`. Trovato pero' un metodo pubblico privilegiato senza guardia (A-3) | 141 (boot/mount/hydrate, esclusi i Providers): 37 in Filament Pages/Clusters/Front, 46 in widget e Livewire, 33 in modelli/trait (non Livewire), 25 altri (Volt, Mobile, componenti) |

| Check | Esito | Motivo | Risultati ricerche |
|---|---|---|---|
| B1 | Pass | Tutti i writer non Spatie scrivono su dischi serviti dal web (`public`, `uploads`, `public_html`, `videos`): per il catalogo non c'e' escalation. I campi Spatie sono writer sicuri. Restano due soli writer non Spatie su dischi non serviti: l'opzione `local` di `AttachmentForm.php:35` (nel config tenant `local` ha root `storage/app` e nessun `serve`, quindi `temporaryUrl()` fallisce e Filament ricade su `url()`, che non e' indirizzabile: al piu' un oracolo su esistenza, dimensione e MIME, nessuna lettura del contenuto) e `S3Test.php:112` (pagina diagnostica, su `s3` solo file di prova). Nessun RichEditor scrive su disco non pubblico (`fileAttachmentsDisk('public')` in `SendAwsEmailPage.php:88`, gli altri sul default `public`). | `FileUpload::make` 56 hit (48 vivi), `RichEditor::make` 22 (21 vivi), `fileAttachmentProvider\|registerRichContent` 0, `preventFilePathTampering\|preventFileAttachmentPathTampering` 0, `configureUsing` 17, disk default 4 |
| B2 | Finding (B-2) | Vedi B-2. Esclusi: campi con `->image()`/`->avatar()`/allowlist (`Job ImportForm.php:23`, `UI Blocks/Post.php:29`, `Image.php:62`, `PersonForm.php:69`, `Cms InfoBlock.php:19`, `LogoBlock.php:17`, `HeroBlock.php:19`, `Blog Setting.php:166` con `->avatar()`, `Notify NotificationTemplateForm.php:61`, `Fixcity TicketForm.php:307` con jpeg/png/pdf, `Media TemporaryUploadForm.php:24`, `SpatieDocumentUpload.php`); campi su dischi non serviti (`VideoSpatie.php:30` e `ImageSpatie.php:28` su `local`, `S3Test.php:112` su `s3`); campi mai istanziati (`Media AddAttachmentAction.php:52` e `HasMediaResource/Actions/AddAttachmentAction.php:54`: i due `MediaRelationManager` del modulo Media non sono registrati da nessun resource, `HasMediaResource` non ha una classe resource; `GetAttachmentsSchemaAction.php:24`: `XotBaseResource::getAttachmentsSchema()` non ha chiamanti). `TicketResource.php:114` (`getFormSchemaOld()`, usato solo da `CreateTicketWidget`, non referenziato da pagine ne' route) ha `acceptedFileTypes` commentato ma la collection `ticket` di `Ticket::registerMediaCollections()` (`Ticket.php:423`) impone `image/jpeg` e `image/png` con `acceptsMimeTypes` prima del salvataggio (Media Library, `FileAdder::guardAgainstDisallowedFileAdditions`), quindi non e' sfruttabile. Il wizard pubblico reale (`CreateTicketWizardWidget` -> `TicketForm::getSteps()`) ha jpeg/png/pdf, `maxFiles(5)`, `maxSize(10 MB)`. | stessa ricerca di B1 (56 hit) + `extends FileUpload\|new FileUpload\|MarkdownEditor::make` 0 |
| B3 | Finding (B-1) | Vedi B-1. Pass per le altre: `NotifyThemeForm.php:36` (Spatie, il blocco estensioni di Media Library vale), `VideoSpatie.php:39` e `ImageSpatie.php:38` (disco `local`, non servito), `SpatieDocumentUpload.php:29,111` (allowlist, disco `private` non definito, nessun chiamante), `GetAttachmentsSchemaAction.php:30` (nessun chiamante, disco `attachments` non definito), `Fixcity .../RelationManagers/MediaRelationManager.php:31` (file cancellato nel working tree). `getUploadedFileNameForStorageUsing`: 0 hit. | `preserveFilenames\|getUploadedFileNameForStorageUsing` 10 hit |

Upload lato Livewire pubblico (wizard front office): `WithFileUploads`, `->store(`, `->storeAs(`, `->move(`, `Storage::put`, `hasFile(`, `->file(`, `allFiles(` non compaiono in nessun percorso del wizard (63 hit totali sulla ricerca di storage, tutti altrove: importer, diagnostica S3, seeder, `addMediaFromUrl`). Gli `<input type="file">` dei temi Sixteen (`components/forms/*.blade.php`, `municipal/service-form.blade.php`, `bootstrap-italia/upload.blade.php`) sono solo markup: nessuna route in `routes/web.php` legge file. Il solo percorso di salvataggio e' il campo Spatie `images` di `TicketForm.php:307`.

| Check | Esito | Motivo e numero di risultati |
|-------|-------|------------------------------|
| C1 Rich editor / markdown in Blade | **Finding** (C-1) | Editor: 22 hit (`RichEditor|MarkdownEditor::make`); echo grezzi: 233 hit di `{!!` su tutte le radici incluse Sixteen e viste mail. Accoppiamenti verificati: UI Paragraph/Hero (finding). Pass: commenti (`{!! $comment->text !!}` in `Modules/Comment/resources/views/livewire/comment.blade.php:44`, `filament/widgets/comment/comment.blade.php:51`, mail `pending|approved-comment-notification.blade.php`) perche' `text` e' sanificato nel hook `saving` (`Comment.php:76-78` -> `ProcessCommentAction` -> `SanitizeCommentHtmlAction`, config `comments.php:41`); documenti legali (`legal-document/design-comuni.blade.php:65`, `gdpr-notice.blade.php:7`) Markdown da file del tenant con `html_input=strip` e `allow_unsafe_links=false`; `Fixcity/resources/views/tickets/show.blade.php:33` (`{!! $ticket->content !!}`) non e' referenziata da nessuna rotta/controller e include componenti `pub_theme::components.ticket-comments` e `ticket-location-map` che non esistono nel tema (la vista reale `pages/tickets/[id].blade.php` usa `{{ }}`); `Cms/livewire/page/show.blade.php:28,47,52` e `Rating/rating-table.blade.php:26` senza riferimenti trovati; anteprime Notify (`preview-*-template.blade.php`) mostrano template dello staff nel solo pannello admin; `User/pages/learn/index.blade.php:42` rende un README remoto di terze parti con `Str::markdown` ma dietro `auth`+`verified`. Markdown di file del repository (`User` PrivacyPolicy) non e' input utente. |
| C2 HtmlString / view() | **Finding** (C-2, C-3) | 138 hit (HtmlString/Htmlable) + 109 hit (HtmlString, `->html(`, `Blade::render`, markdown, `sanitizeHtml`, `toHtml`). Pass: `TicketForm.php:102,226,366` (valori con `e()` o Markdown strip), `CreateTicketWidget.php:106,123` (`e()` / Blade statico), `IDColumn.php:45` (`e()`), `DownloadAttachmentPlaceHolder.php:42` (`htmlspecialchars`), `AutoLabelAction.php:134,165` (solo stringhe di traduzione), `XotBaseResource.php:376` (vista statica), `RenderContextNavigation.php` e provider (nome modulo / `@livewire` costante). Tutti i `->html()` di Filament sanificano a valle. Notifiche cittadino (`TicketAssigned|StatusChangedNotification`): nessun testo libero. Mail accessibilita': `Content(text:)` testo semplice. `UI/.../group.blade.php:128` stampa grezzo solo per colonne `->html()` e nessuna ha dati utente. Viste non raggiungibili e non contate: `Geo/.../address.blade.php:30` (nessun uso), `tickets-map-widget.blade.php:40` (componente Livewire senza riferimenti), `interactive-map.blade.php:281` (nessuna classe), `Sixteen/.../pub_theme/components/blocks/search-results/default.blade.php:1` (percorso annidato mai referenziato), `blocks/sidebar/system-info.blade.php:37` (nessun JSON di pagina lo usa). JSON-LD `Cms/components/metatags.blade.php:51`: `json_encode` senza `JSON_HEX_TAG` ma i parametri di rotta non possono contenere `/`, non sfruttabile. |
| C3 Schemi URL non sicuri | **Finding** (C-4) | `->(url|recordUrl)\(`: 109 hit; forma a rischio (closure o `$record->`): 14 hit; `->link(`: 0 hit. Pass: gli altri 13 usano `getUrl()`/`route()`/`Model::getUrl` (Activity, Media, Passport, Xot). I `->url()` dei blocchi Cms/UI sono regole di validazione di input, non href di colonne. |
| C4 `allowHtml` / `allowOptionsHtml` | Pass | 1 hit: `Modules/Lang/app/Filament/Forms/Components/NationalFlagSelect.php:27`; le etichette sono dati statici (`countries()`, `lang::countries.*`, URL asset), nessun dato utente. |
| C5 `extra*Attributes()` | Pass | 51 hit; tutti array statici (classi, `data-element`, `aria-*`, stile fisso). Nessun valore o nome attributo da dati utente. |
| C6 `allowHtmlValidationMessages` | N/A | 0 hit. |
| C7 JS lato client | Pass | 1 hit sul pattern stretto (`alpineClickHandler` in `Modules/Xot/app/Filament/Traits/HasXotFormAction.php:31`, usa `Js::from($url)`); 30 hit `RawJs` (`TestChartWidget` statico; `ArrayToRawJsAction` senza chiamanti). Nessun `hiddenJs/visibleJs/afterStateUpdatedJs/actionJs/JsContent`. |

Ricerche aggiuntive oltre al catalogo (sink DOM e contesti JS, perche' il front office costruisce HTML lato client): `json_encode|@json|Js::from` in Blade (nessun problema: `@json` usa HEX_TAG/HEX_QUOT), sink DOM `innerHTML|bindPopup|x-html|insertAdjacentHTML|unsafeHTML|DOMParser` su JS e Blade (154 + 3 hit; il solo sink sfruttabile e' C-2), espressioni Blade dentro attributi JS/Alpine/`wire:click` (Blog `rating_with_image.blade.php:12` e `Mobile/tickets/show.blade.php:5,10` hanno `'{{ ... }}'` dentro espressioni JS ma le viste non sono raggiungibili: nessuna rotta, non sotto `pages/`), `Geo/layouts/partials/scripts.blade.php:25-29` (input di richiesta dentro stringa JS con `{{ }}`: i `"` vengono codificati, non sfruttabile, e il layout Geo e' legacy).

| Check | Esito | Motivo e numero di risultati |
|---|---|---|
| D1 | Finding (D-1, D-2) | Seed: 185 file modello con FK proprietario, 89 migrazioni, 81 `view()` nelle policy (28 righe con confronto di proprieta'). Siti di query: 69 occorrenze di `getEloquentQuery/modifyQueryUsing/getTableQuery/getStats/getData`; scope registrati: 1 (`TenantScope` in `InteractsWithTenant`, inerte senza tenancy Filament, nessuna sottoclasse di `BaseInteractsWithTenant` nel codice) + `HasTenantNotifications` (usato solo nei test). `orWhere` fuori da gruppi: 0 nei siti di personalizzazione (19 occorrenze, tutte dentro `where(function...)` o scope locali che Eloquent raggruppa). `SelectFilter`/`SelectConstraint` (34): nessuna `options()` ristretta per utente/ruolo, quindi il sotto-check passa. Pass verificati: `TicketResource` + `TicketPolicy` (lista riservata a operatori/permesso `ticket.viewAny`), `ViewWidget` (`isVisibleOnPublicFrontoffice`), `TicketCitizenRatingPromptWidget` (`userOwnsTicket` nell'azione), area personale, notifiche, tracking per codice (throttle), `NotificationsCenterWidget`, `CommentsWidget` (`commentableType`/`commentableKey` sono scrivibili dal client, ma `resolve()` usa la proprieta' `model`, ricostruita dalla snapshot firmata: un `$set('model', null)` viene reidratato dal synth Eloquent con il modello originale, verificato in `vendor/livewire/livewire/src/Mechanisms/HandleSynths/HandleSynths.php:80-137`; la vista filtra inoltre con `CommentPolicy::see`), `ResolvePageAction` (`Modules/Cms/app/Actions/ResolvePageAction.php`: risolve modelli da segmenti URL con `newQueryWithoutScopes()` e fallback `DB::table()`, ma nel codice nessun blocco stampa `$data['item']`; resta solo un oracolo di esistenza 200/404 su chiavi `user_name`/`id`, quindi lasciato come Pass, vedi limiti), `RecentLoginsWidget` (pannello `user::admin`, solo admin utenti), `EditUserWidget` e `CreateAppointment` (nessun punto di rendering trovato: componenti non raggiungibili senza snapshot firmata). |
| D2 | Finding (D-3) | 288 righe nei modelli con nomi sensibili (43 file), 12 definizioni di `$hidden`, 0 `$appends` con accessor, 89 hit di colonne/entry/input con nomi sensibili nei file Filament, 0 `extra*Attributes()` con dati del record, 26 proprieta' pubbliche Livewire tipizzate a modello (Livewire serializza solo classe e chiave, non gli attributi), nessun `mutateFormDataBeforeFill` che scrubba. `BaseUser` ha `$hidden` con `password`, `remember_token`, `two_factor_*`. `Ticket` ha `email` e `code` senza `$hidden`, ma le pagine Edit/View sono per operatori autorizzati sul record e nessuna vista pubblica li stampa (il dettaglio pubblico e' un array esplicito, `BuildPublicTicketDetailAction`). |
| D3 | N/A | Nessun pannello con tenancy Filament: 20 risultati per `->tenant(`/`HasTenants`/`canAccessTenant`/`getTenants`/`Filament::getTenant`, tutti interfacce, trait mai attivati, codice morto (`ApplyTenancyToPanelAction`, 0 chiamanti) o righe commentate. `withoutGlobalScopes(`: 2 occorrenze (`ScheduleResource.php:34`, `FilterBuilder.php:294`), non legate al tenancy. Colonne `tenant_id`: solo tabelle del modulo `Tenant` senza Resource tenant-scoped. `saveQuietly/withoutEvents/unguarded`: 4 occorrenze (Job, Notify, User) non rilevanti. |
| D4 | N/A | 3 risultati: `HasTenants::canAccessTenant` (`Modules/User/app/Models/Traits/HasTenants.php:27`, gated da `tenants()->whereKey($tenant)->exists()`), `getTenants` (riga 37, restituisce solo i tenant dell'utente) e `BaseUser::canAccessPanel` (`Modules/User/app/Models/BaseUser.php:319`: richiede il ruolo con lo stesso id del pannello, o `super-admin`/`admin`/`restaurant::admin` per `admin`; il ramo `accesso` e' permissivo ma nessun pannello ha quell'id, 1 occorrenza di `'accesso'`). Registrazione dei pannelli Filament assente (`->registration()` commentato in `XotBasePanelProvider.php:68`). |
| D5 | N/A | Nessun pannello tenant-enabled. `->unique(`/`->exists(` sui form Filament: occorrenze in `Modules/User`, `Modules/Notify`, `Modules/Blog`, `Modules/Cms` (tutti pannelli non tenant); regole stringa `unique:`/`Rule::unique`: 2 (`CreateTemporaryUploadFromDirectS3UploadRequest.php:23`, `ProfileEditVoltComponent.php:137`), nessuna su risorse tenant-scoped. |

## 4. Test consigliati

Helper: https://filamentphp.com/docs/5.x/testing/overview. Usare un utente con solo `<risorsa>.viewAny` per T-1.

```php
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use function Pest\Livewire\livewire;

// T-1 (A-1): chi vede la lista ma non ha page.delete non puo' cancellare in blocco.
it('does not let a viewer bulk delete pages', function (): void {
    $viewer = User::factory()->create();
    $viewer->givePermissionTo('page.viewAny');
    $pages = Page::factory()->count(2)->create();

    $this->actingAs($viewer);

    livewire(ListPages::class)
        ->selectTableRecords($pages)
        ->callAction(TestAction::make('delete')->table()->bulk());

    expect(Page::query()->whereKey($pages->modelKeys())->count())->toBe(2);
});

// T-2 (A-2): l'upload RPC e' rifiutato su un widget senza campi upload.
it('rejects livewire uploads on a widget without upload fields', function (): void {
    Livewire::test(LanguageSwitcherWidget::class)
        ->call('_startUpload', 'data.language', [['name' => 'a.php', 'size' => 10, 'type' => 'text/x-php']], false)
        ->assertForbidden();
});

// T-3 (A-3): un utente di pannello senza ruolo super-admin non si promuove.
it('forbids toggleSuperAdmin for a plain panel user', function (): void {
    $user = User::factory()->create();
    $user->assignRole('notify::admin');

    $this->actingAs($user);

    Livewire::test(SuperAdminWidget::class)
        ->call('toggleSuperAdmin')
        ->assertForbidden();

    expect($user->fresh()->hasRole('super-admin'))->toBeFalse();
});
```

I nomi esatti delle factory e dei namespace vanno adattati al modulo (`Modules\Cms\Models\Page`, `Modules\Cms\Filament\Resources\PageResource\Pages\ListPages`, `Modules\Lang\Filament\Widgets\LanguageSwitcherWidget`, `Modules\User\Filament\Widgets\Profile\SuperAdminWidget`). T-2 va ripetuto per `Modules\User\Http\Livewire\Auth\Login` e per un widget di `XotBaseSchemaWidget`.

Pest, helper Filament/Livewire (https://filamentphp.com/docs/5.x/testing/overview).

T-1 (B-1, Cms Attachment sul document root):

```php
it('rifiuta un file .php sul disco public_html', function (): void {
    actingAs(adminConAccessoCms());

    Livewire::test(CreateAttachment::class)
        ->fillForm([
            'title' => 'x', 'slug' => 'x', 'disk' => 'public_html',
            'attachment' => UploadedFile::fake()->createWithContent('shell.php', 'GIF89a<?php echo 1;'),
        ])
        ->call('create')
        ->assertHasFormErrors(['attachment']);

    expect(file_exists(public_path('attachments/shell.php')))->toBeFalse();
});
```

T-2 (B-1, riflessione sui tre campi): costruire `Hero::make()`, `AttachmentForm::getFormSchema()['attachment']`, `TemporaryUploadForm::getFormSchema()['file']` e asserire `->shouldPreserveFilenames() === false`.

T-3 (B-2): riflessione sul default globale, con campo e file reali:

```php
it('applica l allowlist di default a ogni FileUpload', function (): void {
    $field = FileUpload::make('f');

    expect($field->getAcceptedFileTypes())->not->toBeEmpty()->not->toContain('text/html', 'image/svg+xml');
});
```

e, per un campo Spatie su `uploads`, un `Livewire::test(<pagina Create del resource>::class)->fillForm(['image' => UploadedFile::fake()->createWithContent('x.html', '<script>1</script>')])->call('create')->assertHasFormErrors(['image'])`.

Helper Filament: https://filamentphp.com/docs/5.x/testing/overview

1. C-1 (Pest): `expect(view('ui::components.blocks.paragraph.simple', ['text' => '<img src=x onerror=alert(1)><p>ok</p>'])->render())->not->toContain('onerror')->toContain('<p');` e lo stesso per la vista `hero`.
2. C-2 (sonda Playwright in `bashscripts/tools/visual` + Pest di regressione): fixture GeoJSON con `title` = `<img src=x onerror="window.__xss=1">`; aprire `/it/mobile/map`, cliccare il marker, asserire `window.__xss === undefined` e che il testo del titolo compaia come testo. Pest: `get('/it/mobile/map')->assertOk()->assertSee('esc(props.title', false)`.
3. C-3 (Pest unit): classe anonima con `use Modules\Cms\Models\Traits\HasBlocks;`, `compile(['a' => "{{ system('id') }}"])` non esegue il comando; `compile(['b' => "{{ route('home') }}"])` restituisce `route('home')`.
4. C-4 (Pest + Filament): `livewire(ListTreatments::class)` con un trattamento `documentUrl = 'javascript:alert(1)'`, asserire `assertTableColumnStateSet`/`getUrl() === null`; con `https://example.org/a.pdf` l'URL resta.

Helper Filament: https://filamentphp.com/docs/5.x/testing/overview

D-1 (Pest, `Modules/Fixcity/tests/Feature`): salvare e ripristinare `base_path('../public_html/data/tickets.json')` nel test.
```php
it('does not publish the actor own non public tickets in the static map json', function (): void {
    $operator = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'owner_id' => $operator->getKey(),
        'status' => TicketStatusEnum::PENDING,
        'location' => ['lat' => 45.0, 'lng' => 9.0, 'address' => 'Via Test 1'],
    ]);

    $this->actingAs($operator);
    $path = app(GenerateTicketsJsonAction::class)->execute();

    $ids = collect(json_decode(file_get_contents($path), true)['features'])->pluck('properties.id');
    expect($ids)->not->toContain($ticket->id);
});
```

D-2:
```php
it('shows an evaluator only their own rating morphs', function (): void {
    $evaluator = User::factory()->create();
    $evaluator->assignRole(['evaluator', 'rating::admin']);
    $own = RatingMorph::factory()->create(['user_id' => $evaluator->getKey()]);
    $other = RatingMorph::factory()->create();

    $this->actingAs($evaluator);
    Filament::setCurrentPanel(Filament::getPanel('rating::admin'));

    livewire(ListRatingMorphs::class)
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$other]);
});
```

D-3:
```php
it('keeps secrets out of the edit page state', function (): void {
    $provider = SsoProvider::factory()->create(['client_secret' => 'top-secret']);
    $admin = User::factory()->create();
    $admin->assignRole(['super-admin']);

    $this->actingAs($admin);
    Filament::setCurrentPanel(Filament::getPanel('user::admin'));

    livewire(EditSsoProvider::class, ['record' => $provider->getKey()])
        ->assertSet('data.client_secret', null);

    expect($provider->attributesToArray())->not->toHaveKey('client_secret');
});
```

## 5. Cosa non e' stato verificato
- Nessun finding e' stato riprodotto eseguendo l'applicazione o un exploit; le prove sono letture di codice e script PHP/Node isolati.
- I ruoli e i permessi reali nel database (A-1 e D-2 dipendono da essi), il vhost di produzione, la versione di libmagic, le pagine CMS salvate nel DB.
- Le quattro prove dei test Pest preesistenti sul widget del super admin (`SuperAdminWidgetTest`, 4 su 7) falliscono gia' prima delle mie modifiche: l'utente del profilo e' `Modules\Fixcity\Models\User` mentre la factory crea `Modules\User\Models\User`, e i ruoli Spatie sono legati alla classe del modello (provato: `hasRole` vero sull'uno, falso sull'altro). Il test «returns a real 303» (che passava) e' stato adattato collegando il profilo all'utente con il ruolo.
- La skill `search-docs` di Boost non era raggiungibile: gli URL delle docs sono quelli del catalogo.
