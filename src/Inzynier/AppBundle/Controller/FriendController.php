<?php
namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Inzynier\AppBundle\Entity\Friendship;

class FriendController extends Controller {
    /**
     * @Route("/friend/add", name="friend_add")
     */
    public function addFriendAction(Request $request) {
        if($request->request->get('friend_id')) {
            $friend_id = $request->request->get('friend_id');
            
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('InzynierAppBundle:User');
            
            $inviting = $this->getUser();
            $invited = $repo->find($friend_id);
            
            $friendship = new Friendship();
            
            $inviting->addMyFriend($friendship);
            $invited->addFriendWithMe($friendship);
            
            $em->persist($inviting);
            
            $em->flush();            
        }
        
        return $this->redirectToRoute('homepage');
    }
}