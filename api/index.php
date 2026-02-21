<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    
    $_ENV['APP_ENV'] = 'production';
    $_SERVER['APP_ENV'] = 'production';
    putenv('APP_ENV=production');

    if (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
        $scheme = 'https://';
        $url = $scheme . $host;

        $_ENV['APP_URL'] = $url;
        $_SERVER['APP_URL'] = $url;
        putenv("APP_URL={$url}");

        $currentStateful = env('SANCTUM_STATEFUL_DOMAINS', '');
        $newStateful = $currentStateful . ',' . $host;
        $_ENV['SANCTUM_STATEFUL_DOMAINS'] = $newStateful;
        $_SERVER['SANCTUM_STATEFUL_DOMAINS'] = $newStateful;
        putenv("SANCTUM_STATEFUL_DOMAINS={$newStateful}");
    }

    $dbUrl = $_ENV['MYC_POSTGRES_URL'] ?? $_SERVER['MYC_POSTGRES_URL'] ?? 
             $_ENV['MYC_POSTGRES_PRISMA_URL'] ?? $_SERVER['MYC_POSTGRES_PRISMA_URL'] ?? null;

    if ($dbUrl) {
        $parsed = parse_url($dbUrl);
        if ($parsed) {
            $map = [
                'DB_CONNECTION' => 'pgsql',
                'DB_HOST' => $parsed['host'] ?? null,
                'DB_PORT' => $parsed['port'] ?? 5432,
                'DB_DATABASE' => ltrim($parsed['path'] ?? 'postgres', '/'),
                'DB_USERNAME' => $parsed['user'] ?? null,
                'DB_PASSWORD' => $parsed['pass'] ?? null,
            ];

            foreach ($map as $key => $val) {
                if ($val) {
                    $_ENV[$key] = $val;
                    $_SERVER[$key] = $val;
                    putenv("{$key}={$val}");
                }
            }
        }
    }

    $storagePath = '/tmp/storage';
    $bootstrapPath = '/tmp/bootstrap';
    
    $dirs = [
        $storagePath . '/framework/views',
        $storagePath . '/framework/sessions',
        $storagePath . '/framework/cache',
        $storagePath . '/logs',
        $bootstrapPath . '/cache',
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    $app->useStoragePath($storagePath);
    $app->useBootstrapPath($bootstrapPath);
    
    $app['events']->listen('bootstrapped: Illuminate\Foundation\Bootstrap\LoadConfiguration', function ($app) use ($storagePath) {
        $config = $app['config'];
        
        $config->set('view.compiled', $storagePath . '/framework/views');
        $config->set('session.files', $storagePath . '/framework/sessions');
        $config->set('cache.stores.file.path', $storagePath . '/framework/cache');
        
        if (in_array($config->get('logging.default'), ['stack', 'single', 'daily'])) {
             $config->set('logging.default', 'stderr');
        }
    });
}

$app->handleRequest(Request::capture());
