<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('User/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user_attr = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $new_user = new User();
        $new_user->fill($user_attr);
        $new_user->save();

        return redirect(null, 200)
            ->back()
            ->with('inertia_session', [
                'message' => 'Usuario creado',
                'data' => [
                    'user' => $new_user,
                ]
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
        // Validación similar al store pero ignorando el email único para este usuario
        $user_attr = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'sometimes', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Solo actualizar la contraseña si se proporcionó
        if (isset($user_attr['password'])) {
            $user_attr['password'] = Hash::make($user_attr['password']);
        } else {
            unset($user_attr['password']);
        }

        $user->fill($user_attr);
        $user->save();

        return redirect()
            ->back()
            ->with('inertia_session', [
                'message' => 'Usuario actualizado',
                'data' => [
                    'user' => $user->fresh(), // Para obtener los últimos datos
                ]
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
