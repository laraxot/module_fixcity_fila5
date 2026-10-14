<?php

declare(strict_types=1);

use Illuminate\Support\Arr;

$database_name = (config('env.DB_DATABASE') ?? 'database');
$database_name = is_string($database_name) ? $database_name : 'database';
$user_database_name = (config('env.DB_DATABASE_USER') ?? 'fixcity_user');
$user_database_name = is_string($user_database_name) ? $user_database_name : 'fixcity_user';

$res = [
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => (config('env.DB_HOST') ?? '127.0.0.1'),
            'port' => (config('env.DB_PORT') ?? '3306'),
            'database' => (config('env.DB_DATABASE') ?? 'forge84'),
            'username' => (config('env.DB_USERNAME') ?? 'forge_mysql_25_1'),
            'password' => (config('env.DB_PASSWORD') ?? ''),
            // 'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => config('env.DB_URL'),
            'host' => (config('env.DB_HOST') ?? '127.0.0.1'),
            'port' => (config('env.DB_PORT') ?? '3306'),
            'database' => (config('env.DB_DATABASE') ?? 'laravel'),
            'username' => (config('env.DB_USERNAME') ?? 'root'),
            'password' => (config('env.DB_PASSWORD') ?? ''),
            'unix_socket' => (config('env.DB_SOCKET') ?? ''),
            'charset' => (config('env.DB_CHARSET') ?? 'utf8mb4'),
            // 'collation' => (config('env.DB_COLLATION') ?? 'utf8mb4_uca1400_ai_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => config('env.MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => config('env.DATABASE_URL'),
            'database' => database_path($database_name.'.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => (config('env.DB_FOREIGN_KEYS') ?? true),
        ],

        'user_sqlite' => [
            'driver' => 'sqlite',
            'url' => config('env.DATABASE_URL'),
            'database' => database_path($user_database_name.'.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => (config('env.DB_FOREIGN_KEYS') ?? true),
        ],

        'user_mysql' => [
            'driver' => (config('env.DB_CONNECTION') ?? 'mysql'),
            'host' => (config('env.DB_HOST') ?? '127.0.0.1'),
            'port' => (config('env.DB_PORT') ?? '3306'),
            // 'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock',
            'database' => (config('env.DB_DATABASE_USER') ?? 'forge86'),
            'username' => (config('env.DB_USERNAME_USER') ?? 'forge_user_04_1b'),
            'password' => (config('env.DB_PASSWORD_USER') ?? ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'user_mariadb' => [
            'driver' => (config('env.DB_CONNECTION') ?? 'mysql'),
            'host' => (config('env.DB_HOST') ?? '127.0.0.1'),
            'port' => (config('env.DB_PORT') ?? '3306'),
            // 'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock',
            'database' => (config('env.DB_DATABASE_USER') ?? 'forge86'),
            'username' => (config('env.DB_USERNAME_USER') ?? 'forge_user_02_1'),
            'password' => (config('env.DB_PASSWORD_USER') ?? ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        // 'orbit' => [
        //     'driver' => 'sqlite',
        //     // "database" => Orbit::getDatabasePath(),
        //     // "database" => storage_path('framework/cache/orbit/orbit.sqlite'),
        //     // "database" => config('orbit.paths.cache') . DIRECTORY_SEPARATOR . 'orbit.sqlite',
        //     // "database" => '/var/www/html/_bases/base_siram_idoteca_fila5/laravel/config/local/idoteca/orbit.sqlite',
        //     'database' => app(\Modules\Tenant\Actions\Config\GetTenantFilePathAction::class)->execute('orbit.sqlite'),
        //     'foreign_key_constraints' => false,
        // ],
        // 'orbit_meta' => [
        //     'driver' => 'sqlite',
        //     // "database" => Orbit::getMetaDatabasePath(),
        //     'database' => storage_path('framework/cache/orbit/orbit_meta.sqlite'),
        //     'foreign_key_constraints' => false,
        // ],
    ],
];

$database_default = config('database.default');
$database_connection = is_string($database_default) ? $database_default : 'mysql';
Arr::set($res, 'connections.user', Arr::get($res, 'connections.user_'.$database_connection));

return $res;
