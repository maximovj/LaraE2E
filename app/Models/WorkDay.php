<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDay extends Model
{
    use HasFactory;

    // Especificamos la tabla
    protected $table = 'work_days';

    // Especificamos el campo primario
    protected $primaryKey = 'id';

    // Especificamos el tipo de la clave primaria
    protected $keyType = 'int';

    // Especificamos los atributos que no pueden ser asignados masivamente
    protected $guarded = ['id'];

    // Especificamos los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'employee_id',
        'date',
        'hourly_rate',
        'total_hours',
        'total_events',
        'amount',
        'billable',
        'status',
        'details',
        'tags',
        'note',
    ];

    // Especificamos los campos de tipo fecha que deben ser convertidos a instancias Carbon
    protected $dates = [
        'date',
        'created_at',
        'updated_at',
    ];

    // Especificamos cómo los atributos deben ser casteados
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'billable' => 'boolean',
        'total_hours' => 'decimal:2',
        'tags' => 'array',
    ];

    // Especificamos que atributos deben ser ocultos
    protected $hidden = [];

    // ******************************************************
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>> RELACIONES
    // ******************************************************

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function events()
    {
        return $this->hasMany(WorkEvent::class, 'work_day_id', 'id');
    }

}
