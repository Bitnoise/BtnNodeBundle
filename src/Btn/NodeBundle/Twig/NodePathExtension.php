<?php

namespace Btn\NodeBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Btn\NodeBundle\Routing\UrlGenerator;

class NodePathExtension extends \Twig_Extension
{
    /** @var \Btn\NodeBundle\Routing\UrlGenerato */
    protected $urlGenerator;

    /**
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     *
     */
    public function getFunctions()
    {
        return array(
            'btn_node_path' => new \Twig_Function_Method($this, 'nodePath'),
        );
    }

    /**
     *
     */
    public function nodePath($input, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $this->urlGenerator->generate($input, $referenceType);
    }

    /**
     *
     */
    public function getName()
    {
        return 'btn_node.extension.node_path';
    }
}
