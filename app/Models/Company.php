<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

}

