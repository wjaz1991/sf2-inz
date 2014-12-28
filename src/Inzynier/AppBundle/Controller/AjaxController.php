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
    
    /**
     * An AJAX method that returns array of User objects, which requested
     * friendship with provided user.
     * 
     * @param Request $request object representing current HTTP request
     * 
     * @return JsonResponse json array containing user objects
     *
     * @Route("/ajax/get_pending_friends", name="ajax_pending_friends", options={"expose": true})
     */
    public function getPendingFriendsAction(Request $request) {
        $id = $request->request->get('id');
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        $user = $repo->find($id);
        
        $friend_service = $this->get('friends.service');
        $pending = $friend_service->getFriendRequestsArray($user, false);
        
        $counter = 0;
        $users = array();
        foreach($pending as $friendship) {
            $invitee = $friendship->getUserOne();
            $users[$counter]['username'] = $invitee->getUsername();
            $users[$counter]['id'] = $invitee->getId();
            if($invitee->getAvatar()) {
                $users[$counter]['avatar'] = $invitee->getAvatar()->getWebPath();
            }
            $counter++;
        }
        
        return new JsonResponse($users);
    }
    
    /**
     * 
     * @param Request $request
     * 
     * @Route("/ajax/friend_accept", name="ajax_friend_accept", options={"expose": true})
     */
    public function acceptFriendAction(Request $request) {
        $id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('InzynierAppBundle:User');
        $friendRepo = $em->getRepository('InzynierAppBundle:Friendship');
        
        $user = $this->getUser();
        $accepted = $userRepo->find($id);
        
        $friendship = $friendRepo->findOneBy(array(
            'user_one' => $accepted,
            'user_two' => $user,
        ));
        
        $friendship->setAccepted(true);
        
        $em->flush();
        
        $json['user_one'] = $friendship->getUserOne()->getUsername();
        $json['user_two'] = $friendship->getUserTwo()->getUsername();
        
        return new JsonResponse($json);
    }
}
