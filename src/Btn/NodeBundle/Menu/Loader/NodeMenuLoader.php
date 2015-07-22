<?php

namespace Btn\NodeBundle\Menu\Loader;

use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;
use Knp\Menu\Loader\LoaderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NodeMenuLoader implements LoaderInterface
{
    /** @var \Knp\Menu\FactoryInterface $factory */
    protected $factory;
    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    protected $router;

    /**
     *
     */
    public function __construct(FactoryInterface $factory, UrlGeneratorInterface $router)
    {
        $this->factory = $factory;
        $this->router  = $router;
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

        $data->setRouter($this->router);

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
