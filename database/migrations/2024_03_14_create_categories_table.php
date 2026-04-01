<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\Category;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
   

    public function up(): void
    {
        // -- CREATE --
        $this->tableCreate(
            static function (Blueprint $table): void {
                $table->string('id')->primary(); // es. 'strade', 'illuminazione'
                $table->string('name');
                $table->text('description');
                $table->string('icon');
                $table->timestamps();
                
                // Indici espliciti
                $table->index('name', 'categories_name_idx');
            }
        );
        
        // -- UPDATE --
        $this->tableUpdate(
            function (Blueprint $table): void {
                if (! $this->hasColumn('parent_id')) {
                    $table->string('parent_id')->nullable();
                    $table->index('parent_id', 'categories_parent_id_idx');
                }
                
                if (! $this->hasColumn('is_active')) {
                    $table->boolean('is_active')->default(true);
                    $table->index('is_active', 'categories_is_active_idx');
                }
                
                if (! $this->hasColumn('sort_order')) {
                    $table->integer('sort_order')->default(0);
                    $table->index('sort_order', 'categories_sort_order_idx');
                }
                
                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );
    }
};
