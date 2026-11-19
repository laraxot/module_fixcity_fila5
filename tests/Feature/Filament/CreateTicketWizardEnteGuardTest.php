<?php

declare(strict_types=1);

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetMissingEnteFieldsAction;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Actions\InterpolateEnteTokensAction;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Monolog\Handler\NullHandler;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * PRD-04: l'informativa esiste ma i dati dell'ente che usa (titolare, DPO, PEC) non sono compilati in `ente.php`. Il wizard rifiuta
 * l'invio con un messaggio chiaro e registra un warning: niente consenso su un'informativa con segnaposto `[[ente.*]]`.
 */
it('refuses the submit while the privacy policy still has authority placeholders', function (): void {
    /** @var TestCase $this */
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return "# Informativa\n\n## Titolare del trattamento\n\nTitolare: [[ente.name]]. DPO: [[ente.dpo_name]].";
        }
    });
    // `.env.testing` ha LOG_LEVEL=error: Logger::writeLog() scarta i warning prima di emettere MessageLogged, quindi la guardia
    // risultava muta in CI. Un canale proprio a livello debug (NullHandler: nessun file scritto) rende l'asserzione indipendente dall'env.
    config([
        'logging.channels.fixcity_ente_guard_probe' => ['driver' => 'monolog', 'handler' => NullHandler::class, 'level' => 'debug'],
        'logging.default' => 'fixcity_ente_guard_probe',
    ]);
    $warnings = [];
    Event::listen(MessageLogged::class, static function (MessageLogged $log) use (&$warnings): void {
        if ($log->level === 'warning') {
            $warnings[] = $log;
        }
    });

    $user = UserFactory::new()->createOne();
    $this->actingAs($user);

    $component = Livewire::test(CreateTicketWizardWidget::class);
    $component->set('data.privacyAccepted', true);
    $component->set('data.name', 'Buca pericolosa in via Verdi');
    $component->set('data.type', TicketTypeEnum::PUBLIC_LIGHTING->value);
    $component->set('data.content', 'Buca profonda nel mezzo della carreggiata.');
    $component->set('data.location', ['latitude' => '45.4642', 'longitude' => '9.1900', 'address' => 'Via Verdi, Milano']);
    $component->call('submit');
    $component->assertHasErrors(['data.submit']);

    Assert::assertFalse(Ticket::query()->where('owner_id', $user->getAuthIdentifier())->exists());
    $logged = array_filter(
        $warnings,
        static fn (MessageLogged $log): bool => str_contains($log->message, 'authority (ente) data')
            && ($log->context['missing_fields'] ?? null) === ['name', 'dpo_name'],
    );
    Assert::assertCount(1, $logged, 'warning con i campi dell\'ente mancanti non registrato');
});

it('lists only the authority fields the policy uses and the configuration lacks', function (): void {
    config(['ente.name' => 'Comune di Prova', 'ente.dpo_name' => '', 'ente.pec' => '']);

    $policy = app(InterpolateEnteTokensAction::class)->execute(
        'Titolare: {{ente.name}}. DPO: {{ente.dpo_name}}. PEC: {{ente.pec}}. Ancora DPO: {{ente.dpo_name}}.'
    );

    Assert::assertSame(['dpo_name', 'pec'], app(GetMissingEnteFieldsAction::class)->execute($policy));
});

it('accepts a policy whose authority data is complete', function (): void {
    config(['ente.name' => 'Comune di Prova', 'ente.dpo_name' => 'Mario Rossi']);

    $policy = app(InterpolateEnteTokensAction::class)->execute('Titolare: {{ente.name}}. DPO: {{ente.dpo_name}}.');

    Assert::assertSame([], app(GetMissingEnteFieldsAction::class)->execute($policy));
});
