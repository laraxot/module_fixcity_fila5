<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Schemas\Components\Wizard\Step;
use Livewire\Attributes\Locked;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
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
    #[Locked]
    public array $blockData = [];

    #[Locked]
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

        $userId = Filament::auth()->id();
        if (! $userId) {
            $this->addError('global', __('fixcity::ticket.auth.required'));
            return;
        }
        $data['owner_id'] = $userId;
        // Stato e priorita' iniziali li decide il server: `status` e `priority` sono campi Hidden e il client puo' riscriverli
        // (`$wire.set('data.status', 'closed')`): senza questo un cittadino creerebbe ticket gia' aperti o chiusi (SEC-01, DCP-49).
        $data['status'] = TicketStatusEnum::PENDING->value;
        $data['priority'] = TicketPriorityEnum::LOW->value;

        $ticket = app(CreateTicketAction::class)->execute($data, $this->confirmationLocale);

        $this->form->model($ticket);
        $this->form->saveRelationships();

        $this->redirectAfterSuccess($ticket);
    }

    /**
     * Stable submit entrypoint for Livewire forms and theme templates.
     */
    public function submit(): void
    {
        $this->save();
    }

    protected function redirectAfterSuccess(\Modules\Fixcity\Models\Ticket $ticket): void
    {
        $path = SafeStringCastAction::cast(
            $this->blockData['confirmation_path'] ?? '/tickets/confirmation'
        );

        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $locale = array_key_exists($this->confirmationLocale, $supportedLocales)
            ? $this->confirmationLocale
            : app()->getLocale();

        $localizedUrl = LaravelLocalization::getLocalizedURL($locale, $path);

        $redirectUrl = $localizedUrl !== false ? $localizedUrl : $path;
        $code = SafeStringCastAction::cast($ticket->code);
        if ($code !== '') {
            $redirectUrl .= (str_contains($redirectUrl, '?') ? '&' : '?').'code='.rawurlencode($code);
        }

        $this->redirect($redirectUrl);
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
