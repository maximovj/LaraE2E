<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkEvent extends Model
{
    use HasFactory;

    // Especificamos la tabla
    protected $table = 'work_events';

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
        'work_activity_id',
        'title',
        'allDay',
        'start',
        'end',
        'url',
        'classnames',
        'backgroundColor',
        'borderColor',
        'textColor',
        'overlap',
        'editable',
        'startEditable',
        'durationEditable',
        'display',
    ];

    // Especificamos los campos de tipo fecha que deben ser convertidos a instancias Carbon
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Especificamos cómo los atributos deben ser casteados
    protected $casts = [
        'allDay' => 'boolean',
        'start' => 'datetime',
        'end' => 'datetime',
        'overlap' => 'boolean',
        'editable' => 'boolean',
        'startEditable' => 'boolean',
        'durationEditable' => 'boolean',
        'display' => 'boolean',
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

    public function workActivity()
    {
        return $this->belongsTo(WorkActivity::class, 'work_activity_id');
    }

}
