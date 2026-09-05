<?php
header('Content-Type: text/plain');

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "Artisan view:clear executed:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n";
} catch (Exception $e) {
    echo "Artisan Error: " . $e->getMessage() . "\n";
}

// Manually delete compiled view files in storage/framework/views
$viewPath = __DIR__ . '/storage/framework/views';
if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            @unlink($file);
            $count++;
        }
    }
    echo "Manually deleted {$count} view files from {$viewPath}.\n";
} else {
    echo "Directory {$viewPath} not found.\n";
}
