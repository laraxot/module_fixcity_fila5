<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * DCP-291, FO-5-05. La barra dei cookie e' fissa in basso (come il cookiebar di Bootstrap Italia) e prima copriva il campo con il focus
 * (WCAG 2.4.11), riempiva il 100% dello schermo a 400% di zoom e finiva nella stampa. Sono test sul markup reso: la prova del
 * comportamento e' la misura nel browser (copertura del focus su 40 Tab, altezza a 320x256, display in stampa, focus dopo "Ho capito").
 * Il testo e la logica del consenso non cambiano: vedi CookieBannerPagesTest.
 */
function renderCookieBar(): string
{
    app()->setLocale('it');

    return view('cookie-consent::index')->render();
}

it('scrolls the focused element above the fixed bar instead of leaving it underneath', function (): void {
    $html = renderCookieBar();

    // CSS iniziale (prima che giri lo script) e valore esatto aggiornato dallo script, come per il padding del body
    Assert::assertMatchesRegularExpression('/html:has\(\.gdpr-cookiebar:not\(\[hidden\]\)\)\s*\{[^}]*scroll-padding-bottom:/', $html);
    Assert::assertStringContainsString('scrollPaddingBottom', $html);
});

it('keeps the bar out of the printed page', function (): void {
    Assert::assertMatchesRegularExpression('/@media print\s*\{\s*\.gdpr-cookiebar[^{]*\{[^}]*display:\s*none/', renderCookieBar());
});

it('puts the bar in the page flow when the viewport is too short to share with it', function (): void {
    $html = renderCookieBar();

    // 400% di zoom su 1280x1024 = 320x256: la barra alta 255 px copriva tutto lo schermo
    Assert::assertMatchesRegularExpression('/@media \(max-height: 30rem\)\s*\{[^@]*\.gdpr-cookiebar\s*\{[^}]*position:\s*static/', $html);
    Assert::assertMatchesRegularExpression('/@media \(max-height: 30rem\)\s*\{[^@]*body:has\(\.gdpr-cookiebar:not\(\[hidden\]\)\)\s*\{[^}]*padding-bottom:\s*0/', $html);
    // lo script non deve rimettere un padding inline quando la barra non e' fissa
    Assert::assertStringContainsString("getComputedStyle(bar).position === 'fixed'", $html);
});

it('moves the focus to the next stop when the bar disappears under it', function (): void {
    $html = renderCookieBar();

    Assert::assertStringContainsString('const leave = ', $html);
    Assert::assertStringContainsString("document.querySelector('.skiplink a')", $html);
    // il consenso si scrive prima: il ritorno del focus non cambia cosa viene salvato
    Assert::assertMatchesRegularExpression('/document\.dispatchEvent\(new CustomEvent\(\'cookie-consent:changed\'.*?leave\(hadFocus\)/s', $html);
});
