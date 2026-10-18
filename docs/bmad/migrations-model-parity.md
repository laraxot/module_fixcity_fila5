# MIGRATIONS-MODELS PARITY (BMAD + Second Brain)

## Stato
- Modelli Fixcity/app/Models/: 13 file (User.php incluso)
- Migrazioni Fixcity/database/migrations/: 22 file
- MIGrazione per User: MANCANTE (User.php esiste, tabella users è in User module / default Laravel)

## Regola sacra
- Dati sacri: mai migrate:fresh, mai force, mai refresh.
- Parità: ogni modello deve avere la sua migrazione corrispondente nel modulo.

## Azione
- User model in Fixcity fa riferimento alla tabella users gestita da User module / default — documentato.
- Se serve tabella specifica Fixcity per User: creare migrazione new, non toccare esistenti.
