<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dati della pagina "Segnalazioni che seguo" (DCP-110): i ticket seguiti dall'utente che la query autorizzata gli consente di vedere
 * (pubblici per stato, oppure suoi). Il tracciamento e' sempre per codice (`?code=`), mai per id del database.
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
            $rows[] = $this->buildRow->execute($ticket, $locale);
        }

        return ['total' => $total, 'truncated' => $total > $limit, 'rows' => $rows];
    }
}
