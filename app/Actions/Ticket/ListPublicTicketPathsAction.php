<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\Ticket;

use Modules\Fixcity\Actions\BuildPublicTicketsQueryAction;
use Modules\Seo\Contracts\SitemapPathsContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Percorsi di dettaglio delle segnalazioni pubbliche, per la sitemap di Seo (DCP-273).
 *
 * /{lang}/tickets/{id} e' pubblica per scelta (DCP-148) e la serve la pagina Folio del tema `[container0]/[slug0]/index.blade.php`
 * con `BuildPublicTicketDetailAction`, che parte da `BuildPublicTicketsQueryAction`. Qui si usa la stessa query con
 * `includeOwnTickets: false`: la sitemap e' uguale per tutti anche quando la genera un utente loggato (un ticket non ancora pubblico
 * del suo proprietario risponde 404 a chiunque altro).
 */
final class ListPublicTicketPathsAction implements SitemapPathsContract
{
    use QueueableAction;

    /**
     * @return list<string>
     */
    public function execute(): array
    {
        $paths = [];
        foreach (app(BuildPublicTicketsQueryAction::class)->execute(includeOwnTickets: false)->orderBy('id')->pluck('id') as $id) {
            if (is_int($id)) {
                $paths[] = 'tickets/'.$id;
            }
        }

        return $paths;
    }
}
