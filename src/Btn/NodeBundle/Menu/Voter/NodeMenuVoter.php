<?php

namespace Btn\NodeBundle\Menu\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Voter based on the uri
 */
class NodeMenuVoter implements VoterInterface
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    /**
     *
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Checks whether an item is current.
     *
     * If the voter is not able to determine a result,
     * it should return null to let other voters do the job.
     *
     * @param  ItemInterface $item
     * @return boolean|null
     */
    public function matchItem(ItemInterface $item)
    {
        if (ltrim($item->getUri(), '/') === ltrim($this->getRequest()->getRequestUri(), '/')) {
            return true;
        }

        return;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function getRequest() 
    {
        return $this->container->get('request');
    }
}
