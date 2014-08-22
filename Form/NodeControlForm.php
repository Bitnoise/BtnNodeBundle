<?php

namespace Btn\NodeBundle\Form;

use Btn\AdminBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;

class NodeControlForm extends AbstractForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('parent', 'btn_hidden_entity', array(
                'class' => $this->class,
            ))
            ->add('title', null, array(
                'label' => 'btn_node.node.title',
            ))
            ->add('slug', 'btn_slug', array(
                'label'         => 'btn_node.node.slug',
                'addon_prepend' => '/' . $options['data']->getFullSlug(true),
            ))
            ->add('link', null, array(
                'label' => 'btn_node.node.link',
            ))
            ->add('visible', null, array(
                'label' => 'btn_node.node.visible',
            ))
            ->add('metaTitle', null, array(
                'label' => 'btn_node.node.metaTitle',
            ))
            ->add('metaDescription', null, array(
                'label' => 'btn_node.node.metaDescription',
            ))
            ->add('metaKeywords', null, array(
                'label' => 'btn_node.node.metaKeywords',
            ))
            ->add('ogTitle', null, array(
                'label' => 'btn_node.node.ogTitle',
            ))
            ->add('ogDescription', null, array(
                'label' => 'btn_node.node.ogDescription',
            ))
            ->add('ogImage', 'btn_media', array(
                'label' => 'btn_node.node.ogImage',
            ))
            ->add('save', $options['data']->getId() ? 'btn_update' : 'btn_create', array(
                'row' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'btn_node_form_node_control';
    }
}
