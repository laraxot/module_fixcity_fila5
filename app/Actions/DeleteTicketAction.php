<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Webmozart\Assert\Assert;

final class DeleteTicketAction
{
    public function execute(Ticket $ticket): void
    {
        Gate::authorize('delete', $ticket);
        $user = Filament::auth()->user() ?? auth()->user();
        Assert::isInstanceOf($user, UserContract::class);
        $actorId = SafeStringCastAction::cast($user->getAuthIdentifier());

        DB::connection($ticket->getConnectionName())->transaction(function () use ($ticket, $actorId): void {
            // `deleted_by` is intentionally explicit: a newly created model may
            // not yet carry the nullable column in its in-memory attributes, so
            // the generic Updater deleting hook cannot always fill it.
            $ticket->setAttribute('deleted_by', $actorId);
            $ticket->save();
            $ticket->delete();
        });
    }
}
