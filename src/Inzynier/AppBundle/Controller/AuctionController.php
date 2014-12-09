<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Inzynier\AppBundle\Entity\Auction;
use Inzynier\AppBundle\Form\Type\AuctionType;

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
                //var_dump('<h1>' . $image->getAuction() . '</h1>');
            }
            
            //$gallery = $auction->getImages();
            
            //$auction->setImages(array());
            
            $auction->setUser($user);
            
            $em->persist($auction);
            $em->flush();
            
            //$auction->setImages($gallery);
            //$em->persist($auction);
            //$em->flush();
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
}