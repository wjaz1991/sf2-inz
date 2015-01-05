<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Inzynier\AppBundle\Classes\Registration;
use Inzynier\AppBundle\Form\Type\RegistrationType;
use Inzynier\AppBundle\Entity\User;
use Inzynier\AppBundle\Form\Type\LoginType;
use Inzynier\AppBundle\Classes\Login;

class AccountController extends Controller {
    /**
     * @Route("/register", name="account_register")
     */
    public function registerAction(Request $request) {
        $registration = new Registration();
        
        $form = $this->createForm(new RegistrationType(), $registration, array('label' => false));
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $user = $registration->getUser();
            $password = $user->getPassword();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $hashed = $encoder->encodePassword($password, NULL);
            $user->setPassword($hashed);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('Successfully created your account. You can login using new credentials.');
            
            return $this->redirectToRoute('access_login');
        }
        
        return $this->render('account/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/login", name="access_login")
     */
    public function loginAction(Request $request) {
        $session = $this->get('session');
        
        $login = new Login();
        $form = $this->createForm(new LoginType(), $login, array('label' => false));
        
        if($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        }
        else if(null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        }
        else {
            $error = '';
        }
        
        $lastUsername = ($session === null) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);
        
        $form->handleRequest($request);
        
        return $this->render('account/login.html.twig', array(
            'form' => $form->createView(),
            'lastUsername' => $lastUsername,
            'errorLogin' => $error,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/terms", name="account_terms")
     */
    public function termsAction() {
        return $this->render('account/terms.html.twig');
    }
}

