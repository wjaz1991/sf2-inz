<?php

namespace Inzynier\AppBundle\Interfaces;

interface AddressInterface {
    public function getStreet();
    public function setStreet($street);
    
    public function getPostcode();
    public function setPostcode($postcode);
    
    public function getCity();
    public function setCity($city);
    
    public function getCountry();
    public function setCountry($country);
    
    public function getLatitude();
    public function setLatitude($latitude);
    
    public function getLongitude();
    public function setLongitude($longitude);
}