<?php

namespace Inzynier\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Inzynier\AppBundle\Entity\AuctionCategory;

class AuctionCategoryRepository extends EntityRepository {
    public function getAuctionsByCategories($categories) {
        $em = $this->getEntityManager();
        
        //build a category id's array
        $category_array = [];
        
        foreach($categories as $category) {
            $category_array[] = $category->getId();
        }
        
        $dql = 'SELECT a FROM Inzynier\AppBundle\Entity\Auction a '
                . ' WHERE a.category IN(:categories) AND a.endDate >= :date ORDER BY a.startDate DESC';
        
        $today = new \DateTime();
        
        $query = $em->createQuery($dql);
        $query->setParameter('categories', $category_array);
        $query->setParameter('date', $today->format('Y-m-d H:i:s'));
        $results = $query->getResult();
        
        return $results;
    }
}