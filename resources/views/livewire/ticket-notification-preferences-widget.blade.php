<form wire:submit="save" aria-labelledby="ticket-notification-preferences-title">
    <h2 id="ticket-notification-preferences-title" class="h4 mb-3">
        {{ __('fixcity::ticket_notification_preferences.section_title') }}
    </h2>

    <div class="form-check mb-3">
        <input
            class="form-check-input"
            id="email-ticket-updates"
            type="checkbox"
            wire:model="emailTicketUpdates"
        >
        <label class="form-check-label" for="email-ticket-updates">
            {{ __('fixcity::ticket_notification_preferences.email_ticket_updates') }}
        </label>
        <p class="form-text mb-0">
            {{ __('fixcity::ticket_notification_preferences.email_ticket_updates_help') }}
        </p>
    </div>

    <button class="btn btn-primary" type="submit" wire:loading.attr="disabled">
        {{ __('fixcity::ticket_notification_preferences.save') }}
    </button>

    @if (session()->has('status'))
        <p class="text-success mt-3 mb-0" role="status" aria-live="polite">{{ session('status') }}</p>
    @endif
</form>
