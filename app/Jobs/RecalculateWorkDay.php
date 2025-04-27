<?php

namespace App\Jobs;

use App\Models\WorkDay;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateWorkDay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $workDayId;

    /**
     * Create a new job instance.
     */
    public function __construct($workDayId)
    {
        //
        $this->workDayId = $workDayId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $workDay = WorkDay::withCount('events')
                ->withSum('activities', 'duration_hours')
                ->find($this->workDayId);

        if ($workDay) {
            $workDay->update([
                'total_events' => $workDay->events_count,
                'total_hours' => $workDay->activities_sum_duration_hours,
                'amount' => $workDay->hourly_rate * $workDay->activities_sum_duration_hours
            ]);
        }
    }
}
