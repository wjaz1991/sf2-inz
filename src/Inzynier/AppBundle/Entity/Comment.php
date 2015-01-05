<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $text;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdded;
    
    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;
    
    /**
     * @ORM\ManyToOne(targetEntity="Auction", inversedBy="comments")
     * @ORM\JoinColumn(name="auction_id", referencedColumnName="id", nullable=true)
     */
    private $auction;
    
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;
    
    public function __construct() {
        $this->dateAdded = new \DateTime();
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setText($text) {
        $this->text = $text;
        
        return $this;
    }
    
    public function getText() {
        return $this->text;
    }
    
    public function setDateAdded($date) {
        $this->dateAdded = $date;
        
        return $this;
    }
    
    public function getDateAdded() {
        return $this->dateAdded;
    }
    
    public function setUser(User $user) {
        $this->user = $user;
        
        return $this;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function getClassName() {
        return 'Post';
    }
    
    public function getPost() {
        return $this->post;
    }
    
    public function setPost(Post $post) {
        $this->post = $post;
    }
    
    public function getAuction() {
        return $this->auction;
    }
    
    public function setAuction(Auction $auction) {
        $this->auction = $auction;
    }
}