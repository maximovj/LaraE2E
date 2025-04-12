<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWithProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'profile' => [
                'profile_picture' => optional($this->user_profile)->profile_picture,
                'first_names' => optional($this->user_profile)->first_names,
                'last_names' => optional($this->user_profile)->last_names,
                'age' => optional($this->user_profile)->age,
                'birthdate' => optional($this->user_profile)->birthdate,
                'marital_status' => optional($this->user_profile)->marital_status,
                //'blood_type' => optional($this->user_profile)->blood_type,
                //'address' => optional($this->user_profile)->address,
                //'zip_code' => optional($this->user_profile)->zip_code,
            ]
        ];
    }
}
