<?php

declare(strict_types=1);

use function Laravel\Folio\name;

name('services.categories');

?>

{{-- Canonical public category directory owned by the active Sixteen theme. --}}
<x-layouts.app :title="__('pub_theme::services.categories.title')">
    @include('pub_theme::pages.services.categories-content')
</x-layouts.app>
