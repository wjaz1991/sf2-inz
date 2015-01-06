<?php

namespace Inzynier\AppBundle\Listeners;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LanguageListener implements EventSubscriberInterface
{
    private $defaultLocale;
    
    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        //dump($request->getSession()->get('_locale'));
        $lang = $request->getPreferredLanguage();
        
        if (!$event->isMasterRequest()) {
            return;
        }
        if ($request->getSession()->get('_locale') == null) {
            $request->getSession()->set('_locale', $lang);
            $request->setLocale($lang);
        } else {
            $request->setLocale($request->getSession()->get('_locale'));
        }
        /*
        $request->getSession()->set('_locale', $lang);
        $request->setLocale($lang);*/
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}