<?php

declare(strict_types=1);

use Modules\Fixcity\Models\Ticket;

/**
 * Unit tests for Ticket::location() Attribute set/get logic.
 * Uses reflection to call private static helpers without DB.
 */
describe('Ticket location Attribute helpers', function (): void {
    beforeEach(function (): void {
        $this->callStatic = static function (string $method, mixed ...$args): mixed {
            $ref = new ReflectionMethod(Ticket::class, $method);
            $ref->setAccessible(true);

            return $ref->invoke(null, ...$args);
        };
    });

    describe('normalizeCoordinateString', function (): void {
        it('returns null for null input', function (): void {
            expect(($this->callStatic)('normalizeCoordinateString', null))->toBeNull();
        });

        it('converts float to string', function (): void {
            expect(($this->callStatic)('normalizeCoordinateString', 45.5622))->toBe('45.5622');
        });

        it('passes through numeric string', function (): void {
            expect(($this->callStatic)('normalizeCoordinateString', '12.249756'))->toBe('12.249756');
        });

        it('returns null for non-numeric string', function (): void {
            expect(($this->callStatic)('normalizeCoordinateString', 'not-a-number'))->toBeNull();
        });
    });

    describe('extractAddressComponents', function (): void {
        it('returns empty array when no addressdetails present', function (): void {
            $result = ($this->callStatic)('extractAddressComponents', ['lat' => '45.0', 'lng' => '12.0']);
            expect($result)->toBe([]);
        });

        it('maps Nominatim addressdetails keys to structured fields', function (): void {
            $result = ($this->callStatic)('extractAddressComponents', [
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

            expect($result['street'])->toBe('Via Rodolfo Morandi')
                ->and($result['street_number'])->toBe('5')
                ->and($result['zip'])->toBe('31021')
                ->and($result['city'])->toBe('Mogliano Veneto')
                ->and($result['state'])->toBe('Veneto')
                ->and($result['country'])->toBe('Italia')
                ->and($result['country_code'])->toBe('it');
        });

        it('falls back to town then village for city field', function (): void {
            $resultTown = ($this->callStatic)('extractAddressComponents', [
                'addressdetails' => ['town' => 'Mogliano Veneto'],
            ]);
            expect($resultTown['city'])->toBe('Mogliano Veneto');

            $resultVillage = ($this->callStatic)('extractAddressComponents', [
                'addressdetails' => ['village' => 'Trebaseleghe'],
            ]);
            expect($resultVillage['city'])->toBe('Trebaseleghe');
        });

        it('omits null / empty fields from result', function (): void {
            $result = ($this->callStatic)('extractAddressComponents', [
                'addressdetails' => ['road' => 'Via Roma', 'house_number' => ''],
            ]);

            expect($result)->toHaveKey('street')
                ->and($result)->not->toHaveKey('street_number');
        });
    });
});
