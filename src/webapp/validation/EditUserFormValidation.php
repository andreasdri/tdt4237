<?php

namespace tdt4237\webapp\validation;

class EditUserFormValidation
{
    private $validationErrors = [];

    public function __construct($email, $bio, $age, $bankcard, $token)
    {
        $this->validate($email, $bio, $age, $bankcard, $token);
    }

    public function isGoodToGo()
    {
        return count($this->validationErrors) === 0;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($email, $bio, $age, $bankcard, $token)
    {
        $this->validateEmail($email);
        $this->validateAge($age);
        $this->validateBio($bio);
        $this->validateBankcard($bankcard);
    }

    private function validateEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = "Invalid email format on email";
        }
    }

    private function validateAge($age)
    {
        if (! is_numeric($age) or $age < 0 or $age > 130) {
            $this->validationErrors[] = 'Age must be between 0 and 130.';
        }
    }

    private function validateBio($bio)
    {
        if (empty($bio)) {
            $this->validationErrors[] = 'Bio cannot be empty';
        }
    }

    private function validateBankcard($bankcard)
    {
        if( !(strlen($bankcard) == 0 || (strlen($bankcard) == 16 && ctype_digit($bankcard))) ){
            $this->validationErrors[] = 'Bank card number has to be exactly 16 integers long, or empty.';
        }
    }

    private function validateToken($token)
    {
        if (strcmp($token, $_SESSION['csrf_token']) !== 0) {
            $this->validationErrors[] = 'Token must be valid.';
        }
    }
}
