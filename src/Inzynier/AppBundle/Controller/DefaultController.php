<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * 
     * @Route("/", name="welcome")
     */
    public function indexAction()
    {
        $security = $this->get('security.context');
        if($security->isGranted('IS_AUTHENTICATED_FULLY')
                || $security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if($security->isGranted('ROLE_SUPER_ADMIN')) {
                //redirect to admin panel if user is logged as super admin
                return $this->redirect($this->generateUrl('admin_index'));
            } else {
                //redirect user to homepage
                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        //return welcome page
        return $this->render('::layout.html.twig');
    }

    /**
     * @Route("/home", name="homepage")
     */
    public function homeAction() {
        $user = $this->getUser();
        
        $addresses = $user->getAddresses();
        
        return $this->render('home.html.twig', array(
            'addresses' => $addresses,
        ));
    }
    
    /**
     * @Route("/drop", name="drop_test")
     */
    public function dropAction(Request $request) {
        //$text = print_r($_FILES, true);
        
        return $this->render('dev/drop.html.twig');
    }
    
    /**
     * @Route("/drop/upload", name="drop_upload")
     */
    public function dropUploadAction(Request $request) {
        $path_locator = $this->get('path.locator');
        
        $file = $request->files->get('file');
        
        $name = $file->getClientOriginalName();
        
        $result = $file->move($path_locator->getAssetsPath() . 'images/droptest/', $name);
        
        $json = array($result);
        
        return new \Symfony\Component\HttpFoundation\JsonResponse($json);
    }
}
