<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Models\TicketHour;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
    protected ?string $model_class = TicketHour::class;

    public function up(): void
    {
        $connectionName = $this->model->getConnectionName();
        if (! is_string($connectionName) || $connectionName === '') {
            $configured = config('database.default');
            $connectionName = is_string($configured) && $configured !== '' ? $configured : 'mariadb';
        }

        $connection = DB::connection($connectionName);

        if ($connection->getDriverName() !== 'sqlite') {
            $connection->statement('ALTER TABLE ticket_hours MODIFY user_id VARCHAR(36) NOT NULL');

            return;
        }

        $this->tableUpdate(function (Blueprint $table): void {
            if ($this->hasColumn('user_id')) {
                $table->string('user_id', 36)->change();
            }
        });
    }
};
