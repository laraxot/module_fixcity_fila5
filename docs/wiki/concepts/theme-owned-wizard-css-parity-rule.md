# Theme-Owned Wizard CSS Parity Rule

## Regola Fixcity

`ticket-create-wizard.blade.php` nel modulo Fixcity deve restare un wrapper markup/Livewire del wizard.

Non aggiungere:

- blocchi `<style>`;
- attributi `style=""`;
- fix di spacing, width, colori o visibility legati alla parity Design Comuni.

Fixcity espone classi semantiche stabili; il tema Sixteen decide la resa visuale.

## Owner

- Markup e schema: `laravel/Modules/Fixcity/resources/views/filament/widgets/ticket-create-wizard.blade.php`
- CSS parity: `laravel/Themes/Sixteen/resources/css/app.css` o CSS importati da quel file
- Build asset: dalla cartella `laravel/Themes/Sixteen`, eseguire `npm run build` e `npm run copy`

## Nota operativa

Questa regola protegge HTML parity: un Blade modulo con `<style>` locale cambia la pagina senza passare dal bundle tema e rende i confronti visuali non riproducibili.
