<?php

// app/Traits/WithUserProfile.php
namespace App\Traits;

trait WithUserProfile
{
    public function scopeWithUserAndProfile($query)
    {
        return $query
        ->with([
            'user:id,name,email',
            'user.roles:name',
            'user.user_profile:id,user_id,profile_picture,first_names,last_names,age,birthdate,marital_status',
        ]);
        //->whereHas('user.user_profile');
    }
}
