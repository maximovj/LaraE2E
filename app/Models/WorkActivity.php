<?php

namespace App\Models;

use App\Casts\TimeCast;
use App\Enums\WorkActivityStatus;
use App\Jobs\RecalculateWorkDay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkActivity extends Model
{
    use HasFactory;

    // Especificamos la tabla
    protected $table = 'work_activities';

    // Especificamos el campo primario
    protected $primaryKey = 'id';

    // Especificamos el tipo de la clave primaria
    protected $keyType = 'int';

    // Especificamos los atributos que no pueden ser asignados masivamente
    protected $guarded = ['id'];

    // Especificamos los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'employee_id',
        'work_day_id',
        'title',
        'subtitle',
        'description',
        'start_time',
        'end_time',
        'duration_hours',
        'notes',
        'tags',
        'status',
    ];

    // Especificamos los campos de tipo fecha que deben ser convertidos a instancias Carbon
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Especificamos cómo los atributos deben ser casteados
    protected $casts = [
        'status' => WorkActivityStatus::class,
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    // Especificamos que atributos deben ser ocultos
    protected $hidden = [];

    public static function getStatusOptions(): array
    {
        return ['pending', 'approved', 'rejected', 'paid', 'unpaid'];
    }

    // ******************************************************
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>> RELACIONES
    // ******************************************************

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function work_day()
    {
        return $this->belongsTo(WorkDay::class);
    }

    public function work_event()
    {
        return $this->hasOne(WorkEvent::class);
    }

    // ******************************************************
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>> BOOT
    // ******************************************************

    protected static function booted()
    {
        static::created(function ($workActivity) {
            // Usar dispatchSync para ejecución inmediata
            RecalculateWorkDay::dispatch($workActivity->work_day_id)
                ->onQueue('low');
        });

        static::updated(function ($workActivity) {
            if ($workActivity->isDirty('duration_hours')) {
                // Usar dispatchSync para ejecución inmediata
                RecalculateWorkDay::dispatch($workActivity->work_day_id)
                    ->onQueue('low');
            }
        });

        static::deleted(function ($workActivity) {
            // Usar dispatchSync para ejecución inmediata
            RecalculateWorkDay::dispatch($workActivity->work_day_id)
                ->onQueue('low');
        });
    }

}
