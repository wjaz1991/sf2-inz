<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="galleries")
 */
class Gallery {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $filename;
    
    /**
     * @ORM\ManyToOne(targetEntity="Auction", inversedBy="images")
     * @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     */
    protected $auction;
    
    protected $image;
    
    public function getImage() {
        return $this->image;
    }
    
    public function setImage($image) {
        $this->image = $image;
        return $this;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }
    
    public function getFilename() {
        return $this->filename;
    }
    
    public function getAuction() {
        return $this->auction;
    }
    
    public function setAuction(Auction $auction) {
        $this->auction = $auction;
        return $this;
    }
}