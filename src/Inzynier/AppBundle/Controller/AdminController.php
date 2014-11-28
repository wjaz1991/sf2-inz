<?php
namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Inzynier\AppBundle\Form\Type\UserType;
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
}