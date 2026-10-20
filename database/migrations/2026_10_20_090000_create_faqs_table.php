<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\Faq;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Owner Fixcity — domande frequenti del cittadino (connessione fixcity).
 * Il catalogo vive in CivicFaqCatalog e arriva qui dal FaqSeeder.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = Faq::class;

    public function up(): void
    {
        $this->tableCreate(
            static function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->string('locale', 8);
                $table->string('question');
                $table->text('answer');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
            }
        );

        $this->tableUpdate(
            function (Blueprint $table): void {
                if (! $this->hasIndex('faqs_locale_sort_idx')) {
                    $table->index(['locale', 'is_published', 'sort_order'], 'faqs_locale_sort_idx');
                }

                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );
    }
};
