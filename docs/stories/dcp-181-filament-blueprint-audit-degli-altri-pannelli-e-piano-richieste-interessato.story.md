---
id: dcp-181
title: "DCP-181: Filament Blueprint, audit di sicurezza degli altri pannelli e piano del registro delle richieste dell'interessato"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "filament blueprint planning-filament reviewing-filament-plans filament-security-audit audit sicurezza pannelli registro richieste interessato dcp-180"
related: [dcp-180, dcp-140, dcp-178, dcp-176, dcp-154]
---
# DCP-181: Filament Blueprint, audit di sicurezza degli altri pannelli e piano delle richieste dell'interessato

**Coordinamento:** un'altra sessione ha aperto nello stesso momento [DCP-180](./dcp-180-audit-sicurezza-filament-blueprint.story.md), che audita il pannello `fixcity/admin` e i punti trasversali (`BaseUser::canAccessPanel`, widget base di Xot). Questa story (numero preso dopo la collisione) copre gli altri 11 pannelli; i due rapporti non si sovrappongono e vanno letti insieme.

Fase BMAD: Analyze, Plan, Verify. Richiesta dell'utente: studiare https://filamentphp.com/plugins/filament-blueprint e usarlo per completare il progetto.

## Che cos'e' (dalla pagina del plugin, letta il 2026-10-14)
Estensione di Laravel Boost pubblicata dal team Filament (prezzo di listino 49 USD, licenza a pagamento, repository Composer privato `packages.filamentphp.com`). Fornisce tre skill agli agenti: `planning-filament` (scrive un piano di implementazione), `reviewing-filament-plans` (controlla il codice rispetto al piano, senza modificarlo), `filament-security-audit` (cerca errori noti di configurazione: autorizzazioni, upload, XSS, scoping delle query, CVE delle dipendenze). Non aggiunge comandi artisan propri ne' tool MCP. Non dichiarati dalla pagina: termini della licenza, requisiti minimi di PHP/Laravel, istruzioni per Claude Code.

## Stato nel progetto (verificato)
- **Gia' installato**: `filament/blueprint ^2.4` in `laravel/composer.json` (dev), versione installata 2.4.0, con `laravel/boost` 2.10.3, Filament 5.10.1, Livewire 4.4.7, Laravel 13.35.0; repository Composer `https://packages.filamentphp.com/composer` gia' configurato. Le tre skill sono attive in `laravel/.claude/skills/` e `laravel/.agents/skills/` (`boost.json` elenca `filament/blueprint`).
- Le skill sono visibili solo lavorando da `laravel/`: il `.claude` della radice punta a `bashscripts/ai/.agents`, che non le contiene.
- Il server MCP di Boost (`search-docs`) non era raggiungibile in questa sessione: la skill di pianificazione prevede in tal caso la verifica sul sorgente installato; e' quello che e' stato fatto e dichiarato nel piano.

## Che cosa e' stato fatto con Blueprint
1. **Audit di sicurezza `filament-security-audit`** su tutti i pannelli (12 pannelli, uno per modulo, nessuna multi-tenancy Filament). Quattro agenti in parallelo per categoria (A accessi, B upload, C XSS, D dati), E (dipendenze) eseguita direttamente: `composer audit` risponde «No security vulnerability advisories found» (0 avvisi, 0 pacchetti abbandonati), E1 = Pass. Rapporto: `laravel/Modules/Fixcity/docs/security/filament-security-audit.md` (vedi la sezione Esito qui sotto).
2. **Piano `planning-filament`** del registro delle richieste dell'interessato: `laravel/Modules/Gdpr/docs/blueprints/registro-richieste-interessato.md`. Resta in bozza: la skill vieta di dare per assunte scelte su dati, conservazione e azioni distruttive; le 7 decisioni aperte (D1-D7) sono nella sezione 0 del piano. La cancellazione/anonimizzazione e' esplicitamente fuori dal piano.
3. `reviewing-filament-plans`: **non applicabile ora**, nessun codice implementa un piano. Va usata dopo l'implementazione del registro, con lo stesso file e senza modificare il codice.

## Esito dell'audit (2026-10-14)
Rapporto: `laravel/Modules/Fixcity/docs/security/filament-security-audit-altri-pannelli.md`. 12 finding (A 3, B 2, C 4, D 3), E1 senza avvisi. Quattro agenti in parallelo (A-D), io E1 e la verifica nel codice dei sei piu' gravi.
**Corretti e verificati (con test dove possibile):** A-3 (qualunque utente di un pannello poteva diventare super-admin: test rosso prima, verde dopo), B-1 (tre `preserveFilenames()` su dischi serviti dal web, uno sulla document root), A-1 per i due default di bulk delete, C-2 (XSS persistente nel popup della mappa mobile, preventiva: oggi 404), D-1 (export JSON pubblico con le segnalazioni non pubbliche di chi lo lancia), D-3 (`client_secret` e `token` fuori da `toArray()`).
**Corretti in un secondo giro (stesso giorno):** A-2 (trait `RestrictsFileUploadsToSchemaComponents` su `XotBaseWidget`, test che discrimina e prova nel browser), C-4 (`Str::sanitizeUrl`), credenziali FTP del disco `photos` tolte dai 4 file di configurazione (nessun codice usava il disco). **Errore mio corretto:** il primo fix di A-1 con `authorizeIndividualRecords('delete')` negava il bulk delete ai 10 modelli senza policy; ora regola `HasXotTable::bulkDeleteRecordAuthorization()` (consente senza policy, come Filament), registrata una sola volta con `DeleteBulkAction::configureUsing()` in `XotServiceProvider` e quindi valida anche per i 15 siti espliciti e per quelli futuri (chiuso anche il punto dei 16 siti), con test.
**Aperti (decisione o prova mancante):** B-2 (allowlist globale dei 28 campi upload: serve l'elenco dei tipi davvero usati, alcuni caricano video), C-1, C-3 (`Blade::render` sul contenuto CMS), D-2, i 16 siti espliciti di A-1; quattro piste fuori categoria (`JobStatusWidget::saveEnv`, `EnvWidget`, `EditUserWidget`, Passport); e **la password FTP da cambiare sul servizio** (resta nella storia di git).
**Controlli dei sei verificati:** ho riletto il codice di ciascuno; non ho riprodotto nessun exploit. Gli altri sei sono come li hanno riportati gli agenti.

## Criteri di accettazione
- [x] Il plugin e le sue tre skill sono identificati come gia' presenti e attivi (non reinstallati, nessuna credenziale toccata).
- [x] E1 (CVE di Filament, Livewire e plugin): nessun avviso.
- [x] Rapporto di audit con un ID per ogni finding (A-1...D-3), file:riga e correzione.
- [x] Piano del registro con namespace completi, docs, validazione, autorizzazione e test (bozza).
- [ ] Decisioni D1-D7 dell'utente e DPO.
- [ ] Implementazione del registro e poi `reviewing-filament-plans`.

## Chiusura di C-3, contenuto CMS valutato come Blade (2026-10-15)
- **Prova del difetto**: un blocco con `{{ system('id') }}` eseguiva `id` sul server. Test rosso in `Modules/Cms/tests/Unit/Models/Traits/HasBlocksTest.php`, output `uid=1000(...)`.
- **Causa**: `HasBlocks::compile()` passava a `Blade::render()` ogni stringa con `{{ }}`; il blocco `Themes/Sixteen/.../sidebar/system-info.blade.php` rivalutava poi i valori con un secondo `Blade::render` non escapato (blocco oggi non usato da nessun JSON).
- **Correzione**: `compile()` rende solo `LaravelLocalization::localizeURL('...')` e `route('...', [...])` con soli letterali stringa (`SAFE_EXPRESSION`); ogni altra espressione o direttiva resta testo e va in `Log::warning`. Nella vista tolto il secondo render. Nessun file nuovo.
- **Uso legittimo misurato**: DB di sviluppo, 101 pagine con 0 espressioni e 8 sezioni con 8 espressioni di 2 forme (`localizeURL`); JSON del repo, `localizeURL` e `route`. I `{{ name }}` e `{{#each}}` sono template email, un altro percorso.
- **Verifica**: HasBlocksTest 8/8 (5 rossi prima); vista reale con payload ostile, non eseguito e mostrato come testo; `/it` risponde 200 con gli stessi 8 link e nessuna `{{` grezza; PHPStan `Modules` senza errori su questi file.
- **Modifica a un test esistente**: l'asserzione `{{ 1 + 1 }}` uguale a `2` dava per atteso il comportamento pericoloso; ora il caso vale come input da lasciare invariato. Il test non e' stato cancellato.
- **Aperto**: `NotificationTemplate::compileString` (Notify) rende con `Blade::render` i template salvati nel DB, quindi chi li modifica puo' eseguire PHP. Scelta: limitare la modifica ai super-admin oppure usare un motore senza PHP. Non toccato. Contenuti di produzione con altre espressioni non misurati: ora restano testo e compaiono nel log.
- **Limite dei test**: il runner da `laravel/` non carica `tests/Pest.php` di modulo e `Cms\Tests\TestCase` ripunta sqlite sul DB di sviluppo; per questo `HasBlocksTest` usa `XotBaseTestCase`. `SectionModelTest` e' rosso 8/8 per lo stesso motivo (non toccato).

## Non verificato
Le URL `filamentphp.com/docs/5.x/...` del piano non sono state aperte; `search-docs` non disponibile; il costo e la durata della licenza non sono noti.
