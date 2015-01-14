<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Inzynier\AppBundle\Form\Type\UserType;
use Inzynier\AppBundle\Form\UserProfileType;
use Inzynier\AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Inzynier\AppBundle\Entity\Friendship;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Inzynier\AppBundle\Entity\Address;
use Inzynier\AppBundle\Form\Type\AddressType;
use Inzynier\AppBundle\Entity\Avatar;
use Inzynier\AppBundle\Form\Type\AvatarType;
use Inzynier\AppBundle\Entity\Auction;

class ProfileController extends Controller {
    /**
     * @Route("/profile/{id}", name="profile_index")
     */
    public function indexAction($id, Request $request) {
        $user = $this->getUser();
        if($id != $this->getUser()->getId()) {
            throw new AccessDeniedException();
        }
        $lang = $user->getLanguage();
        $request->getSession()->set('_locale', $lang);
        
        $editForm = $this->createForm(new UserProfileType(), $user);
        
        
        $editForm->handleRequest($request);
        
        $msg = null;
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        
        if($editForm->isValid() && $editForm->isSubmitted()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($user);
            $em->flush();
            $translator = $this->get('translator');
            $msg = $translator->trans('Successfully updated your data.', [], 'polish');
            $flash->success($msg);
            //dump('redirecting');
            $lang = $user->getLanguage();
            $request->getSession()->set('_locale', $lang);
            return $this->redirectToRoute('profile_index', ['id' => $user->getId()]);
        } else {
        
        return $this->render('profile/index.html.twig', array(
            'editForm' => $editForm->createView(),
        ));}
    }
    
    /**
     * 
     * @Route("/profile/{id}/auctions", name="profile_auctions")
     */
    public function auctionAction(Request $request, $id) {
        $user = $this->getUser();
        if($user->getId() != $id) {
            throw new AccessDeniedException();
        }
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Auction');
        
        $active_req = $request->query->get('active', true);
        if($active_req == "true") {
            $active = true;
        } else {
            $active = false;
        }
        
        $auctions = $repo->getUserAuctions($user, $active);
        
        return $this->render('profile/auctions.html.twig', [
            'auctions' => $auctions,
        ]);
    }
    
    /**
     * @Route("/user/{user}/", name="user_view")
     */
    public function viewAction(User $user, Request $request) {
        $security = $this->get('security.context');
        $is_logged = null;
        $status = null;
        
        //checking the relationship status
        if($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $is_logged = true;
            $logged_user = $this->getUser();
            
            $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Friendship');
            $friendship = $repo->findExisting($logged_user, $user);
            if($friendship && $friendship->getAccepted()) {
                $status = 'friend';
            } else if($friendship && $friendship->getRejected()){
                $status = 'rejected';
            } else if($friendship) {
                if($friendship->getUserOne() == $logged_user) {
                    $status = 'invited';
                } else if($friendship->getUserTwo() == $logged_user) {
                    $status = 'inviting';
                }
            } else {
                $status = 'none';
            }
        }
        
        $auction_repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Auction');
        $auctions = $auction_repo->getUserAuctions($user);
        
        $paginator = $this->get('knp_paginator');
        
        //retrieving and paging auctions
        $page = $request->query->get('page', 1);
        $pagination = $paginator->paginate($auctions, $page, 10);
        
        //retrieving and paging friends
        $paginator->setDefaultPaginatorOptions(['pageParameterName' => 'friends']);
        $friends_service = $this->get('friends.service');
        $friends_page = $request->query->get('friends', 1);
        $friends = $friends_service->getUserFriends($user, true);
        $friends_paginated = $paginator->paginate($friends, $friends_page, 5);
        
        //retrieving and paging user posts
        $post_repo = $this->get('doctrine')->getRepository('InzynierAppBundle:Post');
        $posts = $post_repo->findBy(['user' => $user], ['dateAdded' => 'DESC']);
        $paginator->setDefaultPaginatorOptions(['pageParameterName' => 'posts']);
        $posts_page = $request->query->get('posts', 1);
        $posts_paginated = $paginator->paginate($posts, $posts_page, 3);
        
        return $this->render('profile/view.html.twig', [
            'user' => $user,
            'is_logged' => $is_logged,
            'status' => $status,
            'pagination' => $pagination,
            'friends_paginated' => $friends_paginated,
            'posts_paginated' => $posts_paginated,
        ]);
    }
    
    /**
     * @Route("/profile/friend/action", name="profile_friend_action")
     */
    public function friendAction(Request $request) {
        $user = $this->getUser();
        $inviting = $request->request->get('user_id');
        $action = $request->request->get('action');
        
        $user_repo = $this->getDoctrine()->getRepository('InzynierAppBundle:User');
        $inviting = $user_repo->find($inviting);
        
        $friendship_repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:Friendship');
        $friendship = $friendship_repo->findBy([
            'user_one' => $inviting,
            'user_two' => $user
        ]);
        
        $friendship = $friendship[0];
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        $translator = $this->get('translator');
        
        if($action == 'accept') {
            $friendship->setAccepted(true);
            $message = $translator->trans('You just have accepted an invitation.', [], 'polish');
            $flash->success($message);
        } else if($action == 'reject') {
            $friendship->setAccepted(false);
            $friendship->setRejected(true);
            $message = $translator->trans('You just have rejected an invitation.', [], 'polish');
            $flash->warning($message);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($friendship);
        $em->flush();
        
        return $this->redirectToRoute('user_view', ['user' => $inviting->getId()]);
    }
    
    /**
     * @Route("/profile/user/invite", name="profile_invite")
     */
    public function inviteAction(Request $request) {
        $user = $this->getUser();
        $invited = $request->request->get('user_id');
        
        $user_repo = $this->getDoctrine()->getRepository('InzynierAppBundle:User');
        $invited = $user_repo->find($invited);
        
        $friendship = new Friendship();
        $friendship->setUserOne($user);
        $friendship->setUserTwo($invited);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($friendship);
        $em->flush();
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        $translator = $this->get('translator');
        
        $message = $translator->trans('You just have sent an invitation.', [], 'polish');
        $flash->success($message);
        
        return $this->redirectToRoute('user_view', ['user' => $invited->getId()]);
    }
    
    /**
     * @Route("/profile/user/remove", name="profile_friend_remove")
     */
    public function removeFriendAction(Request $request) {
        $user = $this->getUser();
        $invited = $request->request->get('user_id');
        
        $user_repo = $this->getDoctrine()->getRepository('InzynierAppBundle:User');
        $invited = $user_repo->find($invited);
        
        $repo = $this->getDoctrine()->getRepository('InzynierAppBundle:Friendship');
        
        $friendship = $repo->findBy([
            'user_one' => $user,
            'user_two' => $invited,
        ]);
        
        /*if($friendship) {
            $friendship = $friendship[0];
        }*/
        
        if(!$friendship) {
            $friendship = $repo->findBy([
                'user_two' => $user,
                'user_one' => $invited,
            ]);
        }
        
        /*if($friendship) {
            $friendship = $friendship[0];
        }*/
        
        $em = $this->get('doctrine')->getManager();
        
        $em->remove($friendship[0]);
        $em->flush();
        
        $flash = $this->get('braincrafted_bootstrap.flash');
        $translator = $this->get('translator');
        
        $message = $translator->trans('You removed this user from your friends.', [], 'polish');
        $flash->success($message);
        
        return $this->redirectToRoute('user_view', ['user' => $invited->getId()]);
    }
    
    /**
     * @Route("/profile/{id}/address", name="profile_address")
     */
    public function addressAction(Request $request, $id) {
        if($id != $this->getUser()->getId()) {
            throw new AccessDeniedException();
        }
        
        $user = $this->getUser();
        
        if($user->getAddresses()) {
            $address = $user->getAddresses()[0];
        } else {
            $address = new Address();
        }
        
        $form = $this->createForm(new AddressType(), $address);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            
            $geolocator = $this->get('geolocator');
            $location = $geolocator->getAddressCoordinates($address);
            
            if($location) {
                $address->setLatitude($location[0]['latitude']);
                $address->setLongitude($location[0]['longitude']);
            }
            
            $em = $this->get('doctrine')->getManager();
            $em->persist($address);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $translator = $this->get('translator');
            $message = $translator->trans('Updated address information!', [], 'polish');
            $flash->success($message);
            
            return $this->redirectToRoute('profile_address', [
                'id' => $this->getUser()->getId(),
            ]);
        }
        
        return $this->render('profile/address.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/profile/{user}/payments", name="profile_payments")
     */
    public function paymentsAction(User $user, Request $request) {
        if($user != $this->getUser()) {
            throw new AccessDeniedException();
        }
        
        $repo = $this->get('doctrine')->getRepository('InzynierAppBundle:Auction');
        $auctions = $repo->getEndedUserAuctions($user);
        
        $won_acutions = [];
        foreach($auctions as $auction) {
            $bid = $auction->getBids()->last();
            if($bid->getUser() == $user) {
                $won_acutions[] = $auction;
            }
        }
        
        $total_left = 0;
        
        foreach($won_acutions as $auction) {
            if($auction->getPaid() == 0) {
                $total_left += $auction->getPrice();
            }
        }
        
        $auctions = [];
        $page = $request->query->get('page', 1);
        $paginator = $this->get('knp_paginator');
        $auctions = $paginator->paginate($won_acutions, $page, 10);
        
        return $this->render('profile/payments.html.twig', [
            'auctions' => $auctions,
            'total' => $total_left,
        ]);
    }
    
    /**
     * @Route("/profile/{user}/map", name="profile_map")
     */
    public function mapAction(User $user) {
        if($this->getUser() != $user) {
            throw new AccessDeniedException();
        }
        
        $auctions = null;
        $user = $this->getUser();
        $map = null;
        $nearest_auctions = null;
        
        if(count($user->getAddresses())) {
            $geolocator = $this->get('geolocator');
            $address = $this->getUser()->getAddresses()[0];
            $nearest_auctions = $geolocator->getNearestAuctions($address, 15);
            
            $map = $geolocator->buildAddressesMap($nearest_auctions, $address);
        }
        
        return $this->render('profile/map.html.twig', [
            'auctions' => $nearest_auctions,
            'map' => $map,
        ]);
    }
    
    /**
     * @Route("/profile/{user}/avatar", name="profile_avatar")
     */
    public function avatarAction(Request $request, User $user) {
        if($user != $this->getUser()) {
            throw new AccessDeniedException();
        }
        
        $user = $this->getUser();
        
        if($user->getAvatar()) {
            $avatar = $user->getAvatar();
        } else {
            $avatar = new Avatar();
        }
        
        $form = $this->createForm(new AvatarType(), $avatar);
        
        $form->handleRequest($request);
        
        if($form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $user->setAvatar($avatar);
            $em->persist($avatar);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $translator = $this->get('translator');
            $message = $translator->trans('Successfully changed your avatar.', [], 'polish');
            $flash->success($message);
        }
        
        return $this->render('profile/avatar.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    //pay for an auction
    /**
     * @Route("/profile/pay/{auction}/{user}", name="profile_pay")
     */
    public function payAction(Request $request, Auction $auction, User $user) {
        if($user != $this->getUser()) {
            throw new AccessDeniedException();
        }
        
        $em = $this->get('doctrine')->getManager();
        $repo = $this->get('doctrine')->getRepository('InzynierAppBundle:Auction');
        $auction = $repo->find($auction);
        $flash = $this->get('braincrafted_bootstrap.flash');
        
        $auction->setPaid(1);
        $em->flush();
        
        return $this->redirectToRoute('profile_payments', ['user' => $user]);      
    }
}

