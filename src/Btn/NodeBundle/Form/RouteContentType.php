<?php

namespace Btn\NodeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RouteContentType extends AbstractType
{
    /** @var array $data */
    private $data;

    /**
     *
     */
    public function __construct($data = array())
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('route', 'btn_select2_choice', array(
                'label'       => 'btn_node.route_node_content_provider.label',
                'placeholder' => 'btn_node.route_node_content_provider.placeholder',
                'choices'     => $this->data,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'btn_node_routecontent';
    }
}
