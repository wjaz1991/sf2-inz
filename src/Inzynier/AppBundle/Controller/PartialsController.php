<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PartialsController extends Controller {
    /**
     * @Route("/partial/notifications", name="partial_notifications")
     */
    public function notificationsAction() {
        $user = $this->getUser();
        
        $friend_service = $this->get('friends.service');
        $friend_req_count = $friend_service->getFriendRequests($user, false);
        
        return $this->render('partials/notifications.html.twig', [
            'friend_req_count' => $friend_req_count,
        ]);
    }
}