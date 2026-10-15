<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Modules\Fixcity\Actions\ListPublicTicketsAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Datas\PublicTicketListFilters;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * DCP-104: elenco pubblico delle segnalazioni (/{lang}/tickets). Solo stati visibili a tutti, filtri veri con conteggi veri,
 * ordinamento e paginazione; nessun dato personale dell'autore e nessun codice di tracciamento (capability token).
 *
 * @param  array<string, mixed>  $attributes
 */
function publicTicket(string $name, TicketStatusEnum $status = TicketStatusEnum::IN_PROGRESS, array $attributes = []): void
{
    TicketFactory::new()->createOne(array_merge([
        'name' => $name,
        'status' => $status,
        'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        'location' => ['lat' => 43.7696, 'lng' => 11.2558, 'address' => 'Via Solferino, Firenze'],
    ], $attributes));
}

/**
 * @param  array<string, mixed>  $query
 * @return array{items: list<array{id: int, title: string, url: string, type: string, type_label: string, status: string, status_label: string, created_at: string, address: string, excerpt: string}>, total: int, page: int, last_page: int, per_page: int, type_facets: list<array{value: string, label: string, count: int}>, status_facets: list<array{value: string, label: string, count: int}>, resolved_last_12_months: int}
 */
function listFor(array $query = []): array
{
    return app(ListPublicTicketsAction::class)->execute(PublicTicketListFilters::fromRequest(Request::create('/it/tickets', 'GET', $query)));
}

describe('ListPublicTicketsAction visibility and privacy', function (): void {
    test('lists only statuses visible to everyone, also to their own author', function (): void {
        $author = UserFactory::new()->createOne();
        publicTicket('Pubblica in lavorazione');
        publicTicket('Bozza interna', TicketStatusEnum::DRAFT);
        publicTicket('In attesa di presa in carico', TicketStatusEnum::PENDING, ['owner_id' => $author->getKey()]);
        $this->actingAs($author);

        $result = listFor();

        Assert::assertSame(1, $result['total']);
        Assert::assertSame(['Pubblica in lavorazione'], array_column($result['items'], 'title'));
    });

    test('exposes no personal data and no tracking code', function (): void {
        $author = UserFactory::new()->createOne(['email' => 'autore.riservato@example.test']);
        publicTicket('Lampione spento', TicketStatusEnum::OPEN, [
            'owner_id' => $author->getKey(),
            'code' => 'TCK-SEGRETO-0042',
            'email' => 'autore.riservato@example.test',
        ]);

        $json = json_encode(listFor(), JSON_THROW_ON_ERROR);

        Assert::assertStringContainsString('Lampione spento', $json);
        Assert::assertStringNotContainsString('TCK-SEGRETO-0042', $json);
        Assert::assertStringNotContainsString('autore.riservato@example.test', $json);
        foreach (listFor()['items'] as $item) {
            Assert::assertSame([], array_intersect(['code', 'owner_id', 'email', 'owner'], array_keys($item)));
        }
    });

    test('links every item to its localized detail page', function (): void {
        publicTicket('Buca in via Solferino');

        $item = listFor()['items'][0];

        Assert::assertSame('/it/tickets/'.$item['id'], $item['url']);
    });
});

describe('ListPublicTicketsAction filters', function (): void {
    test('searches title, description and address and ignores LIKE wildcards', function (): void {
        publicTicket('Buca profonda', attributes: ['content' => 'Pericolo per le biciclette']);
        publicTicket('Cestino rotto', attributes: ['content' => 'Da sostituire', 'location' => ['lat' => 43.77, 'lng' => 11.26, 'address' => 'Piazza Dalmazia']]);
        publicTicket('Altro caso', attributes: ['content' => 'Sconto 100% sul nulla']);

        Assert::assertSame(['Buca profonda'], array_column(listFor(['q' => 'biciclette'])['items'], 'title'));
        Assert::assertSame(['Cestino rotto'], array_column(listFor(['q' => 'dalmazia'])['items'], 'title'));
        Assert::assertSame(['Buca profonda'], array_column(listFor(['q' => 'BUCA'])['items'], 'title'));
        Assert::assertSame(['Altro caso'], array_column(listFor(['q' => '100%'])['items'], 'title'));
        Assert::assertSame(3, listFor(['q' => '%_'])['total']);
    });

    test('filters by type and by status and ignores unknown values', function (): void {
        publicTicket('Strada', TicketStatusEnum::IN_PROGRESS, ['type' => TicketTypeEnum::ROAD_MAINTENANCE]);
        publicTicket('Luce', TicketStatusEnum::RESOLVED, ['type' => TicketTypeEnum::PUBLIC_LIGHTING]);
        publicTicket('Luce 2', TicketStatusEnum::IN_PROGRESS, ['type' => TicketTypeEnum::PUBLIC_LIGHTING]);

        Assert::assertEqualsCanonicalizing(['Luce', 'Luce 2'], array_column(listFor(['types' => ['public_lighting']])['items'], 'title'));
        Assert::assertEqualsCanonicalizing(['Strada', 'Luce 2'], array_column(listFor(['statuses' => ['in_progress']])['items'], 'title'));
        Assert::assertSame(['Luce 2'], array_column(listFor(['types' => ['public_lighting'], 'statuses' => ['in_progress']])['items'], 'title'));
        Assert::assertSame(3, listFor(['types' => ['non_esiste'], 'statuses' => ['pending', 'draft'], 'period' => 'ieri', 'sort' => 'x'])['total']);
    });

    test('filters by period on the creation date', function (): void {
        publicTicket('Di ieri', attributes: ['created_at' => now()->subDay()]);
        publicTicket('Del mese scorso', attributes: ['created_at' => now()->subDays(20)]);
        publicTicket('Di due anni fa', attributes: ['created_at' => now()->subYears(2)]);

        Assert::assertSame(['Di ieri'], array_column(listFor(['period' => '7d'])['items'], 'title'));
        Assert::assertSame(['Di ieri', 'Del mese scorso'], array_column(listFor(['period' => '30d'])['items'], 'title'));
        Assert::assertSame(['Di ieri', 'Del mese scorso'], array_column(listFor(['period' => '12m'])['items'], 'title'));
        Assert::assertSame(3, listFor()['total']);
    });

    test('sorts by recency by default and on request', function (): void {
        publicTicket('Primo', attributes: ['created_at' => now()->subDays(3), 'updated_at' => now()]);
        publicTicket('Secondo', attributes: ['created_at' => now()->subDays(2), 'updated_at' => now()->subDays(2)]);
        publicTicket('Terzo', attributes: ['created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);

        Assert::assertSame(['Terzo', 'Secondo', 'Primo'], array_column(listFor()['items'], 'title'));
        Assert::assertSame(['Primo', 'Secondo', 'Terzo'], array_column(listFor(['sort' => 'oldest'])['items'], 'title'));
        Assert::assertSame(['Primo', 'Terzo', 'Secondo'], array_column(listFor(['sort' => 'updated'])['items'], 'title'));
    });
});

describe('ListPublicTicketsAction pagination and facets', function (): void {
    test('paginates twenty items per page', function (): void {
        for ($index = 1; $index <= 23; $index++) {
            publicTicket('Segnalazione '.$index, attributes: ['created_at' => now()->subMinutes(30 - $index)]);
        }

        $first = listFor();
        $last = listFor(['page' => 2]);
        $beyond = listFor(['page' => 99]);

        Assert::assertSame([23, 20, 2, 1], [$first['total'], count($first['items']), $first['last_page'], $first['page']]);
        Assert::assertSame(3, count($last['items']));
        Assert::assertSame(2, $beyond['page']);
        Assert::assertSame(3, count($beyond['items']));
        Assert::assertSame([], array_intersect(array_column($first['items'], 'id'), array_column($last['items'], 'id')));
    });

    test('counts every type with the other filters applied but not its own', function (): void {
        publicTicket('A', TicketStatusEnum::IN_PROGRESS, ['type' => TicketTypeEnum::ROAD_MAINTENANCE]);
        publicTicket('B', TicketStatusEnum::RESOLVED, ['type' => TicketTypeEnum::ROAD_MAINTENANCE]);
        publicTicket('C', TicketStatusEnum::IN_PROGRESS, ['type' => TicketTypeEnum::PUBLIC_LIGHTING]);
        publicTicket('D', TicketStatusEnum::PENDING, ['type' => TicketTypeEnum::PUBLIC_LIGHTING]);

        $result = listFor(['statuses' => ['in_progress'], 'types' => ['public_lighting']]);
        $counts = array_column($result['type_facets'], 'count', 'value');

        Assert::assertEquals(['road_maintenance' => 1, 'public_lighting' => 1], $counts);
        Assert::assertSame(1, $result['total']);
        Assert::assertSame(['in_progress' => 1], array_column($result['status_facets'], 'count', 'value'));
        Assert::assertSame('Manutenzione Stradale', array_column($result['type_facets'], 'label', 'value')['road_maintenance'] ?? null);
    });

    test('keeps a selected facet visible even when it has no result left', function (): void {
        publicTicket('Solo strada', TicketStatusEnum::IN_PROGRESS, ['type' => TicketTypeEnum::ROAD_MAINTENANCE]);

        $result = listFor(['types' => ['public_lighting'], 'statuses' => ['resolved']]);

        Assert::assertSame(0, $result['total']);
        Assert::assertEquals(['public_lighting' => 0], array_column($result['type_facets'], 'count', 'value'));
        Assert::assertEquals(['resolved' => 0], array_column($result['status_facets'], 'count', 'value'));
    });

    test('counts only resolved tickets of the last twelve months', function (): void {
        publicTicket('Risolta di recente', TicketStatusEnum::RESOLVED);
        publicTicket('Risolta tempo fa', TicketStatusEnum::RESOLVED, ['updated_at' => now()->subYears(2)]);
        publicTicket('Ancora aperta', TicketStatusEnum::OPEN);

        Assert::assertSame(1, listFor()['resolved_last_12_months']);
    });
});

describe('PublicTicketListFilters', function (): void {
    test('chooses the list view when the visitor searches, filters or pages and the map otherwise', function (): void {
        $view = static fn (array $query): string => PublicTicketListFilters::fromRequest(Request::create('/it/tickets', 'GET', $query))->view;

        Assert::assertSame('map', $view([]));
        Assert::assertSame('list', $view(['q' => 'buca']));
        Assert::assertSame('list', $view(['types' => ['public_lighting']]));
        Assert::assertSame('list', $view(['page' => 2]));
        Assert::assertSame('list', $view(['sort' => 'oldest']));
        Assert::assertSame('list', $view(['view' => 'list']));
        Assert::assertSame('map', $view(['view' => 'map', 'q' => 'buca']));
        Assert::assertSame('map', $view(['view' => 'x']));
    });

    test('builds a canonical shareable query without defaults', function (): void {
        $filters = PublicTicketListFilters::fromRequest(Request::create('/it/tickets', 'GET', [
            'q' => '  buca  ', 'types' => ['public_lighting', 'x'], 'statuses' => ['resolved'], 'period' => '30d', 'sort' => 'recent', 'page' => '3',
        ]));

        Assert::assertSame(
            ['q' => 'buca', 'types' => ['public_lighting'], 'statuses' => ['resolved'], 'period' => '30d', 'page' => 3],
            $filters->toQuery(),
        );
        Assert::assertSame([], PublicTicketListFilters::fromRequest(Request::create('/it/tickets'))->toQuery());
        Assert::assertSame(['q' => 'buca'], $filters->withoutPage()->toQuery(['types', 'statuses', 'period']));
    });
});
