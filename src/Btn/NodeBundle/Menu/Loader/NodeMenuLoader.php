<?php

namespace Btn\NodeBundle\Menu\Loader;

use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NodeMenuLoader implements LoaderInterface
{
    /** @var \Knp\Menu\FactoryInterface $factory */
    protected $factory;
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /**
     *
     */
    public function __construct(FactoryInterface $factory, ContainerInterface $container)
    {
        $this->factory   = $factory;
        $this->container = $container;
    }

    /**
     *
     */
    public function load($data)
    {
        if (!$data instanceof NodeInterface) {
            $dataType = is_object($data) ? get_class($data) : gettype($data);
            throw new \InvalidArgumentException(
                sprintf('Unsupported data. Expected Knp\Menu\NodeInterface but got "%s"', $dataType)
            );
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

    /**
     *
     */
    public function supports($data)
    {
        return $data instanceof NodeInterface;
    }
}
