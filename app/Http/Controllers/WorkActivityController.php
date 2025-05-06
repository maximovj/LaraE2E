<?php

namespace App\Http\Controllers;

use App\Enums\WorkActivityStatus;
use App\Imports\WorkActivityImport;
use App\Models\WorkActivity;
use App\Models\WorkDay;
use App\Models\WorkEvent;
use App\Rules\ValidStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class WorkActivityController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', WorkActivity::class);
        /*
        $employee = auth()->user()->load('employee')->employee;
        $work_days = WorkDay::query()->where('employee_id', $employee->id)->with('events')->get();
        $all_events = $work_days->pluck('events')->collapse();
        */
        $employee = auth()->user()->employee;

        $all_events = WorkActivity::with('work_event', 'work_day')
            ->where('employee_id', $employee->id)
            ->get()
            ->pluck('work_event')
            ->filter()
            ->push([
                'title' => 'Nuevo',
                'start' => now(),
                'end' => now(),
            ]);

        return Inertia::render('WorkActivity/Index', [
            'events' => array_values($all_events->toArray()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $this->authorize('create', WorkActivity::class);
        $employee = auth()->user()->employee;

        // Hace 15 días desde hoy
        $startOfLast15Days = Carbon::now()->subDays(15);

        $work_days = WorkDay::query()
        ->where('employee_id', $employee->id)
        ->where('status', 'pending')
        ->where('date', '<', $startOfLast15Days) // Fechas anteriores a los últimos 15 días
        ->get(['id', 'date'])
        ->pluck('date')
        ->toArray();

        return Inertia::render('WorkActivity/Create', [
            'work_days' => $work_days,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', WorkActivity::class);
        $work_activity_attr = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:255',
            'tags' => 'sometimes|nullable|array|max:5',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'duration_hours' => 'required|numeric',
            'notes' => 'sometimes|nullable|string',
            'status' => ['required',
                new ValidStatus()
                //new Enum(WorkActivityStatus::class)
            ],
            'work_day.details' => 'nullable|string',
            'work_day.note' => 'nullable|string',
            'work_day.date' => 'required|date',
        ]);

        $employee = auth()->user()->employee;
        $new_work_day_date = Carbon::parse($work_activity_attr['work_day']['date'])->tz('America/Mexico_City')->format('Y-m-d');

        // !! Crear un nuevo WorkDay (día trabajado)
        // Crear o actualizar WorkDay
        $new_work_day = WorkDay::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $new_work_day_date
            ],
            [
                'status' => $work_activity_attr['status'],
                'details' => $work_activity_attr['work_day']['details'],
                'note' => $work_activity_attr['work_day']['note']
            ]
        );

        // !! Crear un nuevo WorkActivity (actividad del día)
        $new_work_activity = new WorkActivity();
        $new_work_activity->fill($work_activity_attr);
        $new_work_activity->employee_id = $employee->id;
        $new_work_activity->work_day_id = $new_work_day->id;
        $new_work_activity->start_time = $new_work_activity_start_time = Carbon::parse($work_activity_attr['start_time'])->tz('America/Mexico_City')->format('H:i:s');
        $new_work_activity->end_time = $new_work_activity_end_time = Carbon::parse($work_activity_attr['end_time'])->tz('America/Mexico_City')->format('H:i:s');
        $new_work_activity->save();

        // !! Crear un nuevo WorkEvent (evento para FullCalendar.js)
        $new_work_event = new WorkEvent();

        $now = Carbon::now();
        $start = Carbon::createFromFormat('Y-m-d H:i:s', "{$new_work_day_date} {$new_work_activity_start_time}")->format('Y-m-d H:i:s');
        $end = Carbon::createFromFormat('Y-m-d H:i:s', "{$new_work_day_date} {$new_work_activity_end_time}")->format('Y-m-d H:i:s');
        // Verifica si $end ya pasó Y si la diferencia es mayor o igual a 15 días
        $endPassed_isEditable = ($now->diffInDays($end) >= 15) ? false : true;

        $new_work_event->title = $new_work_activity->title;
        $new_work_event->employee_id = $employee->id;
        $new_work_event->work_day_id = $new_work_day->id;
        $new_work_event->work_activity_id = $new_work_activity->id;
        $new_work_event->backgroundColor = '';
        $new_work_event->borderColor = '';
        $new_work_event->overlap = $endPassed_isEditable;
        $new_work_event->editable = $endPassed_isEditable;
        $new_work_event->startEditable = $endPassed_isEditable;
        $new_work_event->durationEditable = $endPassed_isEditable;
        $new_work_event->start = $start;
        $new_work_event->end = $end;
        $new_work_event->save();

        //dd($request->all(), $new_work_day, $new_work_activity, $new_work_event);

        return redirect()
        ->route('work-activities.index')
        ->with('inertia_session', [
            'success' => 'Actividad creado correctamente',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkActivity $workActivity)
    {
        //
        $this->authorize('view', $workActivity);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkActivity $workActivity)
    {
        //dd($workActivity->load(['work_day', 'work_event']));
        return Inertia::render('WorkActivity/Edit', [
            'work_activity' => $workActivity->load(['work_day', 'work_event']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkActivity $workActivity)
    {
        //
        $this->authorize('update', $workActivity);
        $work_activity_attr = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'tags' => 'sometimes|nullable|array|max:5',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'duration_hours' => 'required|numeric',
            'notes' => 'sometimes|nullable|string',
            'status' => ['required',
                new ValidStatus()
                //new Enum(WorkActivityStatus::class)
            ],
            'work_day.details' => 'nullable|string',
            'work_day.note' => 'nullable|string',
            'work_day.date' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($workActivity, $work_activity_attr) {
                $employee = auth()->user()->employee;
                $new_work_day = $workActivity->work_day;
                $new_work_day_date = Carbon::parse($work_activity_attr['work_day']['date'])->tz('America/Mexico_City')->format('Y-m-d');

                // !! Actualizar un WorkActivity (actividad del día)
                $new_work_activity = $workActivity;
                $new_work_activity->fill($work_activity_attr);
                $new_work_activity->employee_id = $employee->id;
                $new_work_activity->work_day_id = $new_work_day->id;
                $new_work_activity->start_time = $new_work_activity_start_time = Carbon::parse($work_activity_attr['start_time'])->tz('America/Mexico_City')->format('H:i:s');
                $new_work_activity->end_time = $new_work_activity_end_time = Carbon::parse($work_activity_attr['end_time'])->tz('America/Mexico_City')->format('H:i:s');
                $new_work_activity->save();

                // !! Actualizar un WorkEvent (evento para FullCalendar.js)
                $new_work_event = $workActivity->work_event;

                $now = Carbon::now();
                $start = Carbon::createFromFormat('Y-m-d H:i:s', "{$new_work_day_date} {$new_work_activity_start_time}")->format('Y-m-d H:i:s');
                $end = Carbon::createFromFormat('Y-m-d H:i:s', "{$new_work_day_date} {$new_work_activity_end_time}")->format('Y-m-d H:i:s');
                // Verifica si $end ya pasó Y si la diferencia es mayor o igual a 15 días
                $endPassed_isEditable = ($now->diffInDays($end) >= 15) ? false : true;

                $new_work_event->title = $new_work_activity->title;
                $new_work_event->employee_id = $employee->id;
                $new_work_event->work_day_id = $new_work_day->id;
                $new_work_event->work_activity_id = $new_work_activity->id;
                $new_work_event->backgroundColor = '';
                $new_work_event->borderColor = '';
                $new_work_event->overlap = $endPassed_isEditable;
                $new_work_event->editable = $endPassed_isEditable;
                $new_work_event->startEditable = $endPassed_isEditable;
                $new_work_event->durationEditable = $endPassed_isEditable;
                $new_work_event->start = $start;
                $new_work_event->end = $end;
                $new_work_event->save();
            });

            return redirect()
                ->route('work-activities.index')
                ->with('inertia_session', [
                    'success' => 'Actividad modificado correctamente',
                ]);
        } catch (Exception $e) {
            //throw $th;
            return back()->withErrors('inertia_session', [
                'error' => 'Actividad no modificado, hubo un error en el proceso',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkActivity $workActivity)
    {
        //
        $this->authorize('delete', $workActivity);
        //$workActivity->work_event->delete();
        $workActivity->delete();

        return redirect()
        ->route('work-activities.index')
        ->with('inertia_session', [
            'success' => 'Actividad eliminado correctamente',
        ]);
    }

    /**
     * Show the form for import resources from Excel.
     */
    public function import_activities()
    {
        return Inertia::render('WorkActivity/ImportActivities');
    }

    /**
     * Import resources from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls|max:1024',
        ]);

        try {
            $file = $request->file('excelFile');
            $path = $file->store('uploads/excel');

            $import_activities = new WorkActivityImport();
            Excel::import($import_activities, $path);

            return back()->with('inertia_session', [
                'ctx_title' => 'Actividad de trabajo',
                'ctx_message' => 'Empleado modificado correctamente',
                'type' => 'success',
                'data' => [
                    'path' => $path,
                ]
            ]);
        }  catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }

            return back()->with('inertia_session', [
                'ctx_title' => 'Actividad de trabajo',
                'ctx_message' => 'Error al subir el archivo',
                'type' => 'error',
                'data' => []
            ]);
        }
    }

}
