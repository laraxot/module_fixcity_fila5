<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Models\Category;
use Modules\Fixcity\Tests\TestCase;

/**
 * Contratto schema `categories` sulla connessione `fixcity` (owner Fixcity).
 *
 * Non usare Schema:: senza connection: `blog` e `fixcity` condividono il nome
 * tabella su MariaDB; dopo un migrate:fresh errato lo schema Blog (`title`)
 * maschera quello Fixcity (`name`).
 */
class CategoryMigrationTest extends TestCase
{
    private function fixcitySchema(): \Illuminate\Database\Schema\Builder
    {
        return Schema::connection('fixcity');
    }

    /**
     * @return list<string>
     */
    private function indexNames(): array
    {
        $indexes = $this->fixcitySchema()->getIndexes('categories');

        return array_values(array_filter(array_map(
            static function (array $index): string {
                $name = $index['name'];

                return is_string($name) ? $name : '';
            },
            $indexes,
        )));
    }

    public function test_categories_table_exists(): void
    {
        $this->assertTrue($this->fixcitySchema()->hasTable('categories'));
    }

    public function test_categories_table_has_required_columns(): void
    {
        $requiredColumns = [
            'id',
            'name',
            'description',
            'icon',
            'parent_id',
            'is_active',
            'sort_order',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        foreach ($requiredColumns as $column) {
            $this->assertTrue(
                $this->fixcitySchema()->hasColumn('categories', $column),
                "Column '{$column}' is missing from fixcity.categories"
            );
        }
    }

    public function test_categories_table_has_required_indexes(): void
    {
        $indexNames = $this->indexNames();

        // Allineato allo schema produzione fixcity_data (nessun categories_name_idx).
        $requiredIndexes = [
            'categories_parent_id_idx',
            'categories_is_active_idx',
            'categories_sort_order_idx',
        ];

        foreach ($requiredIndexes as $index) {
            $this->assertContains(
                $index,
                $indexNames,
                "Index '{$index}' is missing from fixcity.categories"
            );
        }
    }

    public function test_category_model_can_be_created(): void
    {
        $category = Category::query()->create([
            'id' => 'test-category',
            'name' => 'Test Category',
            'description' => 'Test description',
            'icon' => 'test-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('test-category', $category->id);
        $this->assertEquals('Test Category', $category->name);
        $this->assertTrue($category->is_active);
    }

    public function test_category_model_supports_hierarchical_relationships(): void
    {
        $parent = Category::query()->create([
            'id' => 'parent-category',
            'name' => 'Parent Category',
            'description' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $child = Category::query()->create([
            'id' => 'child-category',
            'name' => 'Child Category',
            'description' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertEquals('parent-category', $child->parent_id);
        $this->assertInstanceOf(Category::class, $child->parent);
        $this->assertEquals('Parent Category', $child->parent->name);

        $this->assertTrue($parent->children()->exists());
        $this->assertEquals(1, $parent->children()->count());
    }

    public function test_category_model_supports_scopes(): void
    {
        Category::query()->create([
            'id' => 'active-category',
            'name' => 'Active Category',
            'description' => 'Active description',
            'icon' => 'active-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::query()->create([
            'id' => 'inactive-category',
            'name' => 'Inactive Category',
            'description' => 'Inactive description',
            'icon' => 'inactive-icon',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $activeCategories = Category::query()->active()->get();
        $this->assertEquals(1, $activeCategories->count());
        $active = $activeCategories->first();
        $this->assertNotNull($active);
        $this->assertEquals('active-category', $active->id);

        $rootCategories = Category::query()->root()->get();
        $this->assertEquals(2, $rootCategories->count());
    }

    public function test_category_model_calculates_full_name_correctly(): void
    {
        $parent = Category::query()->create([
            'id' => 'parent',
            'name' => 'Parent',
            'description' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $child = Category::query()->create([
            'id' => 'child',
            'name' => 'Child',
            'description' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertEquals('Parent', $parent->full_name);
        $this->assertEquals('Parent > Child', $child->full_name);
    }

    public function test_category_model_checks_children_correctly(): void
    {
        $parent = Category::query()->create([
            'id' => 'parent',
            'name' => 'Parent',
            'description' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertFalse($parent->hasChildren());

        Category::query()->create([
            'id' => 'child',
            'name' => 'Child',
            'description' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $freshParent = $parent->fresh();
        $this->assertNotNull($freshParent);
        $this->assertTrue($freshParent->hasChildren());
    }
}
