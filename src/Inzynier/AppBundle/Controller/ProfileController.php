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
}

