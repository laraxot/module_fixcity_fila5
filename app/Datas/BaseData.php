<?php

declare(strict_types=1);

namespace Modules\Fixcity\Datas;

use Modules\Xot\Contracts\DataContract;

/**
 * Base Data Transfer Object for Fixcity module.
 *
 * Provides common functionality for all Fixcity DTOs.
 * Extend this class for concrete DTO implementations.
 *
 * Features:
 * - Immutability via readonly properties
 * - Validation in constructor
 * - Type safety
 * - Easy conversion to arrays
 */
abstract class BaseData implements DataContract
{
    /**
     * Validate the DTO data.
     *
     * Implemented by subclasses to validate their specific properties.
     * Called automatically in constructor.
     *
     * @throws \InvalidArgumentException
     * @throws \Webmozart\Assert\InvalidArgumentException
     */
    abstract public function validate(): void;

    /**
     * Convert the DTO to an array.
     *
     * Default implementation uses reflection to extract public properties.
     * Override for custom serialization.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflect = new \ReflectionClass($this);
        $array = [];

        foreach ($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            $array[$name] = $this->$name;
        }

        return $array;
    }

    /**
     * Create DTO instance from array.
     *
     * Override in subclasses for custom instantiation logic.
     *
     * @param array<string, mixed> $data
     */
    public static function from(array $data): static
    {
        /** @var class-string<static> $className */
        $className = static::class;

        return new $className(...$data);
    }
}
