<?php

namespace Btn\NodeBundle\DependencyInjection;

use Btn\BaseBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BtnNodeExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        parent::load($configs, $container);

        $config = $this->getProcessedConfig($container, $configs);

        $container->setParameter('btn_node.node.class', $config['node']['class']);
        $container->setParameter('btn_node.node.cache_lifetime', $config['node']['cache_lifetime']);
        $container->setParameter('btn_node.router_priority', $config['router_priority']);
        $container->setParameter('btn_node.router_prefix', $config['router_prefix']);
        $container->setParameter('btn_node.available_routes', $config['available_routes']);

        if ('dev' !== $container->getParameter('kernel.environment')) {
            $this->addClassesToCompile(array(
                'Btn\\NodeBundle\\EventListener\\RequestSubscriber',
                'Btn\\NodeBundle\\Routing\\Router',
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        parent::prepend($container);

        $cmfRoutingExtraConfig['chain']['routers_by_id']['router.default'] = 100;
        $cmfRoutingExtraConfig['chain']['replace_symfony_router'] = true;
        $container->prependExtensionConfig('cmf_routing', $cmfRoutingExtraConfig);
    }
}
