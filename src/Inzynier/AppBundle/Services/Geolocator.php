<?php

namespace Inzynier\AppBundle\Services;

use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Overlays\InfoWindow;
use Ivory\GoogleMap\Overlays\Animation;

use Geocoder\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\GoogleMapsProvider;

use Inzynier\AppBundle\Interfaces\AddressInterface;

class Geolocator {
    private $map;
    private $doctrine;
    private $router;
    
    public function __construct(\Ivory\GoogleMapBundle\Model\MapBuilder $map_builder, $doctrine, $router) {
        $this->map = $map_builder;
        $this->doctrine = $doctrine;
        $this->router = $router;
    }
    
    public function getAddressCoordinates(AddressInterface $address) {
        if($address !== null) {
            $postcode = ($address->getPostcode() !== null) ? $address->getPostcode() : '';
            $street = ($address->getStreet() !== null) ? $address->getStreet() : '';
            $city = ($address->getCity() !== null) ? $address->getCity() : '';
            $country = ($address->getCountry() !== null) ? $address->getCountry() : '';

            $address_string = $street . ', ' . $postcode . ' ' . $city . ' ' . $country;
            
            try {
                $curl = new CurlHttpAdapter();
                $geocoder = new GoogleMapsProvider($curl);

                $geodata = $geocoder->getGeocodedData($address_string);
            } catch(\Geocoder\Exception\NoResultException $e) {
                return null;
            }

            return $geodata;
        } else {
            return null;
        }
    }
    
    public function getAddressMap(AddressInterface $address) {
        if($address !== null) {
            //$coordinates = $this->getAddressCoordinates($address);
            //dump($this->map);
            $address_map = $this->map->build();
            //$address_map->setCenter($coordinates[0]['latitude'], $coordinates[0]['longitude'], 0);
            $address_map->setCenter($address->getLatitude(), $address->getLongitude(), 0);
            $address_map->setMapOption('zoom', 13);
            $address_map->setAutoZoom(false);
            $address_map->setStylesheetOptions([
                'height' => '300px',
                'width' => '100%',
            ]);
            
            /*
            $info_window = new InfoWindow();
            $info_window->setPrefixJavascriptVariable('info_window_');
            $content = '<h4>' . $auction->getTitle() . '</h4>'
                    . '<p>' . $address->getStreet() . '</p>'
                    . '<p>' . $address->getPostcode() . ' ' . $address->getCity() . '</p>'
                    . '<p>' . $address->getCountry() . '</p>';
            $info_window->setContent($content);
            $info_window->setOpen(TRUE);
            */
            
            $marker = new Marker();
            //$marker->setInfoWindow($info_window);
            $marker->setPrefixJavascriptVariable('marker_');
            $marker->setPosition(
                    $address->getLatitude(),
                    $address->getLongitude(),
                    //$coordinates[0]['latitude'],
                    //$coordinates[0]['longitude'],
                    true
            );
            $marker->setAnimation(Animation::DROP);

            $address_map->addMarker($marker);
            
            return $address_map;
        } else {
            return null;
        }
    }
    
    public function distance($lat1, $lon1, $lat2, $lon2, $unit = "K") {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
    
    public function getNearestAuctions(AddressInterface $address, $days = 10) {
        $user_latitude = $address->getLatitude();
        $user_longitude = $address->getLongitude();

        //calculating auction distances
        $repo = $this->doctrine->getRepository('InzynierAppBundle:Auction');
        $auctions = $repo->getAuctionsByDays($days);
        foreach($auctions as $auction) {
            $address = $auction->getAddress();
            $longitude = $address->getLongitude();
            $latitude = $address->getLatitude();
            $distance = $this->distance($user_latitude, $user_longitude, $latitude, $longitude);
            $auction->getAddress()->setDistance($distance);
        }

        usort($auctions, function($a, $b) {
            $dist1 = $a->getAddress()->getDistance();
            $dist2 = $b->getAddress()->getDistance();
            if($dist1 < $dist2) {
                return -1;
            } else if ($dist1 > $dist2) {
                return 1;
            } else {
                return 0;
            }
        });
        
        return $auctions;
    }
    
    public function buildAddressesMap($auctions, $root_address) {
        if(!$auctions || count($auctions) == 0 || !$root_address) {
            return null;
        }
        
        $address_map = $this->map->build();
        
        $address_map->setCenter($root_address->getLatitude(), $root_address->getLongitude(), 0);
        
        $address_map->setMapOption('zoom', 3);
        $address_map->setAutoZoom(false);
        $address_map->setStylesheetOptions([
            'height' => '600px',
            'width' => '100%',
        ]);
        
        //setting root marker
        $marker = new Marker();
        //$marker->setInfoWindow($info_window);
        $marker->setPrefixJavascriptVariable('marker_root_');
        $marker->setPosition(
                $root_address->getLatitude(),
                $root_address->getLongitude(),
                true
        );
        $marker->setAnimation(Animation::BOUNCE);
        
        $info_window = new InfoWindow();
        $info_window->setPrefixJavascriptVariable('info_window_root_');
        $content = '<h4>Your location</h4>'
                . '<p>' . $root_address->getStreet() . '</p>'
                . '<p>' . $root_address->getPostcode() . ' ' . $root_address->getCity() . '</p>'
                . '<p>' . $root_address->getCountry() . '</p>';
        $info_window->setContent($content);
        $info_window->setOpen(false);
        $marker->setInfoWindow($info_window);

        $address_map->addMarker($marker);
        
        foreach($auctions as $auction) {
            $address = $auction->getAddress();
            
            $marker = new Marker();
            $marker->setPrefixJavascriptVariable('marker_auction_' . $auction->getId() . '_');
            $marker->setPosition(
                    $address->getLatitude(),
                    $address->getLongitude(),
                    true
            );
            $marker->setAnimation(Animation::BOUNCE);
            
            $link = $this->router->generate('auction_single', ['id' => $auction->getId()]);

            $info_window = new InfoWindow();
            $info_window->setPrefixJavascriptVariable('info_window_root_');
            $content = '<h4><a href="' . $link . '">' . $auction->getTitle() . '</a></h4>'
                    . '<p>' . $address->getStreet() . '</p>'
                    . '<p>' . $address->getPostcode() . ' ' . $address->getCity() . '</p>'
                    . '<p>' . $address->getCountry() . '</p>';
            $info_window->setContent($content);
            $info_window->setOpen(false);
            $marker->setInfoWindow($info_window);

            $address_map->addMarker($marker);
        }

        return $address_map;
    }
}