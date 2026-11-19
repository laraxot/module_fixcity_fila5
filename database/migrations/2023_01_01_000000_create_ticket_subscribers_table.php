<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Class .
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = TicketSubscriber::class;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // -- CREATE --
        $this->tableCreate(
            static function (Blueprint $table): void {
                $table->id();
                // User lives on the separate `user` connection and uses UUID/string IDs.
                $table->string('user_id', 36)->index();
                $table->unsignedBigInteger('ticket_id')->index();
            }
        );
        // -- UPDATE --
        $this->tableUpdate(
            function (Blueprint $table): void {
                if (! $this->hasColumn('id')) {
                    $table->id();
                }
                if (! $this->hasColumn('user_id')) {
                    $table->string('user_id', 36)->index();
                } elseif (! $this->isUuidColumnType($this->getColumnType('user_id'))) {
                    $table->string('user_id', 36)->change();
                }
                if (! $this->hasColumn('ticket_id')) {
                    $table->unsignedBigInteger('ticket_id')->index();
                }
                if (! $this->hasIndex('user_id')) {
                    $table->index('user_id');
                }
                if (! $this->hasIndex('ticket_id')) {
                    $table->index('ticket_id');
                }
                if (! $this->hasIndex('ticket_subscribers_ticket_user_unique')) {
                    $table->unique(['ticket_id', 'user_id'], 'ticket_subscribers_ticket_user_unique');
                }
                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );
    }
};
