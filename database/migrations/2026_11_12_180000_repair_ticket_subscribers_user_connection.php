<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/**
 * Repair installations where the original migration was recorded but the
 * user-database table was never created.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = TicketSubscriber::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->id();
            $table->string('user_id', 36)->index();
            $table->unsignedBigInteger('ticket_id')->index();
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('user_id')) {
                $table->string('user_id', 36)->index();
            }
            if (! $this->hasColumn('ticket_id')) {
                $table->unsignedBigInteger('ticket_id')->index();
            }
            $this->updateTimestamps($table, hasSoftDeletes: true);

            if (! $this->hasIndex('ticket_subscribers_ticket_user_unique')) {
                $table->unique(['ticket_id', 'user_id'], 'ticket_subscribers_ticket_user_unique');
            }
        });
    }

    public function down(): void {}
};
