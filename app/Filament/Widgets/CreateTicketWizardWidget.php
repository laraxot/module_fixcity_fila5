<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Filament\Schemas\Components\PubThemeWizard;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

/**
 * CreateTicketWizardWidget.
 *
 * This widget handles the ticket creation process in a multi-step wizard.
 *
 * Non-Negotiable Guard - DO NOT DELETE:
 * - CoordinatePicker
 * - GeopointPicker
 * - LatitudeLongitudeInput
 * - LeafletMarkerMapInput
 * - LocationPicker
 * - MapLocationInput
 * - MapPicker
 * - MapPositioner
 * - PlacePicker
 */
class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    protected string $view = 'fixcity::filament.widgets.ticket-create-wizard';

    /** @var array<string, mixed> */
    public array $blockData = [];

    /** @param array<string, mixed> $blockData */
    public function mount(array $blockData = []): void
    {
        $this->blockData = $blockData;
        $this->initWizardState();
    }

    /**
     * @return array<int, Step>
     */
    public function getWizardSteps(): array
    {
        return TicketForm::getWizardSteps();
    }

    public function submit(CreateTicketAction $createTicketAction): void
    {
        $data = $this->form->getState();
        $ticket = $createTicketAction->execute($data);

        $this->redirectAfterSuccess();
    }

    protected function redirectAfterSuccess(): void
    {
        $slug = SafeStringCastAction::cast(
            $this->blockData['confirmation_slug']
            ?? config('fixcity.wizard.confirmation_slug', 'segnalazione-04-conferma')
        );

        $url = route('tests.view', ['slug' => $slug]);
        $localizedUrl = LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            $url
        );

        $this->redirect($localizedUrl !== false ? $localizedUrl : $url);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'blockData' => $this->blockData,
            'pageTitle' => SafeStringCastAction::cast($this->blockData['title'] ?? __('fixcity::segnalazione.page.title.label')),
            'pageDescription' => SafeStringCastAction::cast($this->blockData['description'] ?? ''),
        ];
    }

    /**
     * @param  array<int, Step>  $steps
     */
    protected function makeWizard(array $steps): Wizard
    {
        $wizard = PubThemeWizard::make($steps)
            ->startOnStep(fn (): int => $this->wizardStartStep)
            ->columnSpanFull()
            ->skippable($this->hasSkippableWizardSteps());

        if ($this->queryStepOverrideAllowed()) {
            $wizard->persistStepInQueryString('step');
        }

        return $wizard;
    }

    /**
     * Step 1-based per layout pagina (sidebar Design Comuni, griglia): allineato allo stato del Wizard Filament.
     */
    public function getWizardDisplayStep(): int
    {
        try {
            $key = $this->getWizardComponentKey();
            $wizard = $this->getSchemaComponent($key);
            if ($wizard instanceof Wizard) {
                return min($this->wizardMaxStep(), max(1, $wizard->getCurrentStepIndex() + 1));
            }
        } catch (\Throwable) {
            // Schema non ancora risolto (primo paint): fallback allo step iniziale.
        }

        return $this->wizardStartStep;
    }
}
