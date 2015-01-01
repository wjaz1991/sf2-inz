<?php

namespace Inzynier\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

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
        $dql = 'SELECT a FROM Inzynier\AppBundle\Entity\Auction a ORDER BY a.startDate';
        $query = $em->createQuery($dql);
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
}

