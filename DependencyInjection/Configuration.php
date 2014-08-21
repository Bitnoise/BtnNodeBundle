<?php

namespace Btn\NodeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('btn_node');

        $rootNode
            ->children()
                ->arrayNode('node')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->cannotBeEmpty()->defaultValue('Btn\\NodeBundle\\Entity\\Node')->end()
                    ->end()
                ->end()
                ->scalarNode('router_priority')->defaultValue(0)->end()
                ->scalarNode('router_prefix')->defaultValue('/')->end()
                ->arrayNode('available_routes')
                    ->cannotBeEmpty()
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}
