<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Xot\Database\Migrations\XotBaseMigration;
use Modules\Xot\Datas\XotData;

return new class extends XotBaseMigration
{
    protected ?string $model_class = TicketSubscriber::class;

    public function up(): void
    {
        $userClass = XotData::make()->getUserClass();

        $this->tableUpdate(function (Blueprint $table) use ($userClass): void {
            if (! $this->hasColumn('id')) {
                $table->id();
            }
            if (! $this->hasColumn('user_id')) {
                $table->foreignIdFor($userClass, 'user_id');
            }
            if (! $this->hasColumn('ticket_id')) {
                $table->unsignedBigInteger('ticket_id');
            }
            if (! $this->hasColumn('created_at')) {
                $table->timestamps();
            }
            if (! $this->hasColumn('deleted_at')) {
                $table->softDeletes();
            }
            if (! $this->hasIndex('ticket_id')) {
                $table->index('ticket_id');
            }
            if (! $this->hasIndex('user_id')) {
                $table->index('user_id');
            }
            if (! $this->getConn()->hasIndex($this->getTable(), 'ticket_subscribers_ticket_user_unique')) {
                $table->unique(['ticket_id', 'user_id'], 'ticket_subscribers_ticket_user_unique');
            }
        });

        $fixcity = Schema::connection('fixcity');
        if ($fixcity->hasTable('tickets') && $fixcity->hasColumn('tickets', 'longitude')) {
            $fixcity->table('tickets', static function (Blueprint $table): void {
                $table->decimal('longitude', 21, 18)->nullable()->change();
            });
        }
    }
};
