<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Filament;

use Modules\Fixcity\Filament\Resources\TicketResource\Pages\EditTicket;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * SEC-08: nella modifica libera del back office `status` e `priority` arrivavano da campi Hidden riscrivibili dal browser,
 * aggirando transizioni consentite, notifiche al cittadino e timeline. Il salvataggio ora li scarta.
 */
it('drops status and priority from the edit form data before saving', function (): void {
    $data = [
        'name' => 'Buca in via Roma',
        'content' => 'Profonda e pericolosa per i ciclisti',
        'status' => 'closed',
        'priority' => 'critical',
    ];

    $saved = EditTicket::withoutWorkflowFields($data);

    Assert::assertSame(['name' => 'Buca in via Roma', 'content' => 'Profonda e pericolosa per i ciclisti'], $saved);
});

it('leaves form data without workflow fields untouched', function (): void {
    $data = ['name' => 'Lampione spento', 'location' => ['latitude' => 45.6, 'longitude' => 12.2, 'address' => 'Via Roma']];

    Assert::assertSame($data, EditTicket::withoutWorkflowFields($data));
});
