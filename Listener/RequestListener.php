<?php

namespace Btn\NodeBundle\Listener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener implements EventSubscriberInterface
{
    /**
     *
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $event->getRequest()->attributes->set('_request_type', $event->getRequestType());
    }

    /**
     *
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 200),
        );
    }
}
