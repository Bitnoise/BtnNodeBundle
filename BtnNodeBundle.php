<?php

namespace Btn\NodeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Btn\NodeBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BtnNodeBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Compiler\ContentProviderCompilerPass());
    }
}
