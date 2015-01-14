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
     * @ORM\OneToMany(targetEntity="Gallery", mappedBy="auction", cascade={"persist"}, orphanRemoval=true)
     */
    protected $images;
    
    /**
     * @ORM\OneToOne(targetEntity="AuctionAddress", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;
    
    /**
     * @ORM\OneToMany(targetEntity="AuctionVote", mappedBy="auction", cascade={"remove"})
     */
    private $votes;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="auction", cascade={"remove"})
     */
    private $comments;
    
    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $paid;
    
    public function __construct() {
        $this->dateAdded = new \DateTime();
        $this->views = 0;
        $this->bids = new ArrayCollection;
        $this->images = new ArrayCollection;
        $this->votes = new ArrayCollection;
        $this->paid = 0;
    }
    
    //helper methods
    public function getDaysLeft() {
        $now = new \DateTime();
        if($this->endDate >= $now) {
            $diff = $this->endDate->diff($now)->format('%a');
            return $diff;
        } else {
            return null;
        }
    }
    
    public function isActive() {
        $now = new \DateTime();
        if($this->endDate >= $now) {
            return true;
        } else {
            return false;
        }
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
        $image->setAuction(null);
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
        if(count($this->images)) {
            $image = $this->images->get(0);
            return $image->getWebPath();
        } else {
            return null;
        }
    }
    
    public function getAddress() {
        return $this->address;
    }
    
    public function setAddress(AuctionAddress $address) {
        $this->address = $address;
        return $this;
    }
    
    public function getClassName() {
        return 'Auction';
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
    
    public function getPaid() {
        return $this->paid;
    }
    
    public function setPaid($paid) {
        $this->paid = $paid;
        return $this;
    }
}
