<?php

namespace Modules\Fixcity\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Volt\Component as VoltComponent;
use Livewire\WithPagination;
use Modules\Fixcity\Models\Ticket;

class TicketList extends VoltComponent
{
    use WithPagination;

    public $search = '';

    public $selectedCategory = '';

    public $selectedStatus = '';

    public array $categories = [
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

    public function getTicketsProperty()
    {
        return Ticket::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%")
                ->orWhere('content', 'like', "%{$this->search}%")
            )
            ->when($this->selectedCategory, fn ($q) => $q->whereHas('category', fn ($q) => $q->where('name', $this->selectedCategory)
            )
            )
            ->when($this->selectedStatus, fn ($q) => $q->where('status', $this->selectedStatus)
            )
            ->latest()
            ->paginate(10);
    }

    public function render(): View
    {
        return view('fixcity::components.blocks.ticket_list.agid', [
            'tickets' => $this->tickets,
        ]);
    }
}
