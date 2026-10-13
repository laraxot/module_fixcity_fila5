<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Modules\Fixcity\Actions\BuildTicketReviewSummaryAction;
use Modules\Fixcity\Database\Factories\ProfileFactory;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/**
 * DCP-33: dati del passo "riepilogo" del wizard `/tickets/create`. Le righe esistono solo se il dato esiste
 * (mai inventare), l'autore e i contatti sono solo quelli dell'utente autenticato, l'ospite non ha ne' l'uno ne' gli altri.
 */
/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function reviewState(array $overrides = []): array
{
    return array_merge([
        'privacyAccepted' => true,
        'priority' => 'low',
        'name' => 'Panchina danneggiata',
        'type' => 'urban_furniture',
        'content' => 'La seduta della panchina risulta inutilizzabile e pericolosa',
        'location' => ['latitude' => 43.7696, 'longitude' => 11.2558, 'address' => 'Via Solferino - 50100 Firenze (FI)'],
        'images' => [],
    ], $overrides);
}

function reviewUser(?string $phone = '+39 331 1234567'): User
{
    $user = UserFactory::new()->createOne(['name' => 'Giulia Bianchi', 'email' => 'giulia.bianchi@example.test']);
    if ($phone !== null) {
        ProfileFactory::new()->createOne(['user_id' => $user->id, 'phone' => $phone]);
    }

    return $user;
}

/**
 * @param  array<string, mixed>  $summary
 * @return list<string>
 */
function reviewLabels(array $summary, string $block): array
{
    $rows = data_get($summary, $block.'.rows', []);
    $labels = [];
    foreach (is_array($rows) ? $rows : [] as $row) {
        $labels[] = is_array($row) ? SafeStringCastAction::cast($row['label'] ?? null) : '';
    }

    return $labels;
}

describe('BuildTicketReviewSummaryAction', function (): void {
    test('builds the report block from a full state in italian', function (): void {
        app()->setLocale('it');

        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState());

        Assert::assertSame(['report', 'author', 'contacts'], array_keys($summary));
        Assert::assertSame('form.dati-di-segnalazione::data::wizard-step', $summary['report']['edit_step']);
        Assert::assertSame([
            ['label' => 'Indirizzo', 'value' => 'Via Solferino - 50100 Firenze (FI)'],
            ['label' => 'Tipo di disservizio', 'value' => 'Arredo Urbano'],
            ['label' => 'Titolo', 'value' => 'Panchina danneggiata'],
            ['label' => 'Dettagli', 'value' => 'La seduta della panchina risulta inutilizzabile e pericolosa'],
        ], $summary['report']['rows']);
        Assert::assertSame([], $summary['report']['images']);
        Assert::assertSame(
            ['latitude' => 43.7696, 'longitude' => 11.2558, 'address' => 'Via Solferino - 50100 Firenze (FI)'],
            $summary['report']['location'],
        );
    });

    test('translates labels, headings and the type in english', function (): void {
        app()->setLocale('en');

        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState(), reviewUser());

        Assert::assertSame('form.report-details::data::wizard-step', $summary['report']['edit_step']);
        Assert::assertSame(['Address', 'Type of issue', 'Title', 'Details'], reviewLabels($summary, 'report'));
        Assert::assertSame('Urban Furniture', $summary['report']['rows'][1]['value']);
        Assert::assertNotNull($summary['author']);
        Assert::assertNotNull($summary['contacts']);
        Assert::assertSame('Report', $summary['report']['title']);
        Assert::assertSame('General data', $summary['author']['group_title']);
        Assert::assertSame('Report author', $summary['author']['card_title']);
        Assert::assertSame('Contact details', $summary['contacts']['title']);
        Assert::assertSame(['Phone', 'Email'], reviewLabels($summary, 'contacts'));
    });

    test('translates the headings in italian', function (): void {
        app()->setLocale('it');

        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState(), reviewUser());

        Assert::assertNotNull($summary['author']);
        Assert::assertNotNull($summary['contacts']);
        Assert::assertSame('Segnalazione', $summary['report']['title']);
        Assert::assertSame('Disservizio', $summary['report']['card_title']);
        Assert::assertSame('Modifica', $summary['report']['edit_label']);
        Assert::assertSame('Dati Generali', $summary['author']['group_title']);
        Assert::assertSame('Autore della segnalazione', $summary['author']['card_title']);
        Assert::assertSame('Contatti', $summary['contacts']['title']);
        Assert::assertSame(['Telefono', 'Email'], reviewLabels($summary, 'contacts'));
    });

    test('omits every row whose data is missing', function (string $field, mixed $value, string $missingLabel): void {
        app()->setLocale('it');

        $labels = reviewLabels(app(BuildTicketReviewSummaryAction::class)->execute(reviewState([$field => $value])), 'report');

        Assert::assertNotContains($missingLabel, $labels);
        Assert::assertCount(3, $labels);
    })->with([
        'blank address' => ['location', ['latitude' => 43.7, 'longitude' => 11.2, 'address' => '   '], 'Indirizzo'],
        'null address' => ['location', ['latitude' => 43.7, 'longitude' => 11.2, 'address' => null], 'Indirizzo'],
        'no type' => ['type', null, 'Tipo di disservizio'],
        'unknown type' => ['type', 'not_a_real_type', 'Tipo di disservizio'],
        'blank title' => ['name', "  \n ", 'Titolo'],
        'empty details' => ['content', '', 'Dettagli'],
    ]);

    test('shows nothing for an empty state', function (): void {
        $summary = app(BuildTicketReviewSummaryAction::class)->execute([]);

        Assert::assertSame([], $summary['report']['rows']);
        Assert::assertSame([], $summary['report']['images']);
        Assert::assertNull($summary['report']['location']);
        Assert::assertNull($summary['author']);
        Assert::assertNull($summary['contacts']);
    });

    test('accepts the type as enum instance or as its value', function (): void {
        app()->setLocale('it');
        $action = app(BuildTicketReviewSummaryAction::class);

        $fromEnum = $action->execute(reviewState(['type' => TicketTypeEnum::ROAD_MAINTENANCE]));
        $fromValue = $action->execute(reviewState(['type' => 'road_maintenance']));

        Assert::assertSame('Manutenzione Stradale', $fromEnum['report']['rows'][1]['value']);
        Assert::assertSame($fromEnum['report']['rows'], $fromValue['report']['rows']);
    });

    test('keeps the address row but drops the location without usable coordinates', function (array $location): void {
        app()->setLocale('it');

        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState(['location' => $location]));

        Assert::assertNull($summary['report']['location']);
        Assert::assertContains('Indirizzo', reviewLabels($summary, 'report'));
    })->with([
        'null coordinates' => [['latitude' => null, 'longitude' => null, 'address' => 'Via Solferino']],
        'missing longitude' => [['latitude' => 43.7, 'address' => 'Via Solferino']],
        'blank strings' => [['latitude' => '', 'longitude' => '', 'address' => 'Via Solferino']],
        'not numeric' => [['latitude' => 'nord', 'longitude' => 'est', 'address' => 'Via Solferino']],
        'latitude out of range' => [['latitude' => 91.0, 'longitude' => 11.2, 'address' => 'Via Solferino']],
        'longitude out of range' => [['latitude' => 43.7, 'longitude' => -181.0, 'address' => 'Via Solferino']],
    ]);

    test('casts numeric string coordinates and tolerates a location without address', function (): void {
        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState([
            'location' => ['latitude' => '43.7696', 'longitude' => '11.2558', 'address' => null],
        ]));

        Assert::assertSame(['latitude' => 43.7696, 'longitude' => 11.2558, 'address' => ''], $summary['report']['location']);
        Assert::assertNotContains('Indirizzo', reviewLabels($summary, 'report'));
    });

    test('lists the uploaded file names and ignores stored media identifiers', function (): void {
        app()->setLocale('it');
        $state = reviewState(['images' => [
            'k1' => UploadedFile::fake()->image('6yhakandsahm413d8da.jpg'),
            'k2' => UploadedFile::fake()->image('panchina-2.png'),
            'k3' => '0d1d3e5c-aaaa-bbbb-cccc-111122223333',
        ]]);

        $report = app(BuildTicketReviewSummaryAction::class)->execute($state)['report'];

        Assert::assertSame(['6yhakandsahm413d8da.jpg', 'panchina-2.png'], $report['images']);
        $rows = $report['rows'];
        Assert::assertSame(['label' => 'Immagini', 'value' => '6yhakandsahm413d8da.jpg, panchina-2.png'], array_pop($rows));
        Assert::assertStringNotContainsString('0d1d3e5c', json_encode($report, JSON_THROW_ON_ERROR));
    });

    test('has no images row when nothing is uploaded', function (mixed $images): void {
        app()->setLocale('it');

        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState(['images' => $images]));

        Assert::assertNotContains('Immagini', reviewLabels($summary, 'report'));
        Assert::assertSame([], $summary['report']['images']);
    })->with([
        'empty' => [[]],
        'null' => [null],
        'not an array' => ['file.jpg'],
    ]);

    test('reads file names from livewire temporary uploads too', function (): void {
        $upload = Mockery::mock(TemporaryUploadedFile::class);
        $upload->shouldReceive('getClientOriginalName')->andReturn('da-livewire.jpg');

        $report = app(BuildTicketReviewSummaryAction::class)->execute(reviewState(['images' => ['k' => $upload]]))['report'];

        Assert::assertSame(['da-livewire.jpg'], $report['images']);
    });

    test('builds author and contacts for the authenticated user only', function (): void {
        app()->setLocale('it');

        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState(), reviewUser());

        Assert::assertNotNull($summary['author']);
        Assert::assertNotNull($summary['contacts']);
        Assert::assertSame('Giulia Bianchi', $summary['author']['name']);
        Assert::assertSame([], $summary['author']['rows']);
        Assert::assertSame([
            ['label' => 'Telefono', 'value' => '+39 331 1234567'],
            ['label' => 'Email', 'value' => 'giulia.bianchi@example.test'],
        ], $summary['contacts']['rows']);
    });

    test('names the author after first and last name, then after the email', function (): void {
        $action = app(BuildTicketReviewSummaryAction::class);
        $withParts = UserFactory::new()->createOne(['name' => '', 'first_name' => 'Giulia', 'last_name' => 'Bianchi', 'email' => 'solo.email@example.test']);
        $withEmailOnly = UserFactory::new()->createOne(['name' => '', 'email' => 'solo.email2@example.test']);

        $fromParts = $action->execute(reviewState(), $withParts)['author'];
        $fromEmail = $action->execute(reviewState(), $withEmailOnly)['author'];

        Assert::assertNotNull($fromParts);
        Assert::assertNotNull($fromEmail);
        Assert::assertSame('Giulia Bianchi', $fromParts['name']);
        Assert::assertSame('solo.email2@example.test', $fromEmail['name']);
    });

    test('leaves out the phone when the profile has none', function (?string $phone): void {
        app()->setLocale('it');

        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState(), reviewUser($phone));

        Assert::assertSame(['Email'], reviewLabels($summary, 'contacts'));
    })->with([
        'no profile' => [null],
        'blank phone' => ['  '],
    ]);

    test('never invents a fiscal code row', function (): void {
        app()->setLocale('it');

        $summary = app(BuildTicketReviewSummaryAction::class)->execute(
            reviewState(['fiscal_code' => 'GLABNC72H25H501Y', 'author_fiscal_code' => 'GLABNC72H25H501Y']),
            reviewUser(),
        );

        Assert::assertNotNull($summary['author']);
        Assert::assertSame([], $summary['author']['rows']);
        Assert::assertStringNotContainsString('GLABNC72H25H501Y', json_encode($summary, JSON_THROW_ON_ERROR));
        Assert::assertStringNotContainsStringIgnoringCase('Codice Fiscale', json_encode($summary, JSON_THROW_ON_ERROR));
    });

    test('gives a guest neither author nor contacts, whatever the state claims', function (): void {
        $summary = app(BuildTicketReviewSummaryAction::class)->execute(reviewState([
            'email' => 'intruso@example.test',
            'phone' => '+39 000 0000000',
            'author_name' => 'Intruso',
            'owner_id' => 'abc',
        ]), null);

        Assert::assertNull($summary['author']);
        Assert::assertNull($summary['contacts']);
        $json = json_encode($summary, JSON_THROW_ON_ERROR);
        foreach (['intruso@example.test', '+39 000 0000000', 'Intruso'] as $leak) {
            Assert::assertStringNotContainsString($leak, $json);
        }
    });

    test('exposes no personal data other than the authenticated user', function (): void {
        $other = UserFactory::new()->createOne(['name' => 'Mario Altro', 'email' => 'mario.altro@example.test']);
        ProfileFactory::new()->createOne(['user_id' => $other->id, 'phone' => '+39 999 9999999']);

        $json = json_encode(
            app(BuildTicketReviewSummaryAction::class)->execute(reviewState(['owner_id' => $other->id]), reviewUser()),
            JSON_THROW_ON_ERROR,
        );

        foreach (['mario.altro@example.test', 'Mario Altro', '+39 999 9999999'] as $leak) {
            Assert::assertStringNotContainsString($leak, $json);
        }
    });

    test('labels the map in the current language', function (string $locale, string $label): void {
        app()->setLocale($locale);

        $report = app(BuildTicketReviewSummaryAction::class)->execute(reviewState())['report'];

        Assert::assertSame($label, $report['map_label']);
    })->with([
        'italian' => ['it', 'Posizione del disservizio sulla mappa'],
        'english' => ['en', 'Location of the issue on the map'],
    ]);
});
