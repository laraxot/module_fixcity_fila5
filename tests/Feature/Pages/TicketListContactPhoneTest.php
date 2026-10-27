<?php

declare(strict_types=1);

use Modules\Cms\Datas\BlockData;
use Modules\Fixcity\Actions\ResolveFooterContactPhoneAction;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

use function Safe\preg_replace;

uses(TestCase::class);

/*
 * La fascia contatti di /{lang}/tickets mostrava `tel:050505` e «05 0505», numeri finti. Il telefono viene ora dal footer del
 * tenant (sections/footer.json, `contact.phone`), come nel footer; senza telefono il link non c'e'.
 */
it('reads the tenant phone from the footer blocks', function (): void {
    $blocks = [
        new BlockData('footer', ['contact' => ['phone' => '  +39 050 123456 ']]),
    ];

    Assert::assertSame('+39 050 123456', app(ResolveFooterContactPhoneAction::class)->fromBlocks($blocks));
    Assert::assertSame('', app(ResolveFooterContactPhoneAction::class)->fromBlocks([
        new BlockData('footer', ['contact' => ['phone' => '']]),
    ]));
    Assert::assertSame('', app(ResolveFooterContactPhoneAction::class)->fromBlocks([]));
});

it('shows no fake phone on the ticket list', function (): void {
    /** @var TestCase $this */
    $phone = app(ResolveFooterContactPhoneAction::class)->execute();
    $html = $this->get('/it/tickets')->assertOk()->getContent();
    Assert::assertIsString($html);

    Assert::assertStringNotContainsString('tel:050505', $html);
    Assert::assertStringNotContainsString('05 0505', $html);
    if ($phone === '') {
        Assert::assertStringNotContainsString('href="tel:', $html);
    } else {
        Assert::assertStringContainsString('href="tel:'.preg_replace('/[^0-9+]/', '', $phone).'"', $html);
    }
});
