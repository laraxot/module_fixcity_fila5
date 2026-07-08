<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\ViewModels;

use Modules\Fixcity\Actions\BuildSegnalazioniFilterAggregateAction;
use PHPUnit\Framework\Assert;
use Modules\Fixcity\ViewModels\SegnalazioniFilterViewModel;
use Modules\Fixcity\Tests\TestCase;

uses(\Modules\Fixcity\Tests\TestCase::class);

describe('Segnalazioni Filter View Model', function (): void {
    test('_it_exposes_filter_items_from_aggregate_action', function (): void {
        /** @var \Modules\Fixcity\Tests\TestCase $this */
$this->bindInstance(BuildSegnalazioniFilterAggregateAction::class, new class extends BuildSegnalazioniFilterAggregateAction
        {
            public function execute(): array
            {
                return [
                    'features' => [
                        [
                            'properties' => [
                                'id' => 2,
                                'name' => 'Seconda',
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
                ];
            }
        });

        $viewModel = new SegnalazioniFilterViewModel();

        Assert::assertSame(3, $viewModel->getTotalCount());
        Assert::assertSame(['waste' => 2, 'other' => 1], $viewModel->getCountsPerType());
        Assert::assertCount(2, $viewModel->getFilterItems());
        $labels = array_column($viewModel->getFilterItems(), 'display_label');
        Assert::assertContains('Raccolta rifiuti', $labels);
        Assert::assertContains('Altro', $labels);
        Assert::assertCount(1, $viewModel->getStatusFilterItems());
        Assert::assertSame(1, $viewModel->getFilteredCount(['other'], ['open']));
        Assert::assertSame(0, $viewModel->getFilteredCount(['waste'], ['open']));
    });

    test('_it_supplements_list_items_from_aggregate_features', function (): void {
$this->bindInstance(BuildSegnalazioniFilterAggregateAction::class, new class extends BuildSegnalazioniFilterAggregateAction
        {
            public function execute(): array
            {
                return [
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
                    'countsPerStatus' => [],
                    'uniqueStatuses' => [],
                    'totalCount' => 2,
                ];
            }
        });

        $viewModel = new SegnalazioniFilterViewModel();
        $items = $viewModel->getSupplementListItems(2, [1]);

        Assert::assertCount(1, $items);
        Assert::assertSame('Seconda supplement test', $items[0]['name']);
    });
});
