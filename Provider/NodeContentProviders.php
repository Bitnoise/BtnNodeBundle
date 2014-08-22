<?php

namespace Btn\NodeBundle\Provider;

class NodeContentProviders
{
    /** @var array $providers */
    protected $providers;

    /**
     *
     */
    public function __construct()
    {
        $this->providers = array();
    }

    /**
     *
     */
    public function addProvider(NodeContentProviderInterface $provider, $id)
    {
        $this->providers[$id] = $provider;
    }

    /**
     *
     */
    public function getProviders()
    {
        return $this->providers;
    }
}
