<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Arch;

use function Modules\Xot\Tests\Arch\production_namespaces_except;

require_once dirname(__DIR__, 3).'/Xot/tests/Arch/scope.php';

/*
 * I moduli condivisi vivono in piu' progetti: nessuno puo' dipendere dal modulo di Fixcity. La regola sta qui e non in
 * Xot perche' i moduli condivisi non nominano il progetto ospite (ModuleCodeIsProjectAgnosticTest).
 * I temi restano fuori: il tema del progetto usa i modelli di Fixcity per scelta.
 *
 * ADR: Modules/Xot/docs/adr/architecture-tests-pest-arch.md
 */
// Un arch() per modulo: con un array di target `not->toUse()` controlla solo che UNO non dipenda (vedi ArchitectureTest di Xot).
foreach (production_namespaces_except(['Modules\Fixcity'], 'modules') as $module) {
    arch($module.' does not depend on the Fixcity module')
        ->expect($module)
        ->not->toUse('Modules\Fixcity');
}
