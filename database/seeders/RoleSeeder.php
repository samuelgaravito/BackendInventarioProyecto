<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Creamos los roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'usuario']);
        
        // Aquí podrías crear permisos en el futuro y asignarlos a los roles
    }
}