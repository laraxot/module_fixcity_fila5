<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Illuminate\Contracts\View\View as ViewContract;
use Livewire\Component;

/**
 * Livewire map widget used on public pages.
 * Note: Namespace kept for backward compatibility with existing references.
 */
final class TicketsMapWidget extends Component
{
    /** @var array<string> */
    public array $categoryFilter = [];

    public ?float $latitude = null;

    public ?float $longitude = null;

    /**
     * @return array<string, string|array<int, string>>
     */
    protected function getListeners(): array
    {
        return [
            'updateMapCenter' => 'updateMapCenter',
            'categoryFilterUpdated' => 'rerender',
        ];
    }

    /**
     * @param array<string> $categoryFilter
     */
    public function mount(?float $latitude = null, ?float $longitude = null, array $categoryFilter = []): void
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->categoryFilter = $categoryFilter;
    }

    public function updateMapCenter(float $latitude, float $longitude): void
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function rerender(): void
    {
        // No-op: forces Livewire to refresh the component when filters change.
    }

    public function render(): ViewContract
    {
        return view('fixcity::filament.widgets.tickets-map-widget');
    }
}


