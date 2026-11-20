<?php

declare(strict_types=1);

/**
 * Vercel Serverless Entrypoint for Laravel 12.
 *
 * This bridge configures the writable /tmp environment required by
 * AWS Lambda / Vercel Serverless runtimes before booting Laravel.
 */

// 1. Ensure required runtime storage directories exist in /tmp
$tmpStorageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/private',
    '/tmp/storage/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($tmpStorageDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Point framework storage and cache paths to writable /tmp
putenv('LARAVEL_STORAGE_PATH=/tmp/storage');
$_ENV['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
$_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';

putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';

putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes-v7.php');
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes-v7.php';

putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';

// 3. Fallback for SQLite database if DB_CONNECTION is sqlite (zero-config preview / demo)
$dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? null);
if (! $dbConnection || $dbConnection === 'sqlite') {
    $targetSqlite = '/tmp/database.sqlite';
    $sourceSqlite = __DIR__.'/../database/database.sqlite';
    if (! file_exists($targetSqlite) && file_exists($sourceSqlite)) {
        @copy($sourceSqlite, $targetSqlite);
    }
    if (file_exists($targetSqlite)) {
        putenv("DB_DATABASE={$targetSqlite}");
        $_ENV['DB_DATABASE'] = $targetSqlite;
        $_SERVER['DB_DATABASE'] = $targetSqlite;
    }
}

// 4. Normalize SCRIPT_NAME so Symfony router matches root URI properly
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__).'/public/index.php';

// 5. Forward request to Laravel public front-controller
require __DIR__.'/../public/index.php';
