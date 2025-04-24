<?php

namespace App\Models;

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
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
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

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

}
