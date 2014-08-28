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
            ->add('route', 'choice', array('choices' => $this->data))
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
