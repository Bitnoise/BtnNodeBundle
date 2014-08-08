<?php

namespace Btn\NodesBundle\Service;

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

    function addProvider($provider, $id)
    {
        $this->providers[$id] = $provider;
    }

    function getProviders()
    {
        return $this->providers;
    }
}
