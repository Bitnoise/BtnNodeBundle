<?php

namespace Btn\NodeBundle\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Btn\NodeBundle\Model\NodeInterface;

class UrlGenerator
{
    /** @var \Symfony\Component\Routing\UrlGeneratorInterface */
    protected $router;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param  string|NodeInterface $input
     * @param  bool                 $referenceType
     * @return string
     */
    public function generate($input, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) 
    {
        $parameters = array(
            'url' => '',
        );

        if ($input instanceof NodeInterface) {
            $parameters['url'] = $input->getUrl();
        } elseif (is_string($input)) {
            $parameters['url'] = $input;
        }

        return $this->router->generate('_btn_node', $parameters, $referenceType);
    }
}
