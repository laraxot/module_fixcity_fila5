<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Models\Category;
use Tests\TestCase;

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
        $this->assertTrue(Schema::hasTable('categories'));
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
            $this->assertTrue(
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
            $this->assertTrue(
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

    /**
     * Test che il modello Category supporti le relazioni gerarchiche.
     */
    public function test_category_model_supports_hierarchical_relationships(): void
    {
        // Crea categoria padre
        $parent = Category::create([
            'id' => 'parent-category',
            'name' => 'Parent Category',
            'description' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Crea categoria figlia
        $child = Category::create([
            'id' => 'child-category',
            'name' => 'Child Category',
            'description' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Test relazione padre
        $this->assertEquals('parent-category', $child->parent_id);
        $this->assertInstanceOf(Category::class, $child->parent);
        $this->assertEquals('Parent Category', $child->parent->name);

        // Test relazione figli
        $this->assertTrue($parent->children()->exists());
        $this->assertEquals(1, $parent->children()->count());
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
            'description' => 'Active description',
            'icon' => 'active-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'id' => 'inactive-category',
            'name' => 'Inactive Category',
            'description' => 'Inactive description',
            'icon' => 'inactive-icon',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        // Test scope active
        $activeCategories = Category::active()->get();
        $this->assertEquals(1, $activeCategories->count());
        $this->assertEquals('active-category', $activeCategories->first()->id);

        // Test scope root
        $rootCategories = Category::root()->get();
        $this->assertEquals(2, $rootCategories->count());
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
            'description' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Crea categoria figlia
        $child = Category::create([
            'id' => 'child',
            'name' => 'Child',
            'description' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Test nome completo
        $this->assertEquals('Parent', $parent->full_name);
        $this->assertEquals('Parent > Child', $child->full_name);
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
            'description' => 'Parent description',
            'icon' => 'parent-icon',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertFalse($parent->hasChildren());

        // Aggiungi figlio
        Category::create([
            'id' => 'child',
            'name' => 'Child',
            'description' => 'Child description',
            'icon' => 'child-icon',
            'parent_id' => 'parent',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertTrue($parent->fresh()->hasChildren());
    }
}
