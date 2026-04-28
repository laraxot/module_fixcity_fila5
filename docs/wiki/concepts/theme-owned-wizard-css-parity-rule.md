# Theme-Owned Wizard CSS Parity Rule

## Regola Fixcity

`ticket-create-wizard.blade.php` nel modulo Fixcity deve restare un wrapper markup/Livewire del wizard.

Non aggiungere:

- blocchi `<style>`;
- attributi `style=""`;
- fix di spacing, width, colori o visibility legati alla parity Design Comuni.

Fixcity espone classi semantiche stabili; il tema Sixteen decide la resa visuale.

Non usare selettori CSS per slug pagina e non usare `.ticket-wizard-root` per regole che devono valere per tutti i wizard. Le regole condivise devono appoggiarsi a hook semantici riusabili (`data-step-section`, classi Design Comuni, classi del componente mappa).

## Owner

- Markup e schema: `laravel/Modules/Fixcity/resources/views/filament/widgets/ticket-create-wizard.blade.php`
- CSS parity: `laravel/Themes/Sixteen/resources/css/app.css` o CSS importati da quel file
- Build asset: dalla cartella `laravel/Themes/Sixteen`, eseguire `npm run build` e `npm run copy`

## Nota operativa

Questa regola protegge HTML parity: un Blade modulo con `<style>` locale cambia la pagina senza passare dal bundle tema e rende i confronti visuali non riproducibili.

## Mappa nello step dati

La mappa del secondo step e' un componente Geo dentro lo schema Filament. Se non appare dopo il click su "Avanti", la correzione non deve duplicare markup nel Blade del wizard: il componente Geo deve invalidare la size Leaflet quando diventa visibile, mentre il tema Sixteen gestisce solo contrasto, spacing e controlli via CSS.
