<?php
namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Inzynier\AppBundle\Form\Type\UserType;
use Inzynier\AppBundle\Form\Type\AuctionCategoryType;
use Inzynier\AppBundle\Entity\AuctionCategory;
use Inzynier\AppBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        
        $form->handleRequest($request);
        
        if($form->isValid()) {
            $password = $user->getPassword();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $hashed = $encoder->encodePassword($password, NULL);
            $user->setPassword($hashed);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('Updated ' . $user->getUsername() . "'s account");
        }
        
        return $this->render('admin/manage/users_add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function adminCategoriesAction(Request $request) {
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:AuctionCategory');
        $categories = $repo->findAll();
        
        $page = $request->query->get('page', 1);
        $paginator = $this->get('knp_paginator');
        $categories = $paginator->paginate($categories, $page, 10);
        
        return $this->render('admin/manage/categories.html.twig', [
            'categories' => $categories,
        ]);
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
    
    /**
     * @Route("/admin/auctions", name="admin_auctions")
     */
    public function auctionsAction(Request $request) {
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Auction');
        $auctions = $repo->getActiveAuctions();
        
        $page = $request->query->get('page', 1);
        $paginator = $this->get('knp_paginator');
        $auctions = $paginator->paginate($auctions, $page, 10);
        
        return $this->render('admin/manage/auctions.html.twig', [
            'auctions' => $auctions,
        ]);
    }
    
    /**
     * @Route("/admin/auction/delete", name="admin_auction_delete")
     */
    public function deleteAuction(Request $request) {
        $security = $this->get('security.context');
        if(!$security->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        $auction_id = $request->request->get('auction_id');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('InzynierAppBundle:Auction');
        
        $auction = $repo->find($auction_id);
        
        $em->remove($auction);
        $em->flush();
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        $flash->success('Deleted an auction.');
        
        return $this->redirectToRoute('admin_auctions');
    }
    
    /**
     * @Route("/admin/category/delete", name="admin_category_delete")
     */
    public function deleteCategoryAction(Request $request) {
        $security = $this->get('security.context');
        if(!$security->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }
        
        $category_id = $request->request->get('category_id');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('InzynierAppBundle:AuctionCategory');
        
        $category = $repo->find($category_id);
        
        $em->remove($category);
        $em->flush();
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        $flash->success('Deleted a category.');
        
        return $this->redirectToRoute('admin_categories');
    }
    
    /**
     * @Route("/admin/category/edit/{category}", name="admin_edit_category")
     */
    public function editCategory(Request $request, AuctionCategory $category) {
        $form = $this->createForm(new AuctionCategoryType(), $category);
        
        $form->handleRequest($request);
        
        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('Edited a category.');
        }
        
        return $this->render('admin/manage/category_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}