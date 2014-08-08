<?php

namespace Btn\NodesBundle\Service;

use Btn\NodesBundle\Service\NodeContentProviderInterface;

/**
*
*/
class NodeContentProviders
{

    private $providers;

    function __construct()
    {
        $this->providers = array();
    }

    function addProvider(NodeContentProviderInterface $provider, $id)
    {
        $this->providers[$id] = $provider;
    }

    function getProviders()
    {
        return $this->providers;
    }
}
