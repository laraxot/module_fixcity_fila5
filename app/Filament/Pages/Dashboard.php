<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Pages;

// use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'fixcity::filament.pages.dashboard';
}
