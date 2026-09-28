---
title: "FixCity Mobile — contratto NativePHP iOS/Android"
type: story
status: in_progress
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, mobile, nativephp, ios, android, geojson, investor-demo]
module: Fixcity
qmd: "FixCity NativePHP mobile iOS Android GeoJSON investor demo"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - competitor-benchmark.md
  - ../../../../Modules/Mobile/docs/stories/1.8.nativephp-integration.story.md
  - ../../../../docs/chat/competitor-benchmark-gap-analysis.md
---

# FixCity Mobile — contratto NativePHP iOS/Android

## Decisione BMAD

Il modulo `Mobile` è il contenitore della distribuzione NativePHP per iOS e
Android. La mappa mobile non avrà un dataset parallelo: usa lo stesso contratto
GeoJSON pubblico di FixCity (`/data/tickets.json`, con API `/api/tickets/geojson` come
fallback). Questo elimina divergenze tra demo web, backoffice e app.

Il modulo Mobile esistente contiene ancora superfici storiche del dominio Restaurant
(waiter, tavoli, ordini). Non vanno mescolate automaticamente con il dominio FixCity:
il prossimo slice nativo deve esporre segnalazioni tramite Actions/DTO del dominio
Fixcity e non importare modelli Restaurant.

## Stato verificato il 2026-09-27

- `nativephp/mobile` installato nel lockfile alla versione `4.5.2`.
- `php artisan native:version` funziona.
- `php artisan native:debug`: PHP 8.4.25 e Linux rilevati; shell Android e PHP
  embedded installati; Java/Gradle di sistema presenti, Android Studio non installato.
- `php artisan native:validate` resta un gate informativo: il progetto segue Folio +
  Volt e non introduce `routes/mobile.php` né una rotta HTTP/controller per il FO.
- APK Android debug prodotto con Gradle 8.14.5, Java 17, SDK 36, NDK 27 e CMake
  3.22.1: `nativephp/android/app/build/outputs/apk/debug/app-debug.apk`.
- Il JSON demo GeoJSON viene rigenerato dal seeder e validato come FeatureCollection
  RFC 7946 con coordinate WGS84 `[longitude, latitude]`; l'ultimo seed produce 27
  ticket in 7 città italiane.

## Acceptance criteria per il primo slice investitore

1. NativePHP installato con `native:install both` su una macchina preparata; il
   workspace `nativephp/` è generato ma resta artefatto di build, non fonte applicativa.
2. Integrare il primo screen nativo solo dopo una decisione architetturale BMAD su come
   NativePHP riceverà il percorso senza introdurre `routes/mobile.php`; fino ad allora
   il Mobile module resta shell agnostico e non dichiara una dashboard compilabile.
3. La dashboard mostra elenco, stato, categoria, città e coordinate provenienti dalla
   stessa FeatureCollection del seed; nessun JSON duplicato nel modulo Mobile.
4. Tap su una segnalazione apre il dettaglio pubblico con il capability code mai
   esposto a utenti non proprietari.
5. Camera/location sono richieste solo nel flusso create; permission copy localizzata.
6. SQLite offline conserva snapshot/read model e una coda di invio; replay idempotente,
   errori visibili e nessun ticket perso.
7. Push status-change è configurabile ma fail-closed senza provider; receipt/failed
   delivery sono osservabili.
8. Android debug APK disponibile per la demo; iOS simulator build, build firmate e
   store submission restano gate separati.

## Prerequisiti esterni non simulabili

NativePHP documenta Android Studio/SDK/JDK per Android e macOS/Xcode per iOS. Linux
può preparare codice e contratto, ma non può produrre localmente un'app iOS firmata.
Servono inoltre license NativePHP, account Apple/Google, keystore/provisioning e
segreti push. Non inserire segreti nel repository e non dichiarare la demo mobile
“pronta” prima di un build reale su entrambe le piattaforme.

## Piano di implementazione

- P0: separare il contratto Fixcity Mobile da Restaurant; definire l'adapter NativePHP
  compatibile con Folio + Volt e il read model GeoJSON testato.
- P0: provare Android debug e iOS simulator su runner appropriati; registrare log,
  versioni SDK e screenshot.
- P1: offline create con allegati, sync idempotente, push e deep link al ticket.
- P1: accessibilità nativa, localizzazione IT/EN/DE/ES e analytics privacy-safe.
- P2: store packaging, signing, TestFlight/internal track e runbook rilascio.

## Fonti tecniche

- NativePHP installazione: https://nativephp.com/docs/mobile/4/getting-started/installation
- NativePHP comandi: https://nativephp.com/docs/mobile/4/getting-started/commands
- NativePHP environment setup: https://nativephp.com/docs/mobile/1/getting-started/environment-setup
- GeoJSON RFC 7946: https://www.rfc-editor.org/rfc/rfc7946.html
