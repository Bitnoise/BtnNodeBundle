<?php

namespace Btn\NodesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ContentProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('btn_nodes.content_providers')) {
            return;
        }

        $definition = $container->getDefinition(
            'btn_nodes.content_providers'
        );

        foreach ($container->findTaggedServiceIds('btn_nodes.content_provider') as $id => $tags) {
            $definition->addMethodCall(
                'addProvider',
                array(new Reference($id), $id)
            );
        }
    }
}
