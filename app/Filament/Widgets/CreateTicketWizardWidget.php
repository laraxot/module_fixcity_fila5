<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    /** @var array<string, mixed> */
    public array $blockData = [];

    protected function wizardAllowStepQueryExtra(): bool
    {
        return true;
    }

    public function mount(array $blockData = []): void
    {
        $this->blockData = $blockData;
        $this->form->fill();
    }

    /**
     * @return array<int, Step>
     */
    public function getWizardSteps(): array
    {
        return TicketForm::getWizardSteps();
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public function getFormSchema(): array
    {
        return TicketForm::getFormSchema();
    }

    public function submit(): void
    {
        

        $data = $this->form->getState();
        $data['owner_id'] = Auth::id();
        $ticket=Ticket::create($data);

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

    protected function getCancelFormAction(): Action
    {
        $cancelUrl = route('tests.view', ['slug' => config('fixcity.wizard.cancel_slug', 'segnalazione-01-inizio')]);
        $localizedCancel = LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            $cancelUrl
        );
        $cancelHref = $localizedCancel !== false ? $localizedCancel : $cancelUrl;

        return Action::make('cancel')
            ->url($cancelHref)
            ->button()
            ->color('secondary');
    }
}
