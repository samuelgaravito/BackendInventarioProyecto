<?php

namespace Database\Seeders;

use App\Models\FormaPago;
use App\Models\TipoMovimiento;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // Importamos el modelo de Spatie

class ParametrosInicialesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Roles usando el modelo de Spatie
        // Se recomienda definir el guard_name (usualmente 'api' o 'web')
       $rolAdmin = Role::firstOrCreate(['name' => 'admin']);
       $rolVendedor = Role::firstOrCreate(['name' => 'vendedor']);
        // 2. Crear Usuario Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('admin123'),
            ]
        );

        // 3. Asignar Rol con el método de Spatie
        // assignRole se encarga de todo el manejo en la base de datos
        $admin->assignRole($rolAdmin);

        // 4. Formas de Pago
        $formas = ['Efectivo', 'Transferencia', 'Punto de Venta', 'Pago Móvil'];
        foreach ($formas as $forma) {
            FormaPago::firstOrCreate(['descripcion' => $forma]);
        }

        // 5. Tipos de Movimiento
        TipoMovimiento::firstOrCreate(['descripcion' => 'Entrada por Compra']); // ID 1
        TipoMovimiento::firstOrCreate(['descripcion' => 'Salida por Venta']);   // ID 2

        // 6. Cliente Genérico
        Cliente::firstOrCreate(
            ['cedula' => '99999999'],
            [
                'nombre' => 'Consumidor Final',
                'telefono' => '00000000000',
                'direccion' => 'Ciudad'
            ]
        );

        // 7. Producto de Prueba
        Producto::firstOrCreate(
            ['referencia' => 'TEST-001'],
            [
                'descripcion' => 'Producto de Prueba',
                'costo_unitario' => 10.00,
                'precio_venta' => 15.00,
                'existencia' => 50,
                'saldo' => 500.00
            ]
        );
    }
}