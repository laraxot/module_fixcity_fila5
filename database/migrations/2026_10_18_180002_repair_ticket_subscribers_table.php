<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/**
 * Restores the user-database table when an installation recorded the original
 * migration but the table was never created there.
 *
 * `TicketSubscriber` explicitly uses the `user` connection, so this repair
 * follows the model instead of accidentally creating the table in
 * `fixcity_data`. Existing rows and schemas are left untouched.
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
            $this->updateTimestamps($table, hasSoftDeletes: true);

            if (! $this->hasIndex('ticket_subscribers_ticket_user_unique')) {
                $table->unique(['ticket_id', 'user_id'], 'ticket_subscribers_ticket_user_unique');
            }
        });
    }

    /**
     * Repair migrations are forward-only: rolling back must not delete user data.
     */
    public function down(): void {}
};
