<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = ['seguridad_salud_trabajo', 'etapa_contractual', 'etapa_precontractual', 'areas', 'sedes', 'roles', 'personas', 'empleados'];

foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $cols = DB::select("DESCRIBE $table");
    foreach ($cols as $col) {
        if ($col->Field == 'estado') {
            print_r($col);
        }
    }
    echo "------------------\n";
}
