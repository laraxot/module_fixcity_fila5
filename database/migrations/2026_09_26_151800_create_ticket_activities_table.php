<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Xot\Database\Migrations\XotBaseMigration;
use Modules\Xot\Datas\XotData;

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
                $this->alignUserIdColumn($table, $userClass);
                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );
    }

    /**
     * User ids are UUIDs in the current User contract. Preserve legacy numeric
     * ids before replacing the column so an existing installation never loses
     * audit data during the type migration.
     *
     * @param class-string<\Illuminate\Database\Eloquent\Model> $userClass
     */
    private function alignUserIdColumn(Blueprint $table, string $userClass): void
    {
        if (! $this->hasColumn('user_id')) {
            return;
        }

        if ($this->getColumnType('user_id') === 'bigint') {
            $connection = $this->model->getConnectionName() ?? 'fixcity';
            $hasRows = DB::connection($connection)->table($this->getTable())->exists();

            if ($hasRows) {
                if (! $this->hasColumn('legacy_user_id')) {
                    $table->renameColumn('user_id', 'legacy_user_id');
                }

                $table->uuid('user_id')->nullable()->index();

                return;
            }

            $table->dropColumn('user_id');
            $table->uuid('user_id')->nullable()->index();

            return;
        }

        $table->foreignIdFor($userClass, 'user_id')->nullable()->change();
    }
};
