<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use LogicException;
use Modules\User\Models\Role;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;

/**
 * Grants the Fixcity demo operator panel access and the matching domain role.
 *
 * Panel access and ticket capabilities are intentionally separate roles.
 */
final class DemoOperatorPanelAccessSeeder extends Seeder
{
    private const string OPERATOR_EMAIL = 'operatore@fixcity.demo';

    private const string OPERATOR_ROLE = 'operator';

    private const string PANEL_ROLE = 'fixcity::admin';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing', 'demo'])) {
            return;
        }

        $userClass = XotData::make()->getUserClass();
        $operator = $userClass::query()->where('email', self::OPERATOR_EMAIL)->first();

        if ($operator === null) {
            if ($this->command !== null) {
                $this->command->warn('DemoOperatorPanelAccessSeeder: demo operator missing; no roles assigned.');
            }

            return;
        }

        if (! $operator instanceof UserContract) {
            throw new LogicException('The configured user model must implement UserContract.');
        }

        Role::firstOrCreate([
            'name' => self::OPERATOR_ROLE,
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => self::PANEL_ROLE,
            'guard_name' => 'web',
        ]);

        $operator->assignRole(self::OPERATOR_ROLE);
        $operator->assignRole(self::PANEL_ROLE);

        if ($this->command !== null) {
            $this->command->info('DemoOperatorPanelAccessSeeder: least-privilege operator panel access aligned.');
        }
    }
}
