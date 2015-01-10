<?php

namespace Inzynier\AppBundle\Listeners;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LoggedLocaleListener
{
    private $session;

    public function setSession(Session $session)
    {
        $this->session = $session;
    }
    
    /**
     * security.interactive_login event. If a user chose a language in preferences, it would be set,
     * if not, a locale that was set by setLocaleForUnauthenticatedUser remains.
     *
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function setLocaleForAuthenticatedUser(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        
        /*if(function_exists($user->getLocale())) {
            if ($lang = $user->getLanguage()) {
                $this->session->set('_locale', $lang);
            }
        }*/
    }
}