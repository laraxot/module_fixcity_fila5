<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\User\Datas\UserData;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * Schema di `users`. Il modello è `Modules\Fixcity\Models\User`:
 * XotBaseMigration lo ricava da `create_users_table` e dal modulo del file.
 * Le colonne, incluso il discriminante Parental `type`, stanno in UserData.
 */
return new class extends XotBaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // -- CREATE --
        $this->tableCreate(
            function (Blueprint $table): void {
                UserData::createColumns($table, $this);
            }
        );
        // -- UPDATE --
        $this->tableUpdate(
            function (Blueprint $table): void {
                UserData::updateColumns($table, $this);
            }
        );
    }
};
