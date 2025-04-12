<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    // Especificamos la tabla
    protected $table = 'companies';

    // Especificamos el campo primario
    protected $primaryKey = 'id';

    // Especificamos el tipo de la clave primaria
    protected $keyType = 'int';

    // Especificamos los atributos que no pueden ser asignados masivamente
    protected $guarded = ['id'];

    // Especificamos los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'legal_name',
        'tax_id',
        'commercial_name',
        'corporate_email_domain',
    ];

    // Especificamos los campos de tipo fecha que deben ser convertidos a instancias Carbon
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',  // Campo de soft delete
    ];

    // Especificamos cómo los atributos deben ser casteados
    protected $casts = [];

    // ******************************************************
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>> RELACIONES
    // ******************************************************

    public function offices()
    {
        return $this->hasMany(Office::class, 'company_id', 'id');
    }

    // Relación directa si users tienen company_id
    public function directUsers()
    {
        return $this->hasMany(User::class);
    }

    // Relación a través de employees
    public function employeeUsers()
    {
        return $this->hasManyThrough(
            User::class,
            Employee::class,
            'company_id',
            'id',
            'id',
            'user_id'
        );
    }

    // Método combinado para obtener todos los usuarios relacionados
    public function getAllUsers()
    {
        // Si existe relación directa
        if (Schema::hasColumn('users', 'company_id'))
        {
            return $this->directUsers();
        }

        // Si existe relación a través de employees
        return $this->employeeUsers();
    }

}

