<?php

namespace App\Imports;

use App\Models\WorkActivity;
use App\Models\WorkDay;
use App\Models\WorkEvent;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WorkActivityImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithValidation
{

    private int $stop_row;
    public int $row_import;
    public int $row_failed;

    public function __construct()
    {
        $this->stop_row = 0;
        $this->row_import = 0;
        $this->row_failed = 0;
    }

    /**
     * The function checks if specific keys in an array are empty and returns a boolean value.
     *
     * @see https://docs.laravel-excel.com/3.1/imports/validation.html#extend-empty-rows-logic
     *
     * @param array row The `isEmptyWhen` function takes an array `` as a parameter. This array is
     * expected to have keys `'titulo'`, `'dia_trabajado'`, `'inicio'`, and `'finalizado'`. The
     * function checks if any of these keys have empty values and returns `true`
     *
     * @return bool The `isEmptyWhen` function is returning a boolean value. It returns `true` if any
     * of the specified keys in the `` array (`'titulo'`, `'dia_trabajado'`, `'inicio'`,
     * `'finalizado'`) are empty, otherwise it returns `false`.
     */
    public function isEmptyWhen(array $row): bool
    {
        return empty($row['titulo'])
        || empty($row['dia_trabajado'])
        || empty($row['inicio'])
        || empty($row['finalizado']);
    }

    /**
     * The function defines validation rules for a PHP array based on specified field requirements.
     *
     * @see https://docs.laravel-excel.com/3.1/imports/validation.html#validating-with-a-heading-row
     *
     * @return array An array containing validation rules for the fields 'titulo', 'subtitulo',
     * 'descripcion', 'notas', 'etiquetas', 'dia_trabajado', 'inicio', and 'finalizado'. The rules
     * include requirements such as 'required', 'nullable', 'sometimes', 'string', 'date_format:d/m/Y',
     * and 'date_format:H:i:s'.
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string'],
            'subtitulo' => ['nullable', 'sometimes', 'string'],
            'descripcion' => ['nullable', 'sometimes', 'string'],
            'notas' => ['nullable', 'sometimes', 'string'],
            'etiquetas' => ['nullable', 'sometimes', 'string'],
            'dia_trabajado' => ['required', 'date_format:d/m/Y'],
            'inicio' => ['required', 'date_format:H:i:s'],
            'finalizado' => ['required', 'date_format:H:i:s'],
        ];
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        $collection->chunk(500)->each(function ($chunk) {
            DB::transaction(function () use ($chunk) {
                foreach ($chunk as $data) {
                            $make_validator = Validator::make($data->toArray(), $this->rules());
                            //dd($data, $make_validator->validate(), $make_validator->passes());

                            if(!$make_validator->passes()) {
                                $this->row_failed++;
                                continue;
                            }

                            $now = Carbon::now();
                            $attr = $make_validator->validate();
                            $new_work_day_date = Carbon::createFromFormat('d/m/Y', $attr['dia_trabajado'])->tz('America/Mexico_City')->format('Y-m-d');
                            $new_work_activity_start_time = $attr['inicio'];
                            $new_work_activity_end_time = $attr['finalizado'];
                            $start = Carbon::createFromFormat('Y-m-d H:i:s', "{$new_work_day_date} {$new_work_activity_start_time}")->format('Y-m-d H:i:s');
                            $end = Carbon::createFromFormat('Y-m-d H:i:s', "{$new_work_day_date} {$new_work_activity_end_time}")->format('Y-m-d H:i:s');
                            //dd($new_work_day_date, $new_work_activity_start_time, $new_work_activity_end_time, $start, $end);

                            // Verifica si $end ya pasó Y si la diferencia es mayor o igual a 15 días
                            $endPassed_isEditable = ($now->diffInDays($end) >= 15) ? false : true;

                            if(!$endPassed_isEditable) {
                                $this->row_failed++;
                                continue;
                            }

                            // !! Crear un nuevo WorkDay (día trabajado)
                            // Crear o actualizar WorkDay
                            $employee = auth()->user()->employee;
                            $new_work_day = WorkDay::firstOrCreate(
                                [
                                    'employee_id' => $employee->id,
                                    'date' => $new_work_day_date
                                ],
                                [
                                    'status' => 'pending',
                                    'details' => null,
                                    'note' => null
                                ]
                            );

                            // !! Crear un nuevo WorkActivity (actividad del día)
                            $new_work_activity = new WorkActivity();
                            $new_work_activity->title = $data['titulo'];
                            $new_work_activity->subtitle = $data['subtitulo'];
                            $new_work_activity->description = $data['descripcion'];
                            $new_work_activity->tags = array_filter(array_map('trim', explode(',', $data['etiquetas'])));
                            $new_work_activity->employee_id = $employee->id;
                            $new_work_activity->work_day_id = $new_work_day->id;
                            $new_work_activity->start_time = $new_work_activity_start_time = Carbon::parse($data['inicio'])->tz('America/Mexico_City')->format('H:i:s');
                            $new_work_activity->end_time = $new_work_activity_end_time = Carbon::parse($data['finalizado'])->tz('America/Mexico_City')->format('H:i:s');
                            $new_work_activity->save();

                            // !! Crear un nuevo WorkEvent (evento para FullCalendar.js)
                            $new_work_event = new WorkEvent();
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

                            //dd('stop');
                            $this->stop_row++;
                            $this->row_import++;
                }
            });
        });
    }

}
