<?php

declare(strict_types=1);

use Dom\Element;
use Dom\HTMLDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Uri;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-110: /{lang}/area-personale/pratiche ("Le mie segnalazioni") e /{lang}/area-personale/seguite ("Segnalazioni che seguo")
 * sono pagine JSON CMS con `"middleware": ["auth"]`, rese dal blocco `personal-area-practices` del tema. Qui si prova il
 * comportamento: ospite al login localizzato, dati veri del solo proprietario/seguace, stato vuoto utile, quattro lingue.
 * Le asserzioni sul contratto con i link (dettaglio, tracciamento, "Nuova segnalazione") vivono anche in CitizenMyTicketsDetailLinkTest
 * e FollowedTicketsPageTest.
 */
function personalAreaDocument(string $html): HTMLDocument
{
    return HTMLDocument::createFromString($html, LIBXML_NOERROR);
}

/**
 * @return list<Element>
 */
function personalAreaElements(HTMLDocument $document, string $selector): array
{
    $list = $document->querySelectorAll($selector);
    $elements = [];
    for ($index = 0; $index < $list->length; $index++) {
        $node = $list->item($index);
        Assert::assertInstanceOf(Element::class, $node);
        $elements[] = $node;
    }

    return $elements;
}

function personalAreaPath(?string $url): string
{
    return '/'.ltrim(Uri::of($url ?? '')->path(), '/');
}

dataset('personal area pages', [
    'practices it' => ['it', 'pratiche'],
    'practices en' => ['en', 'pratiche'],
    'practices de' => ['de', 'pratiche'],
    'practices es' => ['es', 'pratiche'],
    'followed it' => ['it', 'seguite'],
    'followed en' => ['en', 'seguite'],
    'followed de' => ['de', 'seguite'],
    'followed es' => ['es', 'seguite'],
]);

it('sends a guest to the login page of the same language', function (string $locale, string $page): void {
    /** @var TestCase $this */
    $this->get("/{$locale}/area-personale/{$page}")->assertRedirect("/{$locale}/auth/login");
})->with('personal area pages');

it('opens for a signed-in citizen with one h1, the localized title and no raw translation key', function (string $locale, string $page): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $titles = [
        'pratiche' => ['it' => 'Le mie segnalazioni', 'en' => 'My reports', 'de' => 'Meine Meldungen', 'es' => 'Mis incidencias'],
        'seguite' => ['it' => 'Segnalazioni che seguo', 'en' => 'Reports I follow', 'de' => 'Meldungen, denen ich folge', 'es' => 'Incidencias que sigo'],
    ];

    $response = $this->actingAs($citizen)->get("/{$locale}/area-personale/{$page}")->assertOk();

    $html = (string) $response->getContent();
    $document = personalAreaDocument($html);
    $headings = personalAreaElements($document, 'main h1');
    Assert::assertCount(1, $headings, 'La pagina deve avere un solo h1.');
    Assert::assertSame($titles[$page][$locale], trim($headings[0]->textContent ?? ''));
    Assert::assertStringContainsString('<title>'.$titles[$page][$locale].'</title>', $html);
    Assert::assertStringNotContainsString('personal_area.', $html, 'Chiave di traduzione grezza nella pagina.');
    Assert::assertStringNotContainsString('fixcity::', $html, 'Chiave di traduzione grezza nella pagina.');
    Assert::assertNotNull($document->querySelector('main #main-container'), 'Manca il bersaglio del link "Vai ai contenuti".');
})->with('personal area pages');

it('links the four personal area pages in the local navigation and marks the current one', function (string $page, string $current): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();

    $document = personalAreaDocument((string) $this->actingAs($citizen)->get("/it/area-personale/{$page}")->assertOk()->getContent());

    $hrefs = array_map(
        static fn (Element $link): string => personalAreaPath($link->getAttribute('href')),
        personalAreaElements($document, 'main nav.cmp-nav-tab a.nav-link'),
    );
    Assert::assertSame(
        ['/it/area-personale/pratiche', '/it/area-personale/seguite', '/it/area-personale/notifiche', '/it/area-personale/impostazioni'],
        $hrefs,
    );
    $active = personalAreaElements($document, 'main nav.cmp-nav-tab a[aria-current="page"]');
    Assert::assertCount(1, $active);
    Assert::assertSame($current, personalAreaPath($active[0]->getAttribute('href')));
})->with([
    'practices' => ['pratiche', '/it/area-personale/pratiche'],
    'followed' => ['seguite', '/it/area-personale/seguite'],
    'notifications' => ['notifiche', '/it/area-personale/notifiche'],
    'settings' => ['impostazioni', '/it/area-personale/impostazioni'],
]);

it('lists the owned tickets by state with title, code, date, status, detail and tracking links', function (): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $open = TicketFactory::new()->createOne([
        'name' => 'Lampione spento in via Verdi',
        'owner_id' => $citizen->getKey(),
        'status' => TicketStatusEnum::IN_PROGRESS,
        'code' => 'TCK-AREA-OPEN1',
        'created_at' => Carbon::parse('2026-03-05 10:00:00'),
    ]);
    $done = TicketFactory::new()->createOne([
        'name' => 'Buca riparata in via Roma',
        'owner_id' => $citizen->getKey(),
        'status' => TicketStatusEnum::RESOLVED,
        'code' => 'TCK-AREA-DONE1',
        'created_at' => Carbon::parse('2026-02-01 10:00:00'),
    ]);

    $response = $this->actingAs($citizen)->get('/it/area-personale/pratiche')->assertOk();
    $document = personalAreaDocument((string) $response->getContent());

    $response
        ->assertSee('Lampione spento in via Verdi')
        ->assertSee('Buca riparata in via Roma')
        ->assertSee('TCK-AREA-OPEN1')
        ->assertSee('TCK-AREA-DONE1')
        ->assertSee('5 marzo 2026')
        ->assertSee('In lavorazione')
        ->assertSee('Risolta')
        ->assertSee('/it/tickets/'.$open->id, false)
        ->assertSee('/it/tickets/track/TCK-AREA-OPEN1', false)
        ->assertSee('/it/tickets/'.$done->id, false)
        ->assertSee('Nuova segnalazione')
        ->assertSee('/it/tickets/create', false)
        ->assertSee('/it/area-personale/impostazioni', false);

    $inProgress = $document->querySelector('#practices-in_progress');
    $concluded = $document->querySelector('#practices-concluded');
    Assert::assertNotNull($inProgress);
    Assert::assertNotNull($concluded);
    Assert::assertStringContainsString('Lampione spento', (string) $inProgress->textContent);
    Assert::assertStringNotContainsString('Buca riparata', (string) $inProgress->textContent);
    Assert::assertStringContainsString('Buca riparata', (string) $concluded->textContent);
});

it('does not show another citizen tickets, codes or ids, in either page', function (): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $other = UserFactory::new()->createOne();
    TicketFactory::new()->createOne(['name' => 'Segnalazione mia', 'owner_id' => $citizen->getKey(), 'code' => 'TCK-AREA-MINE']);
    $theirs = TicketFactory::new()->createOne(['name' => 'Segnalazione di un altro cittadino', 'owner_id' => $other->getKey(), 'code' => 'TCK-AREA-THEIRS', 'status' => TicketStatusEnum::PENDING]);
    TicketSubscriber::query()->create(['ticket_id' => $theirs->getKey(), 'user_id' => $citizen->getKey()]);

    foreach (['pratiche', 'seguite'] as $page) {
        $this->actingAs($citizen)->get("/it/area-personale/{$page}")
            ->assertOk()
            ->assertDontSee('Segnalazione di un altro cittadino')
            ->assertDontSee('TCK-AREA-THEIRS')
            ->assertDontSee('/it/tickets/'.$theirs->id, false);
    }

    $this->actingAs($other)->get('/it/area-personale/pratiche')
        ->assertOk()
        ->assertSee('Segnalazione di un altro cittadino')
        ->assertDontSee('Segnalazione mia')
        ->assertDontSee('TCK-AREA-MINE');
});

it('shows a useful empty state with a real action when the citizen has no tickets', function (string $page, string $heading, string $ctaPath): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();

    $response = $this->actingAs($citizen)->get("/it/area-personale/{$page}")->assertOk()->assertSee($heading);

    $cta = personalAreaDocument((string) $response->getContent())->querySelector('main .cmp-card a.btn-primary');
    Assert::assertNotNull($cta, 'Lo stato vuoto non ha un\'azione.');
    Assert::assertSame($ctaPath, personalAreaPath($cta->getAttribute('href')));
})->with([
    'practices' => ['pratiche', 'Non hai ancora inviato segnalazioni', '/it/tickets/create'],
    'followed' => ['seguite', 'Nessuna segnalazione da mostrare', '/it/tickets'],
]);

it('localizes status, date and the call to action in every served language', function (string $locale, string $status, string $date, string $cta): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    TicketFactory::new()->createOne([
        'owner_id' => $citizen->getKey(),
        'status' => TicketStatusEnum::RESOLVED,
        'code' => 'TCK-AREA-LANG1',
        'created_at' => Carbon::parse('2026-03-05 10:00:00'),
    ]);

    $this->actingAs($citizen)->get("/{$locale}/area-personale/pratiche")
        ->assertOk()
        ->assertSee($status)
        ->assertSee($date)
        ->assertSee($cta)
        ->assertSee("/{$locale}/tickets/track/TCK-AREA-LANG1", false)
        ->assertSee("/{$locale}/tickets/create", false);
})->with([
    'it' => ['it', 'Risolta', '5 marzo 2026', 'Nuova segnalazione'],
    'en' => ['en', 'Resolved', '5 March 2026', 'New report'],
    'de' => ['de', 'Gelöst', '5. März 2026', 'Neue Meldung'],
    'es' => ['es', 'Resuelta', '5 de marzo de 2026', 'Nueva incidencia'],
]);

it('tracks followed tickets by code in every language and never by database id', function (string $locale, string $unavailable): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $withCode = TicketFactory::new()->createOne(['code' => 'TCK-AREA-FOLL1', 'status' => TicketStatusEnum::IN_PROGRESS]);
    $withoutCode = TicketFactory::new()->createOne(['code' => null, 'status' => TicketStatusEnum::IN_PROGRESS]);
    TicketSubscriber::query()->create(['ticket_id' => $withCode->getKey(), 'user_id' => $citizen->getKey()]);
    TicketSubscriber::query()->create(['ticket_id' => $withoutCode->getKey(), 'user_id' => $citizen->getKey()]);

    $this->actingAs($citizen)->get("/{$locale}/area-personale/seguite")
        ->assertOk()
        ->assertSee("/{$locale}/tickets/track?code=TCK-AREA-FOLL1", false)
        ->assertSee($unavailable)
        ->assertDontSee('ticket_id=', false)
        ->assertDontSee('/tickets/track?ticket', false);
})->with([
    'it' => ['it', 'Il codice di tracciamento non è disponibile per questa segnalazione.'],
    'en' => ['en', 'The tracking code is not available for this report.'],
    'de' => ['de', 'Der Verfolgungscode ist für diese Meldung nicht verfügbar.'],
    'es' => ['es', 'El código de seguimiento no está disponible para esta incidencia.'],
]);

it('has no placeholder link in the page content', function (string $page): void {
    /** @var TestCase $this */
    $citizen = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->createOne(['owner_id' => $citizen->getKey(), 'code' => 'TCK-AREA-HASH1', 'status' => TicketStatusEnum::OPEN]);
    TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $citizen->getKey()]);

    $document = personalAreaDocument((string) $this->actingAs($citizen)->get("/it/area-personale/{$page}")->assertOk()->getContent());

    foreach (personalAreaElements($document, 'main #main-container a[href]') as $link) {
        Assert::assertNotContains(trim((string) $link->getAttribute('href')), ['#', '', 'javascript:void(0)'], 'Link finto nel contenuto: '.trim($link->textContent ?? ''));
    }
})->with(['pratiche', 'seguite']);
