<?php

namespace Btn\NodeBundle\Provider;

use Btn\NodeBundle\Form\RouteContentType;

class RouteNodeContentProvider implements NodeContentProviderInterface
{
    /** @var array $availableRoutes */
    protected $availableRoutes = array();

    /**
     *
     */
    public function __construct(array $availableRoutes)
    {
        $this->availableRoutes = $availableRoutes;
    }

    /**
     *
     */
    public function isEnabled()
    {
        return !empty($this->availableRoutes) ? true : false;
    }

    /**
     *
     */
    public function getForm()
    {
        return new RouteContentType($this->availableRoutes);
    }

    /**
     *
     */
    public function resolveRoute($formData = array())
    {
        return isset($formData['route']) ? $formData['route'] : null;
    }

    /**
     *
     */
    public function resolveRouteParameters($formData = array())
    {
        return array();
    }

    /**
     *
     */
    public function resolveControlRoute($formData = array())
    {
        return null;
    }

    /**
     *
     */
    public function resolveControlRouteParameters($formData = array())
    {
        return array();
    }

    /**
     *
     */
    public function getName()
    {
        return 'btn_node.route_node_content_provider.name';
    }
}
