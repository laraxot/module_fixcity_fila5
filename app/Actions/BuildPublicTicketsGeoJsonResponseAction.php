<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Fixcity\Datas\PublicTicketGeoJsonOptions;
use Modules\Fixcity\Datas\PublicTicketListFilters;
use Spatie\QueueableAction\QueueableAction;

/**
 * Corpo di GET /api/tickets/geojson (mappa pubblica), DB-12 / SEC-12.
 *
 * - Stessa visibilita' e stessi filtri dell'elenco ({@see BuildPublicTicketsQueryAction}, {@see ApplyPublicTicketFiltersAction}).
 * - Al massimo {@see PublicTicketGeoJsonOptions::MAX_FEATURES} feature, `bbox` opzionale.
 * - Cache breve ({@see PublicTicketGeoJsonOptions::CACHE_TTL_SECONDS}) SOLO per gli ospiti: per un utente loggato la query include
 *   anche le sue segnalazioni non ancora pubbliche, che non devono mai finire in una risposta condivisa. Nessuna invalidazione:
 *   un ticket appena approvato compare entro il TTL.
 */
final class BuildPublicTicketsGeoJsonResponseAction
{
    use QueueableAction;

    public const string CACHE_PREFIX = 'fixcity:geojson:public:';

    /**
     * @return array{
     *     type: string,
     *     generated_at: string,
     *     total: int,
     *     limit: int|null,
     *     truncated: bool,
     *     features: array<int, array<string, mixed>>
     * }
     */
    public function execute(Request $request): array
    {
        $filters = PublicTicketListFilters::fromRequest($request);
        $options = PublicTicketGeoJsonOptions::fromRequest($request);

        if ($request->user() !== null) {
            return $this->build($filters, $options);
        }

        /** @var array{type: string, generated_at: string, total: int, limit: int|null, truncated: bool, features: array<int, array<string, mixed>>} $cached */
        $cached = Cache::remember(
            $this->cacheKey($filters, $options),
            PublicTicketGeoJsonOptions::CACHE_TTL_SECONDS,
            fn (): array => $this->build($filters, $options),
        );

        return $cached;
    }

    /**
     * La chiave include filtri normalizzati, opzioni e lingua (`detail_url` contiene il prefisso di lingua).
     */
    public function cacheKey(PublicTicketListFilters $filters, PublicTicketGeoJsonOptions $options): string
    {
        $filterQuery = $filters->toQuery(['sort', 'page']);
        ksort($filterQuery);

        return self::CACHE_PREFIX.sha1(app()->getLocale().'|'.http_build_query($filterQuery).'|'.$options->cacheKeyPart());
    }

    /**
     * @return array{
     *     type: string,
     *     generated_at: string,
     *     total: int,
     *     limit: int|null,
     *     truncated: bool,
     *     features: array<int, array<string, mixed>>
     * }
     */
    private function build(PublicTicketListFilters $filters, PublicTicketGeoJsonOptions $options): array
    {
        $query = app(ApplyPublicTicketFiltersAction::class)->execute(
            app(BuildPublicTicketsQueryAction::class)->execute(),
            $filters,
        );

        return app(BuildTicketsGeoJsonAction::class)->execute($query, [], $options->limit, $options->bbox);
    }
}
