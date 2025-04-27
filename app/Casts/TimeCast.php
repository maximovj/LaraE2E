<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class TimeCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // get para devolver un objeto Carbon
        return $value ? \Carbon\Carbon::createFromFormat('H:i:s', $value) : null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set($model, $key, $value, $attributes)
    {
        // Valor vacío
        if (empty($value)) {
            return null;
        }

        // Si ya es un formato HH:MM:SS
        if (is_string($value) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            return $value;
        }

        // Si es un objeto DateTime
        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i:s');
        }

        // Si es una cadena ISO (como 2025-04-27T02:38:13.297Z)
        if (is_string($value)) {
            try {
                // Intenta con formato con milisegundos y timezone
                if ($date = \DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $value)) {
                    return $date->format('H:i:s');
                }

                // Intenta con formato sin milisegundos
                if ($date = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $value)) {
                    return $date->format('H:i:s');
                }

                throw new InvalidArgumentException("Formato de fecha/hora no válido: {$value}");
            } catch (\Exception $e) {
                throw new InvalidArgumentException("Formato de fecha/hora no válido: {$value}");
            }
        }

        throw new InvalidArgumentException("Valor no válido para conversión a TIME: ".gettype($value));
    }

}
