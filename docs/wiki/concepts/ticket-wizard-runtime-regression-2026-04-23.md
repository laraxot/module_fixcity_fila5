# Segnalazione Wizard Runtime Regression — 2026-04-23

## Sintomi

- Accesso diretto a `/it/tests/segnalazione-crea?step=form.dati-della-segnalazione::data::wizard-step`: il tasto `Indietro` non torna allo step precedente.
- Accesso da `/it/tests/segnalazione-crea` e click su `Avanti`: nello step dati la mappa Leaflet puo' restare invisibile.
- Nello step dati la mappa puo' sembrare disabilitata per effetto di un overlay/filtro bianco.

## Best practices

- Il widget Fixcity deve delegare la navigazione al `Wizard` Filament tramite `callSchemaComponentMethod()`.
- Lo stato server-side `wizardStartStep` deve restare coerente con l'indice Filament anche quando si arriva da query string.
- La view del widget resta markup semantico Design Comuni e renderizza `{{ $this->form }}`; niente workaround JS/CSS inline.
- I problemi mappa si risolvono nel componente Geo e nel CSS generico del tema, non nel widget ticket.

## Bad practices

- Calcolare lo step corrente dalla query string in Blade: dopo una navigazione Livewire la query e' stantia.
- Fare override solo per `segnalazione-crea`, `tests.segnalazione-crea`, `.ticket-wizard-root` o `.segnalazione-wizard-root`.
- Duplicare il footer wizard Filament con logica divergente senza mantenere allineato `wizardStartStep`.

## False friends

- `?step=...` non garantisce che la property Livewire `wizardStartStep` sia gia' coerente al render.
- La mappa bianca non implica necessariamente errore tile: spesso e' Leaflet inizializzato mentre il container era invisibile o aveva dimensioni non stabili.
- Un bottone `wire:click="previousStep"` puo' esistere e non funzionare se l'indice server-side non rappresenta lo step visuale corrente.

## Piano operativo

1. Verificare `resolveInitialStepFromQuery()` e la sincronizzazione di `wizardStartStep`.
2. Verificare con browser il flusso privacy -> avanti e lo step diretto con query.
3. Applicare fix riusabili in Xot/Geo/Sixteen, evitando eccezioni per pagina.
4. Eseguire `php -l`, `node --check`, build/copy tema, clear cache, curl/browser check.
