<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Citizen-only close/reopen decision after the responsible staff marked a report resolved.
 *
 * The public timeline records the transition, while the citizen's dispute reason
 * is retained only for its owner and authorised staff. The same action handles the
 * POST route without a controller, matching the Fixcity Folio/Action convention.
 *
 * @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
 */
final class ConfirmTicketResolutionAction
{
    use QueueableAction;

    /**
     * @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
     */
    public function __invoke(Request $request, string $lang, int $ticket): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof UserContract, 403);

        $input = $request->validate([
            'decision' => ['required', Rule::in(['confirm', 'dispute'])],
            'reason' => ['nullable', 'string', 'max:2000', 'required_if:decision,dispute'],
        ]);

        $accepted = $input['decision'] === 'confirm';
        $reason = is_string($input['reason'] ?? null) ? trim($input['reason']) : '';

        if (! $accepted && $reason === '') {
            throw ValidationException::withMessages([
                'reason' => [__('fixcity::ticket_detail.resolution.reason_required')],
            ]);
        }

        $model = Ticket::query()->findOrFail($ticket);
        $this->execute($model, $user, $accepted, $reason);

        return redirect('/'.$lang.'/tickets/'.$ticket)
            ->with('resolution_feedback', $accepted ? 'confirmed' : 'disputed');
    }

    /**
     * @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
     */
    public function execute(Ticket $ticket, UserContract $actor, bool $accepted, ?string $reason = null): void
    {
        $reason = trim($reason ?? '');
        if (! $accepted && ($reason === '' || mb_strlen($reason) > 2000)) {
            throw ValidationException::withMessages([
                'reason' => [__('fixcity::ticket_detail.resolution.reason_required')],
            ]);
        }

        app(LockTicketForStatusChangeAction::class)->execute(
            $ticket,
            static function (Ticket $locked) use ($actor, $accepted, $reason): void {
                $actorId = $actor->getAuthIdentifier();
                abort_unless($actorId !== null && (string) $locked->owner_id === (string) $actorId, 403);

                $current = TicketStatusEnum::tryFrom($locked->resolveTicketStatusValue());
                if ($current !== TicketStatusEnum::RESOLVED) {
                    throw ValidationException::withMessages([
                        'decision' => [__('fixcity::ticket_detail.resolution.only_resolved')],
                    ]);
                }

                $target = $accepted ? TicketStatusEnum::CLOSED : TicketStatusEnum::REOPENED;
                $current->assertCanTransitionTo($target);
                $locked->setStatus($target);

                app(RecordTicketActivityAction::class)->execute(
                    $locked,
                    $accepted ? TicketActivityEventTypeEnum::Closed : TicketActivityEventTypeEnum::StatusChange,
                    $current,
                    $target,
                    $accepted ? (string) __('fixcity::ticket_detail.resolution.confirmed_audit') : null,
                    TicketActivityVisibilityEnum::Public,
                    is_int($actorId) || is_string($actorId) ? $actorId : null,
                );

                if (! $accepted) {
                    app(RecordTicketActivityAction::class)->execute(
                        $locked,
                        TicketActivityEventTypeEnum::StatusChange,
                        $target,
                        $target,
                        $reason,
                        TicketActivityVisibilityEnum::AuthorOnly,
                        is_int($actorId) || is_string($actorId) ? $actorId : null,
                        ['source' => 'citizen_resolution_dispute'],
                    );
                }
            },
        );
    }
}
