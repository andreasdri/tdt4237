<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Post;
use tdt4237\webapp\models\PostCollection;

class PostRepository
{

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function create($id, $author, $title, $content, $date, $payed, $isAnswered)
    {
        $post = new Post;

        return $post
            ->setPostId($id)
            ->setAuthor($author)
            ->setTitle($title)
            ->setContent($content)
            ->setDate($date)
            ->setIsPayedPost($payed)
            ->setIsAnswered($isAnswered);
    }

    public function find($postId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE postId = ?");
        $stmt->execute(array($postId));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row === false) {
            return false;
        }

        return $this->makeFromRow($row);
    }

    public function all()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts");
        $stmt->execute();
        $results = $stmt->fetchAll();

        if (count($results) == 0) {
            return false;
        }

        return new PostCollection(
            array_map([$this, 'makeFromRow'], $results)
        );
    }

    public function makeFromRow($row)
    {
        return static::create(
            $row['postId'],
            $row['author'],
            $row['title'],
            $row['content'],
            $row['date'],
            $row['ispayedpost'],
            $row['isanswered']
        );
    }

    public function deleteByPostid($postId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE postId = ?");
        $stmt->execute(array($postId));
        return $stmt->rowCount();
    }


    public function save(Post $post)
    {
        $title   = $post->getTitle();
        $author = $post->getAuthor();
        $content = $post->getContent();
        $date    = $post->getDate();
        $payed = $post->isPayedPost();
        $answered = $post->isAnswered();

        // Can't update posts
        if ($post->getPostId() !== null) {
          return;
        }

        $stmt = $this->pdo->prepare("INSERT INTO posts (title, author, content, date, ispayedpost, isanswered) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($title, $author, $content, $date, $payed, $answered));
        return $this->pdo->lastInsertId();
    }
}
