<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Modules\Fixcity\Actions\SubmitCitizenTicketRatingAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('rejects rating outside 1-5 range', function (): void {
    $ticket = new Ticket(['status' => TicketStatusEnum::RESOLVED->value]);

    try {
        app(SubmitCitizenTicketRatingAction::class)->execute($ticket, 0);
        Assert::fail('Expected ValidationException was not thrown');
    } catch (ValidationException $exception) {
        Assert::assertInstanceOf(ValidationException::class, $exception);
    }
});
