<?php

declare(strict_types=1);

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * DCP-104: pagina reale /{lang}/tickets (JSON tickets.index, blocco ticket-list/design-comuni).
 * L'elenco e' PUBBLICO per scelta (trasparenza): nessun middleware `auth` nel JSON; la riservatezza sta nei dati (solo stati visibili
 * a tutti, nessun autore, nessun codice di tracciamento).
 *
 * @param  array<string, mixed>  $attributes
 */
function listedTicket(string $name, array $attributes = []): int
{
    return TicketFactory::new()->createOne(array_merge([
        'name' => $name,
        'status' => TicketStatusEnum::IN_PROGRESS,
        'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        'location' => ['lat' => 43.7696, 'lng' => 11.2558, 'address' => 'Via Solferino - 50100 Firenze'],
    ], $attributes))->id;
}

/**
 * @return list<string>
 */
function hrefsIn(string $html, string $xpath): array
{
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8"?>'.$html);
    $links = [];
    foreach ((new DOMXPath($dom))->query($xpath.'//a/@href') ?: [] as $href) {
        $links[] = $href->nodeValue ?? '';
    }

    return $links;
}

describe('guest access and languages', function (): void {
    test('is public in all four languages with localized texts', function (): void {
        /** @var TestCase $this */
        listedTicket('Buca in via Solferino');

        $expected = [
            'it' => ['Elenco segnalazioni', '1 Risultato', 'Cerca nelle segnalazioni'],
            'en' => ['Reported issues', '1 result', 'Search reports'],
            'de' => ['Liste der Meldungen', '1 Ergebnis', 'Meldungen durchsuchen'],
            'es' => ['Lista de incidencias', '1 resultado', 'Buscar en las incidencias'],
        ];
        foreach ($expected as $locale => $texts) {
            $response = $this->get('/'.$locale.'/tickets')->assertOk()->assertSee('Buca in via Solferino');
            foreach ($texts as $text) {
                $response->assertSee($text);
            }
            $response->assertSee('/'.$locale.'/tickets/create', false)->assertDontSee('fixcity::ticket_list.');
        }
    });

    test('links every card to the localized detail page and has no dummy links in the block', function (): void {
        /** @var TestCase $this */
        $id = listedTicket('Cestino rotto');
        $html = $this->get('/en/tickets')->assertOk()->assertSee('/en/tickets/'.$id, false)->getContent();
        Assert::assertIsString($html);

        Assert::assertNotContains('#', hrefsIn($html, '//*[@id="main-container"]'));
        Assert::assertNotContains('#', hrefsIn($html, '//*[contains(@class,"cmp-contacts")]'));
        Assert::assertContains('/en/tickets/'.$id, hrefsIn($html, '//*[@id="data-ex-disservizio2"]'));
    });
});

describe('privacy', function (): void {
    test('shows only publicly visible statuses and never author data or tracking codes', function (): void {
        /** @var TestCase $this */
        $author = UserFactory::new()->createOne(['email' => 'autore.riservato@example.test']);
        listedTicket('Visibile a tutti', ['owner_id' => $author->getKey(), 'code' => 'TCK-RISERVATO-7731', 'email' => 'autore.riservato@example.test']);
        listedTicket('Ancora in attesa', ['status' => TicketStatusEnum::PENDING, 'owner_id' => $author->getKey()]);
        listedTicket('Bozza interna', ['status' => TicketStatusEnum::DRAFT]);

        $this->actingAs($author);
        $this->get('/it/tickets')->assertOk()
            ->assertSee('Visibile a tutti')
            ->assertDontSee('Ancora in attesa')
            ->assertDontSee('Bozza interna')
            ->assertDontSee('TCK-RISERVATO-7731')
            ->assertDontSee('autore.riservato@example.test')
            ->assertSee('1 Risultato');
    });

    test('escapes hostile query values and ignores unknown filters', function (): void {
        /** @var TestCase $this */
        listedTicket('Segnalazione normale');

        $this->get('/it/tickets?q='.urlencode('"><script>alert(1)</script>').'&types[]=%3Cimg%20onerror%3Dx%3E&statuses[]=pending&sort=zz&period=ieri&page=abc')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img onerror=x>', false)
            ->assertSee('&quot;&gt;&lt;script&gt;', false);
    });
});

describe('search, filters and views', function (): void {
    test('filters by query string, keeps the choice checked and shows true counts', function (): void {
        /** @var TestCase $this */
        listedTicket('Strada rotta', ['type' => TicketTypeEnum::ROAD_MAINTENANCE]);
        listedTicket('Luce spenta', ['type' => TicketTypeEnum::PUBLIC_LIGHTING, 'status' => TicketStatusEnum::RESOLVED]);
        listedTicket('Altra luce', ['type' => TicketTypeEnum::PUBLIC_LIGHTING]);

        $this->get('/it/tickets?types[]=public_lighting')->assertOk()
            ->assertSee('Luce spenta')->assertSee('Altra luce')->assertDontSee('Strada rotta')
            ->assertSee('2 Risultati')
            ->assertSee('Illuminazione Pubblica (2)')
            ->assertSee('Manutenzione Stradale (1)')
            ->assertSee('id="tl-type-public_lighting"', false);

        $html = $this->get('/it/tickets?types[]=public_lighting&statuses[]=resolved')->assertOk()->assertSee('1 Risultato')->getContent();
        Assert::assertIsString($html);
        Assert::assertMatchesRegularExpression('/id="tl-type-public_lighting"[^>]*\schecked/', $html);
        Assert::assertMatchesRegularExpression('/id="tl-status-resolved"[^>]*\schecked/', $html);
    });

    test('searches text and explains an empty result', function (): void {
        /** @var TestCase $this */
        listedTicket('Buca in via Solferino');
        listedTicket('Cestino rotto', ['location' => ['lat' => 43.77, 'lng' => 11.26, 'address' => 'Piazza Dalmazia']]);

        $this->get('/it/tickets?q=dalmazia')->assertOk()->assertSee('Cestino rotto')->assertDontSee('Buca in via')->assertSee('value="dalmazia"', false);
        $this->get('/it/tickets?q=nessunacorrispondenza')->assertOk()
            ->assertSee('Nessuna segnalazione trovata.')
            ->assertSee('Nessuna segnalazione corrisponde ai filtri scelti.')
            ->assertSee('0 Risultati');
    });

    test('opens the map by default and the list when the visitor searches or pages', function (): void {
        /** @var TestCase $this */
        listedTicket('Qualcosa');

        $this->get('/it/tickets')->assertOk()
            ->assertSee('tab-pane fade show active" id="data-ex-disservizio1"', false)
            ->assertSee('data-url="/api/tickets/geojson"', false);
        $this->get('/it/tickets?q=qualcosa')->assertOk()
            ->assertSee('tab-pane fade show active" id="data-ex-disservizio2"', false)
            ->assertSee('data-url="/api/tickets/geojson?q=qualcosa"', false);
        $this->get('/it/tickets?view=map&q=qualcosa')->assertOk()
            ->assertSee('tab-pane fade show active" id="data-ex-disservizio1"', false);
    });

    test('the geojson endpoint applies the same filters as the list', function (): void {
        /** @var TestCase $this */
        listedTicket('Strada rotta', ['type' => TicketTypeEnum::ROAD_MAINTENANCE]);
        listedTicket('Luce spenta', ['type' => TicketTypeEnum::PUBLIC_LIGHTING]);

        $this->getJson('/api/tickets/geojson?types[]=public_lighting')->assertOk()->assertJsonCount(1, 'features')->assertJsonPath('features.0.properties.title', 'Luce spenta');
        $this->getJson('/api/tickets/geojson?q=strada')->assertOk()->assertJsonCount(1, 'features')->assertJsonPath('features.0.properties.title', 'Strada rotta');
        $this->getJson('/api/tickets/geojson')->assertOk()->assertJsonCount(2, 'features');
    });
});

describe('pagination', function (): void {
    test('links pages with the filters kept in the query string and no JavaScript', function (): void {
        /** @var TestCase $this */
        for ($index = 1; $index <= 21; $index++) {
            listedTicket('Segnalazione '.$index, ['created_at' => now()->subMinutes(60 - $index)]);
        }

        $first = $this->get('/it/tickets?q=segnalazione&page=1')->assertOk()
            ->assertSee('Segnalazioni da 1 a 20 di 21')
            ->assertSee('Segnalazione 21')
            ->assertDontSee('Segnalazione 1<', false)
            ->getContent();
        Assert::assertIsString($first);
        Assert::assertContains('/it/tickets?q=segnalazione&page=2#data-ex-disservizio2', hrefsIn($first, '//nav[contains(@class,"pagination-wrapper")]'));

        $this->get('/it/tickets?q=segnalazione&page=2')->assertOk()
            ->assertSee('Pagina 2 di 2')
            ->assertSee('Segnalazioni da 21 a 21 di 21')
            ->assertSee('/it/tickets?q=segnalazione&amp;page=1', false)
            ->assertSee('tab-pane fade show active" id="data-ex-disservizio2"', false);
    });
});
