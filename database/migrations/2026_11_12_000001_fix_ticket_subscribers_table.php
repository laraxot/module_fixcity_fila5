<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Class 2026_11_12_000001_fix_ticket_subscribers_table
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = TicketSubscriber::class;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the ticket_subscribers table if it doesn't exist
        $this->tableCreate(
            static function (Blueprint $table): void {
                $table->id();
                $table->string('user_id', 36)->index();
                $table->unsignedBigInteger('ticket_id')->index();
                $table->unique(['ticket_id', 'user_id'], 'ticket_subscribers_ticket_user_unique');
                $this->updateTimestamps(table: $table, hasSoftDeletes: true);
            }
        );
    }
};