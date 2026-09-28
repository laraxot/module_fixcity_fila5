<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match;
use function Safe\preg_match_all;
use function Safe\preg_split;

uses(TestCase::class);

/**
 * Il blocco `consenso` e' lo step 1 del flusso di segnalazione disservizio.
 *
 * PERCHE' UN TEST SU UN COMPONENTE DI TEMA
 * =========================================
 * Il requisito di conformita' e' del modulo Fixcity, non del tema: e' il Comune che
 * deve avere la base per trattare dati personali. Il tema e' sostituibile, il requisito
 * no. Quindi il test vive qui e verifica il contratto del blocco che il tema espone.
 *
 * PERCHE' UN TEST E NON UN SCONTRO DI SCHERMO
 * ===========================================
 * Il difetto che questo blocco aveva — due `x-data` fratelli, l'uno sul checkbox e l'altro
 * sul bottone — non si vede in un HTML statico: l'HTML e' identico, e il difetto e' che a
 * runtime lo stato non e' condiviso. Un test di interattizione con browser lo trova, ma
 * la pagina di verifica sta sotto un percorco che il middleware `BlockLegacyPublicPages`
 * chiude al pubblico, e quindi non e' raggiungibile in modo affidabile.
 *
 * La difesa che resta sempre attiva e' l'asserzione strutturale: **un solo `x-data`**, sul
 * contenitore che racchiude sia il checkbox (che scrive) sia il bottone (che legge). Se
 * qualcuno reintroduce un secondo ambito, questo test fallisce — e fallisce subito, senza
 * bisogno di un browser.
 *
 * Vedi `docs/wiki/memories/alpine-sibling-scopes-and-scheduler-timing.md`.
 */
describe('Blocco consenso — contratto e accessibilita', function (): void {
    /** @var TestCase $this */
    $render = static fn (array $data = []): string => Blade::render(
        '<x-pub_theme::consenso :data="$data" />',
        ['data' => $data],
    );

    test('espone UN solo ambito Alpine, sul contenitore che racchiude checkbox e bottone', function () use ($render): void {
        $html = $render(['action' => '/it/tickets/create']);

        // Il difetto: due `x-data`, uno per elemento. Stesso nome, zero condivisione,
        // e il bottone resta disabilitato per sempre.
        $countXData = substr_count($html, 'x-data=');
        Assert::assertSame(1, $countXData, 'Il blocco deve avere un solo x-data: gli ambiti fratelli non condividono lo stato.');

        // E deve stare sul contenitore esterno, non su un figlio: se stesse sul checkbox
        // o sul bottone, l'altro non lo vedrebbe.
        Assert::assertMatchesRegularExpression(
            '/<div[^>]*class="cmp-consenso"[^>]*x-data=/',
            $html,
            'x-data deve essere sul contenitore .cmp-consenso, che racchiude checkbox e bottone.',
        );
    });

    test('il bottone si disabilita in base allo stesso stato che il checkbox scrive', function () use ($render): void {
        $html = $render(['action' => '/it/tickets/create']);

        // Il binding del bottone e il modello del checkbox devono leggere/scrivere
        // lo stesso nome di stato.
        Assert::assertStringContainsString('x-model="accettato"', $html);
        Assert::assertStringContainsString(':disabled="!accettato"', $html);
    });

    test('il label è collegato al checkbox con lo stesso id', function () use ($render): void {
        $html = $render();

        Assert::assertMatchesRegularExpression('/<label[^>]*for="(consenso-[^"]+)"/', $html, 'Il label deve avere un for.');

        preg_match('/<label[^>]*for="(consenso-[^"]+)"/', $html, $label);
        preg_match('/<input[^>]*id="(consenso-[^"]+)"/', $html, $input);

        Assert::assertSame(
            $label[1] ?? null,
            $input[1] ?? null,
            'Il for del label e l\'id del checkbox devono coincidere: un label non cliccabile non e\' un label.',
        );
    });

    test('il checkbox ha un id proprio: due id uguali renderebbero il label ambiguo', function () use ($render): void {
        $html = $render();

        preg_match_all('/<input[^>]*id="(consenso-[^"]+)"/', $html, $ids);

        Assert::assertNotEmpty($ids[1]);
        Assert::assertSame(
            count($ids[1]),
            count(array_unique($ids[1])),
            'Ogni istanza del blocco deve avere un id univoco.',
        );
    });

    test('aria-describedby punta solo a elementi che esistono davvero', function () use ($render): void {
        $html = $render();

        Assert::assertStringContainsString('aria-describedby="', $html);

        preg_match('/aria-describedby="([^"]+)"/', $html, $m);
        // Il tipo lo dichiariamo qui: sotto PHPStan `mixed` concatenato con una stringa
        // e' un errore, e la stringa vuota e' un fallback esplicito, non un silenzio.
        $target = isset($m[1]) && is_string($m[1]) ? $m[1] : '';
        Assert::assertNotSame('', $target, 'Il checkbox deve descrivere l\'informativa.');

        // Ogni id referenziato deve essere presente nel markup: un riferimento vuoto e'
        // un collegamento che non collega, e non dice nulla a chi usa uno screen reader.
        foreach (preg_split('/\s+/', trim($target)) ?: [] as $id) {
            if (! is_string($id) || $id === '') {
                continue;
            }
            Assert::assertStringContainsString('id="'.$id.'"', $html, 'aria-describedby punta a un id inesistente: '.$id);
        }
    });

    test('l\'errore di validazione esiste solo quando il server lo ha mandato', function () use ($render): void {
        // Senza errore dal server non si stampa nulla: un messaggio che compare da solo
        // quando non si ha spuntato la casella serve solo a dare tono, e il bottone e'
        // comunque disabilitato.
        $senza = $render();
        Assert::assertStringNotContainsString('role="alert"', $senza);

        // Con errore dal server, l'errore e' in `role="alert"`: un errore di validazione
        // che non viene annunciato non e' un errore per chi usa uno screen reader.
        $con = $render(['serverError' => 'Il consenso e\' obbligatorio.']);
        Assert::assertStringContainsString('role="alert"', $con);
        Assert::assertStringContainsString(
            'Il consenso e\' obbligatorio.',
            html_entity_decode($con, ENT_QUOTES | ENT_HTML5),
        );
    });

    test('lo stato non interattivo non ha bottone e il checkbox e\' disabilitato', function () use ($render): void {
        $html = $render(['disabled' => true, 'required' => false]);

        Assert::assertStringNotContainsString('<button', $html, 'Nel riepilogo non si continua: non c\'e\' un passo successivo.');
        Assert::assertMatchesRegularExpression('/<input[^>]*disabled/', $html);
    });

    test('nessuna chiave di traduzione resta greffa, in nessuna locale', function (): void {
        $rotte = ['it', 'en', 'de', 'es'];

        foreach ($rotte as $locale) {
            app()->setLocale($locale);
            $html = Blade::render('<x-pub_theme::consenso :data="$data" />', ['data' => []]);

            Assert::assertSame(
                0,
                preg_match_all('/pub_theme::consenso/', $html),
                'Chiave di traduzione non risolta in '.$locale,
            );
            // Il titolo dev'essere nella lingua della locale, non la stringa vuota.
            Assert::assertStringContainsString('class="title-xxlarge', $html, 'Titolo assente in '.$locale);
        }

        app()->setLocale('it');
    });
});
