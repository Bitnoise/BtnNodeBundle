<?php

namespace Btn\NodesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Btn\NodesBundle\DependencyInjection\Compiler\ContentProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BtnNodesBundle extends Bundle
{
    /**
     * @param  ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ContentProviderCompilerPass());
    }
}
