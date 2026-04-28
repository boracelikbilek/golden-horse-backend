<?php

use Illuminate\Support\Str;

$gh = require __DIR__.'/goldenhorse.php';

return [
    'driver'           => $gh['session']['driver'],
    'lifetime'         => (int) $gh['session']['lifetime'],
    'expire_on_close'  => false,
    'encrypt'          => $gh['session']['encrypt'],
    'files'            => storage_path('framework/sessions'),
    'connection'       => null,
    'table'            => 'sessions',
    'store'            => null,
    'lottery'          => [2, 100],
    'cookie'           => Str::slug($gh['app']['name']).'-session',
    'path'             => $gh['session']['path'],
    'domain'           => $gh['session']['domain'],
    'secure'           => null,
    'http_only'        => true,
    'same_site'        => $gh['session']['samesite'],
    'partitioned'      => false,
    'serialization'    => 'json',
];
