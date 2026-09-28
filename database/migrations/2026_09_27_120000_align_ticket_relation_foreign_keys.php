<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketComment;
use Modules\Fixcity\Models\TicketHour;
use Modules\Xot\Database\Migrations\XotBaseMigration;
use Modules\Xot\Datas\XotData;

return new class extends XotBaseMigration
{
    protected ?string $model_class = TicketComment::class;

    public function up(): void
    {
        $userClass = XotData::make()->getUserClass();

        foreach ([TicketComment::class, TicketHour::class] as $modelClass) {
            $model = new $modelClass;
            $schema = Schema::connection($model->getConnectionName());
            if (! $schema->hasTable($model->getTable())) {
                continue;
            }

            $schema->table($model->getTable(), static function (Blueprint $table) use ($userClass): void {
                $table->foreignIdFor(Ticket::class, 'ticket_id')->change();
                $table->foreignIdFor($userClass, 'user_id')->change();
            });
        }
    }

    public function down(): void
    {
        // Forward-only schema policy: old integer user keys cannot represent
        // the UUID identity contract and are intentionally not restored.
    }
};
