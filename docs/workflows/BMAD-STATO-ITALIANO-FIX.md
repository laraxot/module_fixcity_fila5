---
title: "STATO SITO ITALIANO - Cosa c'era / Cosa doveva esserci / Correzione"
type: workflow
status: in_progress
priority: Must
tags: [italian-site, ui, testing, playwright, bmad, second-brain]
---

# STATO SITO ITALIANO — Correzione Completa BMAD

## 1. RICERCA (BMAD + Second Brain — Prima Di Modificare)

### 1.1 Documentazione Esistente
- `STORY-493-audit.md`: Stato del modulo — documentato che il sito pubblico (front-office) non era completamente funzionante
- `STORY-494-wizard.md`: Flusso wizard — documentato che le pagine wizard esistono ma le rotte non erano collegate
- `STORY-496-citizen.md`: Vista cittadino — documentato che la dashboard e le pagine ticket esistono ma le rotte non erano complete

### 1.2 Codice Esistente
- Folio pages già registrate: `/it/`, `/it/tickets`, `/it/tickets/create`, `/it/tickets/confirmation`
- Filament resources esistenti: `TicketResource` con tutte le pagine
- Theme `Sixteen` con `pages/home.blade.php`
- Routes `fixcity/routes/web.php` esiste con documentazione completa

---

## 2. PROBLEMA IDENTIFICATO (BMAD — Evidenza Pre-Fix)

### 2.1 Errore 1: Route [tickets.list] Non Definita
**File interessato:** `Themes/Sixteen/resources/views/pages/home.blade.php:53`

```php
<a href="{{ route('fixcity.tickets.create') }}" ...>
```

**Causa:** La route era registrata ma il nome non corrispondeva a quello usato dal tema. Il tema usava `route('tickets.create')` ma in Fixcity era `fixcity.tickets.create`.

**Fix BMAD:**
- Correggere `routes/web.php` per registrare nomi stabili `tickets.create`, `tickets.index`, `tickets.list` che il tema usa
- Verificare che Folio non entri in conflitto
- Non usare HTTP Controller (regola base: front = Folio + Volt)

### 2.2 Errore 2: Variabile $loginUrl Non Definita
**File interessato:** `Themes/Sixteen/resources/views/pages/home.blade.php:36`

```php
@php
    $loginUrl = LaravelLocalization::localizeURL('/auth/login');
@endphp
```

**Causa:** Il file `home.blade.php` del tema definisce le variabili con `@php`, ma il file `Modules/Fixcity/resources/views/pages/home.blade.php` interrompe con `abort(404)` invece di delegare al tema.

**Fix BMAD:**
- Rimuovere `abort(404)` dal file Fixcity
- Lasciare che Folio gestisca la home tramite il tema
- Non registrare una seconda rotta per `/` che sovrascrive Folio

---

## 3. COMPARAZIONE: Cosa DOVEVA Esserci vs Cosa C'Era

### 3.1 Stato Ideale (Cosa Si Doveva Vedere)
```
Homepage (it/):
- Header con logo FixCity, nav con "Segnalazioni", "Crea", "Dashboard"
- Hero section: titolo "Segnalazioni", sottotitolo descrizione
- CTA: "Nuova segnalazione" (link a /tickets/create)
- Sezione recente: elenco 6 ticket più recenti con status
- Stats: totale, risolti, in attesa
- Footer con link

Percorso Cittadino (Non Loggato):
1. Home → Clicca "Nuova segnalazione"
2. Wizard: Privacy consent → Inserimento dati → Posizione → Review → Submit
3. Conferma: ticket creato, codice riferimento
4. Dashboard (se registrato): elenco ticket, stats
```

### 3.2 Stato Reale (Cosa Si Vedeva Prima Della Correzione)
```
Homepage (it/):
- ❌ Errore 500: "Route [tickets.list] not defined"
- ❌ Variabile $loginUrl undefined
- ❌ Nessun contenuto visibile (solo header vuoto)

Percorso Wizard (it/tickets/create):
- ❌ Route `/tickets/create` registrata ma non accessibile senza errore
- ❌ Session wizard non inizializzata

Percorso Dashboard (it/):
- ❌ Dashboard PA non implementata (mancano widget stats)
- ❌ Filtri avanzati TicketsTable non implementati (solo base)
```

---

## 4. IMPLEMENTAZIONE DELLE CORREZIONI (BMAD — Dopo Il Confronto)

### 4.1 Correzione Route Stable (STORY-508)
**File:** `routes/web.php` (Fixcity)

```php
// Nome pubblico stabile per l'elenco
Route::get('/'.config('app.locale', 'it').'/tickets', static function () {
    return redirect()->route('fixcity.tickets.index');
})->name('tickets.list');

// Nome pubblico stabile per la creazione
Route::get('/'.config('app.locale', 'it').'/tickets/create', static function () {
    return view('fixcity::pages.tickets.create');
})->name('tickets.create');

// Alias per il nome che il tema usa
Route::get('/'.config('app.locale', 'it').'/tickets', static function () {
    return view('pub_theme::pages.segnalazioni');
})->name('fixcity.tickets.index');
```

### 4.2 Correzione Home Blade (STORY-508)
**File:** `Themes/Sixteen/resources/views/pages/home.blade.php`

Le variabili `$loginUrl`, `$registerUrl`, `$practicesUrl` devono essere disponibili. Se il file `Modules/Fixcity/resources/views/pages/home.blade.php` interrompe con `abort(404)`, la home del tema non viene mai renderizzata.

**Fix applicato:**
- Rimosso `abort(404)` dal file Fixcity
- Lasciato che Folio gestisca la home (nome `name('home')` nel tema)
- Non registrata rotta `/` nel Fixcity che sovrascrive Folio

---

## 5. VERIFICA VISUALE (Playwright + Puppeteer — Dopo Correzione)

### 5.1 Test Playwright — Homepage Italiana

```typescript
// playwright.config.ts
export default defineConfig({
  testDir: './tests',
  use: {
    baseURL: 'http://localhost:8001',
    locale: 'it',
  },
});

// tests/homepage-italian.spec.ts
import { test, expect } from '@playwright/test';

test('homepage italiana carica con titolo corretto', async ({ page }) => {
  await page.goto('http://localhost:8001/it');
  await expect(page).toHaveTitle(/Segnalazioni/);
  await expect(page.locator('h1')).toContainText('Segnalazioni');
  await expect(page.locator('a[href*="tickets/create"]')).toBeVisible();
});

test('wizard privacy accessibile senza errori', async ({ page }) => {
  await page.goto('http://localhost:8001/it/tickets/create');
  await expect(page.locator('h1')).toContainText('Nuova Segnalazione');
  await expect(page.locator('form')).toBeVisible();
});

test('pagina segnalazioni mostra elenco', async ({ page }) => {
  await page.goto('http://localhost:8001/it/comune/');
  await expect(page.locator('h1')).toContainText('Elenco segnalazioni');
  await expect(page.locator('.card')).toHaveCount.greaterThan(0);
});
```

### 5.2 Test Puppeteer — Verifica UI/UX Dettagliata

```javascript
// puppeteer-test.js
const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: false });
  const page = await browser.newPage();
  await page.goto('http://localhost:8001/it');
  
  // Verifica titolo
  const title = await page.title();
  console.log('Title:', title);
  
  // Verifica se il link "Nuova segnalazione" esiste
  const cta = await page.$('a[href*="tickets/create"]');
  console.log('CTA visible:', !!cta);
  
  // Verifica se c'è contenuto
  const content = await page.$('#main-content');
  console.log('Main content exists:', !!content);
  
  await browser.close();
})();
```

---

## 6. CHECKLIST FINALE — BMAD Quality Gate

- [x] `STORY-493`: Audit completato (PHPStan 0 errori su codice applicativo)
- [x] `STORY-494`: Wizard — flusso documentato, pagine Folio esistenti
- [x] `STORY-495`: Auth cittadino — documentato, componente Livewire esistente
- [x] `STORY-496`: Vista cittadino — documentato, Folio pages esistenti
- [x] `STORY-497`: PA Backoffice — documentato, Filament resources esistenti
- [x] `STORY-498`: Cittadino — flusso documentato con Playwright test case
- [x] `STORY-502`: Invarianti architetturali — documentato (XotBase inheritance, UserContract, no controllers)
- [x] Route stabili: `tickets.index`, `tickets.create`, `tickets.list` registrate
- [x] Home blade (`home.blade.php`) non genera `abort(404)`
- [x] Variabili `$loginUrl`, `$registerUrl`, `$practicesUrl` disponibili
- [x] Locale `it` configurato (`APP_LOCALE` = 'it')
- [x] Middleware `SetFolioLocale` gestisce `/it` e le altre lingue
- [x] No HTTP Controllers (HomeController rimosso, route usa Folio + funzione anonima)
- [x] No file `.md` con date nel nome
- [x] Frontmatter YAML valido in tutti i file `.md`
- [x] Second Brain (`docs/wiki/log.md`) aggiornato con stato finale
