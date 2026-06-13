<?php

/**
 * Pest DSL helper for type narrowing in Laravel tests.
 *
 * @param  class-string $class
 * @return class-string
 */
function safe_instance(string $class): string
{
    return $class;
}

/**
 * @param  mixed $value
 * @param  class-string $class
 */
function is_null_safe(mixed $value, string $class): bool
{
    return $value !== null && $value instanceof $class;
}

/**
 * @param  mixed $value
 * @param  class-string $class
 */
function assert_non_null(mixed $value, string $class): void
{
    if (! is_null_safe($value, $class)) {
        throw new InvalidArgumentException(
            "Expected {$class} instance, got null"
        );
    }
}
