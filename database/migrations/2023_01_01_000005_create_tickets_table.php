<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Class .
 */
return new class extends XotBaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // -- CREATE --
        $this->tableCreate(
            static function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->longText('content');
                $table->foreignId('owner_id'); // ->constrained('users');
                $table->foreignId('responsible_id')->nullable(); // ->constrained('users');
                $table->foreignId('status_id'); // ->constrained('ticket_statuses');
                // $table->foreignId('project_id')->constrained('projects');
                $table->string('code')->nullable();
                $table->string('ticket_prefix')->nullable();
                $table->integer('order')->default(0);
                $table->foreignId('priority_id'); // ->constrained('ticket_priorities');
                $table->foreignId('project_id')->nullable(); // ->constrained('projects');
                $table->float('estimation')->nullable();
                $table->foreignId('epic_id')->nullable(); // ->constrained('epics');
                // $table->longText('attachments')->nullable();
                $table->foreignId('sprint_id')->nullable(); // ->constrained('sprints');
                $table->softDeletes();
                $table->timestamps();
            }
        );
        // -- UPDATE --
        $this->tableUpdate(
            function (Blueprint $table): void {
                if (! $this->hasColumn('type_id')) {
                    $table->integer('type_id')->nullable()->index();
                }
                if (! $this->hasColumn('latitude')) {
                    $table->decimal('latitude', 20, 18)->nullable();
                }
                if (! $this->hasColumn('longitude')) {
                    $table->decimal('longitude', 20, 18)->nullable();
                }
                if (! $this->hasColumn('status')) {
                    $table->string('status')->nullable();
                }
                if (! $this->hasColumn('type')) {
                    $table->string('type')->nullable();
                }
                if (! $this->hasColumn('priority')) {
                    $table->string('priority')->nullable();
                }
                if (! $this->hasColumn('slug')) {
                    $table->string('slug')->unique();
                }

                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );
    }
};
