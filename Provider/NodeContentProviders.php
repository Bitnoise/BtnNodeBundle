<?php

namespace Btn\NodeBundle\Provider;

/**
 *
 */
class NodeContentProviders
{
    private $providers;

    public function __construct()
    {
        $this->providers = array();
    }

    public function addProvider(NodeContentProviderInterface $provider, $id)
    {
        $this->providers[$id] = $provider;
    }

    public function getProviders()
    {
        return $this->providers;
    }
}
