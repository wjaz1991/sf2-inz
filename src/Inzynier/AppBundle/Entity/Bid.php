<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="bids")
 */
class Bid {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    protected $date;
    
    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     */
    protected $price;
    
    /**
     * @ORM\ManyToOne(targetEntity="Auction", inversedBy="bids")
     * @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     */
    protected $auction;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bids")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    public function __construct() {
        $this->date = new \DateTime();
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setPrice($price) {
        $this->price = $price;
        return $this;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getAuction() {
        return $this->auction;
    }
    
    public function setAuction(Auction $auction) {
        $this->auction = $auction;
        return $this;
    }
    
    public function setDate(\DateTime $date) {
        $this->date = $date;
        return $this;
    }
    
    public function getDate() {
        return $this->date;
    }
    
    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }
    
    public function getUser() {
        return $this->user;
    }
}