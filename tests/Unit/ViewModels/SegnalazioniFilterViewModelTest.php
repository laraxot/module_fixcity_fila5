<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\ViewModels;

use Modules\Fixcity\Actions\BuildSegnalazioniFilterAggregateAction;
use Modules\Fixcity\ViewModels\SegnalazioniFilterViewModel;
use Tests\TestCase;

class SegnalazioniFilterViewModelTest extends TestCase
{
    public function test_it_exposes_filter_items_from_aggregate_action(): void
    {
        $this->mock(BuildSegnalazioniFilterAggregateAction::class, function ($mock): void {
            $mock->shouldReceive('execute')->once()->andReturn([
                'features' => [
                    [
                        'properties' => [
                            'id' => 2,
                            'title' => 'Seconda',
                            'type' => ['value' => 'other', 'label' => 'Altro', 'iconUrl' => '/assets/fixcity/svg/other.svg'],
                            'status' => ['value' => 'open', 'label' => 'Aperta', 'color' => '#2563eb'],
                            'address' => 'Via B',
                        ],
                    ],
                ],
                'countsPerType' => ['waste' => 2, 'other' => 1],
                'uniqueTypes' => [
                    ['value' => 'waste', 'label' => 'Raccolta rifiuti', 'iconUrl' => '/assets/fixcity/svg/waste.svg'],
                    ['value' => 'other', 'label' => 'Altro', 'iconUrl' => '/assets/fixcity/svg/other.svg'],
                ],
                'countsPerStatus' => ['open' => 1],
                'uniqueStatuses' => [
                    ['value' => 'open', 'label' => 'Aperta', 'color' => '#2563eb'],
                ],
                'totalCount' => 3,
            ]);
        });

        $viewModel = new SegnalazioniFilterViewModel();

        $this->assertSame(3, $viewModel->getTotalCount());
        $this->assertSame(['waste' => 2, 'other' => 1], $viewModel->getCountsPerType());
        $this->assertCount(2, $viewModel->getFilterItems());
        $labels = array_column($viewModel->getFilterItems(), 'display_label');
        $this->assertContains('Raccolta rifiuti', $labels);
        $this->assertContains('Altro', $labels);
        $this->assertCount(1, $viewModel->getStatusFilterItems());
        $this->assertSame(1, $viewModel->getFilteredCount(['other'], ['open']));
        $this->assertSame(0, $viewModel->getFilteredCount(['waste'], ['open']));
    }

    public function test_it_supplements_list_items_from_aggregate_features(): void
    {
        $this->mock(BuildSegnalazioniFilterAggregateAction::class, function ($mock): void {
            $mock->shouldReceive('execute')->once()->andReturn([
                'features' => [
                    [
                        'geometry' => ['coordinates' => [12.5, 41.9]],
                        'properties' => ['id' => 1, 'title' => 'Prima', 'type' => ['value' => 'waste', 'label' => 'Rifiuti'], 'address' => 'Via A', 'type_label' => 'Rifiuti'],
                    ],
                    [
                        'geometry' => ['coordinates' => [12.51, 41.91]],
                        'properties' => ['id' => 2, 'title' => 'Seconda supplement test', 'type' => ['value' => 'other', 'label' => 'Altro'], 'address' => 'Via B', 'type_label' => 'Altro'],
                    ],
                ],
                'countsPerType' => [],
                'uniqueTypes' => [],
                'totalCount' => 2,
            ]);
        });

        $viewModel = new SegnalazioniFilterViewModel();
        $items = $viewModel->getSupplementListItems(2, [1]);

        $this->assertCount(1, $items);
        $this->assertSame('Seconda supplement test', $items[0]->name);
    }
}
