<?php

declare(strict_types=1);

namespace Modules\Fixcity\Livewire;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Modules\Fixcity\Actions\GetAccessibilityDeclarationAction;
use Modules\Fixcity\Actions\SendAccessibilityFeedbackAction;

/**
 * Modulo di feedback sull'accessibilita' della dichiarazione `/{lingua}/accessibilita` (L. 4/2004 art. 3-quater c. 2 lett. b; DCP-132).
 * Invia un'email alla casella che l'ente indica nella dichiarazione (`declaration.feedback_email`), letta lato server a ogni invio:
 * il destinatario non e' una proprieta' del componente (sarebbe del client). Senza casella il modulo non invia nulla.
 * Limite: 5 invii all'ora per indirizzo IP. Vista del tema (blocco `accessibility-statement`), una sola radice (requisito di Livewire).
 */
final class AccessibilityFeedbackForm extends Component
{
    public const int MAX_PER_HOUR = 5;

    public string $problem = '';

    public string $page = '';

    public string $contact = '';

    /** Esito dell'ultimo invio, annunciato dall'area `role="status"`: '' | 'sent' | 'throttled' | 'unavailable'. */
    public string $outcome = '';

    public function submit(): void
    {
        $this->outcome = '';
        try {
            $this->validate([
                'problem' => ['required', 'string', 'min:10', 'max:3000'],
                'page' => ['nullable', 'string', 'max:255'],
                'contact' => ['nullable', 'email', 'max:255'],
            ], attributes: (array) __('fixcity::accessibility.form.attributes'));
        } catch (ValidationException $exception) {
            // WCAG 3.3.1: il focus va al primo campo con errore, il cui messaggio e' collegato con aria-describedby
            $this->dispatch('accessibility-feedback-invalid');

            throw $exception;
        }

        $recipient = app(GetAccessibilityDeclarationAction::class)->execute()['feedback_email'] ?? null;
        if (! is_string($recipient) || filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
            $this->outcome = 'unavailable';

            return;
        }

        $sent = RateLimiter::attempt(
            'accessibility-feedback:'.request()->ip(),
            self::MAX_PER_HOUR,
            fn () => app(SendAccessibilityFeedbackAction::class)->execute(
                $recipient,
                $this->problem,
                $this->page,
                $this->contact,
            ),
            3600,
        );

        $this->outcome = $sent === false ? 'throttled' : 'sent';
        if ($sent !== false) {
            $this->reset(['problem', 'page', 'contact']);
        }
    }

    public function render(): View
    {
        $recipient = app(GetAccessibilityDeclarationAction::class)->execute()['feedback_email'] ?? null;

        return view('pub_theme::components.blocks.accessibility-statement.feedback-form', [
            'isAvailable' => is_string($recipient) && filter_var($recipient, FILTER_VALIDATE_EMAIL) !== false,
        ]);
    }
}
