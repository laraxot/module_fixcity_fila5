<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature;

use PHPUnit\Framework\Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Models\Category;
use Modules\Fixcity\Tests\TestCase;

/**
 * Class CategoryMigrationTest.
 *
 * Test per verificare la corretta struttura della tabella categories
 * e il funzionamento del modello Category.
 */
class CategoryMigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test che la tabella categories sia stata creata correttamente.
     */
    public function test_categories_table_exists(): void
    {
        Assert::assertTrue(Schema::hasTable('categories'));
    }

    /**
     * Test che la tabella categories abbia tutte le colonne richieste.
     */
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
            Assert::assertTrue(
                Schema::hasColumn('categories', $column),
                "Column '{$column}' is missing from categories table"
            );
        }
    }

    /**
     * Test che la tabella categories abbia gli indici richiesti.
     */
    public function test_categories_table_has_required_indexes(): void
    {
        $indexes = Schema::getIndexes('categories');

        $requiredIndexes = [
            'categories_name_idx',
            'categories_parent_id_idx',
            'categories_is_active_idx',
            'categories_sort_order_idx',
        ];

        foreach ($requiredIndexes as $index) {
            Assert::assertTrue(
                in_array($index, $indexes, true),
                "Index '{$index}' is missing from categories table"
            );
        }
    }

    /**
     * Test che il modello Category possa essere creato.
     */
    public function test_category_model_can_be_created(): void
    {
        $category = Category::create([
            'id' => 'test-category',
            'name' => 'Test Category',
            'content' => 'Test description',
            'icon' => 'test-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Assert::assertInstanceOf(Category::class, $category);
        Assert::assertEquals('test-category', $category->id);
        Assert::assertEquals('Test Category', $category->name);
        Assert::assertTrue($category->is_active);
    }

    /**
     * Test che il modello Category supporti le relazioni gerarchiche.
     */
    public function test_category_model_supports_hierarchical_relationships(): void
    {
        // Crea categoria padre
        $parent = Category::create([
            'id' => 'parent-category',
            'name' => 'Parent Category',
            'content' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Crea categoria figlia
        $child = Category::create([
            'id' => 'child-category',
            'name' => 'Child Category',
            'content' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Test relazione padre
        Assert::assertEquals('parent-category', $child->parent_id);
        Assert::assertInstanceOf(Category::class, $child->parent);
        Assert::assertEquals('Parent Category', $child->parent->name);

        // Test relazione figli
        Assert::assertTrue($parent->children()->exists());
        Assert::assertEquals(1, $parent->children()->count());
    }

    /**
     * Test che il modello Category supporti gli scope.
     */
    public function test_category_model_supports_scopes(): void
    {
        // Crea categorie attive e inattive
        Category::create([
            'id' => 'active-category',
            'name' => 'Active Category',
            'content' => 'Active description',
            'icon' => 'active-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'id' => 'inactive-category',
            'name' => 'Inactive Category',
            'content' => 'Inactive description',
            'icon' => 'inactive-icon',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        // Test scope active
        $activeCategories = Category::active()->get();
        Assert::assertEquals(1, $activeCategories->count());
        $firstActive = $activeCategories->first();
        Assert::assertNotNull($firstActive);
        Assert::assertEquals('active-category', $firstActive->id);

        // Test scope root
        $rootCategories = Category::root()->get();
        Assert::assertEquals(2, $rootCategories->count());
    }

    /**
     * Test che il modello Category calcoli correttamente il nome completo.
     */
    public function test_category_model_calculates_full_name_correctly(): void
    {
        // Crea categoria padre
        $parent = Category::create([
            'id' => 'parent',
            'name' => 'Parent',
            'content' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Crea categoria figlia
        $child = Category::create([
            'id' => 'child',
            'name' => 'Child',
            'content' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Test nome completo
        Assert::assertEquals('Parent', $parent->full_name);
        Assert::assertEquals('Parent > Child', $child->full_name);
    }

    /**
     * Test che il modello Category verifichi correttamente se ha figli.
     */
    public function test_category_model_checks_children_correctly(): void
    {
        // Crea categoria senza figli
        $parent = Category::create([
            'id' => 'parent',
            'name' => 'Parent',
            'content' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Assert::assertFalse($parent->hasChildren());

        // Aggiungi figlio
        Category::create([
            'id' => 'child',
            'name' => 'Child',
            'content' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $refreshedParent = $parent->fresh();
        Assert::assertNotNull($refreshedParent);
        Assert::assertTrue($refreshedParent->hasChildren());
    }
}
