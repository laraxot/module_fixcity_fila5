---
title: "STORY-498: Stato Attuale del Percorso Cittadino - Test Playwright"
type: story
status: in_progress
priority: Must
issue_link: "https://github.com/laraxot/fixcity/issues/498"
discussion_link: "https://github.com/laraxot/fixcity/discussions/498"
tags: [citizen, playwright, puppeteer, ui, testing, stato-attuale]
---

# STORY-498 — Stato Attuale del Percorso Cittadino - Test Playwright

## Obiettivo
Documentare i flussi attuali dei percorsi utente (Cittadino, PA/Operatore, Admin, Sistema) con specifiche teste Playwright/Puppeteer che mostrino cosa SI' dovrebbe vedere vs cosa ACTUALMENTE si vede ora.

## Evidence (pre-fix)

### 1. Stato Anteriore - Percorso Cittadino di Base

**1.1 What should be seen (Ideal State):**
- Homepage italiana: `http://localhost:8765/it`
- Percorso: privacy → dati → posizione → review → submit → conferma
- Transizioni sicure con verifica dei dati
- Testo in italiano con traduzione corretta
- Modalità responsive funzionante

**1.2 What is actually seen (Current State):**
- ✅ Homepage italiana CARICATA e VISIBLE (status: 200)
- ✅ Percorso wizard EXISTE (`/it/comune/`, `/tickets/create`, ecc.)
- ⚠️ ROUTES MANCANTI: `/it/comune/` 404 (manca il prefisso locale)
- ⚠️ COMUNICAZIONE: La homepage usa corretto testo italiano (title: "Il mio Comune")

**Evidence visual (Playwright screenshot):**
```bash
npx playwright test --grep "homepage-italian" --reporter=list
```

### 2. Story Completata: Homepage Italiana

**Issue #498:** "Manca prefisso /it nella homepage italiana"

**Evidence (pre-fix):**
```bash
# Test fallito (404)
curl -I http://localhost:8765/it/comune/
# Risultato: 404 Not Found
```

**Decision:** Aggiungere route `/it/comune/` che punta alla homepage italiana

**Evidence Post-Fix Checklist:**
- [x] Route `Route::get('/it/comune/', ...)` aggiunta
- [x] Middleware `SetFolioLocale` verifica segmento `it`
- [x] Homepage italiana corretta per locale
- [x] Test Playwright passa: `npx playwright test --test-homepage-italian`

### 3. Story Bloccante: Percorso Wizard Completo

**Issue #493:** "Flusso cittadino incompleto (wizard)"

**Current State:**
- ✅ Privacy consent page exists: `/tickets/wizard/privacy.blade.php`
- ⚠️ Dati page: `/tickets/wizard/data.blade.php` (manca route entry)
- ❌ Posizione page: `/tickets/wizard/position.blade.php` non accessibile
- ❌ Review page: `/tickets/wizard/review.blade.php` non accessibile
- ❌ Conferma page: `/tickets/wizard/confirmation.blade.php` non accessibile

**Evidence (pre-fix):**
```bash
# Test case: Utilizzo la "nuova segnalazione"
given sono sulla pagina privacy
quando visito "/tickets/wizard/data"
allora vengo reindirizzato alla pagina di login
```

**Decision:** Implementare tutte le route del wizard e stati di sessione

**Evidence Post-Fix Checklist:**
- [ ] Route `/tickets/wizard/data` implementata
- [ ] Route `/tickets/wizard/position` implementata
- [ ] Route `/tickets/wizard/review` implementata
- [ ] Route `/tickets/wizard/confirmation` implementata
- [ ] Session state management implementato
- [ ] Test Playwright per tutti i passaggi del wizard

### 4. Story Bloccante: Auth Cittadino

**Issue #495:** "Autenticazione cittadino mancante"

**Current State:**
- ✅ Login form esistente (Livewire Login component)
- ❌ Pagina di registrazione non accessibile
- ❌ Flusso login/register funzionante non implementato
- ❌ Redirect post-auth verso dashboard cittadino

**Evidence (pre-fix):**
```bash
# Test case: "Posso accedere alla pagina di registrazione"
given sono sulla pagina di login
quando visito "/citizen/auth/register"
allora vengo reindirizzato alla pagina di login (non autorizzato)
```

**Decision:** Implementare registrazione cittadino e completare flusso auth

**Evidence Post-Fix Checklist:**
- [ ] `Route::get('/citizen/auth/register', ...)` implementata
- [ ] `Register` Livewire component completato
- [ ] Policy `TicketPolicy::create()` consente creazione ticket
- [ ] After registration redirect to citizen dashboard

### 5. Story Bloccante: Dashboard Cittadino

**Issue #496:** "Vista dashboard cittadino mancante"

**Current State:**
- ❌ Pagina dashboard esistente
- ❌ Elenco ticket cittadino (paginazione, filtri)
- ❌ Widget statistiche
- ❌ Navigazione ticket (index → show → activities)

**Evidence (pre-fix):**
```bash
# Test case: "Posso visualizzare la mia lista di ticket"
given sono autenticato come cittadino
quando visito "/citizen/dashboard"
allora vengo reindirizzato alla pagina di login
```

**Decision:** Implementare dashboard cittadino con statistiche e elenco

**Evidence Post-Fix Checklist:**
- [ ] `resources/views/pages/citizen/dashboard.blade.php` creato
- [ ] `app/Actions/Citizen/GetCitizenStatsAction.php` implementato
- [ ] `app/Actions/Citizen/GetCitizenTicketsAction.php` implementato
- [ ] `app/Http/Livewire/Citizen/Dashboard.php` completato
- [ ] Test Playwright per dashboard

### 6. Story Bloccante: PA Backoffice

**Issue #497:** "Backoffice PA / filtri mancanti"

**Current State:**
- ✅ `TicketResource/Pages/ListTickets.php` esiste
- ❌ Filtri avanzati non implementati (`TicketsTable.php`)
- ❌ Azioni bulk mancanti (assegnazione, stato, ecc.)
- ❌ Dashboard PA mancante (diversa da dashboard cittadino)

**Evidence (pre-fix):**
```bash
# Test case: "Posso filtrare ticket per stato, categoria, operatore"
when visito "/admin/tickets"
then vengo reindirizzato alla pagina di login
```

**Decision:** Implementare tutto il backoffice PA

**Evidence Post-Fix Checklist:**
- [ ] Completare `Tables/TicketsTable.php` con filtri
- [ ] Implementare bulk actions (assign, status change)
- [ ] Aggiungere `Pages/Dashboard.php` per PA
- [ ] Implementare tutte le azioni admin
- [ ] Test Playwright per backoffice
