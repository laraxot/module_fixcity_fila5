<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\File;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Xot\Actions\File\AssetAction;
use Modules\Xot\Actions\File\GetModulePathAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Oggetto {@code properties.type} per GeoJSON — una sola icona per tipologia.
 *
 * Sorgente canonica: `laravel/Modules/Fixcity/resources/svg/`
 * Riferimento: `fixcity::svg/{nome-file}.svg` via {@see AssetAction}.
 *
 * @phpstan-type TicketTypeGeoJson array{value: string, label: string, iconUrl: string}
 */
class ResolveTicketTypeMarkerPropertiesAction
{
    use QueueableAction;

    /**
     * @return TicketTypeGeoJson
     */
    public function execute(TicketTypeEnum $typeEnum): array
    {
        return [
            'value' => $typeEnum->value,
            'label' => $typeEnum->getLabel(),
            'iconUrl' => $this->resolveTypeIconUrl($typeEnum),
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
                'iconUrl' => app(AssetAction::class)->execute('fixcity::svg/other.svg'),
            ];
        }
    }

    private function resolveTypeIconUrl(TicketTypeEnum $typeEnum): string
    {
        $filename = $this->canonicalTypeSvgFilename($typeEnum);

        if ($this->moduleSvgExists($filename)) {
            return app(AssetAction::class)->execute('fixcity::svg/'.$filename);
        }

        return app(AssetAction::class)->execute('fixcity::svg/other.svg');
    }

    /**
     * Una sola icona per enum: {value-kebab}.svg in resources/svg/.
     * Il nome del modulo è autoregistrato come prefisso (fixcity::svg/...).
     * Vietato fallback heroicon → trash.svg / light-bulb.svg (seconda icona diversa).
     */
    private function canonicalTypeSvgFilename(TicketTypeEnum $typeEnum): string
    {
        return str_replace('_', '-', $typeEnum->value).'.svg';
    }

    private function moduleSvgExists(string $filename): bool
    {
        $modulePath = app(GetModulePathAction::class)->execute('fixcity');
        $modulePath = rtrim($modulePath, '/');

        return File::exists($modulePath.'/resources/svg/'.$filename);
    }
}
