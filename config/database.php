<?php

use Illuminate\Support\Str;

$gh = require __DIR__.'/goldenhorse.php';

return [

    'default' => $gh['db']['driver'],

    'connections' => [

        'sqlite' => [
            'driver'                  => 'sqlite',
            'database'                => database_path('database.sqlite'),
            'prefix'                  => '',
            'foreign_key_constraints' => true,
            'busy_timeout'            => null,
            'journal_mode'            => null,
            'synchronous'             => null,
            'transaction_mode'        => 'DEFERRED',
        ],

        'pgsql' => [
            'driver'         => 'pgsql',
            'host'           => $gh['db']['host'],
            'port'           => $gh['db']['port'],
            'database'       => $gh['db']['database'],
            'username'       => $gh['db']['username'],
            'password'       => $gh['db']['password'],
            'charset'        => 'utf8',
            'prefix'         => '',
            'prefix_indexes' => true,
            'search_path'    => 'public',
            'sslmode'        => $gh['db']['sslmode'],
        ],

    ],

    'migrations' => [
        'table'                  => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [
        'client'  => $gh['redis']['client'],
        'options' => [
            'cluster'    => 'redis',
            'prefix'     => $gh['redis']['prefix'] ?? 'gh:',
            'persistent' => false,
        ],
        'default' => [
            'host'     => $gh['redis']['host'],
            'username' => null,
            'password' => $gh['redis']['password'],
            'port'     => $gh['redis']['port'],
            'database' => $gh['redis']['default_db'] ?? 0,
        ],
        'cache' => [
            'host'     => $gh['redis']['host'],
            'username' => null,
            'password' => $gh['redis']['password'],
            'port'     => $gh['redis']['port'],
            'database' => $gh['redis']['cache_db'] ?? 1,
        ],
    ],

];
