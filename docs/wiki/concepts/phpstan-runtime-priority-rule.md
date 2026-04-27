# PHPStan Runtime Priority Rule

## Context

L'analisi `./vendor/bin/phpstan analyse Modules` ha trovato centinaia di errori. In parallelo il wizard `segnalazione-crea` ha mostrato fatal runtime reali.

La regola e': quando convivono errori statici massivi e fatal runtime su una URL critica, la priorita' va al ripristino runtime verificato.

## Best Practices

- Usare PHPStan per individuare cluster, non per perdere il focus sul bug attivo della URL utente.
- Sistemare prima:
  - parse error
  - merge conflicts residui
  - class not found
  - method signature incompatibili
- Documentare i cluster PHPStan in backlog tecnico per modulo.
- Verificare sempre la URL reale dopo ogni fix al wizard.

## Bad Practices

- Tentare cleanup tipologico diffuso mentre la pagina richiesta dall'utente e' ancora in 500.
- Chiudere il lavoro dopo `phpstan` verde locale senza controllo browser.
- Mischiare in un'unica patch fix runtime e refactor statico ampio.

## False Friends

- Un report PHPStan molto lungo non implica che il primo errore da correggere sia il piu' importante per l'utente.
- Un file “solo di docs o tipi” puo' comunque rompere l'autoload o il runtime se il parser lo incontra.

## Regola Operativa

- Sul flusso `segnalazione-crea`, la Definition of Done minima richiede:
  1. nessun fatal sulla URL reale
  2. wizard navigabile
  3. mappa visibile e stabile nello step dati
  4. solo dopo, si passa ai cluster PHPStan non bloccanti
