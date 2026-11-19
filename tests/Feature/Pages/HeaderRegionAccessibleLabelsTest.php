<?php

declare(strict_types=1);

use Dom\HTMLDocument;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-198 A10: title e aria-label del link regione nel header slim non devono contenere graffe letterali del template CMS.
 */
it('does not expose brace placeholders in the region portal link accessible names', function (string $locale): void {
    $response = $this->get("/{$locale}")->assertOk();

    $document = HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR);
    $regionBrand = $document->querySelector('.it-header-slim-wrapper-content a.navbar-brand, .it-header-slim-wrapper-content span.navbar-brand');
    Assert::assertNotNull($regionBrand, "Voce regione assente nell'header slim ({$locale}).");

    $visibleName = trim((string) $regionBrand->textContent);
    Assert::assertNotSame('', $visibleName, "Nome regione vuoto ({$locale}).");
    Assert::assertStringNotContainsString('{', $visibleName, "Graffa letterale nel nome regione ({$locale}): {$visibleName}");
    Assert::assertStringNotContainsString('}', $visibleName, "Graffa letterale nel nome regione ({$locale}): {$visibleName}");

    if ($regionBrand->nodeName !== 'a') {
        return;
    }

    foreach (['aria-label', 'title'] as $attribute) {
        if (! $regionBrand->hasAttribute($attribute)) {
            continue;
        }

        $value = (string) $regionBrand->getAttribute($attribute);
        Assert::assertStringNotContainsString('{', $value, "Graffa letterale in {$attribute} ({$locale}): {$value}");
        Assert::assertStringNotContainsString('}', $value, "Graffa letterale in {$attribute} ({$locale}): {$value}");
    }
})->with(['it', 'en', 'de', 'es']);
