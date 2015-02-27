<?php

namespace Btn\NodeBundle\Form\Type;

use Btn\AdminBundle\Form\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Btn\NodeBundle\Provider\NodeContentProviders;

class NodeContentProviderType extends AbstractType
{
    /** @var \Btn\NodeBundle\Provider\NodeContentProviders $contentProviders */
    protected $contentProviders;

    /**
     *
     */
    public function __construct(NodeContentProviders $contentProviders)
    {
        $this->contentProviders = $contentProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $choices = array();

        foreach ($this->contentProviders->getProviders() as $providerId => $provider) {
            $choices[$providerId] = $provider->getName();
        }

        $resolver->setDefaults(array(
            'label'       => 'btn_node.form.type.node_content_provider.label',
            'placeholder' => 'btn_node.form.type.node_content_provider.placeholder',
            'choices'     => $choices,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'btn_select2_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_node_content_provider';
    }
}
