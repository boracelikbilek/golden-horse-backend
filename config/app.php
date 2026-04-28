<?php

$gh = require __DIR__.'/goldenhorse.php';

return [
    'name'            => $gh['app']['name'],
    'env'             => $gh['app']['env'],
    'debug'           => (bool) $gh['app']['debug'],
    'url'             => $gh['app']['url'],
    'timezone'        => $gh['app']['timezone'],
    'locale'          => $gh['app']['locale'],
    'fallback_locale' => $gh['app']['fallback'],
    'faker_locale'    => $gh['app']['faker'],
    'cipher'          => 'AES-256-CBC',
    'key'             => $gh['app']['key'],
    'previous_keys'   => [],
    'maintenance' => [
        'driver' => $gh['app']['maintenance'],
        'store'  => 'database',
    ],
];
