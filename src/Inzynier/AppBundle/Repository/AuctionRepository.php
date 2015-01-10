<?php

namespace Inzynier\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Inzynier\AppBundle\Entity\User;

class AuctionRepository extends EntityRepository {
    public function getNewestBids($auction_id) {
        $em = $this->getEntityManager();
        $dql = 'SELECT b FROM Inzynier\AppBundle\Entity\Bid b WHERE b.auction = :auction ORDER BY b.price DESC';
        $query = $em->createQuery($dql)->setParameter('auction', $auction_id);
        
        $bids = $query->getResult();
        
        return $bids;
    }
    
    public function getNewestAuctions($number) {
        $em = $this->getEntityManager();
        $dql = 'SELECT a FROM Inzynier\AppBundle\Entity\Auction a WHERE a.endDate > :date ORDER BY a.startDate DESC';
        $query = $em->createQuery($dql);
        $now = new \DateTime();
        $query->setParameter('date', $now);
        $query->setMaxResults($number);
        
        $auctions = $query->getResult();
        
        return $auctions;
    }
    
    public function findActive() {
        $em = $this->getEntityManager();
        $now = new \DateTime();
        
        $dql = 'SELECT a FROM Inzynier\AppBundle\Entity\Auction a WHERE a.endDate >= :now ORDER BY a.startDate DESC';
        $query = $em->createQuery($dql);
        $query->setParameter('now', $now->format('Y-m-d H:i:s'));
        
        $auctions = $query->getResult();
        
        return $auctions;
    }
    
    public function getUserAuctions(User $user, $active = true) {
        $em = $this->getEntityManager();
        
        if($active) {
            $active_where = ' AND a.endDate >= :date ';
        } else {
            $active_where = ' AND a.endDate < :date ';
        }
        
        $dql = 'SELECT a FROM Inzynier\AppBundle\Entity\Auction a WHERE a.user = :user' . $active_where . 'ORDER BY a.startDate DESC';
        $query = $em->createQuery($dql);
        $query->setParameter('user', $user);
        $today = new \DateTime();
        $query->setParameter('date', $today->format('Y-m-d H:i:s'));
        
        $results = $query->getResult();
        
        return $results;
    }
    
    public function getFriendsAuctions($friends) {
        $em = $this->getEntityManager();
        
        $auctions = [];
        
        $dql = "SELECT a FROM Inzynier\AppBundle\Entity\Auction a WHERE a.user = :user "
                . "AND a.endDate >= :date ORDER BY a.startDate DESC";
        $query = $em->createQuery($dql);
        $today = new \DateTime();
        $query->setParameter('date', $today);
        
        foreach($friends as $friend) {
            $query->setParameter('user', $friend);
            
            $results = $query->getResult();
            
            $auctions = array_merge($auctions, $results);
        }
        
        return $auctions;
    }
    
    public function getAuctionsByDays($days = 10) {
        $em = $this->getEntityManager();
        
        $dql = "SELECT a FROM Inzynier\AppBundle\Entity\Auction a WHERE a.endDate >= :date AND a.dateAdded >= :date_from";
        $query = $em->createQuery($dql);
        $now = new \DateTime();
        $date_from = new \DateTime();
        $date_from->modify("- $days days");
        $query->setParameter('date', $now);
        $query->setParameter('date_from', $date_from);
        
        $results = $query->getResult();
        
        return $results;
    }
    
    public function getEndedUserAuctions(User $user) {
        $em = $this->getEntityManager();
        
        $dql = 'SELECT a FROM Inzynier\AppBundle\Entity\Auction a JOIN a.bids b WHERE b.user = :user '
                . 'AND a.endDate <= :date';
        
        $query = $em->createQuery($dql);
        $query->setParameter('user', $user);
        $now = new \DateTime();
        $query->setParameter('date', $now);
        
        $results = $query->getResult();
        
        return $results;
    }
    
    public function getAuctionsCount() {
        $em = $this->getEntityManager();
        
        $dql = 'SELECT COUNT(a) FROM Inzynier\AppBundle\Entity\Auction a';
        
        $query = $em->createQuery($dql);
        
        $result = $query->getSingleScalarResult();
        
        return $result;
    }
    
    public function getActiveAuctionsCount() {
        $em = $this->getEntityManager();
        
        $dql = 'SELECT COUNT(a) FROM Inzynier\AppBundle\Entity\Auction a WHERE a.endDate >= :date';
        
        $query = $em->createQuery($dql);
        $now = new \DateTime();
        $query->setParameter('date', $now);
        
        $result = $query->getSingleScalarResult();
        
        return $result;
    }
}

