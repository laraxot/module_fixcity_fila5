<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildPersonalAreaTicketRowAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-287 (FO-2-32): lo stato ha UNA etichetta per lingua, quella dell'enum (`ticket_status_enum`), la stessa di elenco, dettaglio e
 * notifiche. L'area personale aveva un secondo elenco (`personal_area.status`) con genere e parole diverse: "Aperta" qui, "Aperto" li'.
 */
it('labels every status in the personal area exactly as the list and the detail do', function (string $locale): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $row = app(BuildPersonalAreaTicketRowAction::class);

    foreach (TicketStatusEnum::cases() as $status) {
        $ticket = TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'status' => $status]);
        $personalArea = $row->execute($ticket, $locale)['status_label'];

        app()->setLocale($locale);
        Assert::assertSame($status->getLabel(), $personalArea, $locale.' / '.$status->value);
    }
})->with(['it', 'en', 'de', 'es']);
