<?php
// Vercel Serverless File System Fix (Harus dieksekusi sebelum Laravel boot)
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $tmpDir = '/tmp';
    $_SERVER['APP_CONFIG_CACHE'] = $tmpDir . '/config.php';
    $_SERVER['APP_EVENTS_CACHE'] = $tmpDir . '/events.php';
    $_SERVER['APP_PACKAGES_CACHE'] = $tmpDir . '/packages.php';
    $_SERVER['APP_ROUTES_CACHE'] = $tmpDir . '/routes.php';
    $_SERVER['APP_SERVICES_CACHE'] = $tmpDir . '/services.php';
    $_SERVER['VIEW_COMPILED_PATH'] = $tmpDir;
    $_SERVER['CACHE_PREFIX'] = 'tengganglopi_';

    // Buat direktori temp jika belum ada
    $dirs = [
        $tmpDir . '/storage/framework/views',
        $tmpDir . '/storage/framework/cache/data',
        $tmpDir . '/storage/framework/sessions',
        $tmpDir . '/storage/logs',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}

require __DIR__ . '/../public/index.php';
