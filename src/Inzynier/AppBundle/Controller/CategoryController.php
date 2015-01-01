<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller {
    /**
     * @Route("/categories/all", name="category_all")
     */
    public function listAction() {
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:AuctionCategory');
        
        $categories = $repo->findAll();
        
        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }
    
    /**
     * @Route("/categories/{category}", name="category_single")
     */
    public function singleAction(Category $category) {
        return $this->render('category/single.html.twig');
    }
}