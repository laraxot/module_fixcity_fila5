---
title: Admin Configuration Flow
id: STORY-017
author: BMAD
status: done
priority: high

## Attore: Admin (Super Admin)

### Cosa vede dopo login (Admin view)
- **Admin Dashboard** → Configurazione sistema
- **Moduli** → Elenco moduli installati
- **Themes** → Elenco temi installati
- **Utenti** → Gestione utenti (con Xot)
- **Impostazioni** → Configurazione globale

### Cosa può fare
- ✅ Tutto quello che fa PA Operator
- ✅ Configurare il sistema (module.json, composer.json)
- ✅ Gestire gli utenti
- ✅ Configurare i temi
- ✅ Gestire i provider del modulo
- ✅ Eseguire migrazioni/seeder
- ✅ Gestire i permessi (Policies)
- ✅ Visualizzare log e audit
- ❌ Non può eliminare dati permanentemente (forward-only)

### Flusso di navigazione
1. **Admin Dashboard** → `GET /admin/settings`
2. **Moduli** → `GET /admin/modules` → Lista moduli
3. **Themes** → `GET /admin/themes` → Lista temi
4. **Users** → `GET /admin/users` → Gestione utenti
5. **Settings** → `GET /admin/settings/config` → Configurazione
6. **Logs** → `GET /admin/logs` → Log di sistema

### Regole architetturali
- Module providers dichiarati in `module.json` + `composer.json`
- Nessun `$this->app->register()` nel provider genitore
- `module-providers-manifest` regola
- Composer root minimal (nwidart)

### Second Brain
- `docs/chat/admin-config-flow.md`
- `docs/wiki/log.md` aggiornato

### Quality Gate
- PHPStan 0 errori su tutti i moduli
- Test di integrazione passano
- Module root cleanup completato