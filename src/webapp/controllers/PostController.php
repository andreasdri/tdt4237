<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Post;
use tdt4237\webapp\controllers\UserController;
use tdt4237\webapp\models\Comment;
use tdt4237\webapp\validation\PostValidation;

class PostController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        if($this->auth->user()->isDoctor()){
            $posts = $this->postRepository->doctorPosts();
        }
        else {
            $posts = $this->postRepository->all();
        }

        $posts->sortByDate();
        $this->render('posts.twig', ['posts' => $posts]);
    }

    public function show($postId)
    {
        if(!$this->auth->check()){
            $this->app->flash('info', 'You have to be logged in to view a post.');
            $this->app->redirect('/login');
        }
        $post = $this->postRepository->find($postId);
        $comments = $this->commentRepository->findByPostId($postId);
        $request = $this->app->request;
        $message = $request->get('msg');
        $variables = [];

        if($message) {
            $variables['msg'] = $message;

        }

        $this->render('showpost.twig', [
            'post' => $post,
            'comments' => $comments,
            'flash' => $variables
        ]);

    }

    public function addComment($postId)
    {

        if ($this->auth->guest()) {
            $this->app->redirect('/login');
            $this->app->flash('info', 'you must log in to do that');
        }
        else {
            $author = $_SESSION['user'];
            $text = $this->app->request->post("text");
            $token = $this->app->request->post("csrf_token");

            $validation = new PostValidation('title', $author, $text, $token, false);
            if ($validation->isGoodToGo()) {
                $comment = new Comment();
                $comment->setAuthor($author);
                $comment->setText($text);
                $comment->setDate(date("dmY"));
                $comment->setPost($postId);
                $this->commentRepository->save($comment);
                $this->app->redirect('/posts/' . $postId);
            }

        }
        $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
        $this->app->render('createpost.twig');



    }

    public function showNewPostForm()
    {

        if ($this->auth->check()) {
            $username = $_SESSION['user'];
            $this->render('createpost.twig', ['username' => $username]);
        } else {

            $this->app->flash('error', "You need to be logged in to create a post");
            $this->app->redirect("/");
        }

    }

    public function create()
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged in to create a post");
            $this->app->redirect("/login");
        } else {
            $request = $this->app->request;
            $title = $request->post('title');
            $content = $request->post('content');
            $token = $request->post('csrf_token');
            $payed = $request->post('ispayedpost');
            $author = $this->auth->user()->getUsername(); // Username of logged in user
            $date = date("dmY");

            $missingBankAccountWhenNeeded = $payed == '1' && $this->auth->user()->getBankcard() == '';
            $validation = new PostValidation($title, $author, $content, $token, $missingBankAccountWhenNeeded);

            if ($validation->isGoodToGo()) {
                $post = new Post();
                $post->setAuthor($author);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $post->setIsPayedPost($payed);
                $savedPost = $this->postRepository->save($post);
                $this->app->redirect('/posts/' . $savedPost . '?msg=Post succesfully posted');
            }
        }

            $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
            $this->app->render('createpost.twig');
            // RENDER HERE

    }
}
