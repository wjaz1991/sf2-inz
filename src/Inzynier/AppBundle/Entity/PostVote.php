<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="post_votes")
 */
class PostVote {
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
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="votes")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;
    
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
    
    public function getPost() {
        return $this->post;
    }
    
    public function setPost(Post $post) {
        $this->post = $post;
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

