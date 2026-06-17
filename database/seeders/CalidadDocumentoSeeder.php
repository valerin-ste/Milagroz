<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalidadDocumento;
use App\Models\Empleado;
use Carbon\Carbon;

class CalidadDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener empleados activos
        $empleados = Empleado::where('estado', 1)->pluck('id')->toArray();

        if (empty($empleados)) {
            $this->command->warn('⚠️  No hay empleados activos. El seeder de CalidadDocumento no insertará datos.');
            return;
        }

        $registros = [
            [
                'categoria'         => 'Manual',
                'nombre_documento'  => 'Manual de Calidad del Sistema de Gestión',
                'codigo'            => 'MAN-CAL-001',
                'version'           => 'v3.0',
                'fecha_emision'     => '2024-01-15',
                'fecha_vencimiento' => '2026-01-15',
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Procedimiento',
                'nombre_documento'  => 'Procedimiento de Control de Documentos',
                'codigo'            => 'PRO-CAL-002',
                'version'           => 'v2.1',
                'fecha_emision'     => '2024-03-01',
                'fecha_vencimiento' => '2025-06-01',
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Política',
                'nombre_documento'  => 'Política de Gestión de la Calidad',
                'codigo'            => 'POL-CAL-001',
                'version'           => 'v1.5',
                'fecha_emision'     => '2023-07-20',
                'fecha_vencimiento' => Carbon::today()->addDays(15)->format('Y-m-d'), // próximo a vencer
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Instructivo',
                'nombre_documento'  => 'Instructivo de Uso de Equipos de Medición',
                'codigo'            => 'INS-CAL-003',
                'version'           => 'v1.0',
                'fecha_emision'     => '2024-06-10',
                'fecha_vencimiento' => Carbon::today()->addDays(8)->format('Y-m-d'), // próximo a vencer
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Formato',
                'nombre_documento'  => 'Formato de Registro de No Conformidades',
                'codigo'            => 'FOR-CAL-005',
                'version'           => 'v2.0',
                'fecha_emision'     => '2024-08-01',
                'fecha_vencimiento' => '2026-08-01',
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Normativa',
                'nombre_documento'  => 'Normativa ISO 9001:2015 Aplicada',
                'codigo'            => 'NOR-ISO-001',
                'version'           => 'v4.2',
                'fecha_emision'     => '2022-11-05',
                'fecha_vencimiento' => '2024-11-05', // vencido
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Protocolo',
                'nombre_documento'  => 'Protocolo de Auditoría Interna de Calidad',
                'codigo'            => 'PRO-AUD-001',
                'version'           => 'v1.3',
                'fecha_emision'     => '2024-02-14',
                'fecha_vencimiento' => '2026-02-14',
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Certificado',
                'nombre_documento'  => 'Certificado de Conformidad de Producto',
                'codigo'            => 'CER-PRO-002',
                'version'           => 'v1.0',
                'fecha_emision'     => '2025-01-10',
                'fecha_vencimiento' => Carbon::today()->addDays(20)->format('Y-m-d'), // próximo a vencer
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Registro',
                'nombre_documento'  => 'Registro de Capacitaciones en Calidad',
                'codigo'            => 'REG-CAP-004',
                'version'           => 'v2.3',
                'fecha_emision'     => '2024-05-05',
                'fecha_vencimiento' => null,
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Procedimiento',
                'nombre_documento'  => 'Procedimiento de Acciones Correctivas',
                'codigo'            => 'PRO-ACC-003',
                'version'           => 'v3.1',
                'fecha_emision'     => '2023-09-18',
                'fecha_vencimiento' => '2024-09-18', // vencido
                'estado'            => 0,
            ],
            [
                'categoria'         => 'Manual',
                'nombre_documento'  => 'Manual de Procedimientos Operativos Estándar',
                'codigo'            => 'MAN-POE-002',
                'version'           => 'v1.8',
                'fecha_emision'     => '2024-10-01',
                'fecha_vencimiento' => '2027-10-01',
                'estado'            => 1,
            ],
            [
                'categoria'         => 'Otro',
                'nombre_documento'  => 'Informe de Revisión por la Dirección 2025',
                'codigo'            => 'INF-DIR-001',
                'version'           => 'v1.0',
                'fecha_emision'     => '2025-03-28',
                'fecha_vencimiento' => null,
                'estado'            => 1,
            ],
        ];

        foreach ($registros as $index => $data) {
            // Asignar empleado de forma rotativa
            $empleadoId = $empleados[$index % count($empleados)];

            CalidadDocumento::create([
                'empleado_id'       => $empleadoId,
                'categoria'         => $data['categoria'],
                'nombre_documento'  => $data['nombre_documento'],
                'codigo'            => $data['codigo'],
                'version'           => $data['version'],
                'fecha_emision'     => $data['fecha_emision'],
                'fecha_vencimiento' => $data['fecha_vencimiento'],
                'archivo' => 'calidad/documento-demo.pdf',
                'estado'            => $data['estado'],
            ]);
        }

        $this->command->info('✅  CalidadDocumentoSeeder: ' . count($registros) . ' registros insertados.');
    }
}
