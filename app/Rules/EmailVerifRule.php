<?php

namespace App\Rules;

use App\Helpers\VerifyEmail;
use Illuminate\Contracts\Validation\Rule;

class EmailVerifRule implements Rule
{
    public $verifmail;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->verifmail = new VerifyEmail();
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
        if ($this->verifmail->check($value)) {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Le format de mail est invalid';
    }
}
