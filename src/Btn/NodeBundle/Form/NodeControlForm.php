<?php

namespace Btn\NodeBundle\Form;

use Btn\AdminBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Btn\NodeBundle\Provider\NodeContentProviders;

class NodeControlForm extends AbstractForm
{
    /** @var \Btn\NodeBundle\Provider\NodeContentProviders $nodeContentProviders */
    protected $nodeContentProviders;

    /**
     *
     */
    public function setNodeContentProviders(NodeContentProviders $nodeContentProviders)
    {
        $this->nodeContentProviders = $nodeContentProviders;
    }

    /**
     * {@inheritdoc}
     */
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
        ;

        if (!$options['data']->getParent()) {
            // fields for root node
            $builder
                ->add('slug', 'btn_slug', array(
                    'label' => 'btn_node.node.identifier',
                ))
            ;
        } else {
            // fields for menu node
            $builder
                ->add('slug', 'btn_slug', array(
                    'label'         => 'btn_node.node.slug',
                    'addon_prepend' => '/' . $options['data']->getFullSlug(true),
                ))
                ->add('link', null, array(
                    'label' => 'btn_node.node.link',
                ))
                ->add('providerId', 'btn_node_content_provider', array(
                ))
            ;

            if ($options['data']->getProviderId()) {
                $providerParametersForm = $this->nodeContentProviders->get($options['data']->getProviderId())->getForm();
                $builder
                    ->add('providerParameters', $providerParametersForm, array(
                        'label'  => false,
                    ))
                ;
            }

            $builder
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
            ;
        }

        $builder
            ->add('save', $options['data']->getId() ? 'btn_update' : 'btn_create', array(
                'row' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    // public function setDefaultOptions(OptionsResolverInterface $resolver)
    // {
    //     parent::setDefaultOptions($resolver);

    //     $resolver->setOptional(array(
    //         'provider_form',
    //     ));

    //     $resolver->setDefaults(array(
    //         'provider_form' => null,
    //     ));

    //     $resolver->setAllowedTypes(array(
    //         'provider_form' => array('null', 'string', 'Symfony\\Component\\Form\\AbstractType'),
    //     ));
    // }

    public function getName()
    {
        return 'btn_node_form_node_control';
    }
}
