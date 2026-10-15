<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;

/**
 * POST /{lang}/tickets/{ticket}/follow (DCP-148): il cittadino loggato segue o smette di seguire una segnalazione pubblica.
 *
 * Invocabile e non closure, cosi' `route:cache` funziona. La route sta in `laravel/routes/web.php` (le route dei moduli non sono
 * caricate). Un solo campo, `following` (1 = segui, 0 = smetti): lo stato e' esplicito, quindi ripetere la richiesta e' idempotente.
 * La visibilita' e' quella della pagina di dettaglio: un ticket non pubblico (e non proprio) risponde 404, come se non esistesse.
 */
final class FollowTicketAction
{
    public function __invoke(Request $request, string $lang, int $ticket): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof UserContract, 403);

        $visible = app(BuildPublicTicketsQueryAction::class)->execute()->whereKey($ticket)->first();
        abort_unless($visible instanceof Ticket, 404);

        $request->validate(['following' => ['required', 'boolean']]);
        $following = $request->boolean('following');
        app(SetTicketSubscriptionAction::class)->execute($visible, $user, $following);

        // il flash porta la chiave, non il testo: la pagina di dettaglio lo traduce nella lingua del proprio URL
        return redirect('/'.$lang.'/tickets/'.$ticket)->with('ticket_follow', $following ? 'followed' : 'unfollowed');
    }
}
