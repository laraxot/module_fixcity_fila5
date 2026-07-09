<?php

declare(strict_types=1);

namespace Modules\Fixcity\Datas;

use Carbon\Carbon;
use Webmozart\Assert\Assert;

/**
 * Data Transfer Object for Ticket creation.
 *
 * Encapsulates all required and optional data for creating a new ticket.
 * Validates data in constructor to ensure consistency.
 *
 * Properties:
 * - name: Ticket title/name (required)
 * - content: Ticket description (optional)
 * - owner_id: User ID of ticket creator (required)
 * - priority_id: Priority enum value (optional, defaults to medium)
 * - type_id: Ticket type enum value (optional)
 * - latitude: Geographic latitude (optional, for geo-located tickets)
 * - longitude: Geographic longitude (optional)
 */
final class TicketCreateData extends BaseData
{
    /**
     * Create a new TicketCreateData instance.
     *
     * @param string $name Ticket title/name
     * @param int $owner_id Creator user ID
     * @param string|null $content Ticket description
     * @param int|null $priority_id Priority level
     * @param int|null $type_id Ticket type
     * @param string|null $latitude Geographic latitude
     * @param string|null $longitude Geographic longitude
     * @param array<string, mixed>|null $metadata Additional metadata
     */
    public function __construct(
        public readonly string $name,
        public readonly int $owner_id,
        public readonly ?string $content = null,
        public readonly ?int $priority_id = null,
        public readonly ?int $type_id = null,
        public readonly ?string $latitude = null,
        public readonly ?string $longitude = null,
        public readonly ?array $metadata = null,
    ) {
        $this->validate();
    }

    /**
     * Validate TicketCreateData.
     *
     * @throws \Webmozart\Assert\InvalidArgumentException
     */
    public function validate(): void
    {
        Assert::stringNotEmpty($this->name, 'Ticket name cannot be empty');
        Assert::minLength($this->name, 3, 'Ticket name must be at least 3 characters');
        Assert::maxLength($this->name, 255, 'Ticket name must not exceed 255 characters');

        Assert::greaterThan($this->owner_id, 0, 'Owner ID must be a positive integer');

        if ($this->priority_id !== null) {
            Assert::greaterThanEq($this->priority_id, 1, 'Priority ID must be positive');
        }

        if ($this->type_id !== null) {
            Assert::greaterThanEq($this->type_id, 1, 'Type ID must be positive');
        }

        if ($this->latitude !== null) {
            Assert::numeric($this->latitude, 'Latitude must be numeric');
            $lat = (float) $this->latitude;
            Assert::range($lat, -90, 90, 'Latitude must be between -90 and 90');
        }

        if ($this->longitude !== null) {
            Assert::numeric($this->longitude, 'Longitude must be numeric');
            $lon = (float) $this->longitude;
            Assert::range($lon, -180, 180, 'Longitude must be between -180 and 180');
        }
    }

    /**
     * Convert to array suitable for model creation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'content' => $this->content,
            'owner_id' => $this->owner_id,
            'priority_id' => $this->priority_id,
            'type_id' => $this->type_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'metadata' => $this->metadata,
        ], static fn ($value) => $value !== null);
    }
}
