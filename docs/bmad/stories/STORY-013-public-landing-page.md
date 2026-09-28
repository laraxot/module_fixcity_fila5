---
title: Public Landing Page Flow
id: STORY-013
author: BMAD
status: done
priority: must

## Attore: Utente non loggato (Guest)

### Cosa vede quando apre il sito
- **Home page** (`/` → `pub_theme::home`) → Benvenuto con call-to-action
- **Navigation**: Home, About, FAQ, Login/Register
- **Banner**: "Hai trovato un problema? Segnalalo in 2 minuti"
- **Map**: Visualizzazione mappa dei ticket attivi
- **Lista ticket pubblici**: Ultime segnalazioni

### Cosa può fare
- ✅ Vedere la home page e le informazioni
- ✅ Vedere la mappa dei ticket pubblici
- ✅ Leggere i ticket pubblici (elenco)
- ❌ Non può creare ticket (richiede login)
- ❌ Non può accedere al pannello admin
- ❌ Non può vedere ticket privati

### Flusso di navigazione
1. **Home** → `GET /` → `home.blade.php`
2. **Login** → `GET /login` → `auth/login.blade.php`
3. **Register** → `GET /register` → `auth/register.blade.php`
4. **Ticket pubblici** → `GET /tickets` → `tickets/index.blade.php`
5. **Dettaglio ticket** → `GET /tickets/{id}` → `tickets/show.blade.php`

### Regole architetturali
- `pub_theme` è il tema pubblico (tema Sixteen)
- Nessun accesso al backoffice senza autenticazione
- `Guest` middleware applicato alle route pubbliche
- Filament Resource non accessibile a utenti non loggati

### Second Brain
- `docs/chat/guest-welcome-flow.md`
- `docs/wiki/log.md` aggiornato
- `docs/chat/INDEX.md` aggiornato

### Quality Gate
- Nessun errore PHPStan
- Test di accesso guest passano
- UI/UX verificata con Design Comuni