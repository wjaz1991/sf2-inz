<?php

namespace Inzynier\AppBundle\Services;

use Inzynier\AppBundle\Entity\AuctionCategory;
use Symfony\Component\Routing\Router;

class CategoryService {
    private $levels;
    private $lists;
    private $router;
    private $cats;
    
    public function __construct(Router $router) {
        $this->router = $router;
    }
    
    private function traverse(AuctionCategory $category, $level = 0) {
        $children = $category->getChildren();
        
        if(count($children)) {
            $this->levels[$level]++;
            foreach($children as $child) {
                $this->traverse($child, $level);
            }
        } else {
            return;
        }
    }
    
    private function buildLists(AuctionCategory $category, $level = 0) {
        $list = [];
        
        $children = $category->getChildren();
        $link = '<a href="' . $this->router->generate('category_single', ['category' => $category->getId()]) . '">'
                . $category->getName() . '</a>';
        $this->lists[$level] .= '<ul><li>' . $link;
        
        if(count($children)) {
            foreach($children as $child) {
                $this->buildLists($child, $level);
            }
            $this->lists[$level] .= '</ul>';
        }
        
        $this->lists[$level] .= '</li></ul>';
    }
    
    public function getParentCategories(AuctionCategory $category) {
        $result = [];
        
        if($category->getParent() !== null) {
            //$this->cats[] = $category->getParent();
            $result[] = $category->getParent();
            $result = array_merge($result, $this->getParentCategories($category->getParent()));
        } else {
            //$result[] = null;
        }
        
        //dump($result);
        
        return array_reverse($result);
    }
    
    public function getChildCategories(AuctionCategory $category) {
        $result = [];
        
        if($category->getChildren() !== null) {
            foreach($category->getChildren() as $child) {
                $result = array_merge($result, $this->getChildCategories($child));
            }
        }
        
        $result[] = $category;
        
        return $result;
    }
    
    public function transformArray($categories) {
        $levels = 0;
        
        $length = count($categories);
        for($i=0; $i<$length; $i++) {
            if($categories[$i]->getParent() !== null) {
                unset($categories[$i]);
            }
        }
        
        //fix keys
        $categories = array_values($categories);
        
        $counter = 0;
        foreach($categories as $category) {
            $this->levels[$counter] = 0;
            $level = $this->traverse($category, $counter);
            $counter++;
        }
        
        $counter = 0;
        foreach($categories as $category) {
            $this->lists[$counter] = '';
            //$this->lists[$counter] = '<h1>' . $category->getName() . '</h1>';
            $level = $this->buildLists($category, $counter);
            $counter++;
        }
        
        dump($this->lists);
        
        $result['data'] = $categories;
        $result['levels'] = max($this->levels);
        $result['lists'] = $this->lists;
        
        return $result;
    }
}