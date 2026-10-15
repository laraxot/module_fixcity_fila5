<?php

declare(strict_types=1);

namespace Modules\Fixcity\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Modules\Fixcity\Actions\BuildPersonalAreaNotificationsDataAction;
use Modules\Fixcity\Actions\MarkPersonalAreaNotificationsAsReadAction;
use Modules\Xot\Contracts\UserContract;

/**
 * Elenco delle notifiche di stato di `/{lang}/area-personale/notifiche` con "segna come letta" (DCP-111).
 * Dati e scritture passano dalle Action, che limitano tutto all'utente autenticato; la vista e' del tema (blocchi `personal-area-settings/*`).
 */
final class PersonalAreaNotificationsWidget extends Component
{
    /** Esito dell'ultima azione, annunciato dall'area `role="status"`. */
    public string $statusMessage = '';

    public function markAsRead(string $notificationId): void
    {
        $changed = app(MarkPersonalAreaNotificationsAsReadAction::class)->execute($this->citizen(), $notificationId);
        $this->statusMessage = $changed > 0 ? (string) __('fixcity::personal_area_settings.notifications.marked_read') : '';
    }

    public function markAllAsRead(): void
    {
        $changed = app(MarkPersonalAreaNotificationsAsReadAction::class)->execute($this->citizen());
        $this->statusMessage = $changed > 0
            ? trans_choice('fixcity::personal_area_settings.notifications.marked_all_read', $changed, ['count' => $changed])
            : '';
    }

    public function render(): View
    {
        return view('pub_theme::components.blocks.personal-area-settings.notifications-list', [
            'notifications' => app(BuildPersonalAreaNotificationsDataAction::class)->execute($this->citizen()),
        ]);
    }

    private function citizen(): UserContract
    {
        $user = Auth::user();
        abort_unless($user instanceof UserContract, 403);

        return $user;
    }
}
