<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Inzynier\AppBundle\Interfaces\AddressInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="auction_addresses")
 */
class AuctionAddress implements AddressInterface {
    private $distance = null;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $city;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street;
    
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $postcode;
    
    /**
     * @ORM\Column(type="decimal", scale=2, precision=6, nullable=true)
     */
    protected $latitude;
    
    /**
     * @ORM\Column(type="decimal", scale=2, precision=6, nullable=true)
     */
    protected $longitude;
    
    public function __construct() {
        $this->latitude = NULL;
        $this->longitude = NULL;
        $this->distance = null;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getCity() {
        return $this->city;
    }
    
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }
    
    public function getCountry() {
        return $this->country;
    }
    
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }
    
    public function getStreet() {
        return $this->street;
    }
    
    public function setStreet($street) {
        $this->street = $street;
        return $this;
    }
    
    public function getPostcode() {
        return $this->postcode;
    }
    
    public function setPostcode($postcode) {
        $this->postcode = $postcode;
        return $this;
    }
    
    public function getLatitude() {
        return $this->latitude;
    }
    
    public function setLatitude($latitude) {
        $this->latitude = $latitude;
        return $this;
    }
    
    public function getLongitude() {
        return $this->longitude;
    }
    
    public function setLongitude($longitude) {
        $this->longitude = $longitude;
        return $this;
    }
    
    public function getDistance() {
        return $this->distance;
    }
    
    public function setDistance($distance) {
        $this->distance = $distance;
        return $this;
    }
}

