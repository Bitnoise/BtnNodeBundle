<?php

namespace Btn\NodeBundle\Menu\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Knp\Menu\Loader\LoaderInterface;
use Btn\BaseBundle\Provider\EntityProviderInterface;

class NodeMenuProvider implements MenuProviderInterface
{
    /** @var \Knp\Menu\FactoryInterface $factory */
    protected $factory = null;
    /** @var \Knp\Menu\Loader\LoaderInterface */
    protected $loader = null;
    /** @var \Btn\BaseBundle\Provider\EntityProviderInterface $ep */
    protected $ep = null;
    /** @var array $nodeArrayCache */
    protected $nodeArrayCache = array();

    /**
     * @param FactoryInterface $factory the menu factory used to create the menu item
     */
    public function __construct(FactoryInterface $factory, LoaderInterface $loader, EntityProviderInterface $ep)
    {
        $this->factory = $factory;
        $this->loader  = $loader;
        $this->ep      = $ep;
    }

    /**
     * Retrieves a menu by its name
     *
     * @param  string                    $name
     * @param  array                     $options
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get($name, array $options = array())
    {
        $menu = $this->getNodeForSlugWithCache($name);

        if (null === $menu) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        // if it's root element of menu then get full nodes list by root
        if ($menu->getId() === $menu->getRoot()) {
            $nodes = $this->ep->getRepository()->getNodesForRoot($menu->getRoot());
            $nodes[0] = $menu;
            //clear children object to prevent unnecessary requests
            foreach ($nodes as $node) {
                $node->clearChildren();
            }
            //fill children object from nodes list
            $lvlPointers = array();
            foreach ($nodes as $node) {
                $lvl = $node->getLvl();
                $lvlPointers[$lvl] = $node;
                if ($lvl > 0) {
                    $lvlPointers[$lvl-1]->addChildren($node);
                }
            }
        }

        $menuItem = $this->loader->load($menu);

        //add class if provided
        if (isset($options['class'])) {
            $menuItem->setChildrenAttribute('class', $options['class']);
        }

        return $menuItem;
    }

    /**
     * Checks whether a menu exists in this provider
     *
     * @param  string $name
     * @param  array  $options
     * @return bool
     */
    public function has($name, array $options = array())
    {
        $menu = $this->getNodeForSlugWithCache($name);

        return $menu !== null;
    }

    /**
     * Get node for slug with local array cache to prevent unnecessary
     */
    protected function getNodeForSlugWithCache($name)
    {
        if (!array_key_exists($name, $this->nodeArrayCache)) {
            $this->nodeArrayCache[$name] = $this->ep->getRepository()->getNodeForSlug($name);
        }

        return $this->nodeArrayCache[$name];
    }
}
