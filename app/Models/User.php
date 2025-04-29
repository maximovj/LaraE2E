<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasPermissions, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ******************************************************
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>> RELACIONES
    // ******************************************************

    public function user_profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }

    // Obtener una company
    // Relación a través de employees
    public function company()
    {
        // Relación One to One
        return $this->hasOneThrough(
            Company::class,
            Employee::class,
            'user_id', // desde modelo Employee
            'id', // desde modelo User (actual)
            'id', // desde modelo Company
            'company_id' // desde modelo Employee
        );
    }

    // Obtener una office
    // Relación a través de employees
    public function office()
    {
        // Relación One to One
        return $this->hasOneThrough(
            Office::class,
            Employee::class,
            'user_id', // desde modelo Employee
            'id', // desde modelo User (actual)
            'id', // desde modelo Office
            'office_id' // desde modelo Employee
        );
    }

}
