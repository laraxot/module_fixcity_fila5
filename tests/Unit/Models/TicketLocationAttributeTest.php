<?php

declare(strict_types=1);

use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;
use ReflectionMethod;

uses(TestCase::class);

describe('Ticket location Attribute helpers', function (): void {
    beforeEach(function (): void {
        /** @var TestCase $this */
        $this->callStatic = static function (string $method, mixed ...$args): mixed {
            $ref = new ReflectionMethod(Ticket::class, $method);
            $ref->setAccessible(true);

            return $ref->invoke(null, ...$args);
        };
    });

    it('normalizes coordinate strings', function (): void {
        /** @var TestCase $this */
        Assert::assertNotNull($this->callStatic);
        $callStatic = $this->callStatic;

        Assert::assertNull($callStatic('normalizeCoordinateString', null));
        Assert::assertSame('45.5622', $callStatic('normalizeCoordinateString', 45.5622));
        Assert::assertSame('12.249756', $callStatic('normalizeCoordinateString', '12.249756'));
        Assert::assertNull($callStatic('normalizeCoordinateString', 'not-a-number'));
    });

    it('extracts address components from nominatim payload', function (): void {
        /** @var TestCase $this */
        Assert::assertNotNull($this->callStatic);
        $callStatic = $this->callStatic;

        $empty = $callStatic('extractAddressComponents', ['lat' => '45.0', 'lng' => '12.0']);
        Assert::assertSame([], $empty);

        $result = $callStatic('extractAddressComponents', [
            'addressdetails' => [
                'road' => 'Via Rodolfo Morandi',
                'house_number' => '5',
                'postcode' => '31021',
                'city' => 'Mogliano Veneto',
                'state' => 'Veneto',
                'country' => 'Italia',
                'country_code' => 'it',
            ],
        ]);

        /** @var array<string, string> $result */
        Assert::assertSame('Via Rodolfo Morandi', $result['street']);
        Assert::assertSame('5', $result['street_number']);
        Assert::assertSame('31021', $result['zip']);
        Assert::assertSame('Mogliano Veneto', $result['city']);
        Assert::assertSame('Veneto', $result['state']);
        Assert::assertSame('Italia', $result['country']);
        Assert::assertSame('it', $result['country_code']);
    });

    it('falls back to town or village for city', function (): void {
        /** @var TestCase $this */
        Assert::assertNotNull($this->callStatic);
        $callStatic = $this->callStatic;

        $resultTown = $callStatic('extractAddressComponents', [
            'addressdetails' => ['town' => 'Mogliano Veneto'],
        ]);
        /** @var array<string, string> $resultTown */
        Assert::assertSame('Mogliano Veneto', $resultTown['city']);

        $resultVillage = $callStatic('extractAddressComponents', [
            'addressdetails' => ['village' => 'Trebaseleghe'],
        ]);
        /** @var array<string, string> $resultVillage */
        Assert::assertSame('Trebaseleghe', $resultVillage['city']);
    });
});
