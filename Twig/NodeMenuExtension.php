<?php

namespace Btn\NodeBundle\Twig;

use Btn\NodeBundle\Menu\Provider\NodeMenuProvider;

class NodeMenuExtension extends \Twig_Extension
{
    /** @var \Btn\NodeBundle\Provider\NodeProvider $nodeMenuProvider */
    protected $nodeMenuProvider;

    /**
     *
     */
    public function __construct(NodeMenuProvider $nodeMenuProvider)
    {
        $this->nodeMenuProvider = $nodeMenuProvider;
    }

    /**
     *
     */
    public function getFunctions()
    {
        return array(
            'btn_menu_has' => new \Twig_Function_Method($this, 'has'),
        );
    }

    /**
     *
     */
    public function has($name, array $options = array())
    {
        return $this->nodeMenuProvider->has($name, $options);
    }

    /**
     *
     */
    public function getName()
    {
        return 'btn_node.extension.node_menu';
    }
}
