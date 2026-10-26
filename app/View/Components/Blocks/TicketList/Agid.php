<?php

declare(strict_types=1);

namespace Modules\Fixcity\View\Components\Blocks\TicketList;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Modules\Fixcity\Enums\ReportStatusEnum;

use function Safe\json_decode;

class Agid extends Component
{
    /** @return Collection<int, array<string, mixed>> */
    public function getReports(): Collection
    {
        return DB::table('reports')
            ->select([
                'id',
                'title',
                'description',
                'location',
                'address',
                'category',
                'status',
                'metadata',
                'created_at',
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (object $report): array {
                $location = is_string($report->location) ? json_decode($report->location, true) : [];
                $metadata = is_string($report->metadata) ? json_decode($report->metadata, true) : [];
                $status = is_string($report->status) ? $report->status : '';

                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'location' => is_array($location) ? $location : [],
                    'address' => $report->address,
                    'category' => $report->category,
                    'status' => ReportStatusEnum::from($status),
                    'metadata' => is_array($metadata) ? $metadata : [],
                    'created_at' => $report->created_at,
                ];
            });
    }

    /** @return Collection<int|string, array{name: mixed, description: mixed, icon: mixed}> */
    public function getCategories(): Collection
    {
        return DB::table('categories')
            ->select([
                'id',
                'name',
                'description',
                'icon',
            ])
            ->get()
            ->mapWithKeys(function (object $category): array {
                $id = $category->id;
                if (! is_int($id) && ! is_string($id)) {
                    return [];
                }

                return [
                    $id => [
                        'name' => $category->name,
                        'description' => $category->description,
                        'icon' => $category->icon,
                    ],
                ];
            });
    }

    public function render(): View
    {
        return view('fixcity::components.blocks.ticket_list.ticket_list', [
            'reports' => $this->getReports(),
            'categories' => $this->getCategories(),
        ]);
    }
}
