<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Utilities\Get;
use Modules\Fixcity\Enums\TicketTypeEnum;

/**
 * Entry Filament {@see TextEntry} per lo step wizard di riepilogo (readonly).
 *
 * Separato da {@see TicketForm} per limitare coupling PHPMD e mantenere DRY su formattazioni enum/geo.
 *
 * Doc ufficiale: https://filamentphp.com/docs/5.x/infolists/overview
 */
final class TicketFormReviewInfolist
{
    /**
     * Blocco riepilogo segnalazione (luogo, tipo, testi, allegati sintetici).
     *
     * @return array<string, SchemaComponent>
     */
    public static function summarySectionEntries(): array
    {
        return [
            'review_location' => TextEntry::make('review_location')
                ->columnSpanFull()
                ->state(static fn (Get $get): string => static::formatLocationReviewState($get)),
            'review_type' => TextEntry::make('review_type')
                ->badge()
                ->state(static fn (Get $get): string => static::formatTicketTypeDisplay(static::coerceTicketTypeValue($get('type')))),
            'review_name' => TextEntry::make('review_name')
                ->columnSpanFull()
                ->state(static fn (Get $get): string => (string) ($get('name') ?? '')),
            'review_content' => TextEntry::make('review_content')
                ->columnSpanFull()
                ->prose()
                ->state(static fn (Get $get): string => (string) ($get('content') ?? '')),
            'review_images' => TextEntry::make('review_images')
                ->columnSpanFull()
                ->state(static function (Get $get): string {
                    $images = $get('images');

                    return is_array($images) ? static::formatImagesReviewSummaryState($images) : '';
                }),
        ];
    }

    /**
     * Normalizza campo `type` da {@see Get} verso formato accettato da {@see self::formatTicketTypeDisplay()}.
     *
     * @param  mixed  $raw
     */
    private static function coerceTicketTypeValue($raw): TicketTypeEnum|string|null
    {
        if ($raw instanceof TicketTypeEnum) {
            return $raw;
        }

        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_string($raw)) {
            return $raw;
        }

        return is_scalar($raw) ? (string) $raw : null;
    }

    protected static function formatTicketTypeDisplay(TicketTypeEnum|string|null $raw): string
    {
        if ($raw instanceof TicketTypeEnum) {
            return $raw->getLabel();
        }

        if ($raw === null || $raw === '') {
            return '';
        }

        $enum = TicketTypeEnum::tryFrom($raw);

        return $enum?->getLabel() ?? $raw;
    }

    protected static function formatLocationReviewState(Get $get): string
    {
        /** @var mixed $location */
        $location = $get('location');
        if (is_array($location)) {
            return static::summarizeLocationArray($location);
        }

        $address = $get('location.address');

        return is_string($address) && $address !== '' ? $address : '';
    }

    /**
     * @param  array<mixed>  $location
     */
    protected static function summarizeLocationArray(array $location): string
    {
        $parts = [];

        if (isset($location['address']) && is_string($location['address']) && $location['address'] !== '') {
            $parts[] = $location['address'];
        }

        $lat = $location['latitude'] ?? $location['lat'] ?? null;
        $lng = $location['longitude'] ?? $location['lng'] ?? null;

        if ($lat !== null && $lat !== '' && $lng !== null && $lng !== '') {
            $parts[] = (string) $lat.', '.(string) $lng;
        }

        return $parts === [] ? '' : implode(' · ', $parts);
    }

    /**
     * @param  array<mixed>  $images
     */
    protected static function formatImagesReviewSummaryState(array $images): string
    {
        $count = count($images);

        if ($count === 0) {
            return (string) __('fixcity::ticket_form.summaries.images_none');
        }

        return (string) trans_choice('fixcity::ticket_form.summaries.images_choice', $count, ['count' => $count]);
    }
}
