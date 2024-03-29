<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;
use tdt4237\webapp\repository\UserRepository;

class RegistrationFormValidation extends Validator
{
    const MIN_USER_LENGTH = 3;
    private $validationErrors = [];

    public function __construct($username, $password, $fullname, $address, $postcode)
    {
        parent::__construct();
        return $this->validate($username, $password, $fullname, $address, $postcode);
    }

    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $fullname, $address, $postcode)
    {
        if (empty($password)) {
            $this->validationErrors[] = 'Password cannot be empty.';
        }

        if (strlen($password) < 10) {
            $this->validationErrors[] = 'Password must be at least 10 characters.';
        }

        if(empty($fullname)) {
            $this->validationErrors[] = "Please write in your full name.";
        }
        else if(strpos($password, $fullname) !== false) {
            $this->validationErrors[] = 'Password cannot contain full name.';
        }

        if(empty($address)) {
            $this->validationErrors[] = "Please write in your address.";
        }

        if(empty($postcode)) {
            $this->validationErrors[] = "Please write in your post code.";
        }

        if (strlen($postcode) != "4") {
            $this->validationErrors[] = "Post code must be exactly four digits.";
        }

        if(strlen($username) > 100 or strlen($username) < self::MIN_USER_LENGTH){
            $this->validationErrors[] = 'Username must be between 3 and 100 characters.';
        }
        else if (strpos($password, $username) !== false) {
            $this->validationErrors[] = 'Password cannot contain username or fullname.';
        }

        if($this->userRepository->findByUser($username)){
            $this->validationErrors[] = 'Username is already in use.';
        }

        if (preg_match('/^[A-Za-z0-9_]+$/', $username) === 0) {
            $this->validationErrors[] = 'Username can only contain letters and numbers.';
        }
    }
}
