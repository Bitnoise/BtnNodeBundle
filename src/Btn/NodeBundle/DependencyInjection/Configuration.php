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
                        ->integerNode('cache_lifetime')->defaultValue(0)->end()
                    ->end()
                ->end()
                ->arrayNode('router')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->defaultValue('_btn_node')->end()
                        ->scalarNode('prefix')->defaultValue('/')->end()
                        ->scalarNode('priority')->defaultValue(0)->end()
                        ->scalarNode('locale')->defaultValue(false)->end()
                        ->scalarNode('controller')->defaultValue('BtnNodeBundle:Node:resolve')->end()
                    ->end()
                ->end()
                ->arrayNode('available_routes')
                    ->cannotBeEmpty()
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()

            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}
