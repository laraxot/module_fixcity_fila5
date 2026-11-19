---
id: dcp-48
title: "Codice fiscale dell'autore: il riferimento lo mostra, il DB non lo ha"
type: story
epic: dc-parity-v2
status: needs-decision
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [product, data, gdpr]
related: [Modules/Fixcity/docs/stories/dcp-33-tickets-create-riepilogo-design-comuni-mappa.story.md]
qmd: "Codice fiscale dell'autore: il riferimento lo mostra, il DB non lo ha"
---

# DCP-48: codice fiscale dell'autore

## Evidenza (DCP-33, punto 5)
`segnalazione-02-dati` (card Autore) e `segnalazione-03-riepilogo` ("Dati Generali") mostrano nome, **Codice Fiscale**, telefono, email. Nel progetto: autore (nome) ed email esistono su `users`; telefono solo su `profiles.phone`; il codice fiscale "non esiste da nessuna parte". Senza la riga l'altezza della pagina non coincide col riferimento. PHPStan segnala inoltre `User::$codice_fiscale` e `User::$fiscal_code` non definite (`TicketForm.php`, blocco autore).

## Stato
Analisi del subagent CODICE-FISCALE, 2026-10-13 (sola lettura: nessuna migrazione, nessuna modifica a codice, lingue, JSON o record). Percorsi relativi a `laravel/`. Evidenza raccolta con grep, `php artisan tinker` (`Schema`, `information_schema`, `select`), PHPStan sui due file e Playwright sulle pagine di riferimento.

### 1. Dove vive oggi l'identita' dell'utente
| Fatto | Prova |
|---|---|
| Nessuna colonna anagrafica fiscale in tutto il DB. `fixcity_data` (1224 colonne) e `fixcity_user` (387): zero colonne che combaciano con `fisc\|tax\|vat\|piva\|codice\|cf\|national\|ssn\|document_number\|id_number`; gli unici match sono `avatar` e `avatar_path` (falsi positivi su "vat"). | tinker su `information_schema.columns` |
| `users` (conn `user`, db `fixcity_user`): `id, uuid, name, first_name, last_name, email, email_verified_at, password, two_factor_*, remember_token, current_team_id, profile_photo_path, deleted_at, lang, is_active, type, state, is_otp, password_expires_at` + audit. | `Schema::getColumnListing` |
| Profilo effettivo = `Modules\Fixcity\Models\Profile` (conn `fixcity`, db `fixcity_data`, tabella `profiles`): `id, uuid, user_id, first_name, last_name, email, credits, slug, extra, type, is_active, preferences, status, timezone, locale, avatar, bio, phone` + audit. `XotData::getProfileClass()` e `config('auth.providers.users.model')` (Fixcity `User`) verificati con tinker. | tinker; `Modules/Fixcity/app/Models/Profile.php:119` (`$connection = 'fixcity'`) |
| Esiste una seconda `profiles` in `fixcity_user` (schema modulo User: `birth_date, date_of_birth, address, gender, user_name, website...`), ma ha 0 righe ed e' orfana per l'app. Anche li' nessun campo fiscale. | tinker (`user.profiles`) |
| Dati: 8 `users`, 1 solo `profiles` (la fixture "Giulia Bianchi", `phone` "+39 331 1234567", `extra` NULL), 2 `tickets`. Quindi 7 utenti su 8 non hanno nemmeno la riga profilo. `tickets` ha solo `owner_id` ed `email`, nessun dato anagrafico. | tinker |
| Le uniche occorrenze di `fiscal_code`/`codice_fiscale` nel codice sono letture di proprieta' inesistenti: `Modules/Fixcity/app/Filament/Concerns/HasTicketAuthorData.php:29-38` e `.../TicketResource/Schemas/TicketForm.php:258` (+ `TicketForm.php.backup:100`). `Modules/Gdpr/app/Models/Profile.php:42,76,140,142` ha `@property tax_code/fiscal_code` solo nel docblock: `gdpr.profiles` ha le stesse colonne di `fixcity.profiles`, quindi nessuna colonna. Nessuna migrazione in `Modules/*/database` e `database/` cita `fiscal_code`, `codice_fiscale`, `tax_code`, `vat_number` (grep: 0). | grep -i |
| Trappola: `Modules/Tenant/app/Models/Tenant.php:36,69` (`tax_code`) e `config/comune.php:24` (`'cf' => env('COMUNE_CF', '00000000000')`) sono il codice fiscale dell'ENTE, non dell'utente; la colonna non esiste nemmeno in `user.tenants`. | tinker + lettura |
| Unico contenitore generico senza migrazione: `profiles.extra` (JSON). Cast `SchemalessAttributes` (`Modules/User/app/Models/BaseProfile.php:216`), in `$fillable` (:102) e `formlessAttributes` (:117), NULL nell'unico profilo. `preferences` (JSON) e `bio` (testo) esistono ma sono semanticamente impropri (`preferences['fixcity']` e' gia' usato da `SetFixcityEmailNotificationPreferenceAction.php:18-31`). | lettura |

### 2. Come lo usano oggi `HasTicketAuthorData` e `TicketForm`
- `getAuthUserFiscalCode()` (`HasTicketAuthorData.php:29-38`): `data_get($user,'fiscal_code') ?? data_get($user,'codice_fiscale') ?? ''`, quindi sempre `''`. PHPStan non lo segnala perche' `data_get` nasconde la proprieta'. E' codice morto: grep di `getAuthUser(Name|FiscalCode|Phone|Email)` trova solo la definizione e un uso di `getAuthUserName()` in `Modules/Fixcity/resources/views/filament/widgets/wizard/steps/summary.blade.php:181`.
- `TicketForm::getAuthorHtml()` (`TicketForm.php:254-272`): `Filament::auth()->user()` (256), nome `$user?->name` (257), CF `$user?->fiscal_code ?? $user?->codice_fiscale ?? ''` (258), poi `<dt>Codice fiscale</dt><dd></dd>` (264-265) sempre vuoto e un bottone "Mostra tutto" (267-268) che alterna `hidden` sul `dl`, che contiene solo il CF (niente Telefono ed Email, a differenza del riferimento). Inserito da `authorSection` (245-250) dentro `getDataSchema()`.
- PHPStan eseguito ora su `TicketForm.php` + `HasTicketAuthorData.php`: 8 errori in `TicketForm.php`, di cui 6 nel blocco autore: 257 `nullsafe.neverNull`; 258 `property.notFound` per `User::$codice_fiscale` e `User::$fiscal_code`, `cast.string`, 2 `nullsafe.neverNull`. Gli altri 2 (62, 113) sono del riepilogo. `HasTicketAuthorData.php`: 0.
- Passo 3 (riepilogo): `Modules/Fixcity/app/Actions/BuildTicketReviewSummaryAction.php:111-115` restituisce `author.rows = []` (commento "il codice fiscale non ha una sorgente"); i contatti sono `profile.phone` (:129) ed `email`. Tre test fissano questo comportamento: `tests/Unit/Actions/BuildTicketReviewSummaryActionTest.php:258-270` ("never invents a fiscal code row"), `tests/Feature/Filament/CreateTicketWizardWidgetTest.php:42-55`, `tests/Unit/TicketFormWizardSummarySchemaTest.php:52-57`.
- Che cosa vede oggi il front office (da codice e test; il browser non e' osservabile, vedi sezione 5): un ospite su `/it/tickets/create` e' rediretto a `/it/auth/login`; da autenticato il passo dati ha la scheda con il nome e una riga "Codice fiscale" con valore vuoto, il riepilogo ha solo nome, telefono (se esiste il profilo) ed email, senza codice fiscale.
- Precedente: il 2026-09-27 CF, telefono, nome ed email furono rimossi dal wizard perche' non persistiti, "falsa impressione di salvataggio" (`docs/wiki/log.md:380-384`, `docs/bmad/gap-analysis.md` G-22, `docs/bmad/workflows/07-ui-ux.md:47`). DCP-48 riapre una scelta di minimizzazione gia' fatta.
- Back office: `XotBaseResource::form()` (`Modules/Xot/app/Filament/Resources/XotBaseResource.php:173-180`) usa `TicketResource\Schemas\TicketForm::getFormSchema()` = privacy + dati + riepilogo, quindi `authorSection` compare anche in `/fixcity/admin/tickets/create` e `/{record}/edit` (`route:list`). `getAuthorHtml()` legge `Filament::auth()->user()`, cioe' l'OPERATORE loggato, non il proprietario del ticket (esiste `Ticket::owner()`, `Models/Concerns/HasTicketRelations.php:33`). Oggi nel BO stampa il nome dell'operatore e un CF vuoto: gia' sbagliato; con una sorgente stamperebbe il CF dell'operatore.

### 3. Registrazione e autenticazione
- La registrazione FO e' `Themes/Sixteen/resources/views/components/pages/auth-register.blade.php:11` -> `Modules\Gdpr\Filament\Widgets\Auth\RegisterWidget` con vista Blade custom `Themes/Sixteen/resources/views/filament/widgets/auth/register.blade.php` (`getFormSchema()` ritorna `[]`, `RegisterWidget.php:66-70`). Campi: `first_name`, `last_name`, `email`, `password` (+ privacy, termini, marketing). Il codice fiscale non viene chiesto. `ValidateUserDataAction` passa a `CreateUserAction` solo nome, cognome, email, password, `type=customer_user`, `lang`, `email_verified_at`.
- La registrazione non crea il profilo (nessun `profile()->create`): nasce pigro con `$user->profile()->firstOrCreate([])` (`SetFixcityEmailNotificationPreferenceAction.php:18`).
- Il fallback `Modules/User/.../Auth/RegisterWidget` + `UserForm::getRegisterFormSchema()` (`UserForm.php:94`) ha gli stessi 4 campi. Il profilo FO (`Modules/User/resources/views/pages/profile/edit.blade.php`, Volt) modifica solo nome, cognome, email, password: oggi non esiste nessuna UI che scriva `profiles.phone` o un CF (solo seed e BO).
- SPID/CIE: nessuna traccia. grep `spid|cie|agid|saml|oidc` su `Modules/*/app` e `config`: solo i tipi generici `saml/oidc/oauth` del form `SsoProviderResource`; pacchetti `socialiteproviders/auth0` e `microsoft` (`Modules/User/composer.json:54-56`) e `config/services.php` con facebook/google/github/microsoft; `socialite_users` e `sso_providers` hanno 0 righe; `Socialite/CreateUserAction.php:40-44` mappa solo name, last_name, email. Nessun flusso porta un CF: l'identita' reale e' email e password autodichiarate.
- Il riferimento mostra il CF perche' il suo servizio presuppone un'identita' digitale (inferenza, non verificata sul sito). Il valore d'esempio `GLABNC72H25H501Y` non supera il checksum del CF (carattere di controllo atteso `R`, calcolato con l'algoritmo ufficiale, verificato su `RSSMRA85T10A562S`). Nessun pacchetto di validazione CF in `vendor/`.

### 4. Opzioni, con impatto
Misure sul riferimento reale (Playwright, riga CF nascosta nel mio browser, nessun file toccato):
| Pagina | 1440 px | 768 px | 375 px |
|---|---|---|---|
| 02-dati, `p.card-info` CF | pagina 3064 -> 2999 (-65 px, 2,12%) | 3246 -> 3181 (-65, 2,00%) | 4056 -> 3994 (-62, 1,53%) |
| 03-riepilogo, `div.single-line-info` CF | 3115 -> 3058 (-57, 1,83%) | 3206 -> 3149 (-57, 1,78%) | 3956 -> 3906 (-50, 1,26%) |

La stima "circa 28% di pixel diversi sotto la riga" di DCP-33 (punto 7) non e' stata rimisurata qui.

| Opzione | Sorgente del dato | Migrazione | Altezza vs riferimento | Costo | GDPR |
|---|---|---|---|---|---|
| (a) colonna `fiscal_code` su `fixcity_data.profiles` | nuova, compilata in registrazione/profilo | si' (serve ok esplicito) | uguale | alto: 1 migrazione, modello, registrazione, profilo, regola CF, Action, vista, 4 lingue, test | nuova categoria di dato: informativa, DPO, cifratura, cancellazione |
| (b) `profiles.extra` (unico campo esistente) | stessa UI di (a), valore in JSON | no | uguale | medio-alto: come (a) meno la migrazione | come (a), ma senza vincolo di lunghezza ne' cifratura nativa |
| (c) riga omessa se manca il dato (stato attuale) | nessuna | no | -57/-65 px (1,3-2,1%) | minimo | nessun dato nuovo |
| (d) valore fittizio solo nelle fixture | fixture di test | no | uguale solo nella misura | basso, ma da solo non mostra nulla in produzione | nessun dato reale |

(a) Colonna nuova.
- File: migrazione owner `Modules/Fixcity/database/migrations/2026_06_10_123000_create_profiles_table.php` (intestazione: "modificare QUESTO file + bump timestamp", blocco `tableUpdate` con `hasColumn`; esiste anche `2026_06_05_090000_create_profiles_table.php` con la stessa intestazione: sceglierne una e non crearne una terza); `Models/Profile.php` (`$fillable`, cast `encrypted`, docblock); `Modules/Gdpr/.../RegisterWidget.php` (proprieta' + `#[Validate]`) e `Themes/Sixteen/.../auth/register.blade.php`; creazione del profilo alla registrazione; pagina profilo Volt se il dato e' modificabile; `BuildTicketReviewSummaryAction.php:114`; `TicketForm.php:254-272` e `HasTicketAuthorData.php:29-38` (leggere `data_get($user->profile, 'fiscal_code')`, come per il telefono a `:129`, e togliere `codice_fiscale`); lang `fixcity::ticket_review.author.fields.fiscal_code` e `gdpr::register.fields.fiscal_code` in it/en/de/es (la chiave `fiscal_code` in `Modules/User/lang/*/registration.php` esiste solo in en e de: it 0, es 0).
- Validazione: nuova Rule (nessun pacchetto): trim + maiuscolo, `^[A-Z]{6}[0-9LMNPQRSTUV]{2}[A-EHLMPR-T][0-9LMNPQRSTUV]{2}[A-Z][0-9LMNPQRSTUV]{3}[A-Z]$` (include l'omocodia) e carattere di controllo (somma di 15 caratteri con tabelle pari/dispari, modulo 26). Verifica solo la forma, non che il CF sia della persona: l'etichetta deve dire "dichiarato".
- GDPR: base giuridica art. 6.1.e per un Comune (non il consenso, considerando 43), valida solo se il CF e' necessario alla gestione della segnalazione; oggi non lo e' (il ticket usa `owner_id`, la conferma l'email dell'account). Minimizzazione art. 5.1.c e art. 25. Serve aggiornare l'informativa pubblicata dal Comune (`GetPublishedPrivacyPolicyAction`; `lang/it/ticket.php:571`), il registro dei trattamenti (`treatments` ha 0 righe nel DB di sviluppo) e la conferma del DPO (non e' un parere legale). Cancellazione: `profiles` ha soft delete, quindi azzerare il campo. Mascheramento: il riferimento mostra il CF in chiaro al proprietario nel riepilogo; mascherarlo (`GLA**********01Y`) cambia il testo rispetto al riferimento (decisione di prodotto); nei log mai (pattern esistente: `Gdpr/.../RegisterWidget.php` logga solo `email_hash` sha256); nelle email mai (`TicketAssignedNotification` e `TicketStatusChangedNotification` oggi non leggono il profilo: grep `->profile` in `Modules/Fixcity/app` trova solo 3 Action); nel tracking pubblico mai (G-20). `User` e `Profile` non usano `LogsActivity` (grep: 0), quindi il CF non entrerebbe in `activity_log` per default.
- Back office: va fatto passare il proprietario del ticket a `getAuthorHtml` (`$record->owner`), altrimenti il BO mostra il CF dell'operatore; decidere quali ruoli vedono il dato. `ProfileInfolist.php:30` e `ProfilesTable.php:29` (User) mostrano `phone` ma non una colonna nuova: nessuna esposizione accidentale.
- Test: unit sulla Rule (valido, non valido, omocodia, minuscolo, spazi); feature sulla registrazione che salva il CF nel profilo e lo cifra a riposo; feature BO (ticket del proprietario A, operatore B: la scheda mostra A); riepilogo con e senza CF; aggiornare `BuildTicketReviewSummaryActionTest.php:258-270` (oggi vieta il testo "Codice Fiscale"); lasciare i due test che vietano le chiavi `author_*` nello stato; 4 lingue; PHPStan a zero.

(b) Campo esistente: nessuno e' compatibile (`bio`, `preferences`, `status`, `type` hanno un altro scopo). Solo `profiles.extra`.
- File: gli stessi di (a) tranne la migrazione; lettura `$profile->extra->get('fiscal_code')`. Il candidato indicato da DCP-33 punto 5.
- Validazione: la stessa Rule, ma niente `string(16)` ne' cast `encrypted` (andrebbe cifrato a mano).
- GDPR: come (a); in piu' un dato personale dentro un JSON generico e' piu' difficile da trovare per accesso e cancellazione; `extra` e' gia' nel `$fillable`, quindi un campo di form mal scritto potrebbe scriverci altro.
- BO e test: come (a). Risparmia solo la migrazione, non il costo applicativo.

(c) Riga omessa quando manca il dato.
- File: nessuno per il passo 3 (e' gia' cosi). Per il passo dati: togliere da `TicketForm.php` la variabile (258) e la coppia `<dt>/<dd>` vuota (264-265), e cancellare `getAuthUserFiscalCode()` (codice morto): -6 errori PHPStan senza `@phpstan-ignore`. Lasciare il punto d'innesto `BuildTicketReviewSummaryAction.php:114`.
- Altezza: -57/-65 px come da tabella; misura con filtro dichiarato: `parity-gate.sh ... --hide-ref '<riga CF del riferimento>'` (02: `p.card-info`; 03: il `div.single-line-info` che contiene l'etichetta, selettore da fissare con DCP-47). Il gate stampa che e' una misura filtrata.
- GDPR e BO: nessun impatto, nessun dato nuovo. Il BO smette di mostrare una riga CF vuota.
- Test: restano i tre test esistenti; aggiungerne uno che il passo dati non emetta l'etichetta "Codice fiscale" senza sorgente.

(d) Valore fittizio solo nelle fixture.
- Da sola non fa comparire nulla: il codice non ha dove leggerlo. Ha senso solo in coppia con (a) o (b), per la misura di DCP-47 (`--prepare`, utente "Giulia Bianchi", id `01a13598-6d76-700b-b7b8-82b0d89274b7`, profilo id 8).
- Il valore del riferimento fallisce il checksum: una Rule rifiuterebbe `GLABNC72H25H501Y` (valido sarebbe `GLABNC72H25H501R`, un glifo di differenza, stessa altezza). Scrivere la fixture direttamente su DB/modello, oppure usare il valore valido; mai seed o valori fittizi in un DB reale; mai un fallback "se local mostra un CF di esempio".

### 5. Raccomandazione
Adottare (c): riga omessa quando manca il dato, con la deviazione dichiarata e misura filtrata con `--hide-ref`.
1. Non esiste una sorgente: 0 colonne su 1611, nessun SPID/CIE, registrazione e profilo non chiedono il CF. Qualunque valore sarebbe autodichiarato e non verificato, ma apparirebbe in "Dati Generali" come dato dell'identita'.
2. La scelta contraria era gia' stata fatta e verificata il 2026-09-27 (G-22): niente dati personali che il ticket non persiste.
3. Una segnalazione non richiede il CF; aggiungerlo apre informativa, DPO, registro, cifratura, cancellazione e una UI di inserimento che oggi non esiste neanche per il telefono (6-8 file, 1 migrazione, 4 lingue).
4. Il costo e' misurato e piccolo (1,3-2,1% dell'altezza) e si neutralizza nella misura con uno strumento che il gate ha gia'.
5. Reversibile: l'innesto e' una riga (`BuildTicketReviewSummaryAction.php:114`).
Se l'utente decide che la riga serve: (a), con ok esplicito alla colonna, conferma del DPO e correzione del BO (proprietario, non operatore). Non (b): stesso costo applicativo, meno garanzie.

Da NON fare:
- Non leggere `config('comune.cf')` ne' `Tenant.tax_code` (CF dell'ente, 11 cifre).
- Non calcolare un CF dal nome (mancano luogo e data di nascita: sarebbe inventato) ne' inserire il valore del riferimento in seed o produzione (checksum errato).
- Non aggiungere la colonna a `users` (tabella condivisa del modulo User, repo separato) ne' a `fixcity_user.profiles` (orfana).
- Non creare colonne senza ok esplicito (regola "ask first") ne' un terzo file `create_profiles_table`.
- Non sopprimere PHPStan con `@phpstan-ignore` o cast: rimuovere le letture inesistenti.
- Non lasciare `getAuthorHtml` su `Filament::auth()->user()` nel BO se una sorgente viene introdotta.
- Non mostrare il CF nel tracking pubblico, nei log o nelle email; non cancellare i tre test "never invents" senza una decisione scritta.

### 6. Non verificato e incidentale
- Non ho eseguito Pest ne' modificato nulla. Il checksum e' stato calcolato con uno script nello scratchpad, non con una libreria. Le implicazioni GDPR sono tecniche, non un parere legale.
- Il front office autenticato non e' osservabile ora: `/it/auth/login` e `/it/auth/register` rispondono 500 per `syntax error, unexpected token "endif"` in `Themes/Sixteen/resources/views/pages/[container0]/[slug0]/index.blade.php` (mtime 14:14 UTC, circa un minuto prima della verifica: modifica in corso di un'altra sessione, non mia). Quindi "cosa mostra il FO" e' dedotto da codice e test.

## Decisione richiesta all'utente
Quale opzione adottare. Non si applica nulla senza.

### Nota della sessione tema (PHPStan 14:4x, ordine permanente: PHPStan a zero)
`phpstan analyse Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php` segnala 7 errori che NON sono del cablaggio del riepilogo (corretto il solo errore a riga 62, mio): (1) riga ~257-258, `getAuthorHtml()`: `Access to an undefined property Modules\Fixcity\Models\User::$codice_fiscale` e `::$fiscal_code`, piu' `nullsafe.neverNull` e `Cannot cast mixed to string`: il codice cerca un campo che, per DCP-48, non ha una sorgente (la decisione di prodotto e' aperta): finche' non esiste una colonna o un campo, questa lettura va tolta (o la decisione presa) e non indovinata con due nomi; (2) riga 113, `getSummarySectionSchema()` dichiara `list<...>` ma restituisce un array con chiave `review_map` (la `CoordinatePicker` non in sola lettura che DCP-33 sostituisce con `geo::components.point-map`). Proprietario: sessione sul wizard; da chiudere insieme a DCP-48 e DCP-33.

