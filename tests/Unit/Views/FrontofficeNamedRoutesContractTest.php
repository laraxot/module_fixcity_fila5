<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;

use function Safe\file_get_contents;

/**
 * Anti-regressione: Blade FO usano route Folio / FrontofficeUrl, mai localizeUrl.
 * Canon: docs/frontoffice-link-conventions.md
 */
test('front office links use named Folio routes or the CMS URL normalizer', function (): void {
    $laravelRoot = dirname(__DIR__, 5);
    $routeContracts = [
        'Themes/Sixteen/resources/views/components/blocks/personal-area-settings/breadcrumb.blade.php' => "route('home')",
        'Themes/Sixteen/resources/views/components/blocks/personal-area-settings/notifications.blade.php' => 'FrontofficeRoutes::personalAreaImpostazioni',
        'Themes/Sixteen/resources/views/components/blocks/personal-area-settings/settings.blade.php' => 'FrontofficeRoutes::personalAreaNotifiche',
        'Themes/Sixteen/resources/views/components/blocks/personal-area-settings/notifications-list.blade.php' => 'FrontofficeRoutes::personalAreaPratiche',
        'Themes/Sixteen/resources/views/components/sections/header/partials/user-dropdown.blade.php' => 'FrontofficeRoutes::personalAreaPratiche',
        'Modules/User/resources/views/pages/profile/edit.blade.php' => "name('profile.edit')",
        'Themes/Sixteen/resources/views/pages/auth/logout.blade.php' => 'FrontofficeRoutes::personalAreaImpostazioni',
        'Modules/Fixcity/resources/views/components/blocks/ticket_list/ticket_list.blade.php' => "route('container0.",
        'Modules/User/resources/views/filament/widgets/user-dropdown.blade.php' => "route('profile.edit')",
    ];

    foreach ($routeContracts as $relativePath => $expected) {
        $full = $laravelRoot.'/'.$relativePath;
        if (! is_file($full)) {
            continue;
        }
        $content = file_get_contents($full);

        Assert::assertStringContainsString($expected, $content, $relativePath);
        Assert::assertStringNotContainsString('LaravelLocalization::localizeUrl', $content, $relativePath);
        Assert::assertStringNotContainsString('LaravelLocalization::localizeURL', $content, $relativePath);
        Assert::assertStringNotContainsString('localizeURL(', $content, $relativePath);
    }

    $searchModal = file_get_contents($laravelRoot.'/Themes/Sixteen/resources/views/components/sections/search-modal/design-comuni.blade.php');
    Assert::assertStringContainsString('FrontofficeUrl::fromStoredUrl', $searchModal);
    Assert::assertStringNotContainsString('LaravelLocalization::localizeUrl', $searchModal);
    Assert::assertStringNotContainsString('LaravelLocalization::localizeURL', $searchModal);
});

test('area personale riusa una navigazione tab comune su tutte le pagine', function (): void {
    $laravelRoot = dirname(__DIR__, 5);
    $themeRoot = $laravelRoot.'/Themes/Sixteen';
    $navigation = file_get_contents($themeRoot.'/resources/views/components/blocks/personal-area/navigation.blade.php');
    $heading = file_get_contents($themeRoot.'/resources/views/components/blocks/personal-area/heading.blade.php');

    expect($navigation)
        ->toContain("'practices'")
        ->toContain("'followed'")
        ->toContain("'notifications'")
        ->toContain("'settings'");

    expect($heading)->toContain('components.blocks.personal-area.navigation');

    foreach ([
        'resources/views/components/blocks/personal-area-settings/notifications.blade.php',
        'resources/views/components/blocks/personal-area-settings/settings.blade.php',
        'resources/views/components/blocks/personal-area-practices/heading.blade.php',
    ] as $relative) {
        expect(file_get_contents($themeRoot.'/'.$relative))
            ->toContain('components.blocks.personal-area.heading');
    }
});
