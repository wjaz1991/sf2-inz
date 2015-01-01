<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Inzynier\AppBundle\Repository\FriendshipRepository")
 * @ORM\Table(name="friends")
 */
class Friendship {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $startedOn;
    
    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $accepted;
    
    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $rejected;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="my_friends")
     * @ORM\JoinColumn(name="user_one_id", referencedColumnName="id", nullable=FALSE)
     */
    private $user_one;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friends_with_me")
     * @ORM\JoinColumn(name="user_two_id", referencedColumnName="id", nullable=FALSE)
     */
    private $user_two;
    
    public function __construct() {
        $this->startedOn = new \Datetime();
        $this->rejected = 0;
        $this->accepted = 0;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getStartedOn() {
        return $this->startedOn;
    }
    
    public function setStartedOn(\DateTime $date) {
        $this->startedOn = $date;
        return $this;
    }
    
    public function getAccepted() {
        return $this->accepted;
    }
    
    public function setAccepted($accepted) {
        $this->accepted = $accepted;
        return $this;
    }
    
    public function getRejected() {
        return $this->rejected;
    }
    
    public function setRejected($rejected) {
        $this->rejected = $rejected;
        return $this;
    }
    
    public function getUserOne() {
        return $this->user_one;
    }
    
    public function setUserOne(User $user) {
        $this->user_one = $user;
        
        return $this;
    }
    
    public function getUserTwo() {
        return $this->user_two;
    }
    
    public function setUserTwo(User $user) {
        $this->user_two = $user;
        
        return $this;
    }
}
