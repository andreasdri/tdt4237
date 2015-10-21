<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;

class UserRepository
{
    const INSERT_QUERY   = "INSERT INTO users(user, pass, email, age, bio, isadmin, isdoctor, fullname, address, postcode, bankcard, moneyspent) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    const UPDATE_QUERY   = "UPDATE users SET email=?, age=?, bio=?, isadmin=?, isdoctor=?, fullname =?, address = ?, postcode = ?, bankcard = ?, moneyspent = ? WHERE id=?";
    const FIND_BY_NAME   = "SELECT * FROM users WHERE user=?";
    const DELETE_BY_NAME = "DELETE FROM users WHERE user=?";
    const SELECT_ALL     = "SELECT * FROM users";
    const FIND_FULL_NAME   = "SELECT * FROM users WHERE user=?";

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makeUserFromRow(array $row)
    {
        $user = new User($row['user'], $row['pass'], $row['fullname'], $row['address'], $row['postcode']);
        $user->setUserId($row['id']);
        $user->setFullname($row['fullname']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode'])));
        $user->setBio($row['bio']);
        $user->setIsAdmin($row['isadmin']);
        $user->setBankcard($row['bankcard']);
        //$user->setMoneyspent($row['moneyspent']);

        if (!empty($row['email'])) {
            $user->setEmail(new Email($row['email']));
        }

        if (!empty($row['age'])) {
            $user->setAge(new Age($row['age']));
        }

        return $user;
    }

    public function getNameByUsername($username)
    {
        $stmt = $this->pdo->prepare(self::FIND_FULL_NAME);
        $stmt->execute(array($username));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['fullname'];
    }

    public function findByUser($username)
    {
        $stmt = $this->pdo->prepare(self::FIND_BY_NAME);
        $stmt->execute(array($username));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return false;
        }

        return $this->makeUserFromRow($row);
    }

    public function deleteByUsername($username)
    {
        $stmt = $this->pdo->prepare(self::DELETE_BY_NAME);
        $stmt->execute(array($username));
        return $stmt->rowCount();
    }

    public function all()
    {
        $stmt = $this->pdo->prepare(self::SELECT_ALL);
        $stmt->execute();
        return array_map([$this, 'makeUserFromRow'], $stmt->fetchAll());
    }

    public function save(User $user)
    {
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        $this->saveExistingUser($user);
    }

    public function saveNewUser(User $user)
    {
        $stmt = $this->pdo->prepare(self::INSERT_QUERY);
        $stmt->execute(array(
          $user->getUsername(),
          $user->getHash(),
          $user->getEmail(),
          $user->getAge(),
          $user->getBio(),
          $user->isAdmin(),
          $user->getFullname(),
          $user->getAddress(),
          $user->getPostcode(),
          $user->getBankcard(),
          $user->getMoneyspent()
        ));

        return $stmt->rowCount();
    }

    public function saveExistingUser(User $user)
    {
        $stmt = $this->pdo->prepare(self::UPDATE_QUERY);
        $stmt->execute(array(
          $user->getEmail(),
          $user->getAge(),
          $user->getBio(),
          $user->isAdmin(),
          $user->isDoctor(),
          $user->getFullname(),
          $user->getAddress(),
          $user->getPostcode(),
          $user->getBankcard(),
          $user->getMoneyspent(),
          $user->getUserId()
        ));

        return $stmt->rowCount();
    }

}
