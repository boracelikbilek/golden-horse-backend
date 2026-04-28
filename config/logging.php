<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;

$gh = require __DIR__.'/goldenhorse.php';

return [

    'default' => $gh['log']['channel'],

    'deprecations' => [
        'channel' => $gh['log']['deprecations'],
        'trace'   => false,
    ],

    'channels' => [

        'stack' => [
            'driver'            => 'stack',
            'channels'          => explode(',', $gh['log']['stack']),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver'               => 'single',
            'path'                 => storage_path('logs/laravel.log'),
            'level'                => $gh['log']['level'],
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver'               => 'daily',
            'path'                 => storage_path('logs/laravel.log'),
            'level'                => $gh['log']['level'],
            'days'                 => 14,
            'replace_placeholders' => true,
        ],

        'stderr' => [
            'driver'  => 'monolog',
            'level'   => $gh['log']['level'],
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver'               => 'syslog',
            'level'                => $gh['log']['level'],
            'facility'             => LOG_USER,
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver'               => 'errorlog',
            'level'                => $gh['log']['level'],
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];
