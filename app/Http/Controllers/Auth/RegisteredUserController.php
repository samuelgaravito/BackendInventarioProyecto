<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => ['required', Rules\Password::defaults()],
            'role'     => 'required|exists:roles,name' 
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Spatie se encarga de insertar en 'model_has_roles'
        $user->assignRole($request->role);

        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'user'    => $user->load('roles') 
        ], 201);
    }
}