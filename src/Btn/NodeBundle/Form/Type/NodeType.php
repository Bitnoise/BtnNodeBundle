<?php

namespace Btn\NodeBundle\Form\Type;

use Btn\AdminBundle\Form\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

class NodeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'empty_value' => 'btn_node.type.node.empty_value',
            'label'       => 'btn_node.type.node.label',
            'class'       => $this->entityProvider->getClass(),
            'attr'        => array(
                'class' => 'btn-node',
            ),
            'query_builder' => function (EntityRepository $em) {
                return $em
                    ->createQueryBuilder('n')
                    ->leftJoin('n.rootEntity', 're')
                    ->select('n, re')
                    ->andWhere('n.lvl > 0')
                    ->orderBy('n.root', 'ASC')
                    ->addOrderBy('n.lft', 'ASC')
                ;
            },
            'group_by' => 'rootEntity.name',
            'property' => 'titleLvlPrefixed',
            'required' => true,
            'expanded' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['attr']['btn-select2-tree'] = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'btn_select2_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_node';
    }
}
