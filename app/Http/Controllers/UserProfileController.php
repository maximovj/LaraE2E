<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user_profile_attr = $request->validate([
            'first_names' => ['required', 'string', 'max:255'],
            'last_names' => ['required', 'string', 'max:255'],
            'age' => ['required', 'numeric'],
            'birthdate' => ['required', 'date'],
            'blood_type' => ['required', 'string', 'max:10'],
            'marital_status.code' => ['required', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:255'],
            'ssn' => ['required', 'string', 'max:255'],
            'bank' => ['required', 'string', 'max:255'],
            'interbank_clabe' => ['required', 'string', 'max:255'],
        ]);

        $new_user_profile = new UserProfile();
        $user_profile_attr['marital_status'] = $user_profile_attr['marital_status']['code'];
        $new_user_profile->fill($user_profile_attr);

        $user_id = intval(request()->input('user.id'));
        $new_user_profile->user_id = $user_id;

        $new_user_profile->save();

        return redirect()
        ->back()
        ->with('inertia_session', [
            'message' => 'Perfil de usuario creado exitosamente',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserProfile $userProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserProfile $userProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserProfile $userProfile)
    {
        //
        // Validar los datos de entrada (igual que en store)
        $user_profile_attr = $request->validate([
            'first_names' => ['required', 'string', 'max:255'],
            'last_names' => ['required', 'string', 'max:255'],
            'age' => ['required', 'numeric'],
            'birthdate' => ['required', 'date'],
            'blood_type' => ['required', 'string', 'max:10'],
            'marital_status.code' => ['required', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'zip_code' => ['required', 'string', 'max:255'],
            'ssn' => ['required', 'string', 'max:255'],
            'bank' => ['required', 'string', 'max:255'],
            'interbank_clabe' => ['required', 'string', 'max:255'],
        ]);

        // Procesar marital_status igual que en store
        $user_profile_attr['marital_status'] = $user_profile_attr['marital_status']['code'];

        // Actualizar el perfil existente en lugar de crear uno nuevo
        $userProfile->update($user_profile_attr);

        return redirect()
            ->back()
            ->with('inertia_session', [
                'message' => 'Perfil de usuario actualizado exitosamente',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserProfile $userProfile)
    {
        //
    }
}
