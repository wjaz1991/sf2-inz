<?php

namespace Inzynier\AppBundle\Services;

use Inzynier\AppBundle\Entity\User;
use Inzynier\AppBundle\Entity\Friendship;

class FriendsService {
    private $doctrine;
    
    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }
    
    public function getUserFriends(User $user, $accepted) {
        $repo = $this->doctrine->getRepository('InzynierAppBundle:User');
        $friends = $repo->getFriends($user, $accepted);
        
        return $friends;
    }
    
    public function getFriendRequests(User $user, $accepted) {
        $repo = $this->doctrine->getRepository('InzynierAppBundle:User');
        $friends = $repo->getFriendRequests($user, $accepted);
        
        return count($friends) > 0 ? count($friends) : null;
    }
    
    public function getFriendRequestsArray(User $user, $accepted) {
        $repo = $this->doctrine->getRepository('InzynierAppBundle:User');
        $friends = $repo->getFriendRequests($user, $accepted);
        
        return $friends;
    }
}