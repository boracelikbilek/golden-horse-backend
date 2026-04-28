<?php

$gh = require __DIR__.'/goldenhorse.php';

return [

    'default' => $gh['filesystem']['disk'],

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app/private'),
            'serve'  => true,
            'throw'  => false,
            'report' => false,
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => rtrim($gh['app']['url'], '/').'/storage',
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
