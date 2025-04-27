<?php

namespace App\Rules;

use App\Enums\WorkActivityStatus;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidStatus implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array($value, WorkActivityStatus::values(), true)) {
            $fail('The selected :attribute is invalid.');
        }
    }
}
