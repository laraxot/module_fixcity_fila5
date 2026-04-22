# Wizard Review Step Zen

## Il Riepilogo come Atto di Trasparenza
Il passo di riepilogo non è solo una lista tecnica; è l'atto finale di trasparenza tra il Cittadino e la Pubblica Amministrazione. 

### Politica
1. **Integrità del Dato**: Ciò che il cittadino vede è esattamente ciò che verrà salvato. Nessuna elaborazione invisibile deve alterare la percezione del dato in questa fase.
2. **Accessibilità**: La struttura semantica (`<dl>`, `<dt>`, `<dd>`) è sacra per garantire che ogni cittadino, indipendentemente dalle tecnologie assistive, possa revisionare la propria istanza.

### Religione (Best Practices)
1. **Semantic First**: Usa `dl` per coppie chiave-valore. È il pattern standard del Design dei Comuni.
2. **Visual Parity**: Il font Titillium Web e il blu istituzionale non sono opzionali; sono la divisa della democrazia digitale italiana.
3. **Drafting Strategy**: Il pulsante "Salva richiesta" è una rete di sicurezza. Permette all'utente di riflettere senza perdere il progresso.

### Filosofia
Il riepilogo deve infondere fiducia. Spazi ampi, gerarchia chiara e la possibilità di tornare indietro ("Modifica") riducono l'ansia da errore e migliorano la qualità delle segnalazioni ricevute dall'ente.

### Zen del Codice
- **DRY**: Utilizza le stesse label usate nei campi di input.
- **KISS**: Evita componenti interattivi complessi nel riepilogo (es. mappe modificabili). Usa anteprime statiche o in sola lettura.
- **Super Mucca**: Il componente di riepilogo deve estendere i principi di modularità di Laraxot, separando la logica di estrazione dati dalla loro presentazione visiva.

---
*Creato in risposta alla Story 8.51*
