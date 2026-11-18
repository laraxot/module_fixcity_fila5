<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;

/*
 * Regola: Modules/Xot/docs/rules/action-behavioral-contract.md. Le Action che creano o cambiano le segnalazioni hanno il
 * contratto in docs/contracts/ e il docblock rimanda al file. Completezza e collegamento li verifica Xot
 * (ActionContractsAreCompleteTest); qui l'elenco delle Action obbligatorie del modulo.
 */
it('gives a behavioral contract to the Actions that create reports', function (string $action, string $contract): void {
    $module = dirname(__DIR__, 2);

    Assert::assertFileExists($module.'/docs/contracts/'.$contract);
    Assert::assertStringContainsString('Contratto di comportamento: docs/contracts/'.$contract, file_get_contents($module.'/app/Actions/'.$action));
})->with([
    ['CreateTicketAction.php', 'create-ticket-action.md'],
]);
