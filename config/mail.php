<?php

$gh = require __DIR__.'/goldenhorse.php';

return [

    'default' => $gh['mail']['mailer'],

    'mailers' => [

        'smtp' => [
            'transport'    => 'smtp',
            'scheme'       => $gh['mail']['scheme'],
            'host'         => $gh['mail']['host'],
            'port'         => $gh['mail']['port'],
            'username'     => $gh['mail']['username'],
            'password'     => $gh['mail']['password'],
            'timeout'      => null,
            'local_domain' => parse_url($gh['app']['url'], PHP_URL_HOST),
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => '/usr/sbin/sendmail -bs -i',
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => null,
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport'   => 'failover',
            'mailers'     => ['smtp', 'log'],
            'retry_after' => 60,
        ],

    ],

    'from' => [
        'address' => $gh['mail']['from_address'],
        'name'    => $gh['mail']['from_name'],
    ],

];
