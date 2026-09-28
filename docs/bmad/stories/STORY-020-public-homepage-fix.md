---
title: Public Homepage Fix - Analysis and Solution
id: STORY-020
author: BMAD
status: to_do
priority: must

## Analisi Problema

### Stato Attuale (Realtà)
- URL: http://localhost:8001/it
- Titolo: "Elenco segnalazioni" (errato, dovrebbe essere "Home" o simile)
- data-page="ticket-list": La pagina è trattata come lista ticket
- Contenuto: Mostra la lista ticket, non la homepage

### Stato Atteso (BMAD STORY-019)
- URL: http://localhost:8001/it
- Titolo: "Home - Fixcity" o "Segnala un disservizio"
- Homepage con:
  - Header con logo e navigazione
  - Hero section con CTA
  - Ultime segnalazioni
  - Come funziona
  - Statistiche
  - Footer completo

## Gap Identificati

### Gap 1: Routing
La route `/it` dovrebbe puntare alla homepage, ma punta alla lista ticket.
**Soluzione**: Creare una route specifica per la homepage pubblica.

### Gap 2: Template Homepage
La homepage non è configurata correttamente come pagina principale.
**Soluzione**: Creare un template homepage dedicato con:
- Hero section
- Sezione ultime segnalazioni
- Come funziona
- Statistiche

### Gap 3: Traduzioni
`meta description` mostra `sixteen::home.meta.description` non risolta.
**Soluzione**: Aggiungere traduzioni mancanti nel file `lang/it/home.php`.

### Gap 4: Contenuto generico
La homepage mostra contenuto del Comune, non contenuto Fixcity-specifico.
**Soluzione**: Creare una homepage Fixcity-specifica con contenuto civic reporting.

## Documentazione BMAD - Fix Plan

### Fase 1: Correzione Routing
Creare route `/it` → `HomeController@index` → view `fixcity::homepage`

### Fase 2: Creare Homepage Template
File: `laravel/Modules/Fixcity/resources/views/pages/home.blade.php`
Contenuto:
- Hero section con CTA "Segnala un disservizio"
- Sezione ultime segnalazioni pubbliche
- Sezione "Come funziona" (3 step)
- Sezione statistiche (con badge)
- Sezione mappa (opzionale)
- Footer con link utili

### Fase 3: Aggiungere Traduzioni
File: `laravel/Modules/Fixcity/lang/it/home.php`
Traduzioni necessarie:
- `title` → "Home - Segnala un disservizio"
- `meta.description` → "La piattaforma civica per segnalare disservizi nel tuo comune"
- `hero.title` → "Segnala un disservizio"
- `hero.subtitle` → "La tua segnalazione arriva direttamente al Comune"
- `hero.cta` → "Crea nuova segnalazione"
- `latest.title` → "Ultime segnalazioni"
- `steps.title` → "Come funziona"
- `steps.step1` → "Segnala"
- `steps.step2` → "Verifica"
- `steps.step3` → "Risolvi"
- `stats.total` → "Segnalazioni totali"
- `stats.resolved` → "Risolte quest'anno"
- `stats.response` → "Tempo medio di risposta"

### Fase 4: Correggere Template Esistente
File: `Themes/Sixteen/Sixteen/pages/comune/homepage.blade.php`
Modificare per:
- Usare `__()` per tutte le stringhe italiane
- Aggiungere contenuto Fixcity-specifico
- Aggiungere traduzioni mancanti

### Fase 5: Verifica con Playwright
Setup Playwright MCP per verificare:
- Titolo corretto
- Contenuto presente
- Link funzionanti
- Responsivo su mobile/desktop
- Nessuna parola italiana hardcoded

## Criteri di Accettazione

- [ ] Titolo pagina: "Home - Fixcity" o "Segnala un disservizio"
- [ ] data-page="homepage" (non ticket-list)
- [ ] Hero section con CTA visibile
- [ ] Ultime segnalazioni visibili
- [ ] Come funziona con 3 step
- [ ] Statistiche mostrate
- [ ] Tutte le stringhe traduibili tramite `__()`
- [ ] Nessuna parola italiana hardcoded nei Blade
- [ ] Meta description risolta
- [ ] Navigazione completa
- [ ] Footer corretto

## Second Brain
- `docs/chat/homepage-fix-2026-09-26.md`
- `docs/wiki/log.md` aggiornato
- `docs/chat/INDEX.md` aggiornato

## Quality Gate
- PHPStan 0 errori
- Pint 0 errori
- Traduzioni complete
- Test visivi Playwright pass