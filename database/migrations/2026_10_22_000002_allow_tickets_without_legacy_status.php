<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
    protected ?string $model_class = Ticket::class;

    public function up(): void
    {
        $tableName = $this->getTable();
        $connectionName = $this->model->getConnectionName() ?? config('database.default');

        if (is_string($connectionName) && DB::connection($connectionName)->getDriverName() !== 'sqlite') {
            DB::connection($connectionName)->statement(
                'ALTER TABLE '.$tableName.' MODIFY status_id BIGINT UNSIGNED NULL, MODIFY priority_id BIGINT UNSIGNED NULL'
            );

            return;
        }

        $this->tableUpdate(function (Blueprint $table): void {
            if ($this->hasColumn('status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->change();
            }
            if ($this->hasColumn('priority_id')) {
                $table->unsignedBigInteger('priority_id')->nullable()->change();
            }
        });
    }
};
