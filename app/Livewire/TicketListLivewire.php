<?php

namespace Modules\Fixcity\Livewire;

use Livewire\WithPagination;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TicketListLivewire extends Component
{
    use WithPagination;

    public $locationSet = false;
    public $userLatitude;
    public $userLongitude;
    public $search = '';
    public $selectedCategories = [];
    public $selectedStatus = '';
    public $resolvedTicketsCount = 0;
    public $perPage = 3;
    public $expandedTickets = [];
    public $filteredCount = 0;
    public $currentTab = 'lista';

    public function toggleTicket($ticketId)
    {
        if (in_array($ticketId, $this->expandedTickets)) {
            $this->expandedTickets = array_diff($this->expandedTickets, [$ticketId]);
        } else {
            $this->expandedTickets[] = $ticketId;
        }
    }

    public function switchTab($tab)
    {
        $this->currentTab = $tab;
    }

    public function mount()
    {
        $this->resolvedTicketsCount = Ticket::query()
            ->where(function ($q) {
                $q->whereIn('status', TicketStatusEnum::canViewByAll())
                    ->orWhere('created_by', authId())
                    ->orWhere('updated_by', authId());
            })
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->count();
        $this->dispatch('get-user-location');
    }

    public function setUserLocation($latitude, $longitude)
    {
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;
        $this->locationSet = true;
    }

    public function render()
    {
        $query = Ticket::query()
            ->with(['user', 'category', 'status'])
            ->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                  ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->selectedCategories)) {
            $query->whereIn('category_id', $this->selectedCategories);
        }

        if ($this->selectedStatus !== '') {
            $query->where('status', $this->selectedStatus);
        }

        $tickets = $query->paginate($this->perPage);

        $categories = \Modules\Fixcity\Models\TicketCategory::query()
            ->active()
            ->get()
            ->map(fn ($c) => ['value' => $c->id, 'label' => $c->name])
            ->values()
            ->all();

        return view('fixcity::livewire.ticket-list', [
            'categories' => $categories,
            'tickets' => $tickets,
            'hasMorePages' => $tickets->hasMorePages(),
            'filteredCount' => $this->filteredCount,
            'userLatitude' => $this->userLatitude,
            'userLongitude' => $this->userLongitude,
        ]);
    }
}