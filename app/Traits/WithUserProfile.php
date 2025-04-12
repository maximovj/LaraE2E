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
            'user.user_profile:id,user_id,first_names,last_names',
        ]);
    }
}
