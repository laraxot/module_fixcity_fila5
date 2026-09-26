<?php

/**
 * Ticket Status Enum - Stati del ticket.
 *
 * SSoT delle regole di workflow del ticket. La matrice `allowedTransitions()` è
 * l'unico posto dove sono dichiarate le transizioni legali: nessuna Action, form o
 * pagina deve duplicarla.
 *
 * @see ../../../../docs/stories/STORY-496-ticket-status-transition-matrix.md
 * @see ../../../../docs/prd-base_fixcity_fila5-2026-05-28.md  (user flow operatore)
 */

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

enum TicketStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case DRAFT = 'draft';
    case PENDING = 'pending';
    case IN_REVIEW = 'in_review';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case REOPENED = 'reopened';
    case OPEN = 'open';

    /**
     * Stati la cui presenza nel ticket è pubblica: il cittadino li vede nel tracking.
     *
     * NOTA: `IN_PROGRESS` sta qui e NON in `canNotViewByAll()`. fino al 2026-09-26
     * era in entrambe le liste, il che rendeva la visibility dipendente dal chiamante.
     * L'unione delle due liste copre tutti i casi, senza sovrapposizione.
     *
     * @return list<self>
     */
    public static function canViewByAll(): array
    {
        return [
            self::OPEN,
            self::IN_REVIEW,
            self::IN_PROGRESS,
            self::ON_HOLD,
            self::RESOLVED,
            self::CLOSED,
            self::REOPENED,
        ];
    }

    /**
     * Stati interni: visibili solo a chi ha un permesso sul backoffice.
     *
     * @return list<self>
     */
    public static function canNotViewByAll(): array
    {
        return [
            self::DRAFT,
            self::PENDING,
        ];
    }

    public static function default(): static
    {
        return self::OPEN;
    }

    /**
     * Transizioni legali: SSoT del workflow.
     *
     * Il flusso dell'operatore (PRD §8) è: riceve -> prende in carico -> aggiorna stato
     * -> risolve o rifiuta motivando. Da qui le regole:
     *
     * - `DRAFT`     non e' assegnabile: e' una bozza, non c'e' lavoro da fare;
     * - `PENDING`   -> presa in carico da parte dell'ente;
     * - `IN_REVIEW` -> il soprintendente deve approdare il testo, poi si lavora;
     * - `ON_HOLD`   -> sospeso per attesa di informazioni: torna indietro a `IN_PROGRESS`;
     * - `RESOLVED`  -> chiuso dall'ente, o rifiutato dall'amministratore: si puo' riaprire;
     * - `CLOSED`    -> terminale: il cittadino ha accettato, o il termine e' scaduto;
     * - `REOPENED`  -> il cittadino non ha accettato: torna `IN_PROGRESS`.
     *
     * @return array<string, list<self>> chiave = valore dell'enum
     */
    public static function allowedTransitions(): array
    {
        return [
            self::DRAFT->value => [],

            self::PENDING->value => [
                self::IN_REVIEW,
                self::CLOSED,
            ],

            self::IN_REVIEW->value => [
                self::IN_PROGRESS,
                self::ON_HOLD,
                self::CLOSED,
            ],

            self::IN_PROGRESS->value => [
                self::ON_HOLD,
                self::RESOLVED,
            ],

            self::ON_HOLD->value => [
                self::IN_PROGRESS,
                self::RESOLVED,
            ],

            self::RESOLVED->value => [
                self::REOPENED,
                self::CLOSED,
            ],

            self::CLOSED->value => [],

            self::REOPENED->value => [
                self::IN_PROGRESS,
                self::CLOSED,
            ],

            self::OPEN->value => [
                self::PENDING,
            ],
        ];
    }

    /**
     * @return list<self>
     */
    public function allowedTargets(): array
    {
        return self::allowedTransitions()[$this->value] ?? [];
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTargets(), true);
    }

    /**
     * Per il percorso di persistenza: fallisce rumorosamente invece di salvare
     * uno stato che il workflow non consente.
     */
    public function assertCanTransitionTo(self $target): void
    {
        if ($this->canTransitionTo($target)) {
            return;
        }

        $allowed = array_map(
            static fn (self $status): string => $status->value,
            $this->allowedTargets(),
        );

        throw new \DomainException(sprintf(
            'Transizione non consentita: %s -> %s. Consentite: [%s].',
            $this->value,
            $target->value,
            $allowed === [] ? 'nessuna (stato terminale)' : implode(', ', $allowed),
        ));
    }

    public function isTerminal(): bool
    {
        return $this->allowedTargets() === [];
    }
}
