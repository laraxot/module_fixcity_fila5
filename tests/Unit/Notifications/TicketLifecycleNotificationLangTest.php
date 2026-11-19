<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;

use function Safe\preg_match_all;

/*
 * DCP-284: la lingua di fallback e' `it` (config/app.php), quindi una chiave mancante in en/de/es non da' errore ma mostra
 * l'italiano. Questo test legge i file direttamente (senza traduttore, che potrebbe scrivere chiavi mancanti) e pretende le
 * stesse chiavi e gli stessi segnaposto in tutte e quattro le lingue.
 */

/**
 * @return array<string, string>
 */
function dcp284LangFile(string $locale): array
{
    $path = dirname(__DIR__, 3).'/lang/'.$locale.'/ticket_notification.php';
    expect(is_file($path))->toBeTrue("manca $path");

    /** @var array<string, mixed> $translations */
    $translations = require $path;

    $flat = [];
    foreach (Arr::dot($translations) as $key => $value) {
        $flat[(string) $key] = is_string($value) ? $value : '';
    }

    return $flat;
}

/**
 * @return list<string>
 */
function dcp284Placeholders(string $text): array
{
    preg_match_all('/:[a-z_]+/', $text, $matches);
    $found = array_unique($matches[0]);
    sort($found);

    return $found;
}

it('has the same keys in it, en, de and es, none empty', function (): void {
    $italian = dcp284LangFile('it');

    foreach (['en', 'de', 'es'] as $locale) {
        $other = dcp284LangFile($locale);

        expect(array_keys($other))->toEqualCanonicalizing(array_keys($italian), "chiavi diverse in $locale");
        foreach ($other as $key => $text) {
            expect(trim($text))->not->toBe('', "valore vuoto: $locale $key");
        }
    }
});

it('uses the same placeholders in every language', function (): void {
    $italian = dcp284LangFile('it');

    foreach (['en', 'de', 'es'] as $locale) {
        foreach (dcp284LangFile($locale) as $key => $text) {
            expect(dcp284Placeholders($text))->toBe(dcp284Placeholders($italian[$key] ?? ''), "segnaposto diversi: $locale $key");
        }
    }
});

it('has a title, a body and a short feed label for every lifecycle event', function (): void {
    foreach (['it', 'en', 'de', 'es'] as $locale) {
        $texts = dcp284LangFile($locale);

        foreach (TicketLifecycleEventEnum::cases() as $event) {
            expect($texts)->toHaveKeys([
                'lifecycle.'.$event->value.'.title',
                'lifecycle.'.$event->value.'.body',
                'event.'.$event->value,
            ]);
        }
        expect($texts)->toHaveKeys(['lifecycle.rejected.body_with_reason', 'staff.received.title', 'staff.received.body', 'view_ticket']);
    }
});
