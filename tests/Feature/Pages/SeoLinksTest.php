<?php

declare(strict_types=1);

use Modules\Blog\Models\Article;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

use function Safe\file_get_contents;
use function Safe\parse_url;
use function Safe\preg_match;
use function Safe\preg_match_all;
use function Safe\simplexml_load_string;

uses(TestCase::class);

/*
 * Linee guida di design 4.3.9 (DCP-166): canonical e hreflang su ogni pagina, sitemap.xml con tutte le lingue.
 * Il canonical non porta mai la query: `?code=` e' il codice di tracciamento di una segnalazione.
 */

it('declares canonical and hreflang with the four languages and x-default', function (string $lang): void {
    /** @var TestCase $this */
    $html = (string) $this->get("/{$lang}/privacy")->assertOk()->getContent();

    expect($html)->toContain('<link rel="canonical" href="'.url("/{$lang}/privacy").'">');
    foreach (['it', 'en', 'de', 'es'] as $alternate) {
        expect($html)->toContain('hreflang="'.$alternate.'" href="'.url("/{$alternate}/privacy").'"');
    }
    expect($html)->toContain('hreflang="x-default" href="'.url('/it/privacy').'"');
})->with(['it', 'en', 'de', 'es']);

it('never puts the tracking code in the canonical url', function (): void {
    /** @var TestCase $this */
    $html = (string) $this->get('/it/tickets/track?code=ABC123SECRET')->getContent();

    expect($html)->toContain('<link rel="canonical" href="'.url('/it/tickets/track').'">');
    preg_match_all('/<link rel="(?:canonical|alternate)"[^>]*>/', $html, $links);
    expect(implode('', $links[0]))->not->toContain('ABC123SECRET');
});

it('serves a valid sitemap with the public pages in every language and nothing private', function (): void {
    /** @var TestCase $this */
    $response = $this->get('/sitemap.xml')->assertOk();
    expect((string) $response->headers->get('Content-Type'))->toContain('application/xml');

    $xml = simplexml_load_string((string) $response->getContent());
    $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $locs = array_map('strval', $xml->xpath('//s:url/s:loc') ?: []);

    foreach (['it', 'en', 'de', 'es'] as $lang) {
        expect($locs)->toContain(url("/{$lang}"))->toContain(url("/{$lang}/privacy"))->toContain(url("/{$lang}/tickets/track"));
    }
    foreach ($locs as $loc) {
        expect($loc)->not->toMatch('#/(tests|auth|area-personale|admin)(/|$)|confirmation|segnalazione-dettaglio|\?#');
    }
});

/*
 * Cosa segnala il report Sitemap di Search Console (DCP-272): «La Sitemap contiene URL bloccati dal file robots.txt»
 * e «URL non seguiti» (un URL della sitemap che risponde con un reindirizzamento).
 */

/** @return list<string> URL della sitemap, nella forma assoluta in cui li legge Google */
function sitemapLocs(TestCase $test): array
{
    $xml = simplexml_load_string((string) $test->get('/sitemap.xml')->assertOk()->getContent());
    $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    return array_values(array_map('strval', $xml->xpath('//s:url/s:loc') ?: []));
}

/** Percorso di un URL assoluto della sitemap (stringa vuota se non ne ha uno). */
function sitemapPathOf(string $loc): string
{
    $path = parse_url($loc, PHP_URL_PATH);

    return is_string($path) ? $path : '';
}

/**
 * Percorsi `Disallow:` del gruppo `User-agent: *` di public_html/robots.txt (nel monorepo sta accanto a laravel/).
 *
 * @return list<string>
 */
function robotsDisallowedPaths(TestCase $test): array
{
    $robots = dirname(base_path()).'/public_html/robots.txt';
    if (! is_file($robots)) {
        $test->markTestSkipped('public_html/robots.txt non c\'e\' in questo checkout.');
    }

    $paths = [];
    foreach (explode("\n", file_get_contents($robots) ?: '') as $line) {
        if (preg_match('/^Disallow:\s*(\S+)/i', $line, $match) === 1 && isset($match[1])) {
            $paths[] = $match[1];
        }
    }

    return $paths;
}

it('lists no url that robots.txt blocks', function (): void {
    /** @var TestCase $this */
    // `Allow: /` e' sempre piu' corto di ogni Disallow, quindi basta confrontare i Disallow (prefisso, `*` jolly).
    $patterns = array_map(
        static fn (string $path): string => '#^'.str_replace('\*', '.*', preg_quote($path, '#')).'#',
        robotsDisallowedPaths($this),
    );

    $blocked = array_values(array_filter(
        sitemapLocs($this),
        static fn (string $loc): bool => array_filter($patterns, static fn (string $rx): bool => preg_match($rx, sitemapPathOf($loc)) === 1) !== [],
    ));

    expect($blocked)->toBe([]);
});

it('closes the same private and development areas in every language', function (): void {
    /** @var TestCase $this */
    $disallowed = robotsDisallowedPaths($this);
    $italian = array_values(array_map(
        static fn (string $path): string => substr($path, 3),
        array_filter($disallowed, static fn (string $path): bool => str_starts_with($path, '/it/')),
    ));

    $missing = [];
    foreach (['en', 'de', 'es'] as $lang) {
        foreach ($italian as $suffix) {
            if (! in_array('/'.$lang.$suffix, $disallowed, true)) {
                $missing[] = '/'.$lang.$suffix;
            }
        }
    }

    expect($missing)->toBe([]);
});

it('lists only final urls, never one that redirects', function (): void {
    /** @var TestCase $this */
    $redirecting = [];
    foreach (sitemapLocs($this) as $loc) {
        if (! str_starts_with(sitemapPathOf($loc), '/it')) {
            continue;
        }
        if ($this->get($loc)->isRedirection()) {
            $redirecting[] = $loc;
        }
    }

    expect($redirecting)->toBe([]);
});

/*
 * Pagine di dettaglio (DCP-273): un articolo del blog o un evento ha un URL proprio ma non e' una pagina del CMS.
 * La sitemap deve elencarlo se e solo se la sua pagina risponde con quel contenuto (stesse regole di Cms\ResolvePageAction).
 */

/**
 * `articles.title` e' unico e gli stati demo della factory usano un titolo fisso: ogni articolo di prova ne porta uno suo.
 *
 * @param array<string, mixed> $attributes
 */
function samplePost(string $title, array $attributes = []): Article
{
    return Article::factory()->demoPost()->create(['title' => $title] + $attributes);
}

function sampleEvent(string $title): Article
{
    return Article::factory()->demoEvent(now()->addDays(10), now()->addDays(10)->addHours(2))->create(['title' => $title]);
}

it('lists the detail page of every published post and event in every language', function (): void {
    /** @var TestCase $this */
    $post = samplePost('Come leggere una mappa civica');
    $event = sampleEvent('Laboratorio di segnalazione');

    $locs = sitemapLocs($this);

    foreach (['it', 'en', 'de', 'es'] as $lang) {
        expect($locs)
            ->toContain(url("/{$lang}/blog/{$post->slug}"))
            ->toContain(url("/{$lang}/events/{$event->slug}"));
    }
});

it('lists every detail link that the blog and events lists show', function (): void {
    foreach (['Dal problema alla soluzione', 'Dopo l\'invio', 'Fotografare senza dati personali'] as $title) {
        samplePost($title);
    }
    sampleEvent('Aperitivo digitale con le associazioni');

    /** @var TestCase $this */
    $locs = sitemapLocs($this);

    foreach (['blog', 'events'] as $section) {
        $html = (string) $this->get("/it/{$section}")->assertOk()->getContent();
        preg_match_all('#href="(?:https?://[^"/]+)?/it/'.$section.'/([^"/?\#]+)"#', $html, $found);
        $slugs = array_values(array_diff(array_unique($found[1]), ['view']));

        expect($slugs)->not->toBe([]);
        foreach ($slugs as $slug) {
            expect($locs)->toContain(url("/it/{$section}/{$slug}"));
        }
    }
});

it('never lists a scheduled post, an unpublished article or a detail template', function (): void {
    /** @var TestCase $this */
    $scheduled = samplePost('Post programmato per la prossima settimana', ['published_at' => now()->addDays(5)]);
    $draft = samplePost('Bozza non pubblicata', ['published_at' => null]);
    $post = samplePost('Post pubblicato oggi');

    $locs = sitemapLocs($this);

    expect(implode(' ', $locs))->not->toContain($scheduled->slug)->not->toContain($draft->slug);
    expect($locs)->toContain(url("/it/blog/{$post->slug}"));
    foreach ($locs as $loc) {
        expect($loc)->not->toMatch('#/view$#');
    }
});

it('lists only detail urls whose page shows that article', function (): void {
    /** @var TestCase $this */
    $post = samplePost('Guida alla segnalazione utile');
    $event = sampleEvent('Incontro con i cittadini');

    $detail = array_values(array_filter(
        sitemapLocs($this),
        static fn (string $loc): bool => preg_match('#/it/(blog|events)/[^/]+$#', $loc) === 1,
    ));

    expect($detail)->toContain(url("/it/blog/{$post->slug}"))->toContain(url("/it/events/{$event->slug}"));
    foreach ([$post, $event] as $article) {
        $section = $article->content_type === 'event' ? 'events' : 'blog';
        $this->get("/it/{$section}/{$article->slug}")->assertOk()->assertSee($article->title);
    }
});

/*
 * Segnalazioni pubbliche (DCP-273): /{lang}/tickets/{id} e' pubblica per scelta (DCP-148), con la stessa visibilita' di mappa ed
 * elenco. La sitemap la elenca solo per l'insieme visibile a un anonimo, anche quando e' generata da un utente loggato.
 */

/** @param array<string, mixed> $attributes */
function sitemapTicket(array $attributes = []): Ticket
{
    return TicketFactory::new()->createOne(array_merge([
        'name' => 'Buca in via Solferino',
        'content' => 'Una buca profonda davanti al civico.',
        'status' => TicketStatusEnum::IN_PROGRESS,
        'location' => ['lat' => 43.7696, 'lng' => 11.2558, 'address' => 'Via Solferino - 50100 Firenze'],
    ], $attributes));
}

it('lists the detail page of every public ticket in every language', function (): void {
    /** @var TestCase $this */
    $ticket = sitemapTicket();

    $locs = sitemapLocs($this);

    foreach (['it', 'en', 'de', 'es'] as $lang) {
        expect($locs)->toContain(url("/{$lang}/tickets/{$ticket->id}"));
    }
});

it('never lists a ticket the public cannot see, even when its owner builds the sitemap', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $pending = sitemapTicket(['status' => TicketStatusEnum::PENDING, 'owner_id' => $owner->getKey()]);
    $withoutPlace = sitemapTicket(['location' => null]);
    $public = sitemapTicket(['name' => 'Cassonetti stracolmi in piazza']);

    $asGuest = sitemapLocs($this);
    $this->actingAs($owner);
    $asOwner = sitemapLocs($this);

    foreach ([$asGuest, $asOwner] as $locs) {
        expect($locs)
            ->toContain(url("/it/tickets/{$public->id}"))
            ->not->toContain(url("/it/tickets/{$pending->id}"))
            ->not->toContain(url("/it/tickets/{$withoutPlace->id}"));
    }
});

it('lists only ticket urls whose page shows that report', function (): void {
    /** @var TestCase $this */
    $first = sitemapTicket(['name' => 'Lampione spento in via Roma']);
    $second = sitemapTicket(['name' => 'Cassonetti stracolmi in piazza']);

    $detail = array_values(array_filter(
        sitemapLocs($this),
        static fn (string $loc): bool => preg_match('#/it/tickets/\d+$#', $loc) === 1,
    ));

    expect($detail)->toContain(url("/it/tickets/{$first->id}"))->toContain(url("/it/tickets/{$second->id}"));
    foreach ([$first, $second] as $ticket) {
        $this->get("/it/tickets/{$ticket->id}")->assertOk()->assertSee($ticket->name);
    }
});
