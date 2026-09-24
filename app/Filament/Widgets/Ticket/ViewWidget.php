<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets\Ticket;

use Illuminate\Database\Eloquent\Model;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketInfolist;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Filament\Widgets\XotBaseInfolistWidget;

/**
 * Dettaglio segnalazione FO: {@see TicketInfolist::getPublicFrontofficeSchema()} (layout verticale, no tab).
 */
class ViewWidget extends XotBaseInfolistWidget
{
    public ?Ticket $ticket = null;

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
        }
    }

    protected function getInfolistRecord(): ?Model
    {
        return $this->ticket;
    }

    /**
     * @return array<int|string, \Filament\Schemas\Components\Component|\Illuminate\Contracts\Support\Htmlable|string>
     */
    protected function getInfolistSchema(): array
    {
        return TicketInfolist::getPublicFrontofficeSchema();
    }
}
