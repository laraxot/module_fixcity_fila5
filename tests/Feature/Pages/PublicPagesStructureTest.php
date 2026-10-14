<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_match_all;

uses(TestCase::class);

/*
 * DCP-153: struttura minima di ogni pagina pubblica in ogni lingua: risponde 200, `<html lang>` uguale alla lingua, un solo <h1> e un
 * <title>, e nessuna chiave di traduzione grezza nel testo visibile. L'ultimo controllo nasce da un difetto reale: la cookie policy e
 * il banner mostravano `cookie-consent::texts.alert_ok` perche' il namespace delle traduzioni era sovrascritto da un pacchetto, e
 * nessun test lo vedeva (il fallback e' la chiave stessa, non un errore).
 */
it('serves every public page with a valid structure and no raw translation key', function (string $locale): void {
    $pages = ['', '/tickets', '/tickets/track', '/auth/login', '/auth/register', '/privacy', '/domande-frequenti', '/mappa-sito', '/accessibilita', '/note-legali', '/media-policy', '/cookie-policy'];
    $problems = [];
    $titles = [];

    foreach ($pages as $path) {
        $response = $this->get("/{$locale}{$path}");
        if ($response->getStatusCode() !== 200) {
            $problems[] = "/{$locale}{$path}: HTTP ".$response->getStatusCode();

            continue;
        }
        $document = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR);
        $root = $document->documentElement;
        if ($root?->getAttribute('lang') !== $locale) {
            $problems[] = "/{$locale}{$path}: <html lang> = '".$root?->getAttribute('lang')."'";
        }
        $h1 = $document->querySelectorAll('h1')->length;
        if ($h1 !== 1) {
            $problems[] = "/{$locale}{$path}: {$h1} elementi <h1>";
        }
        $titleElement = $document->querySelector('title');
        $title = $titleElement === null ? '' : trim((string) $titleElement->textContent);
        if ($title === '') {
            $problems[] = "/{$locale}{$path}: <title> vuoto";
        }
        $titles[$title][] = $path;

        $main = $document->querySelector('main') ?? $document->querySelector('body');
        if ($main === null) {
            $problems[] = "/{$locale}{$path}: nessun <main> ne' <body>";

            continue;
        }
        foreach ($main->querySelectorAll('script, style, svg') as $skip) {
            $skip->remove();
        }
        if (preg_match_all('/\b[a-z][a-z0-9_-]*::[a-z][a-z0-9_.-]+\b/u', (string) $main->textContent, $keys) > 0) {
            $problems[] = "/{$locale}{$path}: chiavi di traduzione visibili: ".implode(', ', array_unique($keys[0]));
        }
    }
    foreach ($titles as $title => $paths) {
        if ($title !== '' && count($paths) > 1) {
            $problems[] = "/{$locale}: lo stesso <title> '{$title}' su ".implode(', ', $paths);
        }
    }

    Assert::assertSame([], $problems);
})->with(['it', 'en', 'de', 'es']);
