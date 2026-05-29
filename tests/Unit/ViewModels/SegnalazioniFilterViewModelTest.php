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
                    ['properties' => ['id' => 2, 'title' => 'Seconda', 'type' => ['value' => 'other', 'label' => 'Altro'], 'address' => 'Via B']],
                ],
                'countsPerType' => ['waste' => 2, 'other' => 1],
                'uniqueTypes' => [
                    ['value' => 'waste', 'label' => 'Raccolta rifiuti', 'color' => 'green', 'icon' => ''],
                    ['value' => 'other', 'label' => 'Altro', 'color' => 'gray', 'icon' => ''],
                ],
                'totalCount' => 3,
            ]);
        });

        $viewModel = new SegnalazioniFilterViewModel();

        $this->assertSame(3, $viewModel->getTotalCount());
        $this->assertSame(['waste' => 2, 'other' => 1], $viewModel->getCountsPerType());
        $this->assertSame(2, $viewModel->getFilteredCount(['waste']));
        $this->assertCount(2, $viewModel->getFilterItems());
    }

    public function test_it_supplements_list_items_from_aggregate_features(): void
    {
        $this->mock(BuildSegnalazioniFilterAggregateAction::class, function ($mock): void {
            $mock->shouldReceive('execute')->once()->andReturn([
                'features' => [
                    ['properties' => ['id' => 1, 'title' => 'Prima', 'type' => ['value' => 'waste', 'label' => 'Rifiuti'], 'address' => 'Via A', 'type_label' => 'Rifiuti']],
                    ['properties' => ['id' => 2, 'title' => 'Seconda supplement test', 'type' => ['value' => 'other', 'label' => 'Altro'], 'address' => 'Via B', 'type_label' => 'Altro']],
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
