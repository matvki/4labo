<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidUserLogin extends Constraint
{
    public $message = 'Invalid credentials.';

    public function validatedBy(): string
    {
        return 'valid_user_login'; // Alias du validateur
    }
}
 
