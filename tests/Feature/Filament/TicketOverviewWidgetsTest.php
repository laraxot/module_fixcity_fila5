<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Livewire\Livewire;
use Modules\Fixcity\Filament\Widgets\TicketOverview;
use Modules\Fixcity\Filament\Widgets\TicketSlaOverviewWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Filament\Widgets\XotBaseStatsOverviewWidget;

uses(TestCase::class);

it('uses the shared Xot stats widget base for both PA overview widgets', function (): void {
    $ticketParent = (new ReflectionClass(TicketOverview::class))->getParentClass();
    $slaParent = (new ReflectionClass(TicketSlaOverviewWidget::class))->getParentClass();

    expect($ticketParent instanceof ReflectionClass ? $ticketParent->getName() : null)
        ->toBe(XotBaseStatsOverviewWidget::class)
        ->and($slaParent instanceof ReflectionClass ? $slaParent->getName() : null)
        ->toBe(XotBaseStatsOverviewWidget::class);
});

it('renders localized ticket and SLA metrics in the PA queue', function (): void {
    $operator = UserFactory::new()->createOne();
    Filament::setCurrentPanel('fixcity::admin');
    $this->actingAs($operator);

    Livewire::test(TicketOverview::class)
        ->assertSuccessful()
        ->assertSee(__('fixcity::ticket_kpi.stats.total.label'))
        ->assertSee(__('fixcity::ticket_kpi.stats.backlog.label'));

    Livewire::test(TicketSlaOverviewWidget::class)
        ->assertSuccessful()
        ->assertSee(__('fixcity::ticket_kpi.sla.avg_hours.label'))
        ->assertSee(__('fixcity::ticket_kpi.sla.resolved_total.label'))
        ->assertSee('—');
});
