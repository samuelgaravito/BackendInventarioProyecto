<?php

namespace App\Services\Fase3_RRHH;

use App\Models\Nomina;
use App\Models\Empleado;
use App\Services\AuditoriaService; // <-- IMPORTAMOS EL SERVICIO DE AUDITORÍA
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NominaService
{
   public function generarNomina(array $data)
    {
        $validator = Validator::make($data, [
            'id_empleado'     => 'required|exists:empleados,id',
            'dias_trabajados' => 'required|integer|min:1|max:30',
        ]);

        if ($validator->fails()) return ['status' => 400, 'errors' => $validator->errors()];

        // --- NUEVA VALIDACIÓN DE DUPLICADOS ---
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;

        $existeNomina = Nomina::where('id_empleado', $data['id_empleado'])
            ->whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $anioActual)
            ->exists();

        if ($existeNomina) {
            return [
                'status' => 400,
                'message' => 'Error: Ya se ha registrado la nómina de este empleado para el mes actual. Para realizar cambios, debe editar el registro existente.'
            ];
        }
        // ---------------------------------------

        return DB::transaction(function () use ($data) {
            $empleado = Empleado::with('cargo')->findOrFail($data['id_empleado']);
            $sueldo_mensual = $empleado->cargo->sueldo_base;
            
            $dias = $data['dias_trabajados'];
            $sueldo_ganado = ($sueldo_mensual / 30) * $dias;

            // Cálculos de ley
            $ivss = $sueldo_ganado * 0.04;
            $faov = $sueldo_ganado * 0.01;
            $paro_forzoso = $sueldo_ganado * 0.005;
            $cesta_ticket_mensual = 1400.00; 
            $cesta_ticket_recibir = ($cesta_ticket_mensual / 30) * $dias;

            $salario_quincenal = ($sueldo_ganado / 2) - ($ivss + $faov + $paro_forzoso);

            $nomina = Nomina::create([
                'id_empleado'          => $empleado->id,
                'fecha'                => now(),
                'dias_trabajados'      => $dias,
                'ivss'                 => $ivss,
                'faov'                 => $faov,
                'paro_forzoso'         => $paro_forzoso,
                'caja_ahorro'          => 0,
                'cesta_ticket_dia'     => $cesta_ticket_mensual / 30,
                'cesta_ticket_recibir' => $cesta_ticket_recibir,
                'salario_quincenal'    => $salario_quincenal,
                'salario_mensual'      => $sueldo_ganado,
            ]);

            // <-- REGISTRAMOS LA CREACIÓN EN LA AUDITORÍA
            AuditoriaService::registrar("Generó la nómina del empleado ID {$empleado->id} ({$empleado->nombre} {$empleado->apellido}) para el mes actual por {$dias} días trabajados");

            return [
                'status' => 201,
                'message' => 'Nómina generada con éxito',
                'data' => $nomina
            ];
        });
    }

    public function historial()
    {
        return [
            'status' => 200,
            'data' => Nomina::with('empleado.cargo')->latest()->get()
        ];
    }


    public function editarNomina(int $id, array $data)
    {
        $validator = Validator::make($data, [
            'dias_trabajados' => 'required|integer|min:1|max:30',
        ]);

        if ($validator->fails()) return ['status' => 400, 'errors' => $validator->errors()];

        return DB::transaction(function () use ($id, $data) {
            $nomina = Nomina::findOrFail($id);
            $empleado = Empleado::with('cargo')->findOrFail($nomina->id_empleado);
            
            $sueldo_mensual = $empleado->cargo->sueldo_base;
            $dias = $data['dias_trabajados'];
            
            // Recalcular todo
            $sueldo_ganado = ($sueldo_mensual / 30) * $dias;
            $ivss = $sueldo_ganado * 0.04;
            $faov = $sueldo_ganado * 0.01;
            $paro_forzoso = $sueldo_ganado * 0.005;
            
            $cesta_ticket_mensual = 1400.00; 
            $cesta_ticket_recibir = ($cesta_ticket_mensual / 30) * $dias;

            $salario_quincenal = ($sueldo_ganado / 2) - ($ivss + $faov + $paro_forzoso);

            // Actualizar registro
            $nomina->update([
                'dias_trabajados'      => $dias,
                'ivss'                 => $ivss,
                'faov'                 => $faov,
                'paro_forzoso'         => $paro_forzoso,
                'cesta_ticket_recibir' => $cesta_ticket_recibir,
                'salario_quincenal'    => $salario_quincenal,
                'salario_mensual'      => $sueldo_ganado,
            ]);

            // <-- REGISTRAMOS LA MODIFICACIÓN EN LA AUDITORÍA
            AuditoriaService::registrar("Modificó y recalculó la nómina ID {$id} correspondiente al empleado ID {$empleado->id}. Nuevos días trabajados: {$dias}");

            return [
                'status' => 200,
                'message' => 'Nómina actualizada y recalculada con éxito',
                'data' => $nomina
            ];
        });
    }
}