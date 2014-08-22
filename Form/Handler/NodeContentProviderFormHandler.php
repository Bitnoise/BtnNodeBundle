<?php

namespace Btn\NodeBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Btn\AdminBundle\Form\Handler\FormHandlerInterface;
use Btn\BaseBundle\Provider\EntityProviderInterface;
use Btn\NodeBundle\Provider\NodeContentProviderInterface;

class NodeContentProviderFormHandler implements FormHandlerInterface
{
    /** \Btn\BaseBundle\Provider\EntityProvider $entityProvider */
    protected $entityProvider;
    /** \Btn\NodeBundle\Provider\NodeContentProviderInterface $ncp */
    protected $nodeContentProvider;

    /**
     *
     */
    public function __construct(EntityProviderInterface $entityProvider)
    {
        $this->entityProvider = $entityProvider;
    }

    /**
     *
     */
    public function setNodeContentProvider(NodeContentProviderInterface $nodeContentProvider)
    {
        $this->nodeContentProvider = $nodeContentProvider;

        return $this;
    }

    /**
     *
     */
    public function handle(FormInterface $form, Request $request = null)
    {
        if (!$this->nodeContentProvider) {
            throw new \Exception('Set node content provider via setNodeContentProvider() method before handeling form');
        }

        if (!$request) {
            throw new \Exception('$request parameter is required for handle() method');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get node entity
            $entity       = $form->getData();
            // get provider form data for resolver
            $providerData = $form->get('providerForm')->getData();

            //get correct route name from service
            $route                  = $this->nodeContentProvider->resolveRoute($providerData);
            $routeParameters        = $this->nodeContentProvider->resolveRouteParameters($providerData);
            $controlRoute           = $this->nodeContentProvider->resolveControlRoute($providerData);
            $controlRouteParameters = $this->nodeContentProvider->resolveControlRouteParameters($providerData);

            //set routeName to the node
            $entity->setRoute($route);
            $entity->setRouteParameters($routeParameters);
            $entity->setControlRoute($controlRoute);
            $entity->setControlRouteParameters($controlRouteParameters);
            $entity->setProviderName($this->nodeContentProvider->getName());
            $this->entityProvider->save($entity);

            return true;
        }
    }
}
