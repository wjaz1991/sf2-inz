<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Inzynier\AppBundle\Repository\AuctionRepository")
 * @ORM\Table(name="auctions")
 */
class Auction {
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
    protected $title;
    
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $description;
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type(type="boolean")
     */
    protected $private;
    
    /**
     * @ORM\Column(type="integer", options={"unsigned":true, "default":0})
     */
    protected $views;
    
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    protected $startDate;
    
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    protected $endDate;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $dateAdded;
    
    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $price;
    
    /**
     * @ORM\ManyToOne(targetEntity="AuctionCategory", inversedBy="auctions")
     * @ORM\JoinColumn(name="auction_category_id", referencedColumnName="id")
     */
    protected $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="auctions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\OneToMany(targetEntity="Bid", mappedBy="auction")
     */
    protected $bids;
    
    /**
     * @ORM\OneToMany(targetEntity="Gallery", mappedBy="auction", cascade={"persist"})
     */
    protected $images;
    
    public function __construct() {
        $this->dateAdded = new \DateTime();
        $this->views = 0;
        $this->bids = new ArrayCollection;
        $this->images = new ArrayCollection;
    }
    
    //setters, getters
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function isPrivate() {
        return $this->private;
    }
    
    public function setPrivate($private) {
        $this->private = $private;
        return $this;
    }
    
    public function getViews() {
        return $this->views;
    }
    
    public function setViews($views) {
        $this->views = $views;
    }
    
    public function setStartDate(\DateTime $date) {
        $this->startDate = $date;
        return $this;
    }
    
    public function getStartDate() {
        return $this->startDate;
    }
    
    public function setEndDate(\DateTime $date) {
        $this->endDate = $date;
        return $this;
    }
    
    public function getEndDate() {
        return $this->endDate;
    }
    
    public function setDateAdded(\DateTime $date) {
        $this->dateAdded = $date;
        return $this;
    }
    
    public function getDateAdded() {
        return $this->dateAdded;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function setPrice($price) {
        $this->price = $price;
        return $this;
    }
    
    public function setCategory(AuctionCategory $category) {
        $this->category = $category;
    }
    
    public function getCategory() {
        return $this->category;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }
    
    public function addBid(Bid $bid) {
        $this->bids[] = $bid;
        return $this;
    }
    
    public function removeBid(Bid $bid) {
        $this->bids->removeElement($bid);
        return $this;
    }
    
    public function getBids() {
        return $this->bids;
    }
    
    public function addImage(Gallery $image) {
        $this->images[] = $image;
        $image->setAuction($this);
        return $this;
    }
    
    public function removeImage(Gallery $image) {
        $this->images->removeElement($image);
        return $this;
    }
    
    public function getImages() {
        return $this->images;
    }
    
    public function setImages($images) {
        $this->images = $images;
        return $this;
    }
    
    public function getFirstImage() {
        $image = $this->images->get(0);
        return $image->getFilename();
    }
}
