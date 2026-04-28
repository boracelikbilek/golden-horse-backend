<?php

use Illuminate\Support\Str;

$gh = require __DIR__.'/goldenhorse.php';

return [

    'default' => $gh['cache']['store'],

    'stores' => [

        'array' => [
            'driver'    => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver'          => 'database',
            'connection'      => null,
            'table'           => 'cache',
            'lock_connection' => null,
            'lock_table'      => null,
        ],

        'file' => [
            'driver'    => 'file',
            'path'      => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'redis' => [
            'driver'          => 'redis',
            'connection'      => 'cache',
            'lock_connection' => 'default',
        ],

    ],

    'prefix'              => Str::slug($gh['app']['name']).'-cache-',
    'serializable_classes' => false,

];
