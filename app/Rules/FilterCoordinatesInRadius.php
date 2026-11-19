<?php

declare(strict_types=1);

namespace Modules\Fixcity\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Fixcity\Models\Ticket;
use Modules\Geo\Actions\FilterCoordinatesInRadiusAction as CoordinatesFilter;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Webmozart\Assert\Assert;

class FilterCoordinatesInRadius implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        Assert::isArray($value);
        Assert::keyExists($value, 'lat');
        Assert::keyExists($value, 'lng');
        Assert::numeric($value['lat']);
        Assert::numeric($value['lng']);

        $latitude = (float) $value['lat'];
        $longitude = (float) $value['lng'];

        $rawCoordinates = Ticket::where('latitude', '!=', null)
            ->where('longitude', '!=', null)
            ->select('id', 'latitude', 'longitude')
            ->get()
            ->toArray();

        /** @var array<array{latitude: string, longitude: string}> $coordinatesArray */
        $coordinatesArray = array_map(
            static fn (mixed $row): array => [
                'latitude' => SafeStringCastAction::cast(is_array($row) ? ($row['latitude'] ?? '0') : '0'),
                'longitude' => SafeStringCastAction::cast(is_array($row) ? ($row['longitude'] ?? '0') : '0'),
            ],
            $rawCoordinates
        );

        $ticket_vicini = app(CoordinatesFilter::class)->execute($latitude, $longitude, $coordinatesArray, 1);

        // dddx([$value['lat'], $value['lng'], $coordinatesArray, $ticket_vicini]);
        if (count($ticket_vicini) > 0) {
            $fail('Ci sono già '.(string) count($ticket_vicini).' ticket in questa posizione');
        }
    }
}
