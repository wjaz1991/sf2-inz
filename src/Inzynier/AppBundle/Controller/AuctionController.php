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
            
            $images = $auction->getImages();
            
            //saving geolocation info
            $geolocator = $this->get('geolocator');
            $address = $auction->getAddress();
            $location = $geolocator->getAddressCoordinates($address);
            
            if($location) {
                $auction->getAddress()->setLatitude($location[0]['latitude']);
                $auction->getAddress()->setLongitude($location[0]['longitude']);
            }
            
            foreach($images as $image) {
                if($image) {
                    $image->setAuction($auction);
                }
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
    function showAuctions() {
        $repo = $this->get('doctrine')->getManager()->getRepository('InzynierAppBundle:Auction');
        
        $auctions = $repo->findActive();
        
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
        
        $category_srv = $this->get('category.service');
        $parent_categories = $category_srv->getParentCategories($auction->getCategory());
        
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
        
        $active = $auction->isActive();
        
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
            $flash = $this->get('braincrafted_bootstrap.flash');
            
            if((is_null($highest_bid) && $bid->getPrice() >= $auction->getPrice())
                    || $highest_bid->getPrice() < $bid->getPrice()) {
                $bid->setUser($user);
                $bid->setAuction($auction);

                $em->persist($bid);
                $em->flush();
                
                $msg = $flash->success('You just have placed an offer!');
            } else {
                $msg = $flash->error('Failed to place new bid! You must specify an offer with higher value than current highest bid.');
            }

            return $this->redirectToRoute('auction_single', array(
                'id' => $auction->getId(),
                'msg' => $msg,
            ));
        }
        
        return $this->render('auction/single.html.twig', array(
            'auction' => $auction,
            'bid_form' => $bid_form->createView(),
            'bids' => $bids,
            'highest_bid' => $highest_bid,
            'map' => $map,
            'active' => $active,
            'parent_categories' => $parent_categories,
        ));
    }
    
    /**
     * @Route("/auction/edit/{id}", name="auction_edit")
     */
    public function editAction(Request $request, $id) {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('InzynierAppBundle:Auction');
        $auction = $repo->find($id);
        
        $images = $auction->getImages();
        
        if($user->getId() != $auction->getUser()->getId()) {
            return $this->redirectToRoute('auction_single', array(
                'id' => $id,
            ));
        }
        
        $form = $this->createForm(new AuctionType(), $auction);
        
        $form->handleRequest($request);
        
        if($form->isValid() && $form->isSubmitted()) {
            $images = $auction->getImages();
            
            foreach($images as $image) {
                if($image) {
                    $image->setAuction($auction);
                }
            }
            
            $em->persist($auction);
            $em->flush();
            
            return $this->redirectToRoute('auction_single', array(
                'id' => $id,
            ));
        }
        
        return $this->render('auction/edit.html.twig', array(
            'form' => $form->createView(),
            'auction' => $auction,
        ));
    }
    
    /**
     * @Route("/auction/{auction}/delete", name="auction_delete")
     */
    public function deleteAction(Auction $auction) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($auction);
        $em->flush();
        
        return $this->redirectToRoute('profile_auctions');
    }
}