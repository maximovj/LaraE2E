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
        $authUserWithOffice = auth()->user()->load('office');
        if(!isset($authUserWithOffice->office)) {
            return abort(404, "Lo siento, compañía no encontrada.");
        }

        $my_office = $authUserWithOffice->office;
        $employees = $my_office
        ->employees()
        ->withUserAndProfile()
        ->get();

        //dd( json_encode(EmployeeResource::collection($employees)->response()->getData(true) , JSON_PRETTY_PRINT));
        //dd(EmployeeResource::collection($employees)->toArray(request()));
        //dd(EmployeeResource::collection($employees)->toArray(request()));

        return EmployeeResource::collection($employees);

        $employees = collect([
            [
                'nombre' => 'Example',
                'apellido' => 'Example',
                'edad' => 20,
            ],
            [
                'nombre' => 'Example',
                'apellido' => 'Example',
                'edad' => 20,
            ]
        ]);

        return Inertia::render('Employees', [
            'employees' => $employees
        ]);
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        //
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
        //
    }
}
