<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Post;

class PostValidation {

    private $validationErrors = [];

    public function __construct($author, $title, $content, $token, $missingBankAccountWhenNeeded) {
        return $this->validate($author, $title, $content, $token, $missingBankAccountWhenNeeded);
    }

    public function isGoodToGo()
    {
        return \count($this->validationErrors) ===0;
    }

    public function getValidationErrors()
    {
    return $this->validationErrors;
    }

    public function validate($author, $title, $content, $token, $missingBankAccountWhenNeeded)
    {
        if ($author == null) {
            $this->validationErrors[] = "Author needed";

        }
        if ($title == null) {
            $this->validationErrors[] = "Title needed";
        }

        if ($content == null) {
            $this->validationErrors[] = "Text needed";
        }

        if (strcmp($token, $_SESSION['csrf_token']) !== 0) {
            $this->validationErrors[] = "Token must be valid";
        }

        if ($missingBankAccountWhenNeeded) {
          $this->validationErrors[] = "You must add a bank account to pay";
        }

        return $this->validationErrors;
    }


}
