# Refactoring CreateTicketWizardWidget

Documentazione del processo di rifattorizzazione del widget di creazione ticket nel modulo Fixcity, seguendo i principi di Martin Fowler e gli standard Laraxot.

## I 10 Attributi della Rifattorizzazione (Applicazione Pratica)

Di seguito i 10 punti (ispirati ai "Code Smells" di Fowler e ai benefici di Nextre) che guideranno questa rifattorizzazione:

1.  **Eliminazione del Codice Duplicato (Smell #2)**:
    - **Cosa farò**: Sposterò le definizioni degli schema (`privacy`, `data`, `summary`) interamente in `TicketForm.php`.
    - **Perché**: Attualmente il widget e la classe schema del resource hanno definizioni quasi identiche, violando il principio DRY.

2.  **Rimozione del "Middle Man" (Smell #9)**:
    - **Cosa farò**: Semplificherò il widget affinché deleghi la costruzione dello schema direttamente alla classe `TicketForm`.
    - **Perché**: Evita di avere metodi nel widget che sono solo involucri pass-through.

3.  **Miglioramento del Naming e Semantica (Smell #1)**:
    - **Cosa farò**: Rinominerò i metodi di utilità per la formattazione (es. `formatLocationSummary`) e li centralizzerò.
    - **Perché**: I nomi devono essere auto-esplicativi e la loro posizione deve essere logica.

4.  **Risoluzione della "Feature Envy" (Smell #4)**:
    - **Cosa farò**: Sposterò la logica di formattazione dei dati del ticket (tipo, location) verso la classe che gestisce lo schema o verso il modello/enum.
    - **Perché**: Il widget non dovrebbe preoccuparsi di "come" si formatta un indirizzo per il riepilogo.

5.  **Pulizia del Codice Morto (Smell #7 - Generalità Speculativa)**:
    - **Cosa farò**: Rimuoverò tutti i pickers geografici commentati (`location1`, `location2`, ecc.) nel `getDataSchema`.
    - **Perché**: Mantengono il file sporco e confuso senza aggiungere valore reale.

6.  **Approccio Incrementale (Micro-step)**:
    - **Cosa farò**: Applicherò le modifiche un pezzo alla volta: prima `TicketForm`, poi il widget, verificando ogni passaggio.
    - **Perché**: Riduce il rischio di regressioni e rende il debugging più semplice.

7.  **Test-Driven Validation**:
    - **Cosa farò**: Eseguirò i test Pest esistenti e ne aggiungerò di nuovi se necessario per coprire i casi limite del wizard.
    - **Perché**: Il refactoring non deve cambiare il comportamento esterno, e i test sono l'unica garanzia.

8.  **Uso dei Design Pattern (Schema Provider)**:
    - **Cosa farò**: Utilizzerò il pattern "Schema Provider" centralizzato in `TicketForm` per fornire componenti Filament consistenti tra frontoffice e backoffice.
    - **Perché**: Garantisce coerenza visiva e funzionale in tutto l'applicativo.

9.  **Miglioramento della Leggibilità (Clean Code)**:
    - **Cosa farò**: Ridurrò la dimensione del widget `CreateTicketWizardWidget` spostando la logica non-UI altrove.
    - **Perché**: Una classe più piccola è più facile da leggere, testare e mantenere.

10. **Quality Gate Compliance**:
    - **Cosa farò**: Verificherò il codice finale con PHPStan (Lvl 10), PHPMD e PHPInsights.
    - **Perché**: Assicura che il codice non solo funzioni, ma rispetti gli standard qualitativi più elevati definiti dal progetto.

---

## Stato Pre-Refactoring
- `CreateTicketWizardWidget.php`: ~400 righe. Contiene schema completo, logica di submission, logica di formattazione riepilogo.
- `TicketForm.php`: Contiene schema duplicato ma non completamente allineato.

## Stato Post-Refactoring (Obiettivo)
- `CreateTicketWizardWidget.php`: Focalizzato su mount, submission e redirect. Schema delegato.
- `TicketForm.php`: Unica fonte di verità per lo schema del ticket (Wizard & Form lineare).
