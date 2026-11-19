<?php

declare(strict_types=1);

namespace Modules\Fixcity\QueryBuilders;

use Illuminate\Database\Eloquent\Collection;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\QueryBuilders\BaseQueryBuilder;

/**
 * Query Builder for Ticket model.
 *
 * Provides a fluent interface for building complex ticket queries.
 * Encapsulates query logic and makes it more readable and testable.
 *
 * Example usage:
 *     $tickets = (new TicketQueryBuilder())
 *         ->whereStatus('open')
 *         ->wherePriority('high')
 *         ->orderByCreatedAt('desc')
 *         ->with(['owner', 'responsible'])
 *         ->paginate(15);
 *
 * @extends BaseQueryBuilder<Ticket>
 */
class TicketQueryBuilder extends BaseQueryBuilder
{
    /**
     * Get the model class this query builder is for.
     *
     * @return class-string<Ticket>
     */
    protected function getModel(): string
    {
        return Ticket::class;
    }

    /**
     * Filter by ticket status.
     */
    public function whereStatus(string|int $status): static
    {
        return $this->where('status_id', $status);
    }

    /**
     * Filter by ticket priority.
     */
    public function wherePriority(int $priority): static
    {
        return $this->where('priority_id', $priority);
    }

    /**
     * Filter by ticket type.
     */
    public function whereType(int $type): static
    {
        return $this->where('type_id', $type);
    }

    /**
     * Filter by ticket owner.
     */
    public function whereOwner(int $userId): static
    {
        return $this->where('owner_id', $userId);
    }

    /**
     * Filter by assigned user.
     */
    public function whereResponsible(int $userId): static
    {
        return $this->where('responsible_id', $userId);
    }

    /**
     * Get tickets grouped by status.
     *
     * @return array<string|int, Collection<int, Ticket>>
     */
    public function groupByStatus(): array
    {
        return $this->get()->groupBy('status_id')->all();
    }

    /**
     * Get tickets grouped by priority.
     *
     * @return array<string|int, Collection<int, Ticket>>
     */
    public function groupByPriority(): array
    {
        return $this->get()->groupBy('priority_id')->all();
    }

    /**
     * Filter active (non-deleted) tickets.
     */
    public function whereActive(): static
    {
        return $this->whereNull('deleted_at');
    }

    /**
     * Filter deleted tickets.
     */
    public function onlyTrashed(): static
    {
        $this->query = $this->query->onlyTrashed();

        return $this;
    }

    /**
     * Include deleted tickets.
     */
    public function withTrashed(): static
    {
        $this->query = $this->query->withTrashed();

        return $this;
    }

    /**
     * Order by creation date ascending.
     */
    public function orderByCreatedAt(string $direction = 'asc'): static
    {
        return $this->orderBy('created_at', $direction);
    }

    /**
     * Order by update date ascending.
     */
    public function orderByUpdatedAt(string $direction = 'asc'): static
    {
        return $this->orderBy('updated_at', $direction);
    }

    /**
     * Order by priority.
     */
    public function orderByPriority(string $direction = 'desc'): static
    {
        return $this->orderBy('priority_id', $direction);
    }

    /**
     * Search by name and content.
     */
    public function search(string $query): static
    {
        $this->query = $this->query->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%");
        });

        return $this;
    }

    /**
     * Eager load common relationships.
     */
    public function withRelations(): static
    {
        return $this->with(['owner', 'responsible', 'comments', 'media']);
    }
}
