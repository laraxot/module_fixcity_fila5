<?php

declare(strict_types=1);

namespace Modules\Fixcity\Datas;

use Modules\Fixcity\Actions\Search\SearchFrontOfficeAction;

/**
 * One result of the site-wide front-office search (/{lang}/ricerca): only data already public on the linked page.
 *
 * @see SearchFrontOfficeAction
 */
final readonly class FrontOfficeSearchItem
{
    public const string TYPE_TICKET = 'ticket';

    public const string TYPE_POST = 'post';

    public const string TYPE_EVENT = 'event';

    public const string TYPE_PAGE = 'page';

    /**
     * @param  string  $type  one of the TYPE_* constants
     * @param  string  $typeLabel  translated label of $type, shown above the title
     */
    public function __construct(
        public string $title,
        public string $url,
        public string $type,
        public string $typeLabel,
        public string $excerpt = '',
    ) {}
}
