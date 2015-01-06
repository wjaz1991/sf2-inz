<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Inzynier\AppBundle\Entity\Friendship;
use Inzynier\AppBundle\Entity\User;
use Inzynier\AppBundle\Entity\Post;
use Inzynier\AppBundle\Form\Type\PostType;

class DefaultController extends Controller
{
    /**
     * 
     * @Route("/", name="welcome")
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security.context');
        if($security->isGranted('IS_AUTHENTICATED_FULLY')
                || $security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if($security->isGranted('ROLE_SUPER_ADMIN')) {
                //redirect to admin panel if user is logged as super admin
                return $this->redirect($this->generateUrl('admin_index'));
            } else {
                $user = $this->getUser();
                $locale = $user->getLanguage();
                $session = $request->getSession();
                $session->set('_locale', $locale);
                //redirect user to homepage
                return $this->redirect($this->generateUrl('homepage'));
            }
        }

        //return welcome page
        return $this->render('::welcome.html.twig');
    }

    /**
     * @Route("/home", name="homepage")
     */
    public function homeAction(Request $request) {
        $user = $this->getUser();
        
        $geolocator = $this->get('geolocator');
        $em = $this->getDoctrine()->getManager();
        $repo = $this->get('doctrine')->getManager()->getRepository('InzynierAppBundle:Auction');
        $address_msg = null;
        
        if(count($user->getAddresses())) {
            $user_address = $user->getAddresses()[0];
            $auctions = $geolocator->getNearestAuctions($user_address);
        } else {
            $auctions = null;
            $translator = $this->get('translator');
            dump($request->getLocale());
            $address_msg = $translator->trans('Add an address in your profile page to get list of nearest auctions', [], 'polish');
            dump($address_msg);
        }
        
        //post form handling
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $post->setUser($user);
            $em->persist($post);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            
            $translator = $this->get('translator');
            $message = $translator->trans('Successfully added new post!', [], 'polish');
            $flash->success($message);
            
            return $this->redirectToRoute('homepage');
        }
        
        $newest_auctions = $repo->getNewestAuctions(5);
        
        $friend_service = $this->get('friends.service');
        $friend_requests = $friend_service->getFriendRequests($user, false);
        
        //getting data from last 2 days
        $date = new \DateTime();
        $date->modify('-2 days');
        $wall_data = $friend_service->getWallData($user, $date);
        
        //get recommended people
        $recommended_people = $friend_service->getRecommendedPeople($user);
        
        if(count($recommended_people) > 5) {
            $recommended_people = array_slice($recommended_people, 0, 5);
        }
        
        return $this->render('home.html.twig', array(
            'newest_auctions' => $newest_auctions,
            'auctions' => $auctions,
            'address_message' => $address_msg,
            'friend_req_count' => $friend_requests,
            'post_form' => $form->createView(),
            'wall_data' => $wall_data,
            'date_stamp' => $date,
            'recommended_people' => $recommended_people,
        ));
    }
    
    /**
     * @Route("/people", name="user_list")
     */
    public function listUsers() {
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        $users = $repo->findAll();
        
        return $this->render('default/people.html.twig', array(
            'users' => $users,
        ));
    }
    
    /**
     * @Route("/home/load", name="home_load_data", options={"expose": true})
     */
    public function loadMoreDataAction(Request $request) {
        $user = $this->getUser();
        
        $date_end = $request->request->get('end_date');
        $date_start = $request->request->get('start_date');
        
        $start_date = new \DateTime($date_start);
        $end_date = new \DateTime($date_end);
        
        $friend_service = $this->get('friends.service');
        
        $data = $friend_service->getWallData($user, $start_date, $end_date);
        
        return $this->render('ajax/wall_data.html.twig', [
            'wall_data' => $data,
        ]);
    }
    
    /**
     * @Route("/search", name="search")
     */
    public function searchAction() {
        $elastica = $this->get('fos_elastica.finder.website');
        
        $users = $elastica->find('*iteration*');
        
        return $this->render('search/search.html.twig', [
            'users' => $users,
        ]);
    }
}
