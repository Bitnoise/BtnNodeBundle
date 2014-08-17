<?php

namespace Btn\NodesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Btn\BaseBundle\Provider\EntityProviderInterface;

class NodeType extends AbstractType
{
    /** @var \Btn\BaseBundle\Provider\EntityProviderInterface */
    protected $provider;

    public function __construct(EntityProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'empty_value'   => 'btn_node.type.node.empty_value',
            'label'         => 'btn_node.type.node.label',
            'class'         => $this->provider->getClass(),
            'attr'          => array('class' => 'btn-node'),
            'query_builder' => function (EntityRepository $em) {
                return $em
                    ->createQueryBuilder('node')
                    ->orderBy('node.title', 'ASC');
            },
            'required' => true,
            'expanded' => false,
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'btn_node';
    }
}
