<?php

declare(strict_types=1);

namespace Modules\Fixcity\Datas;

/**
 * Results of the site-wide front-office search. Each source is capped (SearchFrontOfficeAction::LIMIT_PER_SOURCE):
 * when more public tickets match, $ticketTotal tells the page to link the full, paginated tickets list.
 */
final readonly class FrontOfficeSearchResult
{
    /**
     * @param  string  $query  the cleaned query that was searched ('' when the visitor typed nothing)
     * @param  list<FrontOfficeSearchItem>  $items
     * @param  int  $ticketTotal  public tickets matching the query, including those beyond the cap
     */
    public function __construct(
        public string $query,
        public array $items = [],
        public int $ticketTotal = 0,
    ) {}

    public function isEmptyQuery(): bool
    {
        return $this->query === '';
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function hasMoreTickets(): bool
    {
        $shown = count(array_filter($this->items, static fn (FrontOfficeSearchItem $item): bool => $item->type === FrontOfficeSearchItem::TYPE_TICKET));

        return $this->ticketTotal > $shown;
    }
}
