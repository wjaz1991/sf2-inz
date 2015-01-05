<?php
namespace Inzynier\AppBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Inzynier\AppBundle\Entity\User;

class UserRepository extends EntityRepository implements UserProviderInterface {
    //finding some number of newest users
    public function findNewest($number) {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT u FROM Inzynier\AppBundle\Entity\User u ORDER BY u.dateAdded DESC');
        $query->setFirstResult(0);
        $query->setMaxResults($number);
        
        $users = $query->getResult();
        
        return $users;       
    }
    
    //get count of all users
    public function getUsersCount() {
        $em = $this->getEntityManager();
        
        $query = $em->createQuery('SELECT COUNT(u.id) FROM Inzynier\AppBundle\Entity\User u');
        $count['all'] = $query->getSingleScalarResult();
        
        $query = $em->createQuery('SELECT COUNT(u.id) FROM Inzynier\AppBundle\Entity\User u WHERE u.isActive = 1');
        $count['active'] = $query->getSingleScalarResult();
        
        return $count;
    }
    
    public function loadUserByUsername($username) {
        $query = $this->createQueryBuilder('u')
                ->where('u.username = :username OR u.email = :email')
                ->setParameter('username', $username)
                ->setParameter('email', $username)
                ->getQuery();
        
        try {
            $user = $query->getOneOrNullResult();
        } catch (NoResultException $ex) {
            $message = 'Unable to find an user with provided username or email';
            throw new UsernameNotFoundException($message, 0, $ex);
        }
        
        return $user;
    }
    
    public function refreshUser(UserInterface $user) {
        $class = get_class($user);
        if(!$this->supportsClass($class)) {
            throw new UnsupportedUserException('These user instances are not supported');
        }
        
        return $this->find($user->getId());
    }
    
    public function supportsClass($class) {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
    
    //getting user friends
    public function getFriends(User $user, $accepted = true) {
        $em = $this->getEntityManager();
        
        $dql = 'SELECT DISTINCT f FROM Inzynier\AppBundle\Entity\Friendship f '
                . 'JOIN f.user_one uo JOIN f.user_two ut '
                . 'WHERE f.accepted = :accepted AND (uo.id=:id OR ut.id=:id)'
                . ' AND f.rejected != 1';
        $query = $em->createQuery($dql);
        $query->setParameter('id', $user->getId());
        $query->setParameter('accepted', $accepted);
        
        $results = $query->getResult();
        
        $friends = array();
        foreach($results as $friendship) {
            //dump($friendship);
            $friends[] = ($friendship->getUserOne() === $user)
                    ? $friendship->getUserTwo()
                    : $friendship->getUserOne();
        }
        
        return $friends;
    }
    
    //getting friend requests
    public function getFriendRequests(User $user, $accepted = true) {
        $em = $this->getEntityManager();
        
        $dql = 'SELECT DISTINCT f FROM Inzynier\AppBundle\Entity\Friendship f '
                . 'WHERE f.user_two = :id AND f.accepted = :accepted AND f.rejected != 1';
        $query = $em->createQuery($dql);
        $query->setParameter('id', $user->getId());
        $query->setParameter('accepted', $accepted);
        
        $results = $query->getResult();
        
        return $results;
    }
}
