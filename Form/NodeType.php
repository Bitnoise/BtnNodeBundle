<?php

namespace Btn\NodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('slug', 'btn_slug', array(
                'label'       => 'node.form.slug',
                'slug_source' => 'title',
                'addon_pre'   => '/' . $options['data']->getFullSlug(true),
            ))
            ->add('title', null, array('label' => 'node.form.title'))
            ->add('visible', null, array('label' => 'node.form.visible'))
            ->add('metaTitle', null, array('label' => 'node.form.metaTitle'))
            ->add('metaDescription', null, array('label' => 'node.form.metaDescription'))
            ->add('metaKeywords', null, array('label' => 'node.form.metaKeywords'))
            ->add('ogTitle', null, array('label' => 'node.form.ogTitle'))
            ->add('ogDescription', null, array('label' => 'node.form.ogDescription'))
            ->add('ogImage', null, array(
                'label' => 'node.form.ogImage',
                'attr'  => array('class' => 'btn-media')
                ))
            ->add('link', null, array('label' => 'node.form.link'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Btn\\NodeBundle\\Entity\\Node',
        ));
    }

    public function getName()
    {
        return 'btn_node_nodetype';
    }
}
