<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Inzynier\AppBundle\Entity\Auction;
use Inzynier\AppBundle\Form\Type\AuctionType;
use Inzynier\AppBundle\Form\Type\BidType;
use Inzynier\AppBundle\Entity\Bid;

use Geocoder\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\GoogleMapsProvider;

use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\Overlays\Marker;
use Ivory\GoogleMap\Overlays\InfoWindow;

class AuctionController extends Controller {
    /**
     * @Route("/auctions/new", name="auction_new")
     */
    public function newAction(Request $request) {
        $auction = new Auction();
        
        $now = new \DateTime();
        $nextDay = new \DateTime();
        $nextDay->add(new \DateInterval('P1D'));
        $auction->setStartDate($now);
        $auction->setEndDate($nextDay);
        
        $form = $this->createForm(new AuctionType(), $auction);
        
        $form->handleRequest($request);
        
        $user = $this->getUser();
        
        if($form->isSubmitted() && $form->isValid() && $user) {
            $auction = $form->getData();
            
            $em = $this->get('doctrine')->getManager();
            
            $images = $form['images']->getData();
            
            $path_locator = $this->get('path.locator');
            
            //setting gallery objects paths
            foreach($images as $image) {
                $extension = $image->getImage()->guessExtension();
                if(!$extension) {
                    $extension = 'png';
                }
                
                $fileName = uniqid() . '.' . $extension;
                $filePath = 'images/galleries/' . $fileName;
                $image->setFilename($filePath);
                
                $file = $image->getImage();
                $file->move($path_locator->getAssetsPath() . 'images/galleries/', $fileName);
                
                $image->setAuction($auction);
            }
            
            $auction->setUser($user);
            
            $em->persist($auction);
            $em->flush();
        }
        
        return $this->render('auction/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/auctions", name="auctions_all")
     */
    function showAuction() {
        $repo = $this->get('doctrine')->getManager()->getRepository('InzynierAppBundle:Auction');
        
        $auctions = $repo->findAll();
        
        return $this->render('auction/show_all.html.twig', array(
            'auctions' => $auctions
        ));
    }
    
    /**
     * 
     * @Route("/auction/{id}", name="auction_single")
     */
    function singleAction($id, Request $request) {
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('InzynierAppBundle:Auction');
        
        $auction = $repo->find($id);
        $user = $this->getUser();
        $bids = $repo->getNewestBids($auction->getId());
        $highest_bid = (isset($bids[0]) ? $bids[0] : null);
        
        //getting geolocation data
        $address = $auction->getAddress();
        $geolocator = $this->get('geolocator');

        if($address) {
            try {
                $map = $geolocator->getAddressMap($address);
            } catch(\Geocoder\Exception\NoResultException $ex) {
                $map = null;
            }
        } else {
            $map = null;
        }
        
        //incrementing view count
        $views = $auction->getViews();
        $auction->setViews($views + 1);
        $em->persist($auction);
        $em->flush();
        
        //display and handle new bid form
        $bid = new Bid();
        $bid_default = (!is_null($highest_bid)) ? $highest_bid->getPrice() + 10 : $auction->getPrice();
        $bid->setPrice($bid_default);
        $bid_form = $this->createForm(new BidType, $bid);
        
        $bid_form->handleRequest($request);
        
        if($bid_form->isValid() && $bid_form->isSubmitted()) {
            $bid->setUser($user);
            $bid->setAuction($auction);
            
            $em->persist($bid);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            
            return $this->redirectToRoute('auction_single', array(
                'id' => $auction->getId(),
                'msg' => $flash->success('You just have placed an offer!'),
            ));
        }
        
        return $this->render('auction/single.html.twig', array(
            'auction' => $auction,
            'bid_form' => $bid_form->createView(),
            'bids' => $bids,
            'highest_bid' => $highest_bid,
            'map' => $map,
        ));
    }
}