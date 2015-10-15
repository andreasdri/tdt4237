<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Comment;

class CommentRepository
{

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Comment $comment)
    {
        $id = (int) $comment->getCommentId();
        $author  = $comment->getAuthor();
        $text    = $comment->getText();
        $date = (string) $comment->getDate();
        $postid = $comment->getPost();

        if ($comment->getCommentId() !== null) {
          return;
        }

        $stmt = $this->pdo->prepare("INSERT INTO comments (author, text, date, belongs_to_post) VALUES (?, ?, ?, ?)");
        $stmt->execute(array($author, $text, $date, $postid));
        return $this->pdo->lastInsertId();
    }

    public function findByPostId($postId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE belongs_to_post = ?");
        $stmt->execute(array($postId));
        return array_map([$this, 'makeFromRow'], $stmt->fetchAll());
    }

    public function makeFromRow($row)
    {
        $comment = new Comment;

        return $comment
            ->setCommentId($row['commentId'])
            ->setAuthor($row['author'])
            ->setText($row['text'])
            ->setDate($row['date'])
            ->setPost($row['belongs_to_post']);
    }
}
