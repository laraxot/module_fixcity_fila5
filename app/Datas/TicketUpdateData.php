<?php

declare(strict_types=1);

namespace Modules\Fixcity\Datas;

use Webmozart\Assert\Assert;

/**
 * Data Transfer Object for Ticket updates.
 *
 * Encapsulates all optional fields for updating a ticket.
 * Only non-null fields should be applied in the update operation.
 *
 * Properties:
 * - name: Ticket title/name
 * - content: Ticket description
 * - priority_id: Priority level
 * - status_id: Current status
 * - responsible_id: Assigned user ID
 * - type_id: Ticket type
 * - latitude: Geographic latitude
 * - longitude: Geographic longitude
 */
final class TicketUpdateData extends BaseData
{
    /**
     * Create a new TicketUpdateData instance.
     *
     * @param string|null $name Ticket title/name
     * @param string|null $content Ticket description
     * @param int|null $priority_id Priority level
     * @param int|null $status_id Current status
     * @param int|null $responsible_id Assigned user ID
     * @param int|null $type_id Ticket type
     * @param string|null $latitude Geographic latitude
     * @param string|null $longitude Geographic longitude
     * @param array<string, mixed>|null $metadata Additional metadata
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $content = null,
        public readonly ?int $priority_id = null,
        public readonly ?int $status_id = null,
        public readonly ?int $responsible_id = null,
        public readonly ?int $type_id = null,
        public readonly ?string $latitude = null,
        public readonly ?string $longitude = null,
        public readonly ?array $metadata = null,
    ) {
        $this->validate();
    }

    /**
     * Validate TicketUpdateData.
     *
     * @throws \Webmozart\Assert\InvalidArgumentException
     */
    public function validate(): void
    {
        if ($this->name !== null) {
            Assert::stringNotEmpty($this->name, 'Ticket name cannot be empty');
            Assert::minLength($this->name, 3, 'Ticket name must be at least 3 characters');
            Assert::maxLength($this->name, 255, 'Ticket name must not exceed 255 characters');
        }

        if ($this->priority_id !== null) {
            Assert::greaterThanEq($this->priority_id, 1, 'Priority ID must be positive');
        }

        if ($this->status_id !== null) {
            Assert::greaterThanEq($this->status_id, 1, 'Status ID must be positive');
        }

        if ($this->responsible_id !== null) {
            Assert::greaterThanEq($this->responsible_id, 1, 'Responsible ID must be positive');
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
     * Convert to array, filtering out null values.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'content' => $this->content,
            'priority_id' => $this->priority_id,
            'status_id' => $this->status_id,
            'responsible_id' => $this->responsible_id,
            'type_id' => $this->type_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'metadata' => $this->metadata,
        ], static fn ($value) => $value !== null);
    }

    /**
     * Check if any fields are set for update.
     */
    public function hasChanges(): bool
    {
        return ! empty($this->toArray());
    }
}
