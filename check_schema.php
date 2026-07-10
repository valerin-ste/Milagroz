<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modelsPath = __DIR__ . '/app/Models';
$models = [];
foreach (glob($modelsPath . '/*.php') as $file) {
    $models[] = 'App\\Models\\' . basename($file, '.php');
}

$output = [];

foreach ($models as $modelClass) {
    if (!class_exists($modelClass)) continue;

    $reflection = new ReflectionClass($modelClass);
    if ($reflection->isAbstract()) continue;

    try {
        $model = new $modelClass();
        $table = $model->getTable();
        $fillable = $model->getFillable();

        // Get columns from Schema
        if (Illuminate\Support\Facades\Schema::hasTable($table)) {
            $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
            $missingInDb = array_diff($fillable, $columns);
            
            if (!empty($missingInDb)) {
                $output[] = "Table: {$table} (Model: {$modelClass}) is missing columns: " . implode(', ', $missingInDb);
            }
        } else {
            $output[] = "Table: {$table} (Model: {$modelClass}) DOES NOT EXIST in DB!";
        }
    } catch (\Throwable $e) {
        $output[] = "Error on {$modelClass}: " . $e->getMessage();
    }
}

if (empty($output)) {
    echo "All fillable columns exist in their respective tables.\n";
} else {
    echo implode("\n", $output) . "\n";
}
