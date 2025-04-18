<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $authUserWithOffice */
        $authUserWithOffice = auth()->user()->load('office');

        if(!isset($authUserWithOffice->office)) {
            return abort(404, "Lo siento, compañía no encontrada.");
        }

        $my_office = $authUserWithOffice->office;
        $employees = $my_office
        ->employees()
        ->withUserAndProfile()
        ->get();

        return Inertia::render('Employee/Index', [
            'employees' => $employees
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('Employee/Create', []);
    }

    public function user(Employee $employee)
    {
        return Inertia::render('Employee/User', [
            'action' =>  !isset($employee->user) ? 'users.store' : 'users.update',
            'user' => $employee->user ?? null,
            'employee' => $employee ?? null,
        ]);
    }

    public function user_profile(Employee $employee)
    {
        return Inertia::render('Employee/UserProfile', [
            'user' => $employee->user ?? null,
            'user_profile' => isset($employee->user->user_profile) ? $employee->user->user_profile : null,
            'employee' => $employee ?? null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $active_step = intval(request('active_step'));

        if($active_step === 1) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            return response()->noContent(200);
        }

        if($active_step === 2) {
            $request->validate([
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
            return response()->noContent(200);
        }

        if($active_step === 3) {
            $employee_attr = $request->validate([
                'employee_number' => ['required', 'string', 'max:255'],
                'job_title' => ['required', 'string', 'max:255'],
                'position' => ['required', 'string', 'max:255'],
                'hired_at' => ['required', 'date'],
                'salary' => ['required', 'numeric'],
                'shift' => ['required', 'string', 'max:255'],
                'emergency_contact' => ['required', 'string', 'max:255'],
            ]);

            /** @var \App\Models\User $authUserWithCompany */
            $authUserWithCompany = auth()->user()->load('company');
            /** @var \App\Models\User $authUserWithOffice */
            $authUserWithOffice = auth()->user()->load('office');

            $employee = new Employee();
            $employee->fill($employee_attr);
            $employee->status = 'active';
            $employee->company_id = $authUserWithCompany->company->id;
            $employee->office_id = $authUserWithOffice->office->id;
            $employee->save();

            return redirect()
            ->route('employees.index')
            ->with('inertia_session', [
                'success' => 'Empleado creado correctamente',
            ]);
        }

        return response()->noContent(200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
        // TODO: Crear una vista vue para mostrar información del empleado
        dd('Ver un empleado', $employee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
        // TODO: Crear una vista vue para editar información del empleado
        dd('Editar un empleado', $employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
        $user_id = intval(request()->input('user.id'));

        $employee->update([
            'user_id' => $user_id ?? null,
        ]);

        return redirect()
        ->back()
        ->with('inertia_session', [
            'success' => 'Empleado modificado correctamente',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
        ->back()
        ->with('inertia_session', [
            'success' => 'Empleado eliminado correctamente',
        ]);
    }
}
