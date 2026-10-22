<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Modules\Fixcity\Datas\PublicTicketListFilters;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Elenco pubblico delle segnalazioni (/{lang}/tickets), DCP-104.
 *
 * Mostra solo le segnalazioni gia' prese in carico (stati visibili a tutti, {@see TicketStatusEnum::canViewByAll()}), uguali per tutti
 * i visitatori. Ogni voce contiene i dati minimi gia' pubblici nella mappa e nel dettaglio: mai il codice di tracciamento (capability
 * token) ne' dati dell'autore (proprietario, email), ne' immagini (audit PP-03 della story DCP-104: finche' non c'e' una miniatura
 * senza EXIF, nell'elenco non escono foto), ne' l'ora di creazione (solo il giorno).
 */
final class ListPublicTicketsAction
{
    use QueueableAction;

    private const int EXCERPT_LENGTH = 300;

    /**
     * @return array{
     *     items: list<array{id: int, title: string, url: string, type: string, type_label: string, status: string, status_label: string, created_at: string, address: string, excerpt: string}>,
     *     total: int,
     *     page: int,
     *     last_page: int,
     *     per_page: int,
     *     type_facets: list<array{value: string, label: string, count: int}>,
     *     status_facets: list<array{value: string, label: string, count: int}>,
     *     resolved_last_12_months: int
     * }
     */
    public function execute(PublicTicketListFilters $filters): array
    {
        $apply = app(ApplyPublicTicketFiltersAction::class);
        $base = static fn (): Builder => app(BuildPublicTicketsQueryAction::class)->execute(includeOwnTickets: false);

        $filtered = $apply->execute($base(), $filters);
        $total = (clone $filtered)->count();
        $lastPage = max(1, (int) ceil($total / PublicTicketListFilters::PER_PAGE));
        $page = min($filters->page, $lastPage);

        $tickets = $this->sorted($filtered, $filters->sort)
            ->select(['id', 'name', 'content', 'type', 'status', 'location', 'created_at'])
            ->forPage($page, PublicTicketListFilters::PER_PAGE)
            ->get();

        return [
            'items' => array_values($tickets->map(fn (Ticket $ticket): array => $this->toItem($ticket))->all()),
            'total' => $total,
            'page' => $page,
            'last_page' => $lastPage,
            'per_page' => PublicTicketListFilters::PER_PAGE,
            'type_facets' => $this->typeFacets($apply->execute($base(), $filters, ['types']), $filters->types),
            'status_facets' => $this->statusFacets($apply->execute($base(), $filters, ['statuses']), $filters->statuses),
            'resolved_last_12_months' => $base()
                ->where('status', TicketStatusEnum::RESOLVED->value)
                ->where('updated_at', '>=', now()->subYear())
                ->count(),
        ];
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return Builder<Ticket>
     */
    private function sorted(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'updated' => $query->orderByDesc('updated_at')->orderByDesc('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    /**
     * @return array{id: int, title: string, url: string, type: string, type_label: string, status: string, status_label: string, created_at: string, address: string, excerpt: string}
     */
    private function toItem(Ticket $ticket): array
    {
        $type = $ticket->type;
        $status = $ticket->status;
        $location = is_array($ticket->location) ? $ticket->location : [];
        $address = $location['address'] ?? $location['display_name'] ?? '';

        return [
            'id' => $ticket->id,
            'title' => $ticket->name,
            'url' => route('container0.view', [
                'container0' => 'tickets',
                'slug0' => (string) $ticket->id,
            ]),
            'type' => $type->value ?? '',
            'type_label' => $type?->getLabel() ?? '',
            'status' => $status->value ?? '',
            'status_label' => $status?->getLabel() ?? '',
            'created_at' => $ticket->created_at?->toDateString() ?? '',
            'address' => is_string($address) ? $address : '',
            'excerpt' => Str::of((string) $ticket->content)->squish()->limit(self::EXCERPT_LENGTH)->toString(),
        ];
    }

    /**
     * Tipologie con almeno una segnalazione, piu' quelle scelte dal visitatore anche se ora a zero (altrimenti non potrebbe deselezionarle).
     *
     * @param  Builder<Ticket>  $query
     * @param  list<string>  $selected
     * @return list<array{value: string, label: string, count: int}>
     */
    private function typeFacets(Builder $query, array $selected): array
    {
        $counts = $this->counts($query, 'type');
        $facets = [];
        foreach (TicketTypeEnum::cases() as $type) {
            $count = $counts[$type->value] ?? 0;
            if ($count > 0 || in_array($type->value, $selected, true)) {
                $facets[] = ['value' => $type->value, 'label' => $type->getLabel(), 'count' => $count];
            }
        }
        usort($facets, static fn (array $a, array $b): int => strcmp(Str::ascii(mb_strtolower($a['label'])), Str::ascii(mb_strtolower($b['label']))));

        return $facets;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @param  list<string>  $selected
     * @return list<array{value: string, label: string, count: int}>
     */
    private function statusFacets(Builder $query, array $selected): array
    {
        $counts = $this->counts($query, 'status');
        $facets = [];
        foreach (TicketStatusEnum::canViewByAll() as $status) {
            $count = $counts[$status->value] ?? 0;
            if ($count > 0 || in_array($status->value, $selected, true)) {
                $facets[] = ['value' => $status->value, 'label' => $status->getLabel(), 'count' => $count];
            }
        }

        return $facets;
    }

    /**
     * @param  Builder<Ticket>  $query
     * @return array<string, int>
     */
    private function counts(Builder $query, string $column): array
    {
        $counts = [];
        foreach ($query->toBase()->select([$column.' as facet'])->selectRaw('count(*) as aggregate')->groupBy($column)->get() as $row) {
            $counts[SafeStringCastAction::cast($row->facet)] = SafeIntCastAction::cast($row->aggregate);
        }

        return $counts;
    }
}
