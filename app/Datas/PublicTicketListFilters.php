<?php

declare(strict_types=1);

namespace Modules\Fixcity\Datas;

use Illuminate\Http\Request;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;

use function Safe\preg_replace;

/**
 * Filtri dell'elenco pubblico delle segnalazioni (/{lang}/tickets e GET /api/tickets/geojson), letti dalla query string.
 *
 * Gli URL sono leggibili e condivisibili: `?q=buca&types[]=public_lighting&statuses[]=resolved&period=30d&sort=oldest&page=2`.
 * Ogni valore sconosciuto viene scartato (mai un errore per un link vecchio) e `toQuery()` omette i valori di default.
 */
final readonly class PublicTicketListFilters
{
    public const int PER_PAGE = 20;

    public const int MAX_QUERY_LENGTH = 100;

    public const string DEFAULT_SORT = 'recent';

    /** @var list<string> */
    public const array SORTS = ['recent', 'oldest', 'updated'];

    /** @var list<string> */
    public const array PERIODS = ['7d', '30d', '12m'];

    /**
     * @param  list<string>  $types  valori di TicketTypeEnum
     * @param  list<string>  $statuses  valori di TicketStatusEnum visibili a tutti
     * @param  string  $view  `map` o `list`: scheda attiva al caricamento
     */
    public function __construct(
        public string $query = '',
        public array $types = [],
        public array $statuses = [],
        public string $period = '',
        public string $sort = self::DEFAULT_SORT,
        public int $page = 1,
        public string $view = 'map',
    ) {}

    public static function fromRequest(Request $request): self
    {
        $query = self::cleanQuery($request->query('q'));
        $types = self::allowedValues($request->query('types'), array_column(TicketTypeEnum::cases(), 'value'));
        $statuses = self::allowedValues(
            $request->query('statuses'),
            array_map(static fn (TicketStatusEnum $status): string => $status->value, TicketStatusEnum::canViewByAll()),
        );
        $period = is_string($request->query('period')) && in_array($request->query('period'), self::PERIODS, true) ? $request->query('period') : '';
        $sort = is_string($request->query('sort')) && in_array($request->query('sort'), self::SORTS, true) ? $request->query('sort') : self::DEFAULT_SORT;
        $rawPage = $request->query('page');
        $page = is_numeric($rawPage) ? min(10000, max(1, (int) $rawPage)) : 1;

        // Chi cerca, filtra, ordina o sfoglia vuole vedere l'elenco; senza nulla di tutto cio' vale la mappa (come il riferimento).
        $wantsList = $query !== '' || $types !== [] || $statuses !== [] || $period !== '' || $sort !== self::DEFAULT_SORT
            || $page > 1 || $request->query->has('page');
        $explicit = $request->query('view');
        $view = is_string($explicit) && in_array($explicit, ['map', 'list'], true) ? $explicit : ($wantsList ? 'list' : 'map');

        return new self($query, $types, $statuses, $period, $sort, $page, $view);
    }

    public function hasActiveFilters(): bool
    {
        return $this->query !== '' || $this->types !== [] || $this->statuses !== [] || $this->period !== '';
    }

    public function withPage(int $page): self
    {
        return new self($this->query, $this->types, $this->statuses, $this->period, $this->sort, max(1, $page), $this->view);
    }

    public function withoutPage(): self
    {
        return $this->withPage(1);
    }

    /**
     * Query string canonica, senza valori di default.
     *
     * @param  list<string>  $except  chiavi da omettere (q, types, statuses, period, sort, page)
     * @return array<string, mixed>
     */
    public function toQuery(array $except = []): array
    {
        $query = array_filter([
            'q' => $this->query !== '' ? $this->query : null,
            'types' => $this->types !== [] ? $this->types : null,
            'statuses' => $this->statuses !== [] ? $this->statuses : null,
            'period' => $this->period !== '' ? $this->period : null,
            'sort' => $this->sort !== self::DEFAULT_SORT ? $this->sort : null,
            'page' => $this->page > 1 ? $this->page : null,
        ], static fn (mixed $value): bool => $value !== null);

        return array_diff_key($query, array_flip($except));
    }

    private static function cleanQuery(mixed $raw): string
    {
        if (! is_string($raw)) {
            return '';
        }

        // via i caratteri jolly di LIKE (% _ \): si cerca testo, non un pattern
        return mb_substr(trim(preg_replace('/\s+/u', ' ', str_replace(['%', '_', '\\'], ' ', $raw))), 0, self::MAX_QUERY_LENGTH);
    }

    /**
     * @param  list<string>  $allowed
     * @return list<string>
     */
    private static function allowedValues(mixed $raw, array $allowed): array
    {
        $values = is_array($raw) ? $raw : (is_string($raw) ? [$raw] : []);

        return array_values(array_unique(array_filter(
            $values,
            static fn (mixed $value): bool => is_string($value) && in_array($value, $allowed, true),
        )));
    }
}
