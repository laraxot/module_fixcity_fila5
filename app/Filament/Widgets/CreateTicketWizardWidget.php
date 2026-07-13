<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Filament\Concerns\HasTicketAuthorData;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Widgets\XotBaseWizardWidget;

class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    use HasTicketAuthorData;

    /**
     * View resolution is intentionally left to XotBaseWidget::resolveView(),
     * which picks 'pub_theme::filament.widgets.create-ticket-wizard'
     * (Themes/Sixteen) when it exists, falling back to
     * 'fixcity::filament.widgets.create-ticket-wizard' (this module)
     * otherwise. Do not declare a $view property here: it would bypass
     * that convention-based lookup (see GetViewByClassAction) and the
     * theme override would silently stop being used.
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

    protected function hasSkippableSteps(): bool
    {
        return false;
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
        $path = SafeStringCastAction::cast(
            $this->blockData['confirmation_path'] ?? '/tickets/confirmation'
        );

        $localizedUrl = LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getCurrentLocale(),
            $path
        );

        $this->redirect($localizedUrl !== false ? $localizedUrl : $path);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'blockData' => $this->blockData,
            'pageTitle' => SafeStringCastAction::cast($this->blockData['title'] ?? __('fixcity::ticket.page.title.label')),
            'pageDescription' => SafeStringCastAction::cast($this->blockData['description'] ?? ''),
        ];
    }
}
