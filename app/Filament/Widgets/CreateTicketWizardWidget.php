<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    
    /** @var array<string, mixed> */
    public array $blockData = [];
    /*
    public function getWizardDisplayStep(): int
    {
        return $this->getWizardStartStep();
    }

    protected function wizardAllowStepQueryExtra(): bool
    {
        return true;
    }
    */

    /**
     * @param  array<string, mixed>  $blockData
     */
    public function mount(array $blockData = []): void
    {
        $this->blockData = $blockData;
        $this->wizardStartStep = 1;
        $this->form->fill(TicketForm::getDefaultFormState());
    }

    /**
     * @return array<int, Step>
     */
    public function getSteps(): array
    {
        $steps = TicketForm::getSteps();

        return $steps;
    }

    public function submit(): void
    {
        // TEMPORANEO: bypass auth per debug salvataggio ticket da utente anonimo.
        // Ripristinare il blocco auth/redirect login dopo il collaudo campi.
        // $authUser = Auth::user();
        // if ($authUser === null) {
        //     Session::put('url.intended', url()->full());
        //     $this->redirect(route('login'));
        //     return;
        // }

        $data = $this->form->getState();
        $authUser = Auth::user();
        if ($authUser !== null) {
            $data['owner_id'] = $authUser->id;
        }
        Ticket::create($data);

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
  
}
