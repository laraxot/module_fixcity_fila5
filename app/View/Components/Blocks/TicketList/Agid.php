<?php

declare(strict_types=1);

namespace Modules\Fixcity\View\Components\Blocks\TicketList;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Modules\Fixcity\Enums\ReportStatusEnum;

class Agid extends Component
{
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
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'location' => json_decode($report->location, true),
                    'address' => $report->address,
                    'category' => $report->category,
                    'status' => ReportStatusEnum::from($report->status),
                    'metadata' => json_decode($report->metadata, true),
                    'created_at' => $report->created_at,
                ];
            });
    }

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
            ->mapWithKeys(function ($category) {
                return [
                    $category->id => [
                        'name' => $category->name,
                        'description' => $category->description,
                        'icon' => $category->icon,
                    ],
                ];
            });
    }

    public function render()
    {
        return view('fixcity::components.blocks.ticket_list.agid', [
            'reports' => $this->getReports(),
            'categories' => $this->getCategories(),
        ]);
    }
}
