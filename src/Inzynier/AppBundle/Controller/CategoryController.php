<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Inzynier\AppBundle\Entity\AuctionCategory;

class CategoryController extends Controller {
    /**
     * @Route("/categories/all", name="category_all")
     */
    public function listAction() {
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:AuctionCategory');
        
        $categories = $repo->findAll();
        
        $category_service = $this->get('category.service');
        
        $transformed = $category_service->transformArray($categories);
        
        $categories = $transformed['data'];
        $levels = $transformed['levels'];
        $lists = $transformed['lists'];
        
        return $this->render('category/list.html.twig', [
            'categories' => $categories,
            'levels' => $levels,
            'lists' => $lists,
        ]);
    }
    
    /**
     * @Route("/categories/{category}/{page}", defaults={"page": 1}, name="category_single")
     */
    public function singleAction(AuctionCategory $category, $page) {
        $cat_service = $this->get('category.service');
        
        $parents = $cat_service->getParentCategories($category);
        $children = $cat_service->getChildCategories($category);
        
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:AuctionCategory');
        
        $auctions = $repo->getAuctionsByCategories($children);
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($auctions, $page, 5);
        
        return $this->render('category/single.html.twig', [
            'category' => $category,
            'pagination' => $pagination,
            'parents' => $parents,
        ]);
    }
}