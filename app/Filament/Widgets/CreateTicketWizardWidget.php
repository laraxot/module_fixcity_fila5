<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    /**
     * Runtime source-of-truth view for frontoffice ticket wizard.
     * This avoids ambiguity with theme files that share the same basename.
     * $view = 'fixcity::filament.widgets.create-ticket-wizard';
     */
    

    /** @var array<string, mixed> */
    public array $blockData = [];

    public static string $resource = TicketResource::class;

    /**
     * @param  array<string, mixed>  $blockData
     */
    public function mount(array $blockData = []): void
    {
        $this->blockData = $blockData;
        $this->form->fill(TicketForm::getDefaultFormState());
    }

    /**
     * @return array<string, Step>
     */
    public function getSteps(): array
    {
        return TicketForm::getSteps();
    }

    

    public function save(): void
    {
        /** @var array<string, mixed> $data */
        $data = $this->form->getState();

        $data['owner_id'] = Auth::id();;

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
