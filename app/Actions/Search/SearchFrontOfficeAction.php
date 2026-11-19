<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\Search;

use Modules\Blog\Models\Article;
use Modules\Blog\View\Composers\Queries\ThemeArticleQueries;
use Modules\Cms\Models\Page;
use Modules\Fixcity\Actions\ListPublicTicketsAction;
use Modules\Fixcity\Datas\FrontOfficeSearchItem;
use Modules\Fixcity\Datas\FrontOfficeSearchResult;
use Modules\Fixcity\Datas\PublicTicketListFilters;
use Spatie\QueueableAction\QueueableAction;

/**
 * Site-wide front-office search for citizens (/{lang}/ricerca?q=), story site-wide-front-office-search.
 *
 * Owner: Fixcity, the site module that already depends on Blog and Cms; putting it in Cms or Blog would make a base module
 * depend on the tickets domain. Every source reuses its own public query, so the search never shows more than the site does:
 * - tickets: ListPublicTicketsAction with its `q` filter (public statuses only, no author data);
 * - posts and events: ThemeArticleQueries, the same «published» rules as the detail pages;
 * - CMS pages: public `<container>.index` pages (no middleware) whose title, in $locale, contains the query. Read only (Sushi).
 */
final class SearchFrontOfficeAction
{
    use QueueableAction;

    public const int LIMIT_PER_SOURCE = 10;

    /** The results page itself is never a result. */
    private const string SEARCH_CONTAINER = 'ricerca';

    public function execute(string $query, string $locale): FrontOfficeSearchResult
    {
        $query = PublicTicketListFilters::cleanQuery($query);
        if ($query === '') {
            return new FrontOfficeSearchResult('');
        }

        $tickets = app(ListPublicTicketsAction::class)->execute(new PublicTicketListFilters(query: $query));
        $items = [];
        foreach (array_slice($tickets['items'], 0, self::LIMIT_PER_SOURCE) as $ticket) {
            $items[] = new FrontOfficeSearchItem($ticket['title'], $ticket['url'], FrontOfficeSearchItem::TYPE_TICKET, $this->typeLabel(FrontOfficeSearchItem::TYPE_TICKET, $locale), $ticket['excerpt']);
        }

        foreach (app(ThemeArticleQueries::class)->searchPublishedPostsAndEvents($query, self::LIMIT_PER_SOURCE) as $article) {
            $items[] = $this->articleItem($article, $locale);
        }

        return new FrontOfficeSearchResult($query, array_merge($items, $this->pageItems($query, $locale)), $tickets['total']);
    }

    private function articleItem(Article $article, string $locale): FrontOfficeSearchItem
    {
        $type = $article->getAttribute('type') === 'event' ? FrontOfficeSearchItem::TYPE_EVENT : FrontOfficeSearchItem::TYPE_POST;
        $slug = $article->getAttribute('slug');
        $excerpt = $article->getAttribute('excerpt');
        if (! is_string($excerpt) || trim($excerpt) === '') {
            $excerpt = $article->getAttribute('description');
        }

        return new FrontOfficeSearchItem(
            $article->getTitle(),
            route('container0.view', ['container0' => $type === FrontOfficeSearchItem::TYPE_EVENT ? 'events' : 'blog', 'slug0' => is_string($slug) ? $slug : '']),
            $type,
            $this->typeLabel($type, $locale),
            is_string($excerpt) ? strip_tags($excerpt) : '',
        );
    }

    /**
     * @return list<FrontOfficeSearchItem>
     */
    private function pageItems(string $query, string $locale): array
    {
        $items = [];
        foreach (Page::query()->get() as $page) {
            $container = $this->publicContainer($page);
            $title = $page->getTranslation('title', $locale, false);
            if ($container === null || ! is_string($title) || mb_stripos($title, $query) === false) {
                continue;
            }
            $items[] = new FrontOfficeSearchItem($title, route('container0.index', ['container0' => $container]), FrontOfficeSearchItem::TYPE_PAGE, $this->typeLabel(FrontOfficeSearchItem::TYPE_PAGE, $locale));
            if (count($items) === self::LIMIT_PER_SOURCE) {
                break;
            }
        }

        return $items;
    }

    /** `<container>` of a public `<container>.index` page served by /{lang}/<container>, or null for every other page. */
    private function publicContainer(Page $page): ?string
    {
        $slug = $page->getAttribute('slug');
        $container = is_string($slug) && str_ends_with($slug, '.index') ? substr($slug, 0, -strlen('.index')) : '';
        // `tests.tests.index` and other pages in a sub-folder carry a dot: they are not served by /{lang}/<container>
        if ($container === '' || str_contains($container, '.') || $container === self::SEARCH_CONTAINER) {
            return null;
        }

        return $page->declaredMiddleware() === [] ? $container : null;
    }

    private function typeLabel(string $type, string $locale): string
    {
        $label = __('fixcity::site_search.types.'.$type, [], $locale);

        return is_string($label) ? $label : $type;
    }
}
