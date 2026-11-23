{{-- @see docs/stories/dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md --}}
@if (isset($similarTickets) && $similarTickets->isNotEmpty())
    <section class="alert alert-info mt-4" aria-labelledby="similar-tickets-heading" data-element="similar-tickets">
        <h3 class="h5" id="similar-tickets-heading">{{ __('fixcity::ticket_form.similar_tickets.title') }}</h3>
        <p>{{ __('fixcity::ticket_form.similar_tickets.description') }}</p>
        <ul class="mb-0">
            @foreach ($similarTickets as $similarTicket)
                <li>
                    <a href="{{ app(\Modules\Fixcity\Actions\BuildTicketDetailUrlAction::class)->execute((int) $similarTicket->getKey()) }}">
                        {{ $similarTicket->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@endif
