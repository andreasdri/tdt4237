<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;

class Hash
{

    public function __construct()
    {
    }

    public static function make($plaintext)
    {
        return password_hash($plaintext, PASSWORD_BCRYPT);
    }

    public function check($plaintext, $hash)
    {
        return password_verify($plaintext, $hash);
    }

}
