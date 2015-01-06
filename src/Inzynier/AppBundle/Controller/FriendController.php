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
            $user = $this->getUser();
            
            $friendRepo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Friendship');
            $friendship = $friendRepo->findExisting($user->getId(), $friend_id);
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $translator = $this->get('translator');
            
            if($friendship) {
                if($friendship->getAccepted()) {
                    $message = $translator->trans('You are already a friend with this user.', [], 'polish');
                    $flash->success($message);
                } else {
                    $message = $translator->trans('You already have sent an invitiation to this user, or he sent it to you.', [], 'polish');
                    $flash->success($message);
                }
                
                return $this->redirect($request->headers->get('referer'));
            }
            
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('InzynierAppBundle:User');
            
            $inviting = $this->getUser();
            $invited = $repo->find($friend_id);
            
            $friendship = new Friendship();
            
            $inviting->addMyFriend($friendship);
            $invited->addFriendWithMe($friendship);
            
            $em->persist($inviting);
            
            $em->flush();
            
            $message = $translator->trans('You have successfully sent an invitation!', [], 'polish');
            $flash->success($message);
        }
        
        return $this->redirect($request->headers->get('referer'));
    }
}