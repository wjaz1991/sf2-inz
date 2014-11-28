<?php

namespace Inzynier\AppBundle\Classes;

use Symfony\Component\Validator\Constraints as Assert;
use Inzynier\AppBundle\Entity\User;

class Registration {
    /**
     * @Assert\Type(type="Inzynier\AppBundle\Entity\User")
     * @Assert\Valid()
     */
    private $user;
    
    /**
     * @Assert\NotBlank()
     * @Assert\True()
     */
    private $termsAccepted;
    
    public function setUser(User $user) {
        $this->user = $user;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function getTermsAccepted() {
        return $this->termsAccepted;
    }
    
    public function setTermsAccepted($terms) {
        $this->termsAccepted = (Boolean)$terms;
    }
}

