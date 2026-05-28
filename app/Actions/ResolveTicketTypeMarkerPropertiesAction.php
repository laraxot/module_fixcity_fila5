<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Str;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Xot\Actions\File\AssetAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Costruisce l'oggetto {@code properties.type} per GeoJSON ticket (mappa elenco).
 *
 * @phpstan-type TicketTypeGeoJson array{value: string, label: string, color: string, icon: string, iconUrl: string|null}
 */
class ResolveTicketTypeMarkerPropertiesAction
{
    use QueueableAction;

    /**
     * @return TicketTypeGeoJson
     */
    public function execute(TicketTypeEnum $typeEnum): array
    {
        $typeIcon = (string) $typeEnum->getIcon();
        $typeColor = (string) $typeEnum->getColor();

        return [
            'value' => $typeEnum->value,
            'label' => $typeEnum->getLabel(),
            'color' => $typeColor !== '' ? $typeColor : '#607d8b',
            'icon' => $typeIcon,
            'iconUrl' => $this->resolveIconUrl($typeIcon),
        ];
    }

    /**
     * @return TicketTypeGeoJson
     */
    public function executeFromValue(string $typeValue): array
    {
        try {
            return $this->execute(TicketTypeEnum::from($typeValue));
        } catch (\ValueError) {
            return [
                'value' => $typeValue,
                'label' => $typeValue,
                'color' => '#607d8b',
                'icon' => '',
                'iconUrl' => null,
            ];
        }
    }

    private function resolveIconUrl(string $icon): ?string
    {
        $icon = trim($icon);
        if ($icon === '') {
            return null;
        }

        if (str_starts_with($icon, 'heroicon-o-')) {
            return $this->tryAsset('ui::svg/'.Str::after($icon, 'heroicon-o-').'.svg');
        }

        if (str_starts_with($icon, 'fas-')) {
            $slug = Str::after($icon, 'fas-');

            return $this->tryAsset('ui::svg/brands/'.$slug.'.svg')
                ?? $this->tryAsset('ui::svg/'.$slug.'.svg');
        }

        return null;
    }

    private function tryAsset(string $path): ?string
    {
        try {
            return app(AssetAction::class)->execute($path);
        } catch (\Exception) {
            return null;
        }
    }
}
