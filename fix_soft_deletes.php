<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $db = app('db');
    
    // Lista de tablas para añadir softDeletes
    $tables = ['seguridad_salud_trabajo', 'etapa_contractual', 'etapa_precontractual', 'empleados'];

    foreach ($tables as $table) {
        $cols = $db->select("SHOW COLUMNS FROM `$table` LIKE 'deleted_at'");
        if (count($cols) == 0) {
            echo "ALTER TABLE $table ADD deleted_at TIMESTAMP NULL; ... ";
            $db->statement("ALTER TABLE `$table` ADD deleted_at TIMESTAMP NULL;");
            echo "OK\n";
        } else {
            echo "$table ya posee deleted_at\n";
        }
    }
    
    // Marcar migración como ejecutada si es necesario, o solo dejarlo así.
    // Usar artisan migrate:install si no está instalada la tabla
    
    echo "PROCESO COMPLETADO\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
