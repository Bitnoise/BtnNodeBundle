<?php

namespace Btn\NodesBundle\Provider;

use Btn\NodesBundle\Form\RouteContentType;

/**
*
*
*/
class RouteContentProvider implements NodeContentProviderInterface
{

    private $availableRoutes = array();

    public function __construct($availableRoutes)
    {
        $this->availableRoutes = $availableRoutes;
    }

    public function getForm()
    {
        return new RouteContentType($this->availableRoutes);
    }

    public function resolveRoute($formData = array())
    {
        return $formData['route'];
    }

    public function resolveRouteParameters($formData = array())
    {
        return array();
    }

    public function resolveControlRoute($formData = array())
    {
        return null;
    }

    public function resolveControlRouteParameters($formData = array())
    {
        return array();
    }

    public function getName()
    {
        return 'Internal routes';
    }
}
