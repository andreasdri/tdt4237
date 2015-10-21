<?php

namespace tdt4237\webapp\controllers;

class Controller
{
    protected $app;

    protected $userRepository;
    protected $auth;
    protected $postRepository;

    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
        $this->userRepository = $this->app->userRepository;
        $this->postRepository = $this->app->postRepository;
        $this->postRepository = $this->app->postRepository;
        $this->commentRepository = $this->app->commentRepository;
        $this->auth = $this->app->auth;
        $this->hash = $this->app->hash;
    }

    protected function render($template, $variables = [])
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }


        if ($this->auth->check()) {
            $variables['isLoggedIn'] = true;
            $variables['isAdmin'] = $this->auth->isAdmin();
            $variables['loggedInUsername'] = $_SESSION['user'];
        }

        $variables['csrf_token'] = $_SESSION['csrf_token'];
        print $this->app->render($template, $variables);
    }
}
