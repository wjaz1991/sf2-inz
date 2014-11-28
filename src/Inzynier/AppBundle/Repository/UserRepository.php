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
        
        $users = $query->getArrayResult();
        
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
}
