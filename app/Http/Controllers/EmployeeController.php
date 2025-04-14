<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        return response()->json([
            'ctx_message' => 'Empleado'
        ], 200);
    }
}
