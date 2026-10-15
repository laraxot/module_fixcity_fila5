<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature\Livewire;

use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    /** @var TestCase $this */
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });

    $this->user = UserFactory::new()->createOne();
    actingAs($this->authUser());
});

describe('CreateTicketWidget validation contract', function (): void {
    test('requires a ticket name', function (): void {
        Livewire::test(CreateTicketWidget::class)
            ->set('data.name', '')
            ->set('data.content', 'Valid content')
            ->call('submit')
            ->assertHasErrors(['data.name']);
    });

    test('requires ticket content', function (): void {
        Livewire::test(CreateTicketWidget::class)
            ->set('data.name', 'Valid name')
            ->set('data.content', '')
            ->call('submit')
            ->assertHasErrors(['data.content']);
    });

    test('requires privacy acceptance', function (): void {
        Livewire::test(CreateTicketWidget::class)
            ->set('data.name', 'Valid name')
            ->set('data.content', 'Valid content')
            ->set('data.type', TicketTypeEnum::COMPLAINT->value)
            ->call('submit')
            ->assertHasErrors(['data.accept_terms']);
    });

    test('rejects invalid latitude and longitude', function (): void {
        Livewire::test(CreateTicketWidget::class)
            ->set('data.name', 'Valid name')
            ->set('data.content', 'Valid content')
            ->set('data.type', TicketTypeEnum::COMPLAINT->value)
            ->set('data.latitude', '200')
            ->set('data.longitude', '300')
            ->call('submit')
            ->assertHasErrors(['data.latitude', 'data.longitude']);
    });

});
