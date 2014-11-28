<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inzynier\AppBundle\Repository\UserRepository;
use Inzynier\AppBundle\Entity\User;

class AjaxController extends Controller {
    /**
     * @Route("/ajax/check_username", name="ajax_check_username", options={"expose":true})
     */
    public function checkUsernameAction(Request $request) {
        $username = $request->request->get('username');
        
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        
        $user = $repo->findOneByUsername($username);
        
        $json = array();
        
        if($user) {
            $json['result'] = 1;
        } else {
            $json['result'] = 0;
        }
        
        return new JsonResponse($json);
    }
}
