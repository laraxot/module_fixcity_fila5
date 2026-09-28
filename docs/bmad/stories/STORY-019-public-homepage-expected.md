---
title: Public Homepage Expected Behavior
id: STORY-019
author: BMAD
status: to_do
priority: must

## Descrizione
Questo documento definisce cosa dovrebbe apparire sulla homepage pubblica di Fixcity (http://localhost:8001/it).

## Cosa dovrebbe vedere l'utente

### 1. Header / Navigazione
- Logo Fixcity a sinistra
- Link di navigazione: Home, Segnalazioni, Mappa, Contatti
- Bottone "Accedi" (login) o nome utente (se loggato)
- Selettore lingua (IT, EN, ...)

### 2. Hero Section (banner principale)
- Titolo principale: "Segnala un disservizio" (o simile)
- Sottotitolo: "La tua segnalazione arriva direttamente al Comune"
- Bottone CTA: "Crea nuova segnalazione" → link a /tickets/create
- Immagine/sfondo coerente con Design Comuni

### 3. Sezione "Ultime Segnalazioni"
- Elenco delle ultime segnalazioni pubbliche (ultime 6-8)
- Per ogni segnalazione:
  - Titolo
  - Categoria/tipo
  - Stato (badge colorato: PENDING, IN_PROGRESS, RESOLVED)
  - Data di creazione
  - Bottone "Leggi di più" → link al dettaglio

### 4. Sezione "Come funziona"
- 3 step: "1. Segnala → 2. Verifica → 3. Risolvi"
- Icone e brevi descrizioni

### 5. Sezione "Statistiche"
- Numero totale segnalazioni
- Segnalazioni risolte quest'anno
- Tempo medio di risposta

### 6. Mappa (opzionale)
- Mappa interattiva con marker delle segnalazioni recenti
- Click su marker → popup con titolo e stato

### 7. Footer
- Link utili (Privacy, Cookie, Contatti)
- Crediti
- Link social (se presenti)

## Regole di design
- Stile: Design Comuni (Tailwind + bootstrap-italiana)
- Responsive: mobile, tablet, desktop
- Accessibilità: WCAG 2.1 AA
- Multilingua: tutte le stringhe tramite __()
- Nessuna parola in italiano hardcoded nei file Blade

## Criteri di accettazione
- [ ] Tutte le stringhe sono tradotte tramite __()
- [ ] Layout responsive funzionante
- [ ] Colori coerenti con Design Comuni
- [ ] Navigazione completa e funzionante
- [ ] Link interni funzionanti
- [ ] Accessibilità verificata

## Second Brain
- `docs/chat/homepage-expected.md`
- `docs/wiki/log.md` aggiornato