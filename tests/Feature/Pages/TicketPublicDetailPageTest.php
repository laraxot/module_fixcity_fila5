<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Modules\Fixcity\Actions\ResolveTicketStatusMarkerPropertiesAction;
use Modules\Fixcity\Database\Factories\TicketActivityFactory;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * DCP-148: dettaglio PUBBLICO /{lang}/tickets/{id} (blocco ticket-detail/design-comuni) e funzione "Segui"
 * (POST /{lang}/tickets/{id}/follow). La pagina e' pubblica per scelta (come l'elenco, DCP-104): la riservatezza sta nei dati
 * (BuildPublicTicketDetailAction costruisce l'insieme esposto) e nello stato del ticket.
 *
 * @param  array<string, mixed>  $attributes
 */
function detailTicket(array $attributes = []): int
{
    return TicketFactory::new()->createOne(array_merge([
        'name' => 'Buca in via Solferino',
        'content' => 'Una buca profonda davanti al civico.',
        'status' => TicketStatusEnum::IN_PROGRESS,
        'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        'created_at' => '2026-03-14 15:42:10',
        'location' => ['lat' => 43.7696, 'lng' => 11.2558, 'address' => 'Via Solferino - 50100 Firenze'],
    ], $attributes))->id;
}

/**
 * Testo (o valore dell'attributo) di ogni nodo che corrisponde all'espressione XPath.
 *
 * @return list<string>
 */
function detailNodes(string $html, string $xpath): array
{
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8"?>'.$html);
    $found = [];
    foreach ((new DOMXPath($dom))->query($xpath) ?: [] as $node) {
        if ($node instanceof DOMNode) {
            $found[] = Str::of($node->textContent)->squish()->toString();
        }
    }

    return $found;
}

function detailStatusChange(int $ticketId, string $visibility, string $to, string $reason, string $when): void
{
    TicketActivityFactory::new()->createOne([
        'ticket_id' => $ticketId,
        'event_type' => 'status_change',
        'payload' => ['from' => 'pending', 'to' => $to],
        'visibility' => $visibility,
        'reason' => $reason,
        'created_at' => $when,
    ]);
}

describe('guest access and languages', function (): void {
    test('is public in all four languages with localized texts', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();

        $expected = [
            'it' => ['headings' => ['Dove', 'Descrizione', 'Stato della segnalazione'], 'texts' => ['Torna all’elenco', 'Segnala un altro problema', 'Accedi per seguire']],
            'en' => ['headings' => ['Where', 'Description', 'Report status'], 'texts' => ['Back to the list', 'Report another problem', 'Sign in to follow']],
            'de' => ['headings' => ['Wo', 'Beschreibung', 'Status der Meldung'], 'texts' => ['Zurück zur Liste', 'Ein weiteres Problem melden', 'Zum Folgen anmelden']],
            'es' => ['headings' => ['Dónde', 'Descripción', 'Estado de la incidencia'], 'texts' => ['Volver a la lista', 'Informar de otro problema', 'Inicia sesión para seguirla']],
        ];
        foreach ($expected as $locale => $checks) {
            $response = $this->get('/'.$locale.'/tickets/'.$id)->assertOk()->assertSee('Buca in via Solferino')->assertDontSee('fixcity::ticket_detail.');
            foreach ($checks['texts'] as $text) {
                $response->assertSee($text);
            }
            $html = $response->getContent();
            Assert::assertIsString($html);
            foreach ($checks['headings'] as $heading) {
                Assert::assertContains($heading, detailNodes($html, '//main//h2'), $locale.': manca il titolo di sezione '.$heading);
            }
        }
    });

    test('has the title as the only h1 and shows status, type, address, description and the day only', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();

        $response = $this->get('/it/tickets/'.$id)->assertOk()
            ->assertSee('In lavorazione')
            ->assertSee('Manutenzione Stradale')
            ->assertSee('Via Solferino - 50100 Firenze')
            ->assertSee('Una buca profonda davanti al civico.')
            ->assertSee('14 marzo 2026')
            ->assertSee('Segnalazione n. '.$id)
            ->assertDontSee('15:42');
        $html = $response->getContent();
        Assert::assertIsString($html);
        Assert::assertSame(['Buca in via Solferino'], detailNodes($html, '//h1'));

        $this->get('/en/tickets/'.$id)->assertOk()->assertSee('14 March 2026')->assertDontSee('15:42');
    });

    test('links the breadcrumb and the buttons to localized pages and never to a dummy anchor', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();
        $html = $this->get('/en/tickets/'.$id)->assertOk()->assertSee('aria-current="page"', false)->getContent();
        Assert::assertIsString($html);

        $links = detailNodes($html, '//*[@id="main-container"]//a/@href');
        Assert::assertNotContains('#', $links);
        Assert::assertNotContains('', $links);
        foreach (['/en', '/en/tickets', '/en/tickets/create', '/en/auth/login'] as $expectedLink) {
            Assert::assertContains($expectedLink, $links);
        }
    });

    test('uses the report title for the page title and a short public excerpt for the description', function (): void {
        /** @var TestCase $this */
        $id = detailTicket(['name' => 'Lampione spento', 'content' => str_repeat('Il lampione non si accende da giorni. ', 20)]);
        $html = $this->get('/it/tickets/'.$id)->assertOk()->getContent();
        Assert::assertIsString($html);

        Assert::assertStringContainsString('Lampione spento', detailNodes($html, '//title')[0] ?? '');
        $description = detailNodes($html, '//meta[@name="description"]/@content')[0] ?? '';
        Assert::assertStringStartsWith('Il lampione non si accende da giorni.', $description);
        Assert::assertLessThanOrEqual(160, mb_strlen($description));
    });
});

describe('visibility', function (): void {
    test('answers 404 for non public statuses, unknown ids and ids that are not numbers', function (): void {
        /** @var TestCase $this */
        $this->get('/it/tickets/'.detailTicket(['status' => TicketStatusEnum::PENDING]))->assertNotFound();
        $this->get('/it/tickets/'.detailTicket(['status' => TicketStatusEnum::DRAFT]))->assertNotFound();
        $this->get('/it/tickets/999999')->assertNotFound();
        $this->get('/it/tickets/abc')->assertNotFound();
    });
});

describe('privacy and escaping', function (): void {
    test('never shows author data, tracking codes or private fields', function (): void {
        /** @var TestCase $this */
        $author = UserFactory::new()->createOne(['email' => 'autore.riservato@example.test']);
        $id = detailTicket(['owner_id' => $author->getKey(), 'code' => 'TCK-RISERVATO-7731', 'email' => 'autore.riservato@example.test', 'created_by' => 'mario.rossi']);

        $this->get('/it/tickets/'.$id)->assertOk()
            ->assertDontSee('TCK-RISERVATO-7731')
            ->assertDontSee('autore.riservato@example.test')
            ->assertDontSee('mario.rossi')
            ->assertDontSee('owner_id');
        $html = $this->get('/it/tickets/'.$id)->getContent();
        Assert::assertIsString($html);
        Assert::assertSame([], detailNodes($html, '//*[@id="main-container"]//img'), 'nessuna immagine nel dettaglio pubblico');
    });

    test('escapes hostile text in title and description and keeps line breaks without raw html', function (): void {
        /** @var TestCase $this */
        $id = detailTicket(['name' => '<script>alert(1)</script>', 'content' => "Prima riga <b>grassetto</b>\nSeconda riga"]);

        $this->get('/it/tickets/'.$id)->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<b>grassetto</b>', false)
            ->assertSee('&lt;b&gt;grassetto&lt;/b&gt;<br />', false)
            ->assertSee('Seconda riga');
    });
});

describe('public timeline', function (): void {
    test('shows public status changes with the day only and hides the private ones', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();
        detailStatusChange($id, 'public', 'in_review', 'Presa in carico dal Comune', '2026-03-15 09:30:00');
        detailStatusChange($id, 'internal', 'in_progress', 'Nota interna riservata', '2026-03-16 10:00:00');
        detailStatusChange($id, 'author_only', 'on_hold', 'Solo per l’autore', '2026-03-17 11:00:00');

        $this->get('/it/tickets/'.$id)->assertOk()
            ->assertSee('In revisione')
            ->assertSee('Presa in carico dal Comune')
            ->assertSee('15 marzo 2026')
            ->assertDontSee('09:30')
            ->assertDontSee('Nota interna riservata')
            ->assertDontSee('Solo per l’autore')
            ->assertDontSee('16 marzo 2026');
    });

    test('explains the current status when there is no public update yet', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();

        $this->get('/it/tickets/'.$id)->assertOk()->assertSee('Per ora non ci sono aggiornamenti: la segnalazione è «In lavorazione».');
        $this->get('/en/tickets/'.$id)->assertOk()->assertSee('There are no updates yet: the report is “In Progress”.');
    });
});

describe('follow', function (): void {
    test('sends a guest to the login and creates no subscription', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();

        $this->post('/it/tickets/'.$id.'/follow', ['following' => '1'])->assertRedirectContains('/it/auth/login');
        Assert::assertSame(0, TicketSubscriber::query()->where('ticket_id', $id)->count());

        $html = $this->get('/it/tickets/'.$id)->assertOk()->getContent();
        Assert::assertIsString($html);
        Assert::assertSame([], detailNodes($html, '//form[contains(@action,"/follow")]'));
    });

    test('offers a logged citizen a protected form to follow', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();
        $html = $this->actingAs(UserFactory::new()->createOne())->get('/it/tickets/'.$id)->assertOk()->assertSee('Segui questa segnalazione')->assertDontSee('Accedi per seguire')->getContent();
        Assert::assertIsString($html);

        $form = '//form[@method="post" and @action="/it/tickets/'.$id.'/follow"]';
        Assert::assertCount(1, detailNodes($html, $form));
        Assert::assertCount(1, detailNodes($html, $form.'//input[@type="hidden" and @name="_token"]'));
        Assert::assertSame(['1'], detailNodes($html, $form.'//input[@name="following"]/@value'));
    });

    test('follows and unfollows idempotently and tells the citizen what happened', function (): void {
        /** @var TestCase $this */
        $citizen = UserFactory::new()->createOne();
        $id = detailTicket();
        $this->actingAs($citizen);
        $count = static fn (): int => TicketSubscriber::query()->where('ticket_id', $id)->where('user_id', $citizen->getKey())->count();

        $this->post('/it/tickets/'.$id.'/follow', ['following' => '1'])->assertRedirect('/it/tickets/'.$id);
        $this->post('/it/tickets/'.$id.'/follow', ['following' => '1'])->assertRedirect('/it/tickets/'.$id);
        Assert::assertSame(1, $count());

        $html = $this->get('/it/tickets/'.$id)->assertOk()->assertSee('Smetti di seguire')->assertDontSee('Segui questa segnalazione')->getContent();
        Assert::assertIsString($html);
        Assert::assertCount(1, detailNodes($html, '//form[@action="/it/tickets/'.$id.'/follow"]//input[@name="following" and @value="0"]'));

        $this->post('/it/tickets/'.$id.'/follow', ['following' => '0'])->assertRedirect('/it/tickets/'.$id);
        $this->post('/it/tickets/'.$id.'/follow', ['following' => '0'])->assertRedirect('/it/tickets/'.$id);
        Assert::assertSame(0, $count());
    });

    test('shows the status message in the language of the page with role status', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();
        $this->actingAs(UserFactory::new()->createOne());
        // in questa versione di Laravel followingRedirects() vale per una sola richiesta
        $post = fn (string $locale, string $following): TestResponse => $this->followingRedirects()->post('/'.$locale.'/tickets/'.$id.'/follow', ['following' => $following]);

        $expected = [
            'it' => ['Ora segui questa segnalazione', 'Non segui più questa segnalazione'],
            'en' => ['You now follow this report', 'You no longer follow this report'],
            'de' => ['Sie folgen dieser Meldung jetzt', 'Sie folgen dieser Meldung nicht mehr'],
            'es' => ['Ahora sigues esta incidencia', 'Ya no sigues esta incidencia'],
        ];
        foreach ($expected as $locale => [$followed, $unfollowed]) {
            $response = $post($locale, '1');
            Assert::assertSame(200, $response->getStatusCode(), $locale.' segui');
            $html = $response->getContent();
            Assert::assertIsString($html);
            Assert::assertStringContainsString($followed, implode(' ', detailNodes($html, '//*[@role="status"]')), $locale);

            $response = $post($locale, '0');
            Assert::assertSame(200, $response->getStatusCode(), $locale.' smetti');
            $html = $response->getContent();
            Assert::assertIsString($html);
            Assert::assertStringContainsString($unfollowed, implode(' ', detailNodes($html, '//*[@role="status"]')), $locale);
        }
    });

    test('cannot follow a report that is not public, nor send an invalid choice', function (): void {
        /** @var TestCase $this */
        $citizen = UserFactory::new()->createOne();
        $hidden = detailTicket(['status' => TicketStatusEnum::PENDING]);
        $id = detailTicket();
        $this->actingAs($citizen);

        $this->post('/it/tickets/'.$hidden.'/follow', ['following' => '1'])->assertNotFound();
        $this->post('/it/tickets/999999/follow', ['following' => '1'])->assertNotFound();
        $this->post('/it/tickets/'.$id.'/follow', ['following' => 'forse'])->assertSessionHasErrors('following');
        $this->post('/it/tickets/'.$id.'/follow')->assertSessionHasErrors('following');
        Assert::assertSame(0, TicketSubscriber::query()->whereIn('ticket_id', [$hidden, $id])->count());
    });

    test('puts the followed report in the personal area list', function (): void {
        /** @var TestCase $this */
        $citizen = UserFactory::new()->createOne();
        $id = detailTicket(['name' => 'Panchina rotta al parco']);
        $this->actingAs($citizen);

        $this->get('/it/area-personale/seguite')->assertOk()->assertDontSee('Panchina rotta al parco');
        $this->post('/it/tickets/'.$id.'/follow', ['following' => '1']);
        $this->get('/it/area-personale/seguite')->assertOk()->assertSee('Panchina rotta al parco');
    });

    test('limits follow requests to thirty per minute', function (): void {
        /** @var TestCase $this */
        $id = detailTicket();
        $this->actingAs(UserFactory::new()->createOne());

        for ($request = 0; $request < 30; $request++) {
            $this->post('/it/tickets/'.$id.'/follow', ['following' => '1'])->assertRedirect();
        }

        $this->post('/it/tickets/'.$id.'/follow', ['following' => '1'])->assertTooManyRequests();
    });
});

describe('map marker', function (): void {
    test('shows the type icon and the status colour of the ticket, as the public map does', function (): void {
        /** @var TestCase $this */
        $html = $this->get('/it/tickets/'.detailTicket())->assertOk()->getContent();
        Assert::assertIsString($html);

        $icon = detailNodes($html, '//coordinate-picker-lit/@marker-icon-url')[0] ?? '';
        $color = detailNodes($html, '//coordinate-picker-lit/@marker-color')[0] ?? '';
        Assert::assertStringEndsWith('road-maintenance.svg', $icon);
        Assert::assertSame(app(ResolveTicketStatusMarkerPropertiesAction::class)->execute(TicketStatusEnum::IN_PROGRESS)['color'], $color);
        Assert::assertSame(['Manutenzione Stradale'], detailNodes($html, '//coordinate-picker-lit/@marker-label'));
    });
});
