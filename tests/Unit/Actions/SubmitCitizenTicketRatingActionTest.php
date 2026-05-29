<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Modules\Fixcity\Actions\SubmitCitizenTicketRatingAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Tests\TestCase;

uses(TestCase::class);

it('rejects rating outside 1-5 range', function (): void {
    $ticket = new Ticket(['status' => TicketStatusEnum::RESOLVED->value]);

    expect(fn () => app(SubmitCitizenTicketRatingAction::class)->execute($ticket, 0))
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});
