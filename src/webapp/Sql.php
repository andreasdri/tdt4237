<?php

namespace tdt4237\webapp;

use tdt4237\webapp\models\User;

class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up()
    {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, user VARCHAR(50), pass VARCHAR(50), email varchar(50) default null, fullname varchar(50), address varchar(50), postcode varchar (4), age varchar(50), bio varchar(50), isadmin INTEGER, isdoctor INTEGER, bankcard varchar(16), moneyspent DOUBLE, moneyearned DOUBLE );";
        $q6 = "CREATE TABLE posts (postId INTEGER PRIMARY KEY AUTOINCREMENT, author TEXT, title TEXT NOT NULL, content TEXT NOT NULL, date TEXT NOT NULL, ispayedpost INTEGER, isanswered INTEGER, FOREIGN KEY(author) REFERENCES users(user));";
        $q7 = "CREATE TABLE comments(commentId INTEGER PRIMARY KEY AUTOINCREMENT, date TEXT NOT NULL, author TEXT NOT NULL, text INTEGER NOT NULL, belongs_to_post INTEGER NOT NULL, FOREIGN KEY(belongs_to_post) REFERENCES posts(postId));";

        self::$pdo->exec($q1);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);

        print "[tdt4237] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
        self::insertPosts();
        self::insertComments();
    }

    static function insertDummyUsers()
    {
        $hash1 = Hash::make('12345678');
        $hash2 = Hash::make('bobdylan');
        $hash3 = Hash::make('liverpool');
        $hash4 = Hash::make('Testuser123');
        $hash5 = Hash::make('TestDoctor');

        $q1 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode) VALUES ('admin', '$hash1', 1, 'admin', 'homebase', '9090')";
        $q2 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode) VALUES ('bob', '$hash2', 0, 'Robert Green', 'Greenland Grove 9', '2010')";
        $q3 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode) VALUES ('bjarni', '$hash3', 0, 'Bjarni Torgmund', 'Hummerdale 12', '4120')";
        $q11 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode, email, age, bio, bankcard) VALUES ('testuser', '$hash4', 1, 'Harald Rex', 'Henrik Ibsens gate 1', '0010', 'harald@slottet.no', 78, 'Helt konge.', 1234567812345678)";
        $q13 = "INSERT INTO users(user, pass, isdoctor, fullname, address, postcode, email, age, bio, bankcard) VALUES ('testdoctor', '$hash5', 1, 'Doctor Jones', 'Henrik Ibsens gate 1', '0010', 'drjones@aqua.no', 30, 'Call me Doctor Jones.', 1234567812345678)";
        $q14 = "INSERT INTO users(user, pass, isdoctor, fullname, address, postcode, email, age, bio) VALUES ('testdoctor2', '$hash5', 1, 'Doctor Dre', 'Henrik Ibsens gate 1', '0010', 'dredre@dre.com', 30, 'Call me Doctor Jones.')";


        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        self::$pdo->exec($q3);
        self::$pdo->exec($q11);
        self::$pdo->exec($q13);
        self::$pdo->exec($q14);


        print "[tdt4237] Done inserting dummy users.".PHP_EOL;
    }

    static function insertPosts() {
        $q4 = "INSERT INTO posts(author, date, title, content) VALUES ('bob', '26082015', 'I have a problem', 'I have a generic problem I think its embarrasing to talk about. Someone help?')";
        $q5 = "INSERT INTO posts(author, date, title, content) VALUES ('bjarni', '26082015', 'I also have a problem', 'I generally fear very much for my health')";
        $q10 = "INSERT INTO posts(author, date, title, content, ispayedpost) VALUES ('bob', '21102015', 'Can a doctor answer this?', 'It hurts when I pee. Do I have AIDS?', 1)";

        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        self::$pdo->exec($q10);
        print "[tdt4237] Done inserting posts.".PHP_EOL;

    }

    static function insertComments() {
        $q1 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bjarni', '26082015', 'Don''t be shy! No reason to be afraid here',0)";
        $q2 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bob', '26082015', 'I wouldn''t worry too much, really. Just relax!',1)";
        $q12 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('admin', '26082015', 'This post does not meet our community guidelines. Please go kill yourself.',1)";
        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        self::$pdo->exec($q12);
        print "[tdt4237] Done inserting comments.".PHP_EOL;


    }

    static function down()
    {
        $q1 = "DROP TABLE users";
        $q4 = "DROP TABLE posts";
        $q5 = "DROP TABLE comments";



        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);

        print "[tdt4237] Done deleting all SQL tables.".PHP_EOL;
    }
}
try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo $e->getMessage();
    exit();
}
