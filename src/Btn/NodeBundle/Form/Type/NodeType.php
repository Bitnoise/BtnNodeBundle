<?php

namespace Btn\NodeBundle\Form\Type;

use Btn\AdminBundle\Form\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class NodeType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'empty_value'   => 'btn_node.type.node.empty_value',
            'label'         => 'btn_node.type.node.label',
            'class'         => $this->entityProvider->getClass(),
            'attr'          => array(
                'class' => 'btn-node',
            ),
            'query_builder' => function (EntityRepository $em) {
                return $em
                    ->createQueryBuilder('node')
                    ->orderBy('node.title', 'ASC')
                ;
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
