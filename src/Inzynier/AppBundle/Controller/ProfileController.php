<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Inzynier\AppBundle\Form\Type\UserType;
use Inzynier\AppBundle\Form\UserProfileType;
use Inzynier\AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller {
    /**
     * @Route("/{id}/profile", name="profile_index")
     */
    public function indexAction($id, Request $request) {
        $user = $this->getUser();
        
        $editForm = $this->createForm(new UserProfileType(), $user);
        
        
        $editForm->handleRequest($request);
        
        $msg = null;
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        
        if($editForm->isValid() && $editForm->isSubmitted()) {
            $em = $this->get('doctrine')->getManager();
            dump($user);
            $em->persist($user);
            $em->flush();
            $msg = $flash->success('Successfully updated your data.');
        }
        if($editForm->isSubmitted() && !$editForm->isValid()) {
            $msg = $flash->error('Failed to update your information.');
        }
        
        return $this->render('profile/index.html.twig', array(
            'editForm' => $editForm->createView(),
            'message' => $msg,
        ));
    }
    
    /**
     * 
     * @Route("/profile/auctions", name="profile_auctions")
     */
    public function auctionAction(Request $request) {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Auction');
        
        $auctions = $repo->findBy(['user' => $user]);
        
        return $this->render('profile/auctions.html.twig', [
            'auctions' => $auctions,
        ]);
    }
    
    /**
     * @Route("/user/{user}", name="user_view")
     */
    public function viewAction(User $user) {
        $security = $this->get('security.context');
        $is_logged = null;
        $status = null;
        
        //checking the relationship status
        if($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $is_logged = true;
            $logged_user = $this->getUser();
            
            $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Friendship');
            $friendship = $repo->findExisting($logged_user, $user);
            if($friendship && $friendship->getAccepted()) {
                $status = 'friend';
            } else if($friendship && $friendship->getRejected()){
                $status = 'rejected';
            } else if($friendship) {
                if($friendship->getUserOne() == $logged_user) {
                    $status = 'invited';
                } else if($friendship->getUserTwo() == $logged_user) {
                    $status = 'inviting';
                }
            } else {
                $status = 'none';
            }
        }
        
        return $this->render('profile/view.html.twig', [
            'user' => $user,
            'is_logged' => $is_logged,
            'status' => $status,
        ]);
    }
}

