<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Illuminate\Contracts\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
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
    /** @var array<string, mixed> */
    public array $blockData = [];

    /** @param array<string, mixed> $blockData */
    public function mount(array $blockData = []): void
    {
        $this->blockData = $blockData;
        $this->initWizardState();
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Wizard\Step>
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

    public function render(): View
    {
        return view($this->view, [
            'blockData' => $this->blockData,
            'pageTitle' => SafeStringCastAction::cast($this->blockData['title'] ?? __('fixcity::segnalazione.page.title.label')),
            'pageDescription' => SafeStringCastAction::cast($this->blockData['description'] ?? ''),
        ]);
    }
}
