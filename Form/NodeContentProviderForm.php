<?php

namespace Btn\NodeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Btn\AdminBundle\Form\AbstractForm;

class NodeContentProviderForm extends AbstractForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('providerForm', $options['provider_form'], array(
                'label'  => false,
                'mapped' => false,
            ));
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(array(
            'provider_form',
        ));

        $resolver->setAllowedTypes(array(
            'provider_form' => array('string', 'Symfony\\Component\\Form\\AbstractType'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_node_form_node_content_provider';
    }
}
