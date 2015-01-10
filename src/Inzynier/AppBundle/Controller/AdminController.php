<?php
namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Inzynier\AppBundle\Form\Type\UserType;
use Inzynier\AppBundle\Form\Type\AuctionCategoryType;
use Inzynier\AppBundle\Entity\AuctionCategory;
use Inzynier\AppBundle\Entity\User;

class AdminController extends Controller {
    /**
     * @Route("/admin", name="admin_index")
     */
    public function indexAction() {
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        $userCount = $repo->getUsersCount();
        
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Auction');
        $auctionCount = $repo->getAuctionsCount();
        $activeAuctionCount = $repo->getActiveAuctionsCount();
        
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:AuctionCategory');
        $categories = $repo->findAll();
        $categories = count($categories);
        
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Post');
        $posts = $repo->findAll();
        $posts = count($posts);
        
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Comment');
        $comments = $repo->findAll();
        $comments = count($comments);
        
        return $this->render('admin/index.html.twig', array(
            'user_count' => $userCount,
            'auctions_count' => $auctionCount,
            'active_auctions' => $activeAuctionCount,
            'categories' => $categories,
            'posts' => $posts,
            'comments' => $comments,
        ));
    }
    
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function usersManageAction(Request $request) {
        $em = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        
        $users = $em->findBy([], ['dateAdded' => 'DESC']);
        
        $paginator = $this->get('knp_paginator');
        $page = $request->query->get('page', 1);
        $users = $paginator->paginate($users, $page, 10);
        
        return $this->render('admin/manage/users.html.twig', array(
            'users' => $users,
        ));
    }
    
    /**
     * @Route("/admin/users/edit/{user}", name="admin_users_edit")
     */
    public function userAddAction(Request $request, User $user) {
        $form = $this->createForm(new UserType(), $user);
        
        return $this->render('admin/manage/users_add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function adminCategoriesAction() {
        return $this->render('admin/manage/categories.html.twig');
    }
    
    /**
     * @Route("/admin/categories/add", name="admin_category_add")
     */
    public function addCategoryAction(Request $request) {
        $category = new AuctionCategory();
        
        $form = $this->createForm(new AuctionCategoryType(), $category);
        
        $form->handleRequest($request);
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if($category->getParent()) {
                $parent = $category->getParent();
                $parent->addChild($category);
                $em->persist($parent);
            }
            
            $em->persist($category);
            $em->flush();
            
            $flash->success('Successfully added new category.');
            
            return $this->redirectToRoute('admin_category_add');
        }
        
        return $this->render('admin/manage/category_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/admin/users/block/{user}", name="admin_users_block")
     */
    public function userBlockAction(Request $request, User $user) {
        if($user->getIsActive()) {
            $user->setIsActive(0);
        } else {
            $user->setIsActive(1);
        }
        
        $em = $this->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        $flash->success('Blocked user account');
        
        return $this->redirectToRoute('admin_users');
    }
    
    /**
     * @Route("/admin/users/delete/{user}", name="admin_users_delete")
     */
    public function userDeleteAction(Request $request, User $user) {
        $em = $this->get('doctrine')->getManager();
        $em->remove($user);
        $em->flush();
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        $flash->success('Removed an user account');
        
        return $this->redirectToRoute('admin_users');
    }
}