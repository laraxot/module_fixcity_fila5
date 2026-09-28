<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Filament\Concerns\HasTicketAuthorData;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
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

    public string $confirmationLocale = 'it';

    public static string $resource = TicketResource::class;

    /**
     * @param  array<string, mixed>  $blockData
     */
    public function mount(array $blockData = []): void
    {
        $this->blockData = $blockData;
        $this->confirmationLocale = LaravelLocalization::getCurrentLocale();
        $this->form->fill(TicketForm::getDefaultFormState());
    }

    /**
     * @return array<string, Step>
     */
    public function getSteps(): array
    {
        return app(TicketForm::class)->getSteps();
    }

    protected function hasSkippableSteps(): bool
    {
        return false;
    }

    public function save(): void
    {
        if (app(GetPublishedPrivacyPolicyAction::class)->execute() === null) {
            $this->addError('data.privacyAccepted', __('fixcity::ticket.privacy.not_configured'));

            return;
        }

        /** @var array<string, mixed> $data */
        // Resolve and validate form state without saving relationships yet:
        // the Media Library field needs the persisted Ticket as its owner.
        $data = $this->form->getState(shouldCallHooksBefore: false);

        $data['owner_id'] = Auth::id();

        $ticket = app(CreateTicketAction::class)->execute($data, $this->confirmationLocale);

        $this->form->model($ticket);
        $this->form->saveRelationships();

        $this->redirectAfterSuccess();
    }

    /**
     * Stable submit entrypoint for Livewire forms and theme templates.
     */
    public function submit(): void
    {
        $this->save();
    }

    protected function redirectAfterSuccess(): void
    {
        $path = SafeStringCastAction::cast(
            $this->blockData['confirmation_path'] ?? '/tickets/confirmation'
        );

        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $locale = array_key_exists($this->confirmationLocale, $supportedLocales)
            ? $this->confirmationLocale
            : app()->getLocale();

        $localizedUrl = LaravelLocalization::getLocalizedURL($locale, $path);

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
