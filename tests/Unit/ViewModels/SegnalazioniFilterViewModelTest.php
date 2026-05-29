<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\ViewModels;

use Illuminate\Support\Facades\File;
use Modules\Fixcity\ViewModels\SegnalazioniFilterViewModel;
use Tests\TestCase;

class SegnalazioniFilterViewModelTest extends TestCase
{
    private string $jsonPath;

    private ?string $originalJson = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jsonPath = base_path('../public_html/data/tickets.json');

        if (File::exists($this->jsonPath)) {
            $this->originalJson = File::get($this->jsonPath);
        }
    }

    protected function tearDown(): void
    {
        if ($this->originalJson !== null) {
            File::put($this->jsonPath, $this->originalJson);
        }

        parent::tearDown();
    }

    public function test_it_aggregates_facet_counts_from_nested_type_properties(): void
    {
        File::ensureDirectoryExists(dirname($this->jsonPath));
        File::put($this->jsonPath, (string) json_encode([
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'geometry' => ['type' => 'Point', 'coordinates' => [12.5, 41.9]],
                    'properties' => [
                        'type' => [
                            'value' => 'waste',
                            'label' => 'Raccolta rifiuti',
                            'color' => 'green',
                            'icon' => 'heroicon-o-trash',
                        ],
                    ],
                ],
                [
                    'type' => 'Feature',
                    'geometry' => ['type' => 'Point', 'coordinates' => [12.6, 41.8]],
                    'properties' => [
                        'type' => [
                            'value' => 'waste',
                            'label' => 'Raccolta rifiuti',
                            'color' => 'green',
                            'icon' => 'heroicon-o-trash',
                        ],
                    ],
                ],
                [
                    'type' => 'Feature',
                    'geometry' => ['type' => 'Point', 'coordinates' => [12.7, 41.7]],
                    'properties' => [
                        'type' => [
                            'value' => 'other',
                            'label' => 'Altro',
                            'color' => 'gray',
                            'icon' => 'heroicon-o-question-mark-circle',
                        ],
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $viewModel = new SegnalazioniFilterViewModel();

        $this->assertSame(3, $viewModel->getTotalCount());
        $this->assertSame(['waste' => 2, 'other' => 1], $viewModel->getCountsPerType());
        $this->assertSame(3, $viewModel->getFilteredCount([]));
        $this->assertSame(2, $viewModel->getFilteredCount(['waste']));

        $items = $viewModel->getFilterItems();
        $this->assertCount(2, $items);
        $this->assertSame('filter-waste', $items[0]['id']);
        $this->assertSame(2, $items[0]['count']);
    }

    public function test_it_returns_empty_when_json_file_missing(): void
    {
        $missingPath = $this->jsonPath . '.missing-test';
        if (File::exists($missingPath)) {
            File::delete($missingPath);
        }

        if (File::exists($this->jsonPath)) {
            File::delete($this->jsonPath);
        }

        $viewModel = new SegnalazioniFilterViewModel();

        $this->assertSame(0, $viewModel->getTotalCount());
        $this->assertSame([], $viewModel->getFilterItems());

        if ($this->originalJson !== null) {
            File::put($this->jsonPath, $this->originalJson);
        }
    }

    public function test_it_supplements_list_items_from_json_features(): void
    {
        File::ensureDirectoryExists(dirname($this->jsonPath));
        File::put($this->jsonPath, (string) json_encode([
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => 1,
                        'title' => 'Prima',
                        'address' => 'Via A',
                        'type' => ['value' => 'waste', 'label' => 'Rifiuti'],
                    ],
                ],
                [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => 2,
                        'title' => 'Seconda',
                        'address' => 'Via B',
                        'type' => ['value' => 'other', 'label' => 'Altro'],
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $viewModel = new SegnalazioniFilterViewModel();
        $items = $viewModel->getSupplementListItems(2, [1]);

        $this->assertCount(1, $items);
        $this->assertSame(2, $items[0]->id);
        $this->assertSame('Seconda', $items[0]->name);
        $this->assertSame('Altro', $items[0]->type_label);
    }
}
