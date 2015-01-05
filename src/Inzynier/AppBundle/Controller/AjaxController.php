<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inzynier\AppBundle\Repository\UserRepository;
use Inzynier\AppBundle\Entity\User;
use Inzynier\AppBundle\Entity\AuctionVote;
use Inzynier\AppBundle\Entity\PostVote;

class AjaxController extends Controller {
    /**
     * @Route("/ajax/check_username", name="ajax_check_username", options={"expose":true})
     */
    public function checkUsernameAction(Request $request) {
        $username = $request->request->get('username');
        
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        
        $user = $repo->findOneByUsername($username);
        
        $json = array();
        
        if($user) {
            $json['result'] = 1;
        } else {
            $json['result'] = 0;
        }
        
        return new JsonResponse($json);
    }
    
    /**
     * An AJAX method that returns array of User objects, which requested
     * friendship with provided user.
     * 
     * @param Request $request object representing current HTTP request
     * 
     * @return JsonResponse json array containing user objects
     *
     * @Route("/ajax/get_pending_friends", name="ajax_pending_friends", options={"expose": true})
     */
    public function getPendingFriendsAction(Request $request) {
        $id = $request->request->get('id');
        $repo = $this->getDoctrine()->getManager()->getRepository('InzynierAppBundle:User');
        $user = $repo->find($id);
        
        $friend_service = $this->get('friends.service');
        $pending = $friend_service->getFriendRequestsArray($user, false);
        
        $counter = 0;
        $users = array();
        foreach($pending as $friendship) {
            $invitee = $friendship->getUserOne();
            $users[$counter]['username'] = $invitee->getUsername();
            $users[$counter]['id'] = $invitee->getId();
            $users[$counter]['link'] = $this->generateUrl('user_view', ['user' => $invitee->getId()]);
            if($invitee->getAvatar()) {
                $users[$counter]['avatar'] = $invitee->getAvatar()->getWebPath();
            }
            $counter++;
        }
        
        return new JsonResponse($users);
    }
    
    /**
     * 
     * @param Request $request
     * 
     * @Route("/ajax/friend_accept", name="ajax_friend_accept", options={"expose": true})
     */
    public function acceptFriendAction(Request $request) {
        $id = $request->request->get('id');
        $action = $request->request->get('action');
        
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('InzynierAppBundle:User');
        $friendRepo = $em->getRepository('InzynierAppBundle:Friendship');
        
        $user = $this->getUser();
        $accepted = $userRepo->find($id);
        
        $friendship = $friendRepo->findOneBy(array(
            'user_one' => $accepted,
            'user_two' => $user,
        ));
        
        if($action == 'accept') {
            $friendship->setAccepted(true);
        } else {
            $friendship->setRejected(true);
        }
        
        $em->flush();
        
        $json['user_one'] = $friendship->getUserOne()->getUsername();
        $json['user_two'] = $friendship->getUserTwo()->getUsername();
        
        return new JsonResponse($json);
    }
    
    /**
     * @Route("/ajax/search", name="ajax_search", options={"expose": true})
     */
    public function searchAction(Request $request) {
        $search_term = $request->request->get('text');
        
        if(strlen($search_term) == 0) {
            return new JsonResponse([]);
        }
        
        //searching for users
        $elastic = $this->get('fos_elastica.finder.website');
        $results = $elastic->find('*' . $search_term . '*');
        
        $json = [];
        
        //if there are some results, segregate them
        if(count($results)) {
            $counter_users = 0;
            $counter_auctions = 0;
            foreach($results as $entity) {
                if($entity instanceof \Inzynier\AppBundle\Entity\User) {
                    if($counter_users >= 5) {
                        continue;
                    }
                    $json['users'][$counter_users]['username'] = $entity->getUsername();
                    $json['users'][$counter_users]['link'] = $this->generateUrl('user_view', ['user' => $entity->getId()]);
                    if($entity->getAvatar()) {
                        $json['users'][$counter_users]['avatar'] = $entity->getAvatar()->getWebPath();
                    }
                    $counter_users++;
                } else if($entity instanceof \Inzynier\AppBundle\Entity\Auction) {
                    if($counter_auctions >= 5) {
                        continue;
                    }
                    $now = new \DateTime();
                    if($entity->getEndDate() <= $now) {
                        continue;
                    }
                    $json['auctions'][$counter_auctions]['title'] = $entity->getTitle();
                    if(strlen($entity->getDescription()) > 70) {
                        $json['auctions'][$counter_auctions]['description'] = substr($entity->getDescription(), 0, 70) . '...';
                    } else {
                        $json['auctions'][$counter_auctions]['description'] = $entity->getDescription();
                    }
                    $json['auctions'][$counter_auctions]['image'] = $entity->getFirstImage();
                    $json['auctions'][$counter_auctions]['link'] = $this->generateUrl('auction_single', [
                        'id' => $entity->getId(),
                    ]);
                    
                    $counter_auctions++;
                }
            }
        }
        
        return new JsonResponse($json);
    }
    
    /**
     * @Route("/ajax/vote", name="ajax_vote", options={"expose": true})
     */
    public function voteAction(Request $request) {
        $type = $request->request->get('type', null);
        $entity_type = $request->request->get('entity_type', null);
        $entity_id = $request->request->get('entity_id', null);
        
        if(!is_null($type) && $entity_type && $entity_id) {
            $em = $this->get('doctrine')->getManager();
            
            if($entity_type == 'post') {
                $repo = $this->get('doctrine')->getRepository('InzynierAppBundle:Post');
                $post = $repo->find($entity_id);
                
                //check for existing vote
                $vote_repo = $this->get('doctrine')->getRepository('InzynierAppBundle:PostVote');
                $existing = $vote_repo->findBy([
                    'post' => $post,
                    'user' => $this->getUser(),
                ]);
                
                if($existing && $existing[0]->getType() == $type) {
                    $post_vote = $existing[0];
                    $em->remove($post_vote);
                    $em->flush();
                    return new JsonResponse($post->getVotesCount());
                } else if($existing && $existing[0]->getType() != $type) {
                    $post_vote = $existing[0];
                    $post_vote->setType($type);
                } else {
                    $post_vote = new PostVote();
                    $post_vote->setPost($post);
                    $post_vote->setType($type);
                    $post_vote->setUser($this->getUser());
                }
                
                $em->persist($post_vote);
                $em->flush();
                
                $data = $post->getVotesCount();
                
                return new JsonResponse($data);
            }
            
            if($entity_type == 'auction') {
                $repo = $this->get('doctrine')->getRepository('InzynierAppBundle:Auction');
                $auction = $repo->find($entity_id);
                
                //check for existing vote
                $vote_repo = $this->get('doctrine')->getRepository('InzynierAppBundle:AuctionVote');
                $existing = $vote_repo->findBy([
                    'auction' => $auction,
                    'user' => $this->getUser(),
                ]);
                
                if($existing && $existing[0]->getType() == $type) {
                    $auction_vote = $existing[0];
                    $em->remove($auction_vote);
                    $em->flush();
                    return new JsonResponse($auction->getVotesCount());
                } else if($existing && $existing[0]->getType() != $type) {
                    $auction_vote = $existing[0];
                    $auction_vote->setType($type);
                } else {
                    $auction_vote = new AuctionVote();
                    $auction_vote->setAuction($auction);
                    $auction_vote->setType($type);
                    $auction_vote->setUser($this->getUser());
                }
                
                $em->persist($auction_vote);
                $em->flush();
                
                $data = $auction->getVotesCount();
                
                return new JsonResponse($data);
            }
        }
        
        return new JsonResponse([]);
    }
}
