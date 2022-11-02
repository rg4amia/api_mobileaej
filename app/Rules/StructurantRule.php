<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StructurantRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return  in_array($value, range(20000001,50000000));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ce montant ne correspond pas au programmes structurants.';
    }
}
