<?php

namespace App\Casts;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PointCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        preg_match('/POINT\(([-\d\.]+) ([-\d\.]+)\)/', $value, $matches);
        return ['lat' => $matches[2], 'lng' => $matches[1]];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return DB::raw("ST_GeomFromText('POINT({$value['lng']} {$value['lat']})', 4326)");
    }
}
