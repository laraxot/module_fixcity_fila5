---
title: "Fixcity - Traduzioni tedesche e spagnole del workflow ticket (UX-07)"
type: story
bmad: true
status: done
created: 2026-11-10
owner: Modules/Fixcity
related:
  - ../audit/PRODUCT_UX_AUDIT.md
---

# Traduzioni de/es del workflow ticket

- Fase BMAD: Quick Flow
- Branch: `dev`

## Problema

Il sito dichiara quattro lingue (it, en, de, es) con fallback italiano. 100 chiavi `fixcity::` usate dal codice e dalle
viste esistevano in italiano ma non in tedesco né in spagnolo: notifiche al cittadino e agli operatori
(`ticket.notification.*`), "segui la segnalazione" (`ticket.subscription.*`), azioni del back office
(`ticket.actions.*`), KPI (`ticket_kpi.*`), filtri e campi del wizard. Un utente tedesco o spagnolo riceveva questi
testi in italiano.

## Modifiche

| File | Contenuto |
| --- | --- |
| `lang/{de,es}/ticket.php`, `fixcity.php`, `ticket_kpi.php` | Chiavi mancanti aggiunte; i valori già presenti restano invariati |
| `lang/{de,es}/create_ticket_wizard.php`, `segnalazione.php`, `ticket_filter.php` | File nuovi con le sole chiavi usate; le altre ricadono sull'italiano chiave per chiave |
| `tests/Unit/Lang/TicketWorkflowTranslationsCoverageTest.php` | Le 100 chiavi devono esistere in de/es, non vuote, con gli stessi segnaposto (`:ticket_code`, `:status`, …) |

Glossario seguito: "Meldung" (de) e "aviso" (es) per segnalazione, come nei file già tradotti.

## Verifica

- Estrazione delle chiavi `fixcity::` usate in `Modules/Fixcity/app`, `resources`, tema Sixteen e `config/local/fixcity`:
  0 chiavi mancanti in de/es fra quelle presenti in italiano.
- `php -l` su tutti i file lingua; la stessa logica del test eseguita a mano senza Pest: 0 errori.

## Aperto

- Circa 110 chiavi usate non esistono nemmeno in italiano: in parte sono falsi positivi dell'estrazione (nomi di viste
  `fixcity::filament.*`), in parte chiavi reali come `fixcity::map.title`. Vanno controllate una per una.
- I file `tickets.php` (it, 2.349 chiavi, probabilmente generato) e le pagine CMS senza blocchi de/es (UX-08) non sono
  stati toccati.
- I testi tradotti vanno riletti da un madrelingua prima di un Comune pilota con utenti tedeschi o spagnoli.
