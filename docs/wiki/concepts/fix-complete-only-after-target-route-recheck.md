# Fix Complete Only After Target Route Recheck

## Regola

Nel flusso `Fixcity` un fix non e' completo finche' non viene ricontrollata la URL finale reale indicata dal bug report o dalla story, inclusi step/query string del wizard.

Per `segnalazione-crea` questo significa verificare la route target completa, non solo la pagina base.

## Perche'

Molti errori recenti erano "risolti" solo sul codice o su una route vicina, ma restavano presenti sulla route reale:

- fatal error su `/it/tests/segnalazione-crea`
- mappa invisibile su `?step=form.dati-della-segnalazione::data::wizard-step`
- regressioni Livewire/Leaflet che emergono solo dopo navigazione `Avanti`

La verifica finale deve essere fatta dove il bug vive davvero.

## Best Practices

- partire sempre dalla URL esatta fornita dall'utente
- includere query string e step del wizard nella verifica
- dopo ogni fix ripetere il check HTTP e il check visuale/minimo runtime
- distinguere tra "pagina si apre" e "componente funziona davvero"
- documentare l'esito della recheck nel log wiki locale

## Bad Practices

- dichiarare chiuso un fix dopo `php -l` o dopo il solo refactor
- controllare `/it/tests/segnalazione-crea` quando il bug sta nello step dati
- fermarsi a "HTTP 200" senza controllare il widget coinvolto
- validare una route di test diversa da quella segnalata

## False Friends

- `optimize:clear` passato non significa fix verificato
- un render Blade senza fatal non significa che Livewire/JS siano sani
- una mappa presente nel DOM non significa che Leaflet abbia renderizzato tile e controlli
- il click su `Avanti` e' parte del bug, non un dettaglio secondario

## Checklist minima

1. aprire la URL target completa
2. verificare assenza di fatal/server error
3. verificare il componente specifico coinvolto
4. se il bug riguarda wizard/mappe, testare anche il passaggio di step
5. registrare il risultato nei docs locali
