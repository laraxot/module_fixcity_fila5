<?php

namespace Modules\Fixcity\View\Components\Blocks;

use Illuminate\View\Component;
use Modules\Fixcity\Models\Ticket;

class TicketList extends Component
{
    public array $categories;

    public $tickets;

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

    public function render()
    {
        return view('fixcity::components.blocks.ticket_list.agid');
    }
}
