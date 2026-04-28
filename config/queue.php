<?php

$gh = require __DIR__.'/goldenhorse.php';

return [

    'default' => $gh['queue']['connection'],

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver'       => 'database',
            'connection'   => null,
            'table'        => 'jobs',
            'queue'        => 'default',
            'retry_after'  => 90,
            'after_commit' => false,
        ],

        'redis' => [
            'driver'       => 'redis',
            'connection'   => 'default',
            'queue'        => 'default',
            'retry_after'  => 90,
            'block_for'    => null,
            'after_commit' => false,
        ],

    ],

    'batching' => [
        'database' => $gh['db']['driver'],
        'table'    => 'job_batches',
    ],

    'failed' => [
        'driver'   => 'database-uuids',
        'database' => $gh['db']['driver'],
        'table'    => 'failed_jobs',
    ],

];
