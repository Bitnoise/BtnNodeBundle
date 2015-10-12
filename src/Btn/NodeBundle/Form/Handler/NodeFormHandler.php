<?php

namespace Btn\NodeBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Btn\AdminBundle\Form\Handler\FormHandlerInterface;
use Btn\BaseBundle\Provider\EntityProviderInterface;
use Btn\NodeBundle\Provider\NodeContentProviders;

class NodeFormHandler implements FormHandlerInterface
{
    /** \Btn\BaseBundle\Provider\EntityProvider $entityProvider */
    protected $entityProvider;
    /** \Btn\NodeBundle\Provider\NodeContentProviders $contentProviders */
    protected $contentProviders;

    /**
     *
     */
    public function __construct(EntityProviderInterface $entityProvider, NodeContentProviders $contentProviders)
    {
        $this->entityProvider   = $entityProvider;
        $this->contentProviders = $contentProviders;
    }

    /**
     *
     */
    public function handle(FormInterface $form, Request $request = null)
    {
        if (!$request) {
            throw new \Exception('$request parameter is required for handle() method');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get node entity
            $entity       = $form->getData();

            // get provider form data for resolver
            $providerData = $form->has('providerParameters') ? $form->get('providerParameters')->getData() : array();

            $providerId = $entity->getProviderId();

            if ($providerId && $entity->isProviderLocked()) {
                // don't modify nothing if provider is locked 
            } elseif ($providerId) {
                $contentProvider = $this->contentProviders->get($providerId);

                //get correct route name from service
                $route                  = $contentProvider->resolveRoute($providerData);
                $routeParameters        = $contentProvider->resolveRouteParameters($providerData);
                $controlRoute           = $contentProvider->resolveControlRoute($providerData);
                $controlRouteParameters = $contentProvider->resolveControlRouteParameters($providerData);

                //set routeName to the node
                $entity->setRoute($route);
                $entity->setRouteParameters($routeParameters);
                $entity->setControlRoute($controlRoute);
                $entity->setControlRouteParameters($controlRouteParameters);
            } else {
                $entity->setRoute(null);
                $entity->setRouteParameters(array());
                $entity->setControlRoute(null);
                $entity->setControlRouteParameters(array());
            }

            if ($form->get('save')->isClicked()) {
                $this->entityProvider->save($entity);

                return true;
            }
        }
    }
}
