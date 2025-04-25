<?php

namespace App\Http\Controllers;

use App\Models\WorkActivity;
use App\Models\WorkDay;
use Illuminate\Http\Request;
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
