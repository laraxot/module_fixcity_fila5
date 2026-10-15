<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets\Ticket;

use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Modules\Fixcity\Actions\SetTicketSubscriptionAction;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketInfolist;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Filament\Widgets\XotBaseInfolistWidget;

/**
 * Dettaglio segnalazione FO: {@see TicketInfolist::getPublicFrontofficeSchema()} (layout verticale, no tab).
 */
class ViewWidget extends XotBaseInfolistWidget
{
    protected string $view = 'fixcity::filament.widgets.ticket.view';

    public ?Ticket $ticket = null;

    public bool $isFollowing = false;

    /**
     * @param  array<string, mixed>  $blockData
     */
    public function mount(?string $slug0 = null, array $blockData = []): void
    {
        $key = $slug0 ?? SafeStringCastAction::cast($blockData['slug0'] ?? null);
        if ($key === '') {
            return;
        }

        $found = Ticket::query()->whereKey((int) $key)->first();
        if ($found instanceof Ticket && $found->isVisibleOnPublicFrontoffice()) {
            $this->ticket = $found;
            $userId = auth()->id();
            $this->isFollowing = $userId !== null
                && $found->ticketSubscribers()->wherePivot('user_id', $userId)->exists();
        }
    }

    public function setFollowing(bool $following): void
    {
        if (! $this->ticket instanceof Ticket) {
            return;
        }

        $user = auth()->user();
        if (! $user instanceof UserContract) {
            return;
        }

        $this->isFollowing = app(SetTicketSubscriptionAction::class)->execute(
            $this->ticket,
            $user,
            $following,
        );
    }

    protected function getInfolistRecord(): ?Model
    {
        return $this->ticket;
    }

    /**
     * @return array<int|string, Component|Htmlable|string>
     */
    protected function getInfolistSchema(): array
    {
        return TicketInfolist::getPublicFrontofficeSchema();
    }
}
