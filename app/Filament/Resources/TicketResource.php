<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources;

use Modules\Fixcity\Filament\Resources\TicketResource\Pages\CreateTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\EditTicket;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ListTickets;
use Modules\Fixcity\Filament\Resources\TicketResource\Pages\ViewTicket;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Filament\Resources\XotBaseResource;

class TicketResource extends XotBaseResource
{
    protected static ?string $model = Ticket::class;

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'edit' => EditTicket::route('/{record}/edit'),
            'view' => ViewTicket::route('/{record}'),
        ];
    }
}