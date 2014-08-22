<?php

namespace Btn\NodeBundle\Event;

use Btn\NodeBundle\Model\NodeInterface;
use Symfony\Component\EventDispatcher\Event;

class NodeEvent extends Event
{
    /** @var \Btn\NodeBundle\Model\NodeInterface */
    protected $node;

    /**
     *
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     *
     */
    public function getNode()
    {
        return $this->node;
    }
}
