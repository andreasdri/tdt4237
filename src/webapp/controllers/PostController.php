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

        if($posts){
            $posts->sortByDate();
        }

        $this->render('posts.twig', ['posts' => $posts]);
    }

    public function show($postId)
    {
        if(!$this->auth->check()){
            $this->app->flash('info', 'You have to be logged in to view a post.');
            $this->app->redirect('/login');
        }
        $post = $this->postRepository->find($postId);

        # Doctors can only view paid posts
        if (!$post->isPayedPost() and $this->auth->user()->isDoctor())  {
            $this->app->flash('info', 'You are not allowed to view this post');
            $this->app->redirect('/');
        }

        $comments = $this->commentRepository->findByPostId($postId);

        foreach ($comments as $comment) { // Get isDoctorStatus for each commenter
            $authorName = $comment->getAuthor();
            $comment->authorIsDoctor = $this->userRepository->findByUser($authorName)->isDoctor();
        }

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
            $post = $this->postRepository->find($postId);

            $validation = new PostValidation('title', $author, $text, $token, false);
            if ($validation->isGoodToGo()) {

                # When the post is paid for, and not answered by a doctor
                # the doctor gets 7 $ and the user pays 10 $.
                if ($post->isPayedPost() and !$post->isAnswered() and $this->auth->user()->isDoctor()) {
                    $this->addTransaction($post);
                }
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

    private function addTransaction($post) {
        $user = $this->userRepository->findByUser($post->getAuthor());
        $doctor = $this->auth->user();
        if($user->getUserId() == $doctor->getUserId()) { // In case a doctor answers his own question
            $doctor->spendMoney(10);
            $doctor->earnMoney(7);
            $post->setIsAnswered(1);
            $this->userRepository->saveExistingUser($doctor);
            $this->postRepository->update($post);
            return;
        }

        $user->spendMoney(10);
        $doctor->earnMoney(7);
        $post->setIsAnswered(1);
        $this->userRepository->saveExistingUser($user);
        $this->userRepository->saveExistingUser($doctor);
        $this->postRepository->update($post);
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

            $this->app->flash('error', join('<br>', $validation->getValidationErrors()));
            $this->app->redirect('/posts/new');
            // RENDER HERE

    }
}
