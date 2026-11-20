<?php

declare(strict_types=1);

/**
 * Vercel Serverless Entrypoint for Laravel 12.
 *
 * This bridge configures the writable /tmp environment required by
 * AWS Lambda / Vercel Serverless runtimes before booting Laravel.
 */
try {
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

    // 2. Ensure APP_KEY has a valid fallback if omitted in Vercel settings
    $currentAppKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? null);
    if (! $currentAppKey) {
        $defaultKey = 'base64:dYvYWbuT65vrIMpzfUy1UCv4F6akVfvBX4dKVSrK0Eo=';
        putenv("APP_KEY={$defaultKey}");
        $_ENV['APP_KEY'] = $defaultKey;
        $_SERVER['APP_KEY'] = $defaultKey;
    }

    // 3. Point framework storage and cache paths to writable /tmp
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

    // 4. Fallback for SQLite database if DB_CONNECTION is sqlite (zero-config preview / demo)
    $dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
    if ($dbConnection === 'sqlite') {
        $targetSqlite = '/tmp/database.sqlite';
        $templateSqlite = __DIR__.'/../database/template.sqlite';
        $sourceSqlite = __DIR__.'/../database/database.sqlite';

        if (! file_exists($targetSqlite)) {
            if (file_exists($templateSqlite)) {
                @copy($templateSqlite, $targetSqlite);
            } elseif (file_exists($sourceSqlite)) {
                @copy($sourceSqlite, $targetSqlite);
            } else {
                @touch($targetSqlite);
            }
        }

        putenv("DB_DATABASE={$targetSqlite}");
        $_ENV['DB_DATABASE'] = $targetSqlite;
        $_SERVER['DB_DATABASE'] = $targetSqlite;
    }

    // 5. Normalize SCRIPT_NAME so Symfony router matches root URI properly
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__).'/public/index.php';

    // 6. Forward request to Laravel public front-controller
    require __DIR__.'/../public/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Startup Error</title><style>body{font-family:sans-serif;padding:30px;line-height:1.5;background:#f8fafc;color:#0f172a}pre{background:#fff;border:1px solid #e2e8f0;padding:16px;border-radius:8px;overflow-x:auto}</style></head><body>';
    echo '<h2>Serverless Application Startup Error</h2>';
    echo '<p><strong>Error:</strong> '.htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8').'</p>';
    echo '<p><strong>File:</strong> '.htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8').' (Line '.$e->getLine().')</p>';
    echo '<h3>Stack Trace</h3>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8').'</pre>';
    echo '</body></html>';
    error_log((string) $e);
}
