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
    
    public function __construct(\Ivory\GoogleMapBundle\Model\MapBuilder $map_builder) {
        $this->map = $map_builder;
    }
    
    public function getAddressCoordinates(AddressInterface $address) {
        if($address !== null) {
            $postcode = ($address->getPostcode() !== null) ? $address->getPostcode() : '';
            $street = ($address->getStreet() !== null) ? $address->getStreet() : '';
            $city = ($address->getCity() !== null) ? $address->getCity() : '';
            $country = ($address->getCountry() !== null) ? $address->getCountry() : '';

            $address_string = $street . ', ' . $postcode . ' ' . $city . ' ' . $country;

            $curl = new CurlHttpAdapter();
            $geocoder = new GoogleMapsProvider($curl);

            $geodata = $geocoder->getGeocodedData($address_string);

            return $geodata;
        } else {
            return null;
        }
    }
    
    public function getAddressMap(AddressInterface $address) {
        if($address !== null) {
            $coordinates = $this->getAddressCoordinates($address);
            dump($this->map);
            $address_map = $this->map->build();
            $address_map->setCenter($coordinates[0]['latitude'], $coordinates[0]['longitude'], 0);
            
            $address_map->setMapOption('zoom', 13);
            $address_map->setAutoZoom(false);
            $address_map->setStylesheetOptions([
                'height' => '300px',
                'width' => '400px',
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
                    $coordinates[0]['latitude'],
                    $coordinates[0]['longitude'],
                    true
            );
            $marker->setAnimation(Animation::DROP);

            $address_map->addMarker($marker);
            
            return $address_map;
        } else {
            return null;
        }
    }
}