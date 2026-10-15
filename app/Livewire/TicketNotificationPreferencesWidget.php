<?php

declare(strict_types=1);

namespace Modules\Fixcity\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Modules\Fixcity\Actions\GetFixcityEmailNotificationPreferenceAction;
use Modules\Fixcity\Actions\SetFixcityEmailNotificationPreferenceAction;
use Modules\Xot\Contracts\UserContract;

final class TicketNotificationPreferencesWidget extends Component
{
    public bool $emailTicketUpdates = true;

    /** Vero solo dopo un salvataggio riuscito e finche' la scelta non cambia: alimenta l'area `role="status"` (DCP-111). */
    public bool $saved = false;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof UserContract, 403);

        $this->emailTicketUpdates = app(GetFixcityEmailNotificationPreferenceAction::class)
            ->execute($user);
    }

    public function updatedEmailTicketUpdates(): void
    {
        $this->saved = false;
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof UserContract, 403);

        app(SetFixcityEmailNotificationPreferenceAction::class)
            ->execute($user, $this->emailTicketUpdates);

        $this->saved = true;
    }

    public function render(): View
    {
        return view('fixcity::livewire.ticket-notification-preferences-widget');
    }
}
