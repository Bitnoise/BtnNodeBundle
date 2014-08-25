<?php

namespace Btn\NodeBundle\Menu\Loader;

use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\Loader\LoaderInterface;

class NodeMenuLoader implements LoaderInterface
{
    protected $factory;
    protected $container;

    public function __construct(FactoryInterface $factory, $container)
    {
        $this->factory = $factory;
        $this->container = $container;
    }

    public function load($data)
    {
        if (!$data instanceof NodeInterface) {
            throw new \InvalidArgumentException(sprintf('Unsupported data. Expected Knp\Menu\NodeInterface but got ', is_object($data) ? get_class($data) : gettype($data)));
        }

        //put the reuqest there
        $data->setRouter($this->container->get('router'));

        $item = $this->factory->createItem($data->getName(), $data->getOptions());

        foreach ($data->getChildren() as $childNode) {
            if ($childNode->getVisible()) {
                $item->addChild($this->load($childNode));
            }
        }

        return $item;
    }

    public function supports($data)
    {
        return $data instanceof NodeInterface;
    }
}
