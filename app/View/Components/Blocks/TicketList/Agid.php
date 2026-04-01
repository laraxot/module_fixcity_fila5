<?php

declare(strict_types=1);

namespace Modules\Fixcity\View\Components\Blocks\TicketList;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Modules\Fixcity\Enums\ReportStatusEnum;

use function Safe\json_decode;

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
            ->map(function (object $report): array {
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'location' => json_decode((string) $report->location, true),
                    'address' => $report->address,
                    'category' => $report->category,
                    'status' => ReportStatusEnum::from((int) $report->status),
                    'metadata' => json_decode((string) $report->metadata, true),
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
            ->mapWithKeys(function (object $category) {
                /** @var int|string $key */
                $key = is_int($category->id) || is_string($category->id) ? $category->id : (int) $category->id;

                return [
                    $key => [
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
