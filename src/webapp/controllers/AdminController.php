<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            $this->notAllowedAccess(true);
            return;
        }

        if (! $this->auth->isAdmin()) {
            $this->notAllowedAccess(false);
            return;
        }

        $variables = [
            'users' => $this->userRepository->all(),
            'posts' => $this->postRepository->all()
        ];
        $this->render('admin.twig', $variables);
    }

    public function delete($username)
    {
        if(!$this->auth->check()){ // Not logged in - no access
            $this->notAllowedAccess(true);
            return;
        }
        if(!$this->auth->isAdmin()){ // Not admin - no access
            $this->notAllowedAccess(false);
            return;
        }

        if ($this->userRepository->deleteByUsername($username) === 1) {
            $this->app->flash('info', "Sucessfully deleted '$username'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to delete user '$username'.");
        $this->app->redirect('/admin');
    }

    public function deletePost($postId)
    {

        if(!$this->auth->check()){ // Not logged in - no access
            $this->notAllowedAccess(false);
            return;
        }
        if(!$this->auth->isAdmin()){ // Not admin - no access
            $this->notAllowedAccess(true);
            return;
        }


        if ($this->postRepository->deleteByPostid($postId) === 1) {
            $this->app->flash('info', "Sucessfully deleted '$postId'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to delete user '$username'.");
        $this->app->redirect('/admin');


    }

    public function toggleDoctorStatus($username, $isDoctor)
    {
        if(!$this->auth->check())
        { // Not logged in - no access
            $this->notAllowedAccess(false);
            return;
        }

        if(!$this->auth->isAdmin())
        { // Not admin - no access
            $this->notAllowedAccess(true);
            return;
        }

        $request = $this->app->request;
        $user = $this->userRepository->findByUser($username);

        $user->setIsDoctor($isDoctor);
        $this->userRepository->save($user);

        return $this->app->redirect('/admin');
    }

    private function notAllowedAccess($isLoggedIn)
    {
        $errorMessage = "";

        if ($isLoggedIn) {
            $errorMessage = "You must be logged in to view the admin page.";
        }
        else {
            $errorMessage = "You must be administrator to view the admin page.";
        }

        $this->app->flash('info', $errorMessage);
        $this->app->redirect('/');
        return;
    }
}
