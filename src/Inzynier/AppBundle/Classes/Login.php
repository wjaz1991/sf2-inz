<?php

namespace Inzynier\AppBundle\Classes;

use Symfony\Component\Validator\Constraints as Assert;

class Login {
    /**
     * @Assert\NotBlank()
     */
    private $login;
    
    /**
     * @Assert\NotBlank()
     */
    private $password;
    
    private $remember;
    
    public function __construct() {
        $this->remember = false;
    }
    
    public function getLogin() {
        return $this->login;
    }
    
    public function setLogin($login) {
        $this->login = login;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function getRemember() {
        return $this->remember;
    }
    
    public function setRemember($remember) {
        $this->remember = (Boolean) $remember;
    }
}

