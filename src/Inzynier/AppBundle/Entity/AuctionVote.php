<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="auction_votes")
 */
class AuctionVote {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    private $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="Auction", inversedBy="votes")
     * @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     */
    private $auction;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    public function getId() {
        return $this->id;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
    
    public function getAuction() {
        return $this->auction;
    }
    
    public function setAuction(Auction $auction) {
        $this->auction = $auction;
        return $this;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }
}

