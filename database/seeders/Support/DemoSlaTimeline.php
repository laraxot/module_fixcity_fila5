<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders\Support;

use Faker\Generator;
use Illuminate\Support\Carbon;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;

/**
 * Costruzione delle timeline SLA della demo: per ogni segnalazione genera il
 * percorso degli stati (legale secondo TicketStatusEnum::allowedTransitions()),
 * gli orari di presa in carico / risoluzione e l'eventuale superamento del SLA.
 *
 * @phpstan-type SlaPayload array{
 *     acknowledge_due_at: string,
 *     resolve_due_at: string,
 *     acknowledged_at: string|null,
 *     resolved_at: string|null,
 *     resolve_breached: bool,
 *     breached: bool,
 *     breach_minutes: int,
 *     priority: string
 * }
 * @phpstan-type Milestone array{status: TicketStatusEnum, at: Carbon, payload: array<string, mixed>}
 */
final class DemoSlaTimeline
{
    /**
     * Ore per la presa in carico e per la risoluzione, per priorità.
     *
     * @var array<string, array{acknowledge: int, resolve: int}>
     */
    private const array SLA_HOURS = [
        'urgent' => ['acknowledge' => 2, 'resolve' => 24],
        'critical' => ['acknowledge' => 4, 'resolve' => 48],
        'high' => ['acknowledge' => 8, 'resolve' => 120],
        'medium' => ['acknowledge' => 24, 'resolve' => 360],
        'low' => ['acknowledge' => 48, 'resolve' => 720],
    ];

    /**
     * Percorsi canonici: ogni catena e' un cammino legale nella matrice di
     * transizione, da OPEN fino allo stato finale del ticket.
     *
     * @var array<string, non-empty-list<TicketStatusEnum>>
     */
    private const array PATHS = [
        'draft' => [TicketStatusEnum::DRAFT],
        'open' => [TicketStatusEnum::OPEN],
        'pending' => [TicketStatusEnum::OPEN, TicketStatusEnum::PENDING],
        'in_review' => [TicketStatusEnum::OPEN, TicketStatusEnum::PENDING, TicketStatusEnum::IN_REVIEW],
        'in_progress' => [TicketStatusEnum::OPEN, TicketStatusEnum::PENDING, TicketStatusEnum::IN_REVIEW, TicketStatusEnum::IN_PROGRESS],
        'on_hold' => [TicketStatusEnum::OPEN, TicketStatusEnum::PENDING, TicketStatusEnum::IN_REVIEW, TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::ON_HOLD],
        'resolved' => [TicketStatusEnum::OPEN, TicketStatusEnum::PENDING, TicketStatusEnum::IN_REVIEW, TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::RESOLVED],
        'closed' => [TicketStatusEnum::OPEN, TicketStatusEnum::PENDING, TicketStatusEnum::IN_REVIEW, TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED],
        'reopened' => [TicketStatusEnum::OPEN, TicketStatusEnum::PENDING, TicketStatusEnum::IN_REVIEW, TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::RESOLVED, TicketStatusEnum::REOPENED],
    ];

    /**
     * Percorso con deviazione: sospensione e poi ripresa, tipica dei cantieri.
     *
     * @var non-empty-list<TicketStatusEnum>
     */
    private const array PATH_WITH_HOLD = [
        TicketStatusEnum::OPEN,
        TicketStatusEnum::PENDING,
        TicketStatusEnum::IN_REVIEW,
        TicketStatusEnum::IN_PROGRESS,
        TicketStatusEnum::ON_HOLD,
        TicketStatusEnum::IN_PROGRESS,
        TicketStatusEnum::RESOLVED,
    ];

    /**
     * Percorso con riapertura: il cittadino non accetta la soluzione.
     *
     * @var non-empty-list<TicketStatusEnum>
     */
    private const array PATH_WITH_REOPEN = [
        TicketStatusEnum::OPEN,
        TicketStatusEnum::PENDING,
        TicketStatusEnum::IN_REVIEW,
        TicketStatusEnum::IN_PROGRESS,
        TicketStatusEnum::RESOLVED,
        TicketStatusEnum::REOPENED,
        TicketStatusEnum::IN_PROGRESS,
        TicketStatusEnum::RESOLVED,
        TicketStatusEnum::CLOSED,
    ];

    /**
     * @return non-empty-list<TicketStatusEnum>
     */
    public static function pathTo(TicketStatusEnum $target, bool $withHold = false, bool $withReopen = false): array
    {
        $path = match (true) {
            $withReopen => self::PATH_WITH_REOPEN,
            $withHold => self::pathWithHoldFor($target),
            default => self::PATHS[$target->value] ?? [TicketStatusEnum::OPEN],
        };

        self::assertLegal($path);

        return $path;
    }

    /**
     * @return non-empty-list<TicketStatusEnum>
     */
    private static function pathWithHoldFor(TicketStatusEnum $target): array
    {
        $canonical = self::PATHS[$target->value] ?? [TicketStatusEnum::OPEN];

        return match ($target->value) {
            'resolved' => self::PATH_WITH_HOLD,
            'closed' => [...self::PATH_WITH_HOLD, TicketStatusEnum::CLOSED],
            'on_hold' => self::PATHS['on_hold'],
            default => $canonical,
        };
    }

    /**
     * @param  non-empty-list<TicketStatusEnum>  $path
     */
    private static function assertLegal(array $path): void
    {
        foreach ($path as $index => $status) {
            if ($index === 0) {
                continue;
            }

            $previous = $path[$index - 1];
            if (! $previous->canTransitionTo($status)) {
                throw new \DomainException(sprintf(
                    'DemoSlaTimeline: percorso demo non valido %s -> %s.',
                    $previous->value,
                    $status->value,
                ));
            }
        }
    }

    /**
     * Genera i milestone (stato + timestamp) del ticket, ancorati alla creazione.
     *
     * Gli istanti vengono interpolati fra presa in carico e fine lavoro: e' l'unico
     * modo di garantire che l'ultimo milestone resti nel passato anche quando lo SLA
     * (720 ore per priorita' bassa) eccede l'eta' del ticket.
     *
     * @return non-empty-list<Milestone>
     */
    public static function milestones(
        Generator $faker,
        Carbon $createdAt,
        TicketStatusEnum $target,
        TicketPriorityEnum $priority,
    ): array {
        $path = self::pathTo($target, $faker->boolean(16), $faker->boolean(9));
        [$acknowledgeAt, $endAt, $resolveDueAt, $resolveAt] = self::schedule($faker, $createdAt, $target, $priority);

        $steps = count($path) - 1;
        $span = $endAt->getTimestamp() - $acknowledgeAt->getTimestamp();
        // La risoluzione non puo' stare oltre la fine lavoro: se lo SLA e' stato
        // compresso perche' il ticket e' troppo recente, il pavimento e' endAt.
        $resolveFloor = $resolveAt->lessThan($endAt) ? $resolveAt : $endAt;

        $milestones = [[
            'status' => $path[0],
            'at' => $createdAt->copy(),
            'payload' => self::slaPayload($createdAt, $resolveDueAt, null, $path[0], $resolveAt, $priority),
        ]];

        for ($index = 1; $index <= $steps; $index++) {
            $status = $path[$index];
            // La divisione e' sicura: il ciclo parte da 1, quindi $steps >= 1.
            $at = $acknowledgeAt->copy()->addSeconds((int) round($span * $index / $steps));

            if ($status === TicketStatusEnum::RESOLVED || $status === TicketStatusEnum::CLOSED) {
                $at = $at->lessThan($resolveFloor) ? $resolveFloor->copy() : $at;
            }

            $milestones[] = [
                'status' => $status,
                'at' => $at->copy(),
                'payload' => self::slaPayload($createdAt, $resolveDueAt, $acknowledgeAt, $status, $resolveAt, $priority),
            ];
        }

        return $milestones;
    }

    /**
     * Prende in carico, fine lavoro e scadenze, senza mai superare l'ora corrente.
     *
     * @return array{0: Carbon, 1: Carbon, 2: Carbon, 3: Carbon}
     */
    private static function schedule(
        Generator $faker,
        Carbon $createdAt,
        TicketStatusEnum $target,
        TicketPriorityEnum $priority,
    ): array {
        $sla = self::SLA_HOURS[$priority->value];
        $now = Carbon::now();
        $latest = $now->copy()->subMinutes(5);

        // Circa un ticket su nove sfonda la presa in carico: il dato che fa vendere lo SLA.
        $breachRatio = $faker->boolean(11)
            ? $faker->randomFloat(2, 1.1, 2.4)
            : $faker->randomFloat(2, 0.2, 0.95);

        $acknowledgeAt = $createdAt->copy()->addHours($sla['acknowledge'] * $breachRatio);
        $resolveDueAt = $createdAt->copy()->addHours($sla['resolve']);
        $resolveAt = $resolveDueAt->copy()->subHours(
            $sla['resolve'] * $faker->randomFloat(2, 0.05, min(0.9, $breachRatio))
        );

        if (in_array($target, [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED], true)) {
            $endAt = $resolveAt->copy();
        } else {
            $progress = $faker->randomFloat(2, 0.15, 0.98);
            $endAt = $createdAt->copy()->addHours($sla['resolve'] * $breachRatio * $progress);
        }

        $cap = $latest->getTimestamp() - $createdAt->getTimestamp();
        if ($cap < 300) {
            // Segnalazione troppo recente per una timeline: si allarga a 5 minuti.
            $latest = $createdAt->copy()->addMinutes(5);
        }

        if ($endAt->getTimestamp() > $latest->getTimestamp()) {
            $endAt = $latest->copy();
        }
        if ($acknowledgeAt->getTimestamp() > $endAt->getTimestamp()) {
            $acknowledgeAt = $endAt->copy();
        }
        if ($acknowledgeAt->getTimestamp() < $createdAt->getTimestamp()) {
            $acknowledgeAt = $createdAt->copy();
        }
        if ($endAt->getTimestamp() < $acknowledgeAt->getTimestamp()) {
            $endAt = $acknowledgeAt->copy();
        }

        return [$acknowledgeAt, $endAt, $resolveDueAt, $resolveAt];
    }

    /**
     * @return SlaPayload
     */
    public static function slaPayload(
        Carbon $createdAt,
        Carbon $resolveDueAt,
        ?Carbon $acknowledgedAt,
        TicketStatusEnum $status,
        Carbon $resolveAt,
        TicketPriorityEnum $priority,
    ): array {
        $sla = self::SLA_HOURS[$priority->value];
        $acknowledgeDueAt = $createdAt->copy()->addHours($sla['acknowledge']);

        $isFinished = in_array($status, [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED], true);
        $resolvedAt = $isFinished ? $resolveAt->copy() : null;

        $breached = $acknowledgedAt !== null && $acknowledgedAt->greaterThan($acknowledgeDueAt);
        $breachMinutes = $breached
            ? (int) $acknowledgedAt->diffInMinutes($acknowledgeDueAt)
            : 0;

        return [
            'acknowledge_due_at' => $acknowledgeDueAt->toIso8601String(),
            'resolve_due_at' => $resolveDueAt->toIso8601String(),
            'acknowledged_at' => $acknowledgedAt?->toIso8601String(),
            'resolved_at' => $resolvedAt?->toIso8601String(),
            'resolve_breached' => $resolvedAt !== null && $resolvedAt->greaterThan($resolveDueAt),
            'breached' => $breached,
            'breach_minutes' => $breachMinutes,
            'priority' => $priority->value,
        ];
    }

    /**
     * Stima ore/uomo necessarie, coerente con la tipologia di intervento.
     */
    public static function estimatedHours(Generator $faker, TicketPriorityEnum $priority): float
    {
        $base = match ($priority->value) {
            'urgent' => $faker->randomFloat(1, 4, 16),
            'critical' => $faker->randomFloat(1, 3, 12),
            'high' => $faker->randomFloat(1, 2, 8),
            'medium' => $faker->randomFloat(1, 1, 5),
            default => $faker->randomFloat(1, 0.5, 3),
        };

        return round($base * 2) / 2;
    }
}
