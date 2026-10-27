<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-55 difetto 2: oltre ai campi di `ticket_form`, il wizard tickets/create usa altre chiavi che in de ed es non esistevano e
 * cadevano sull'italiano ("Avanti", "Indietro", etichette dei passi, riepilogo, controlli della mappa). L'elenco viene da una
 * prova con lingua di ripiego vuota (Lang::handleMissingKeysUsing) sulle tre schermate del wizard.
 */
dataset('wizard chrome keys', [
    'fixcity::fixcity.ticket.steps.auth.label',
    'fixcity::fixcity.ticket.steps.data.label',
    'fixcity::fixcity.ticket.steps.summary.label',
    'fixcity::ticket.privacy.detail_prefix.text',
    'fixcity::ticket.privacy.link.label',
    'fixcity::ticket.steps.active.label',
    'fixcity::ticket.steps.confirmed.label',
    'fixcity::ticket_review.author.card_title',
    'fixcity::ticket_review.author.group_title',
    'fixcity::ticket_review.contacts.fields.email',
    'fixcity::ticket_review.contacts.title',
    'fixcity::ticket_review.report.card_title',
    'fixcity::ticket_review.report.edit_label',
    'fixcity::ticket_review.report.map_label',
    'fixcity::ticket_review.report.title',
    'fixcity::ticket_review.report.fields.address',
    'fixcity::ticket_review.report.fields.type',
    'fixcity::ticket_review.report.fields.name',
    'fixcity::ticket_review.report.fields.content',
    'fixcity::ticket_review.report.fields.images',
    'fixcity::ticket_review.contacts.fields.phone',
    'fixcity::ticket_wizard.actions.next.label',
    'fixcity::ticket_wizard.actions.next.tooltip',
    'fixcity::ticket_wizard.actions.previous.label',
    'fixcity::ticket_wizard.actions.previous.tooltip',
    'geo::coordinate-picker.address',
    'geo::coordinate-picker.close_fullscreen',
    'geo::coordinate-picker.close_search',
    'geo::coordinate-picker.fullscreen',
    'geo::coordinate-picker.latitude',
    'geo::coordinate-picker.locating',
    'geo::coordinate-picker.longitude',
    'geo::coordinate-picker.search',
    'geo::coordinate-picker.search_placeholder',
    'geo::coordinate-picker.switch_layer',
    'geo::coordinate-picker.use_my_location',
    'geo::coordinate-picker.zoom_in',
    'geo::coordinate-picker.zoom_out',
]);

it('has the wizard chrome text in German and Spanish, not only through the Italian fallback', function (string $key): void {
    foreach (['de', 'es'] as $locale) {
        Assert::assertTrue(app('translator')->hasForLocale($key, $locale), "Manca {$key} in {$locale}.");
        Assert::assertNotSame(app('translator')->get($key, [], 'it', false), app('translator')->get($key, [], $locale, false), "{$key} in {$locale} e' ancora il testo italiano.");
    }
})->with('wizard chrome keys');
