<?php

namespace App\Models;

use App\Casts\PointCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use HasFactory, SoftDeletes;

    // Especificamos la tabla
    protected $table = 'offices';

    // Especificamos el campo primario
    protected $primaryKey = 'id';

    // Especificamos el tipo de la clave primaria
    protected $keyType = 'int';

    // Especificamos los atributos que no pueden ser asignados masivamente
    protected $guarded = ['id'];

    // Especificamos los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'company_id',
        'name',
        'city',
        'state',
        'country',
        'address',
        'phone',
        'code_office',
        'zip_code',
        'status',
        'type',
        'coordinates',
        'business_hours',
    ];

    // Especificamos los campos de tipo fecha que deben ser convertidos a instancias Carbon
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',  // Campo de soft delete
    ];

    // Especificamos cómo los atributos deben ser casteados
    protected $casts = [
        'coordinates' => PointCast::class, // Para el campo 'coordinates', que es un tipo 'point' en la base de datos
        'business_hours' => 'array', // Cast a array para el campo JSON
    ];

    // ******************************************************
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>> RELACIONES
    // ******************************************************

    // Relación con la tabla 'companies' (una oficina pertenece a una empresa)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relación con la tabla 'employees' (una oficina puede tener muchos empleados)
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
