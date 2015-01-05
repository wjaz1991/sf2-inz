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
        $em = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        
        $userCount = $em->getUsersCount();
        
        return $this->render('admin/index.html.twig', array(
            'user_count' => $userCount,
        ));
    }
    
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function usersManageAction() {
        $em = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        
        $users = $em->findNewest(10);
        
        return $this->render('admin/manage/users.html.twig', array(
            'users' => $users,
        ));
    }
    
    /**
     * @Route("/admin/users/add", name="admin_users_add")
     */
    public function userAddAction(Request $request) {
        $user = new User();
        
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
}