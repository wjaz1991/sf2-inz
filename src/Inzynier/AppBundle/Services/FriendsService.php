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
    
    public function getFriendsPosts($friends) {
        if(!is_array($friends) || is_null($friends)) {
            return null;
        }
        
        $em = $this->doctrine->getManager();
        
        $dql = 'SELECT p FROM Inzynier\AppBundle\Entity\Post p WHERE p.user IN(:users) ORDER BY p.dateAdded DESC';
        
        $query = $em->createQuery($dql);
        $query->setParameter('users', $friends);
        $results = $query->getResult();
        
        return $results;        
    }
    
    public function getWallData($user, $start, $end = null) {
        if(is_null($end)) {
            $end = new \DateTime();
        }
        
        $friends = $this->getUserFriends($user, true);
        
        //getting friends auctions
        $dql = "SELECT a FROM Inzynier\AppBundle\Entity\Auction a WHERE a.user IN(:friends) "
                . "AND a.endDate >= :date AND a.dateAdded >= :start AND a.dateAdded < :end ORDER BY a.startDate DESC";
        
        $now = new \DateTime();
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('friends', $friends);
        $query->setParameter('start', $start);
        $query->setParameter('end', $end);
        $query->setParameter('date', $now);
        
        $auctions = $query->getResult();
        
        //getting friends posts
        $dql = 'SELECT p FROM Inzynier\AppBundle\Entity\Post p WHERE p.user IN(:friends) '
                . ' AND p.dateAdded >= :start AND p.dateAdded < :end ORDER BY p.dateAdded DESC';
        $query = $em->createQuery($dql);
        $query->setParameter('friends', $friends);
        $query->setParameter('start', $start);
        $query->setParameter('end', $end);
        
        $posts = $query->getResult();
        
        //merging data arrays
        $data = array_merge($auctions, $posts);
        
        //sorting data by date added
        usort ($data,
        function($val1, $val2) 
        {
            $value1 = $val1->getDateAdded();
            $value2 = $val2->getDateAdded();
            if ($value1 > $value2) {
                return -1;
            } elseif ($value1 < $value2) {
                return 1;
            } else {
                return 0;
            }
        });
        
        return $data;
    }
    
    public function getRecommendedPeople(User $user) {
        $friends = $this->getUserFriends($user, true);
        $friend_ids = [];
        $friend_ids[] = $user->getId();
        foreach($friends as $friend) {
            $friend_ids[] = $friend->getId();
        }
        
        $friend_array = [];
        foreach($friends as $friend) {
            $ff = $this->getUserFriends($friend, true);
            $friend_array = array_merge($friend_array, $ff);
        }
        
        $recommended = [];
        foreach($friend_array as $person) {
            if(!in_array($person->getId(), $friend_ids)) {
                $recommended[] = $person;
            }
        }
        
        return $recommended;
    }
}