<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Fixcity\Datas\PublicTicketListFilters;
use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

/**
 * Applica i filtri dell'elenco pubblico (ricerca, tipologia, stato, periodo) a una query di ticket.
 * Unico punto di verita' per l'elenco e per la mappa (GET /api/tickets/geojson), cosi' i due non possono divergere.
 */
final class ApplyPublicTicketFiltersAction
{
    use QueueableAction;

    /**
     * @param  Builder<Ticket>  $query
     * @param  list<string>  $except  filtri da non applicare (`types`, `statuses`): servono ai conteggi per faccetta
     * @return Builder<Ticket>
     */
    public function execute(Builder $query, PublicTicketListFilters $filters, array $except = []): Builder
    {
        if ($filters->query !== '') {
            // i caratteri jolly di LIKE sono gia' tolti da PublicTicketListFilters: si cerca la sottostringa, senza distinzione di maiuscole
            $like = '%'.$filters->query.'%';
            $query->where(static function (Builder $match) use ($like): void {
                $match->where('name', 'like', $like)
                    ->orWhere('content', 'like', $like)
                    ->orWhere('location->address', 'like', $like);
            });
        }

        if ($filters->types !== [] && ! in_array('types', $except, true)) {
            $query->whereIn('type', $filters->types);
        }

        if ($filters->statuses !== [] && ! in_array('statuses', $except, true)) {
            $query->whereIn('status', $filters->statuses);
        }

        $since = match ($filters->period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '12m' => now()->subYear(),
            default => null,
        };
        if ($since !== null) {
            $query->where('created_at', '>=', $since);
        }

        return $query;
    }
}
