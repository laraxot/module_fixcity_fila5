# Wizard Step Index e Integrazione Mappa

## Regola

I pulsanti custom del wizard devono usare lo stato Livewire canonicale (`wizardStartStep`) per chiamare il componente Filament `Wizard`. Non devono rileggere lo step da query string durante il render Livewire.

## Best Practices

- La query `?step=` serve solo per inizializzare lo step in `mount/initWizardState`.
- Dopo la prima renderizzazione, `wizardStartStep` e' la fonte dello step corrente nel wrapper Blade.
- `nextStep()` e `previousStep()` devono passare a Filament l'indice derivato da `wizardStartStep - 1`.
- La mappa dentro lo step dati e' responsabilita' del campo Geo, non del widget Fixcity.

## Bad Practices

- Calcolare `$currentStep` da `request()->query('step')` nel Blade: resta statico e diventa obsoleto dopo click Livewire.
- Applicare fix CSS al solo ticket wizard: gli altri wizard devono comportarsi uguale.
- Duplicare rendering mappa o aggiungere include manuali nel Blade del widget.

## False Friends

- Il tasto `Indietro` puo' sembrare rotto anche se il metodo Livewire viene chiamato: il problema puo' essere l'indice 0-based passato al componente Filament.
- Una mappa non visibile dopo `Avanti` non va risolta con CSS per pagina: spesso e' un problema di visibilita' step/Leaflet `invalidateSize()` o lifecycle del Web Component.

## Evidenza 2026-04-22

Nel wizard `segnalazione-crea`, lo step diretto via URL e la navigazione con `Avanti/Indietro` potevano disallineare l'indice interno del componente Filament. La correzione e' stata portata in `XotBaseWizardWidget`, non nel widget ticket, per mantenere DRY/KISS.
