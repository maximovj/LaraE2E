<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    // Especificamos la tabla
    protected $table = 'user_profiles';

    // Especificamos el campo primario
    protected $primaryKey = 'id';

    // Especificamos el tipo de la clave primaria
    protected $keyType = 'int';

    // Especificamos los atributos que no pueden ser asignados masivamente
    protected $guarded = ['id'];

    // Especificamos los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'user_id',
        'profile_picture',
        'first_names',
        'last_names',
        'age',
        'birthdate',
        'blood_type',
        'marital_status',
        'address',
        'zip_code',
        'ssn',
        'bank',
        'interbank_clabe',
        'notes',
    ];

    // Especificamos los campos de tipo fecha que deben ser convertidos a instancias Carbon
    protected $dates = [
        'birthdate',
        'created_at',
        'updated_at',
    ];

    // Especificamos cómo los atributos deben ser casteados
    protected $casts = [
        'birthdate' => 'date',
    ];

    // Especificamos que atributos deben ser ocultos
    protected $hidden = [
        //'ssn',
        //'bank',
        //'interbank_clabe',
    ];

    // ******************************************************
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>> RELACIONES
    // ******************************************************

    // Definimos la relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
