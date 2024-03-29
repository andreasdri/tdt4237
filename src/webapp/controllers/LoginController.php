<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\repository\UserRepository;

class LoginController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->check()) {
            $username = $this->auth->user()->getUsername();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
            return;
        }

        $this->render('login.twig', []);
    }

    public function login()
    {
        $request = $this->app->request;
        $user    = $request->post('user');
        $pass    = $request->post('pass');
        $token   = $request->post('csrf_token');

        if (strcmp($token, $_SESSION['csrf_token']) == 0 and $this->auth->checkCredentials($user, $pass)) {
            session_regenerate_id();
            $_SESSION['user'] = $user;
            setcookie("user", $user);

            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
            return;
        }

        $this->app->flashNow('error', 'Incorrect user/pass combination.');
        $this->render('login.twig', []);
    }
}
