<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dati della pagina "Le mie segnalazioni" (DCP-110): le segnalazioni di cui l'utente e' PROPRIETARIO (owner_id), mai quelle seguite
 * o di altri. Divise in "in corso" e "concluse" (risolta o chiusa) secondo TicketStatusEnum, dalla piu' recente.
 *
 * Si leggono al massimo `$limit` segnalazioni; `total` e' sempre il numero vero e `truncated` lo dichiara, cosi' la vista non
 * tace mai un taglio.
 *
 * @phpstan-import-type TicketRow from BuildPersonalAreaTicketRowAction
 */
final class BuildPersonalAreaPracticesDataAction
{
    use QueueableAction;

    public const int DEFAULT_LIMIT = 100;

    public function __construct(
        private readonly BuildAuthenticatedUserTicketsQueryAction $ownedTickets,
        private readonly BuildPersonalAreaTicketRowAction $buildRow,
    ) {}

    /**
     * @return array{total: int, truncated: bool, in_progress: list<TicketRow>, concluded: list<TicketRow>}
     */
    public function execute(UserContract $user, string $locale, int $limit = self::DEFAULT_LIMIT): array
    {
        $query = $this->ownedTickets->execute($user);
        $total = $query->count();

        $inProgress = [];
        $concluded = [];
        foreach ($query->limit($limit)->get() as $ticket) {
            // sono tutte del proprietario (query per owner_id): codice e link di tracciamento sono suoi
            $row = $this->buildRow->execute($ticket, $locale, isOwner: true);
            if ($row['is_concluded']) {
                $concluded[] = $row;
            } else {
                $inProgress[] = $row;
            }
        }

        return [
            'total' => $total,
            'truncated' => $total > $limit,
            'in_progress' => $inProgress,
            'concluded' => $concluded,
        ];
    }
}
