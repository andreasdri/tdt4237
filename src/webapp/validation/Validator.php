<?php
/**
 * Created by IntelliJ IDEA.
 * User: Stein-Otto Svorstøl
 * Date: 28.09.15
 * Time: 23:23
 */

namespace tdt4237\webapp\validation;


class Validator
{
    protected $app;

    protected $userRepository;

    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
        $this->userRepository = $this->app->userRepository;
    }

}