<?php

namespace Inzynier\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class FriendshipRepository extends EntityRepository {
    public function findExisting($userOne, $userTwo) {
        $em = $this->getEntityManager();
        $dql = 'SELECT f FROM Inzynier\AppBundle\Entity\Friendship f WHERE (f.user_one = :userOne AND f.user_two = :userTwo) '
                . ' OR (f.user_two = :userOne AND f.user_one = :userTwo)';
        
        $query = $em->createQuery($dql);
        $query->setParameter('userOne', $userOne);
        $query->setParameter('userTwo', $userTwo);
        
        $results = $query->getResult();
        
        if(count($results)) {
            return $results[0];
        } else {
            return null;
        }
    }
}