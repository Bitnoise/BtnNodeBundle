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
        if ($provider->isEnabled()) {
            $this->providers[$id] = $provider;
        }
    }

    /**
     *
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     *
     */
    public function has($id)
    {
        return isset($this->providers[$id]) ? true : false;
    }

    /**
     *
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->providers[$id];
        }

        throw new \Exception(sprintf('NodeContentProvider with id "%s" was not found', $id));
    }
}
