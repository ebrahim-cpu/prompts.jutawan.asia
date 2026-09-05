<?php

require __DIR__.'/../../promtinglibabry/vendor/autoload.php';
$app = require_once __DIR__.'/../../promtinglibabry/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "<h1>Migration Status: " . ($exitCode === 0 ? "SUCCESS" : "FAILED (Code $exitCode)") . "</h1>";
    echo "<pre>" . htmlspecialchars(\Illuminate\Support\Facades\Artisan::output()) . "</pre>";
} catch (Exception $e) {
    echo "<h1>Migration Error</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . "</pre>";
}
