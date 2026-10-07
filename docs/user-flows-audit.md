---
type: actor-flows-audit
title: "Audit Flussi Utente: Registrazione → Login → Segnalazioni"
links: {github_issue: #402, discussion: #403}
---
# Audit Flusso Completo Utente (Fixcity + User)

## Flow End-to-End

### 1. Registrazione
- **Modulo**: `User`
- **Files chiave**: `CreateUserAction.php`, `Auth/` (Actions), `Filament` resources
- **Flusso**: `Register` (Folio/Action) → `CreateUserAction` → salva DB → `UserCreatedEvent` → notifica
- **Permessi**: pubblico (guest) per create
- **Verifica**: `User` module ha `Actions/Authentication/` — check login/register actions
- **Gap**: documentazione flusso registrazione incompleta nel modulo User (docs/ non ha story specifico per auth flow)

### 2. Login
- **Modulo**: `User`
- **Files chiave**: `Auth/` actions, `Filament` pages
- **Flusso**: `Login` (Folio/Action) → verifica credenziali → sessione → redirect
- **Policy**: `UserPolicy` / `BasePolicy`

### 3. Vedere le segnalazioni (Ticket list)
- **Modulo**: `Fixcity`
- **Files chiave**: `TicketResource` (Filament), `Pages/CreateTicket.php` (Folio pubblico)
- **Flusso**: Guest → `TicketResource` viewAny pubblico; Authenticated → `TicketResource` con filtro proprio
- **Evidence**: `actor-flows.md` (linee 11-18); `TicketPolicy::viewAny()` pubblico

### 4. Fare segnalazioni (Create Ticket)
- **Modulo**: `Fixcity`
- **Files**: `Pages/CreateTicket.php` (Folio), `CreateTicketWizardWidget`, `CreateTicketAction`
- **Flusso**: Guest pubblico → wizard → `CreateTicketAction` → salva `Ticket` → `TicketCreatedEvent`
- **Gap**: `actor-flows.md` dice "crea `segala` tramite wizard pubblico" — ma verifica se il wizard richiede auth per alcune tipologie (non documentato)

### 5. Aggiornare stato (Operatore / Citizen track)
- **Modulo**: `Fixcity`
- **Files**: `EditTicket.php`, `ChangeStatus.php`, `RecordTicketActivityAction`
- **Policy**: `TicketPolicy::update()` → role `operator` || `isOwnedByAuthenticatedUser()`
- **Flusso**: Auth → `EditTicket` → `ChangeStatus` → `RecordTicketActivityAction` → timeline aggiornata

### 6. Notifiche
- **Modulo**: `Notify` + `Fixcity` events
- **Files**: `TicketCreatedEvent`, `Notify` module channels
- **Evidence**: `actor-flows.md` linea 42 — "Eventi: TicketCreatedEvent; Notifiche: Notify module"

## Gaps Identified

1. **User auth flow**: manca story BMAD per registrazione/login nel modulo User (docs/ ha 2fa-guide.md ma no story per auth
2. **Wizard requisito auth**: `actor-flows.md` non specifica se il wizard pubblico è completamente open o se richiede login per alcune tipologie
3. **Timeline audit**: `STORY-401` (superseded) — timeline completa non implementata (manca `Timeline` model / Action)
4. **Native mobile**: `Mobile` module — adapter e capability non verificati (evidence: `fixcity-nativephp-mobile.md`)
5. **Moderatore quartiere**: ruolo non definito nei `PA_ROLES` (linea 39 `actor-flows.md`)

## Decisions
- **Audit-first**: alternativa a "implementa subito" — prima documentare flusso, poi implementare gap
- **No new controllers**: tutte le azioni tramite Action + Folio/Filament
- **Keep User module clean**: auth logic in Actions, non in Services

## Evidence References
- `laravel/Modules/Activity/docs/actor-flows.md` (sezione Actor Flows)
- `laravel/Modules/Fixcity/docs/DEEP-DIVE-002-architecture-patterns.md`
- `laravel/Modules/User/app/Actions/Auth/`
- `laravel/Modules/Notify/app/` (notification channels)
