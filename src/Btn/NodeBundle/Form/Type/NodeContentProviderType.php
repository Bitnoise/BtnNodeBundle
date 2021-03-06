<?php

namespace Btn\NodeBundle\Form\Type;

use Btn\AdminBundle\Form\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $choices = array();

        foreach ($this->contentProviders->getProviders() as $providerId => $provider) {
            $choices[$providerId] = $provider->getName();
        }

        $resolver->setDefaults(array(
            'label'       => 'btn_node.form.type.node_content_provider.label',
            'empty_value' => 'btn_node.form.type.node_content_provider.empty_value',
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
