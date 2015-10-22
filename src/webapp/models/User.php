<?php

namespace tdt4237\webapp\models;

class User
{

    protected $userId  = null;
    protected $username;
    protected $fullname;
    protected $address;
    protected $postcode;
    protected $hash;
    protected $email   = null;
    protected $bio     = 'Bio is empty.';
    protected $age;
    protected $bankcard = '';
    protected $moneyspent = 0.0;
    protected $moneyearned = 0.0;
    protected $isAdmin = 0;
    protected $isDoctor = 0;

    function __construct($username, $hash, $fullname, $address, $postcode)
    {
        $this->username = $username;
        $this->hash = $hash;
        $this->fullname = $fullname;
        $this->address = $address;
        $this->postcode = $postcode;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getFullname() {
        return $this->fullname;
    }

    public function setFullname($fullname) {
        $this->fullname = $fullname;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getPostcode() {
        return $this->postcode;

    }

    public function setPostcode($postcode) {
        $this->postcode = $postcode;
    }

    public function isAdmin()
    {
        return $this->isAdmin === '1';
    }

    public function isDoctor() {
      return $this->isDoctor === '1';
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
        return $this;
    }

    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function setIsDoctor($isDoctor)
    {
        $this->isDoctor = $isDoctor;
        return $this;
    }

    public function setBankcard($bankcard){
        $this->bankcard = trim($bankcard);
        return $this;
    }

    public function getBankcard(){
        return $this->bankcard;
    }

    public function getMoneyspent(){
        return $this->moneyspent;
    }

    public function setMoneyspent($moneyspent){
        $this->moneyspent = $moneyspent;
        return $this;
    }

    public function spendMoney($money){
        $this->moneyspent += $money;
        return $this;
    }
    
    public function getMoneyearned(){
        return $this->moneyearned;
    }

    public function setMoneyearned($moneyearned){
        $this->moneyearned = $moneyearned;
        return $this;
    }

    public function earnMoney($money){
        $this->moneyearned += $money;
        return $this;
    }
}
