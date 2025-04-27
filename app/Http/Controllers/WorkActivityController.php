<?php

namespace App\Http\Controllers;

use App\Enums\WorkActivityStatus;
use App\Models\WorkActivity;
use App\Models\WorkDay;
use App\Models\WorkEvent;
use App\Rules\ValidStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class WorkActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        $work_activity_attr = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'duration_hours' => 'required|numeric',
            'notes' => 'required|string|max:255',
            'status' => ['required',
                new ValidStatus()
                //new Enum(WorkActivityStatus::class)
            ],
            'work_day.details' => 'nullable|string',
            'work_day.note' => 'nullable|string',
        ]);

        $employee = auth()->user()->employee;
        $new_work_day_date = Carbon::parse($work_activity_attr['start_time'])->format('Y-m-d');

        // !! Crear un nuevo WorkDay (día trabajado)
        $new_work_day = WorkDay::firstOrNew([
            'employee_id' => $employee->id,
            'date' => $new_work_day_date
        ]);

        // Solo actualizar estos campos si es un registro nuevo
        if (!$new_work_day->exists) {
            $new_work_day->employee_id = $employee->id;
            $new_work_day->date = $new_work_day_date;
            //$new_work_day->duration_hours = '';
            //$new_work_day->hourly_rate = '';
            //$new_work_day->total_events = '';
            //$new_work_day->amount = '';
            //$new_work_day->billable = '';
            $new_work_day->status = $work_activity_attr['status'];
            $new_work_day->details = $work_activity_attr['work_day']['details'];
            $new_work_day->note = $work_activity_attr['work_day']['note'];
        }else
        // Solo actualizar estos campos si es un registro, YA EXISTE!!
        if ($new_work_day->exists) {
            $new_work_day_events = $new_work_day->events;
            $new_work_day_activities = $new_work_day->activities;
            $total_hours = $new_work_day_activities->sum('duration_hours') + intval($work_activity_attr['duration_hours']);
            $new_work_day->total_events = $new_work_day_events->count() + 1;
            $new_work_day->total_hours =  $total_hours;
            $new_work_day->amount = $new_work_day->hourly_rate * $total_hours;
        }

        $new_work_day->save();

        // !! Crear un nuevo WorkActivity (actividad del día)
        $new_work_activity = new WorkActivity();
        $new_work_activity->fill($work_activity_attr);
        $new_work_activity->employee_id = $employee->id;
        $new_work_activity->work_day_id = $new_work_day->id;
        $new_work_activity->start_time = $new_work_activity_start_time = Carbon::parse($work_activity_attr['start_time'])->format('H:i:s');
        $new_work_activity->end_time = $new_work_activity_end_time = Carbon::parse($work_activity_attr['end_time'])->format('H:i:s');
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

        dd($work_activity_attr, $new_work_day, $new_work_activity, $new_work_event);
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkActivity $workActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkActivity $workActivity)
    {
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkActivity $workActivity)
    {
        //
    }
}
