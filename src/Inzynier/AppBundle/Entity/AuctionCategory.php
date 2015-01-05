<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Inzynier\AppBundle\Repository\AuctionCategoryRepository")
 * @ORM\Table(name="auction_categories")
 */
class AuctionCategory {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank()
     */
    protected $name;
    
    /**
     * @ORM\OneToMany(targetEntity="AuctionCategory", mappedBy="parent")
     */
    protected $children;
    
    /**
     * @ORM\ManyToOne(targetEntity="AuctionCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Auction", mappedBy="category")
     */
    protected $auctions;
    
    /**
     * @ORM\OneToMany(targetEntity="Gallery", mappedBy="auction")
     */
    protected $images;
    
    //== SETTERS, GETTERS ==
    public function __construct() {
        $this->parent = null;
        $this->auctions = new ArrayCollection;
        $this->images = new ArrayCollection;
        $this->children = new ArrayCollection();
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Get auction category parent category
     * 
     * @return \Inzynier\AppBundle\Entity\AuctionCategory
     */
    public function getParent() {
        return $this->parent;
    }
    
    /**
     * Set auction category parent category
     * 
     * @param \Inzynier\AppBundle\Entity\AuctionCategory $parent
     * @return \Inzynier\AppBundle\Entity\AuctionCategory
     */
    public function setParent(AuctionCategory $parent) {
        $this->parent = $parent;
        return $this;
    }
    
    public function getChildren() {
        return $this->children;
    }
    
    public function addChild(AuctionCategory $category) {
        if(!$this->children->contains($category)) {
            $this->children->add($category);
        }
        
        return $this;
    }
    
    public function removeChild(AuctionCategory $category) {
        if($this->children->contains($category)) {
            $this->children->removeElement($category);
        }
        
        return $this;
    }
    
    public function getAuctions() {
        return $this->auctions;
    }
    
    public function addAuction(Auction $auction) {
        $this->auctions[] = $auction;
        return $this;
    }
    
    public function removeAuction(Auction $auction) {
        $this->auctions->removeElement($auction);
        return $this;
    }
    
    public function addImage(Gallery $image) {
        $this->images[] = $image;
        return $this;
    }
    
    public function removeImage(Gallery $image) {
        $this->images->removeElement($image);
        return $this;
    }
    
    public function getImages() {
        return $this->images;
    }
}
