<?php

declare(strict_types=1);

namespace Modules\Fixcity\View\Components\Blocks;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Fixcity\Models\Ticket;

/**
 * Blocco lista segnalazioni FO: categorie statiche + paginazione ticket recenti.
 * I conteggi nelle label sono placeholder editoriali del blocco CMS, non query live.
 */
class TicketList extends Component
{
    /** @var list<string> */
    public array $categories;

    /** @var LengthAwarePaginator<int, Ticket> */
    public LengthAwarePaginator $tickets;

    public function __construct()
    {
        $this->categories = [
            'Acqua, allagamenti, problemi fognari (21)',
            'Ambiente, inquinamento, protezione ambientale (14)',
            'Arredo urbano (7)',
            'Disinfestazione, derattizazione, animali randagi (208)',
            'Igiene urbana, rifiuti, pulizia e decoro (321)',
            'Manutenzione immobili, edifici pubblici, scuole, barriere architettoniche, cimiteri (302)',
            'Ordine pubblico, disturbo della quiete (302)',
            'Parchi e verde pubblico (302)',
            'Servizi del comune (302)',
            'Sicurezza, degrado urbano e sociale (302)',
            'Strade, marciapiedi, segnaletica e viabilità (302)',
        ];

        $this->tickets = Ticket::query()
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('fixcity::components.blocks.ticket_list.ticket_list');
    }
}
