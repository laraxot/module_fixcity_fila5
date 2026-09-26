<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Xot\Datas\XotData;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Class .
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = TicketActivity::class;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $userClass = XotData::make()->getUserClass();

        // -- CREATE --
        $this->tableCreate(
            static function (Blueprint $table) use ($userClass): void {
                $table->id();
                $table->foreignIdFor(Ticket::class);
                // Legacy status IDs are not backed by a TicketStatus model/table.
                $table->unsignedBigInteger('old_status_id')->nullable();
                $table->unsignedBigInteger('new_status_id')->nullable();
                $table->foreignIdFor($userClass, 'user_id')->nullable();
                $table->string('event_type')->default('status_change');
                $table->json('payload')->nullable();
                $table->string('visibility')->default('internal');
                $table->text('reason')->nullable();
            }
        );
        // -- UPDATE --
        $this->tableUpdate(
            function (Blueprint $table) use ($userClass): void {
                if (! $this->hasColumn('event_type')) {
                    $table->string('event_type')->default('status_change');
                }
                if (! $this->hasColumn('payload')) {
                    $table->json('payload')->nullable();
                }
                if (! $this->hasColumn('visibility')) {
                    $table->string('visibility')->default('internal');
                }
                if (! $this->hasColumn('reason')) {
                    $table->text('reason')->nullable();
                }
                if ($this->hasColumn('old_status_id')) {
                    $table->unsignedBigInteger('old_status_id')->nullable()->change();
                }
                if ($this->hasColumn('new_status_id')) {
                    $table->unsignedBigInteger('new_status_id')->nullable()->change();
                }
                if ($this->hasColumn('user_id')) {
                    $table->foreignIdFor($userClass, 'user_id')->nullable()->change();
                }
                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );
    }
};
