<?php

namespace App\Models;

use App\Traits\WithUserProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    // Traits Personalizadas
    use WithUserProfile;

    // Especificamos la tabla
    protected $table = 'employees';

    // Especificamos el campo primario
    protected $primaryKey = 'id';

    // Especificamos el tipo de la clave primaria
    protected $keyType = 'int';

    // Especificamos los atributos que no pueden ser asignados masivamente
    protected $guarded = ['id'];

    // Especificamos los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'user_id',
        'company_id',
        'office_id',
        'manager_id',
        'employee_number',
        'job_title',
        'position',
        'hired_at',
        'status',
        'salary',
        'shift',
        'emergency_contact',
    ];

    // Especificamos los campos de tipo fecha que deben ser convertidos a instancias Carbon
    protected $dates = [
        'hired_at',
        'created_at',
        'updated_at',
        'deleted_at',  // Campo de soft delete
    ];

    // Especificamos cómo los atributos deben ser casteados
    protected $casts = [
        'salary' => 'decimal:2', // Casteo del salario a decimal
    ];

    // ******************************************************
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>> RELACIONES
    // ******************************************************

    // Relación con la tabla 'users' (un empleado puede estar asociado a un usuario)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la tabla 'companies' (un empleado está asociado a una empresa)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relación con la tabla 'offices' (un empleado está asociado a una oficina)
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    // Relación con la tabla 'users' para el 'manager_id' (un empleado puede tener un jefe)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

}
