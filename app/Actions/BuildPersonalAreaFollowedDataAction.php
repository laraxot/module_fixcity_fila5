<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dati della pagina "Segnalazioni che seguo" (DCP-110): i ticket seguiti dall'utente che la query autorizzata gli consente di vedere
 * (pubblici per stato, oppure suoi). Il tracciamento e' sempre per codice (`?code=`), mai per id del database, e solo per le
 * segnalazioni proprie: per quelle di altri la riga ha solo il link al dettaglio (DCP-287).
 *
 * @phpstan-import-type TicketRow from BuildPersonalAreaTicketRowAction
 */
final class BuildPersonalAreaFollowedDataAction
{
    use QueueableAction;

    public const int DEFAULT_LIMIT = 100;

    public function __construct(
        private readonly BuildAuthenticatedUserFollowedTicketsQueryAction $followedTickets,
        private readonly BuildPersonalAreaTicketRowAction $buildRow,
    ) {}

    /**
     * @return array{total: int, truncated: bool, rows: list<TicketRow>}
     */
    public function execute(UserContract $user, string $locale, int $limit = self::DEFAULT_LIMIT): array
    {
        $query = $this->followedTickets->execute($user);
        $total = $query->count();

        $rows = [];
        foreach ($query->limit($limit)->get() as $ticket) {
            // chi segue vede il codice di tracciamento solo se la segnalazione e' sua: il codice e' una capability (DCP-287)
            $isOwner = $ticket->owner_id !== null && SafeStringCastAction::cast($ticket->owner_id) === SafeStringCastAction::cast($user->getAuthIdentifier());
            $rows[] = $this->buildRow->execute($ticket, $locale, $isOwner);
        }

        return ['total' => $total, 'truncated' => $total > $limit, 'rows' => $rows];
    }
}
