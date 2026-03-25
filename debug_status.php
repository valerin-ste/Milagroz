<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "SEDES:\n";
$sedes = DB::table('sedes')->get();
foreach($sedes as $s) {
    echo "ID: {$s->id} | NOMBRE: {$s->nombre} | ESTADO: '" . $s->estado . "' (TYPE: " . gettype($s->estado) . ")\n";
}

echo "\nROLES:\n";
$roles = DB::table('roles')->get();
foreach($roles as $r) {
    echo "ID: {$r->id} | NOMBRE: {$r->nombre} | ESTADO: '" . $r->estado . "'\n";
}
