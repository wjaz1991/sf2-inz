<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Inzynier\AppBundle\Entity\Friendship;
use Inzynier\AppBundle\Entity\User;

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
        
        $em = $this->getDoctrine()->getManager();
        $repo = $this->get('doctrine')->getManager()->getRepository('InzynierAppBundle:Auction');
        $auctions = $repo->getNewestAuctions(5);
        
        $repo = $this->get('doctrine')->getManager()->getRepository('InzynierAppBundle:User');
        $user2 = $repo->find(3);
        
        $friend_service = $this->get('friends.service');
        $friend_requests = $friend_service->getFriendRequests($user, false);
        
        $friends = $friend_service->getUserFriends($user, true);
        
        //dump($friends);
        
        
        
        return $this->render('home.html.twig', array(
            'auctions' => $auctions,
            'friends' => $friends,
            'friend_req_count' => $friend_requests,
        ));
    }
    
    /**
     * @Route("/people", name="user_list")
     */
    public function listUsers() {
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        $users = $repo->findAll();
        
        return $this->render('default/people.html.twig', array(
            'users' => $users,
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
