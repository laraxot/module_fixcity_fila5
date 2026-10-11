<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
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
            if (! $this->hasColumn('user_id')) {
                $table->foreignIdFor($userClass, 'user_id');
            } elseif (! $this->isUuidColumnType($this->getColumnType('user_id'))) {
                $table->foreignIdFor($userClass, 'user_id')->change();
            }
        });
    }
};
