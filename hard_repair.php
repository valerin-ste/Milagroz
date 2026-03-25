<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$db = app('db');
$db->statement('SET FOREIGN_KEY_CHECKS=0;');

$tables = ['seguridad_salud_trabajo', 'etapa_contractual', 'etapa_precontractual', 'areas', 'sedes', 'roles', 'personas', 'empleados'];

foreach ($tables as $table) {
    echo "PROCESANDO $table...\n";
    
    // 1. Asegurar que tenemos la columna
    $cols = $db->select("SHOW COLUMNS FROM `$table` LIKE 'estado'");
    if (count($cols) == 0) {
        $db->statement("ALTER TABLE `$table` ADD estado VARCHAR(50) DEFAULT '1'");
        echo " - Columna añadida.\n";
    }

    // 2. Mapear valores string a '1' o '0' como strings primero para evitar truncado al convertir
    $db->statement("UPDATE `$table` SET estado = '1' WHERE estado IN ('activo', 'Activo', 'aprobado', 'en_proceso', '1', 'true', 'active')");
    $db->statement("UPDATE `$table` SET estado = '0' WHERE estado IN ('inactivo', 'Inactivo', 'rechazado', '0', 'false', 'inactive')");
    
    // 3. Forzar conversión a TINYINT(1)
    echo " - Convirtiendo a TINYINT(1)... ";
    try {
        $db->statement("ALTER TABLE `$table` MODIFY COLUMN estado TINYINT(1) DEFAULT 1");
        echo "EXITO\n";
    } catch (Exception $e) {
        echo "FALLO: " . $e->getMessage() . "\n";
    }

    // 4. Asegurar que todos los NULL o valores raros sean 1
    $db->statement("UPDATE `$table` SET estado = 1 WHERE estado IS NULL OR estado NOT IN (0, 1)");
}

$db->statement('SET FOREIGN_KEY_CHECKS=1;');
echo "\nSISTEMA REPARADO.\n";
