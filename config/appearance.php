<?php

declare(strict_types=1);

return [
    'appearance' => [
        'background' => NULL,
        'background_color' => '#b31c1c',
        'overlay_color' => NULL,
        'overlay_opacity' => NULL,
    ],
    'breadcrumb' => [
        'class' => NULL,
        'style' => NULL,
    ],
    'footer' => [
        '_tpl' => NULL,
        'background' => 'bg-light',
        'background_color' => '#ffffff',
        'overlay_color' => '#000000',
        'view' => 'cms::components.footer',
    ],
    'headernav' => [
        '_tpl' => 'simple',
        'background' => NULL,
        'background_color' => '#43bd39',
        'class' => NULL,
        'overlay_color' => NULL,
        'style' => NULL,
        'view' => 'fixcity::components.blocks.headernav.agid',
    ],
];
