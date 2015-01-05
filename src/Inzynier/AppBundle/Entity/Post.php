<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Post {
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\OneToMany(targetEntity="PostVote", mappedBy="post", cascade={"remove"})
     */
    private $votes;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post", cascade={"remove"})
     */
    private $comments;
    
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
    
    public function getComments() {
        return $this->comments;
    }
    
    public function addComment(Comment $comment) {
        $this->comments[] = $comment;
        return $this;
    }
    
    public function removeComment(Comment $comment) {
        $this->comments->removeElement($comment);
        return $this;
    }
    
    public function getVotes() {
        return $this->votes;
    }
    
    public function addVote(AuctionVote $vote) {
        $this->votes[] = $vote;
        return $this;
    }
    
    public function removeVote(AuctionVote $vote) {
        $this->votes->removeElement($vote);
        return $this;
    }
    
    public function getVotesCount() {
        $votes = $this->getVotes();
        $data = [];
        $data['up'] = 0;
        $data['down'] = 0;
        
        if(count($votes)) {
            foreach($votes as $vote) {
                if($vote->getType() == 0) {
                    $data['down']++;
                } else if($vote->getType() == 1) {
                    $data['up']++;
                }
            }
        }
        
        return $data;
    }
}