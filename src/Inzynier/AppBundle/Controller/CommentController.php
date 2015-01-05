<?php

namespace Inzynier\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Inzynier\AppBundle\Entity\Comment;

class CommentController extends Controller {
    /**
     * @Route("/comment/auction/add", name="comment_auction_add")
     */
    public function auctionCommentAdd(Request $request) {
        $user = $this->getUser();
        $auction_id = $request->request->get('auction_id', null);
        $content = $request->request->get('content', null);
        
        if($user && $auction_id && $content) {
            $em = $this->get('doctrine')->getManager();
            $repo = $em->getRepository('InzynierAppBundle:Auction');
            $auction = $repo->find($auction_id);
            
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setAuction($auction);
            $comment->setText($content);
            
            $em->persist($comment);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('Successfully added new comment.');
        }
        
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    
    /**
     * @Route("/comment/post/add", name="comment_post_add")
     */
    public function postCommentAdd(Request $request) {
        $user = $this->getUser();
        $post_id = $request->request->get('post_id', null);
        $content = $request->request->get('content', null);
        
        if($user && $post_id && $content) {
            $em = $this->get('doctrine')->getManager();
            $repo = $em->getRepository('InzynierAppBundle:Post');
            $post = $repo->find($post_id);
            
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setPost($post);
            $comment->setText($content);
            
            $em->persist($comment);
            $em->flush();
            
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('Successfully added new comment.');
        }
        
        $referer = $request->headers->get('referer');
        
        return $this->redirect($referer);
    }
    /*
    public function getRefererRoute($request)
    {
        //look for the referer route
        $referer = $request->headers->get('referer');
        dump($request);
        $lastPath = substr($referer, strpos($referer, $request->getBaseUrl()));
        $lastPath = str_replace($request->getBaseUrl(), '', $lastPath);

        $matcher = $this->get('router')->getMatcher();
        $parameters = $matcher->match($lastPath);
        $route = $parameters['_route'];

        return $route;
    }*/
}