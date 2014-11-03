<?php

namespace Btn\NodeBundle\Twig;

use Btn\BaseBundle\Provider\EntityProviderInterface;
use Btn\NodeBundle\Model\NodeInterface;

class NodeExtension extends \Twig_Extension
{
    /** @var \Btn\BaseBundle\Provider\EntityProviderInterface $nodeProvider */
    protected $nodeProvider;

    /**
     *
     */
    public function __construct(EntityProviderInterface $nodeProvider)
    {
        $this->nodeProvider = $nodeProvider;
    }

    /**
     *
     */
    public function getFunctions()
    {
        return array(
            'btn_node_next_siblings' => new \Twig_Function_Method($this, 'getNextSiblings'),
            'btn_node_prev_siblings' => new \Twig_Function_Method($this, 'getPrevSiblings'),
            'btn_node_next_sibling' => new \Twig_Function_Method($this, 'getNextSibling'),
            'btn_node_prev_sibling' => new \Twig_Function_Method($this, 'getPrevSibling'),
        );
    }

    /**
     *
     */
    public function getNextSiblings(NodeInterface $node, $includeSelf = false)
    {
        $repo = $this->nodeProvider->getRepository();
        $nextSiblings = $repo->getNextSiblings($node, $includeSelf);

        return $nextSiblings;
    }

    /**
     *
     */
    public function getPrevSiblings(NodeInterface $node, $includeSelf = false)
    {
        $repo = $this->nodeProvider->getRepository();
        $nextSiblings = $repo->getPrevSiblings($node, $includeSelf);

        return $nextSiblings;
    }

    /**
     *
     */
    public function getNextSibling(NodeInterface $node)
    {
        $nextSiblings = $this->getNextSiblings($node, false);

        return !empty($nextSiblings) ? $nextSiblings[0] : false;
    }

    /**
     *
     */
    public function getPrevSibling(NodeInterface $node)
    {
        $prevSiblings = $this->getPrevSiblings($node, false);

        return !empty($prevSiblings) ? $prevSiblings[count($prevSiblings) - 1] : false;
    }

    /**
     *
     */
    public function getName()
    {
        return 'btn_node.extension.node';
    }
}
