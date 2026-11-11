<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;

/**
 * Anti-regressione: FO ticket = container0.*, mai tickets.show / localizeUrl.
 * Canon: docs/frontoffice-link-conventions.md
 */
test('i link ticket del front office usano le rotte Folio canoniche', function (): void {
    $root = dirname(__DIR__, 5);
    $paths = [
        $root.'/Themes/Sixteen/resources/views/components/ticket-card.blade.php',
        $root.'/Modules/Fixcity/resources/views/components/blocks/ticket_list/ticket_list.blade.php',
        $root.'/Modules/Fixcity/resources/views/components/blocks/ticket_list/map.blade.php',
        $root.'/Themes/Sixteen/resources/views/components/pages/faq.blade.php',
        $root.'/Themes/Sixteen/pages/comune/homepage.blade.php',
    ];

    foreach ($paths as $path) {
        if (! is_file($path)) {
            continue;
        }
        $content = file_get_contents($path);

        // rotta Folio scritta nel file o tramite l'helper tipizzato che la incapsula (Themes/Sixteen FrontofficeRoutes)
        Assert::assertMatchesRegularExpression("/route\\('container0\\.|FrontofficeRoutes::/", $content, $path);
        Assert::assertStringNotContainsString("localizeUrl('/tickets/", $content, $path);
        Assert::assertStringNotContainsString('localizeURL(', $content, $path);
        Assert::assertStringNotContainsString("route('tickets.show'", $content, $path);
        Assert::assertStringNotContainsString("route('tickets.create'", $content, $path);
        Assert::assertStringNotContainsString("route('tickets.track'", $content, $path);
        Assert::assertStringNotContainsString("route('fixcity.tickets.show'", $content, $path);
    }
});

test('actions e notification FO non usano tickets.track né localizeUrl', function (): void {
    $root = dirname(__DIR__, 5);
    // i link si costruiscono in un solo posto: le due Action URL, che usano la rotta Folio canonica
    foreach (['BuildTicketDetailUrlAction', 'BuildTicketTrackUrlAction'] as $action) {
        $path = $root.'/Modules/Fixcity/app/Actions/'.$action.'.php';
        Assert::assertStringContainsString("route('container0.view'", file_get_contents($path), $path);
    }

    $paths = [
        $root.'/Modules/Fixcity/app/Actions/BuildPersonalAreaNotificationsDataAction.php',
        $root.'/Modules/Fixcity/app/Notifications/TicketStatusChangedNotification.php',
    ];

    foreach ($paths as $path) {
        $content = file_get_contents($path);
        Assert::assertMatchesRegularExpression('/BuildTicket(Detail|Track)UrlAction::class/', $content, $path);
        Assert::assertStringNotContainsString("route('tickets.track'", $content, $path);
        Assert::assertStringNotContainsString('LaravelLocalization::', $content, $path);
    }
});
