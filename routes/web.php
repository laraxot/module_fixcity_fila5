<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes Fixcity — vuoto di proposito
|--------------------------------------------------------------------------
|
| Religione FO/BO (BMAD + second brain):
| - Front office  = Folio + Volt + Filament (widget in pagina) + Actions
| - Back office   = Filament (Resources / Pages / Widgets)
| - Mai Http\Controllers
|
| Elenco pubblico canonico: Themes/Sixteen pages/tickets/index (name tickets.list)
| Compatibilità visuale: Themes/Sixteen pages/segnalazioni (pagina legacy)
| Dettaglio canonico: Modules/Fixcity resources/views/pages/tickets/[id].blade.php
| Create/track/confirmation: Modules/Fixcity resources/views/pages/tickets/*
|
| Non aggiungere Route::get/closure qui: creano ombre a Folio e loop
| (es. segnalazioni↔tickets). Vedi:
| docs/wiki/concepts/no-controllers-folio-volt-filament.md
| Modules/Fixcity/docs/wiki/concepts/no-controllers-folio-volt-filament.md
*/
