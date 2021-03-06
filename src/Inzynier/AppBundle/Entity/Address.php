<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Inzynier\AppBundle\Entity\Country;
use Inzynier\AppBundle\Entity\User;

use Inzynier\AppBundle\Interfaces\AddressInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="addresses")
 */
class Address implements AddressInterface {
    private $distance = null;
    
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
    protected $street;
    
    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank()
     */
    protected $postcode;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $city;
    
    /**
     * @ORM\Column(type="string", length=9, nullable=true)
     */
    protected $telephone;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $country;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="addresses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\Column(type="decimal", scale=2, precision=6, nullable=true)
     */
    protected $latitude;
    
    /**
     * @ORM\Column(type="decimal", scale=2, precision=6, nullable=true)
     */
    protected $longitude;
    
    public function __construct() {
        $this->latitude = null;
        $this->longitude = null;
        $this->distance = null;
    }
    
    
    //SETTERS, GETTERS
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
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
    
    public function getCity() {
        return $this->city;
    }
    
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }
    
    public function getTelephone() {
        return $this->telephone;
    }
    
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
        return $this;
    }
    
    public function setCountry($country = null) {
        $this->country = $country;
        return $this;
    }
    
    public function getCountry() {
        return $this->country;
    }

    public function setUser(User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
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
