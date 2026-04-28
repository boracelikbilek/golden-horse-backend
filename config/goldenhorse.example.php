<?php

/*
|--------------------------------------------------------------------------
| Golden Horse Single Source of Truth (EXAMPLE)
|--------------------------------------------------------------------------
|
| Bu dosya bir sablondur. Sunucuya kurulumda:
|   1. Bu dosyayi `config/goldenhorse.php` olarak kopyala
|   2. APP_KEY'i `php artisan key:generate --show` ile uret ve yapistir
|   3. DB sifresini, domaini, mail bilgilerini kendi degerlerinle doldur
|   4. `php artisan config:cache` calistir
|
| `.env` dosyasi KULLANILMAZ — tum ayarlar config/*.php uzerinden okunur.
| Diger config/*.php dosyalari (app, database, session, vs.) bu dosyayi
| `require` ile okuyup kullanir.
|
*/

return [

    'app' => [
        'name'           => 'Golden Horse',
        'env'            => 'production',
        'debug'          => false,
        'url'            => 'https://CHANGE_ME.example.com',
        'key'            => 'base64:CHANGE_ME_USE_PHP_ARTISAN_KEY_GENERATE',
        'locale'         => 'tr',
        'fallback'       => 'en',
        'faker'          => 'tr_TR',
        'timezone'       => 'Europe/Istanbul',
        'maintenance'    => 'file',
        'default_tenant' => 'golden-horse',
    ],

    'log' => [
        'channel'      => 'stack',
        'stack'        => 'single',
        'level'        => 'warning',
        'deprecations' => 'null',
    ],

    'db' => [
        'driver'   => 'pgsql',
        'host'     => '127.0.0.1',
        'port'     => 5432,
        'database' => 'goldenhorse',
        'username' => 'goldenhorse',
        'password' => 'CHANGE_ME',
        'sslmode'  => 'prefer',
    ],

    'session' => [
        'driver'   => 'database',
        'lifetime' => 120,
        'encrypt'  => false,
        'path'     => '/',
        'domain'   => null,
        'samesite' => 'lax',
    ],

    'cache' => [
        'store' => 'database',
    ],

    'queue' => [
        'connection' => 'database',
    ],

    'broadcast' => [
        'connection' => 'log',
    ],

    'filesystem' => [
        'disk' => 'local',
    ],

    'mail' => [
        'mailer'       => 'log',
        'host'         => '127.0.0.1',
        'port'         => 2525,
        'username'     => null,
        'password'     => null,
        'scheme'       => null,
        'from_address' => 'hello@goldenhorse.coffee',
        'from_name'    => 'Golden Horse',
    ],

    'redis' => [
        'client'   => 'phpredis',
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'password' => null,
    ],

    'bcrypt' => [
        'rounds' => 12,
    ],

];
