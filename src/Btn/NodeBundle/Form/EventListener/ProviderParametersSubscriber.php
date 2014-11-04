<?php

namespace Btn\NodeBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Btn\NodeBundle\Provider\NodeContentProviders;

class ProviderParametersSubscriber implements EventSubscriberInterface
{
    /** @var \Btn\NodeBundle\Provider\NodeContentProviders $nodeContentProviders */
    protected $nodeContentProviders;

    /**
     *
     */
    public function __construct(NodeContentProviders $nodeContentProviders)
    {
        $this->nodeContentProviders = $nodeContentProviders;
    }

    /**
     *
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
        );
    }

    /**
     *
     */
    public function onPreSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data->getProviderId()) {
            $data->setProviderParameters(array());
        }

        $this->addProviderParametersForm($form, $data->getProviderId());
    }

    /**
     *
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (empty($data['providerId'])) {
            $data['providerParameters'] = '';
            $event->setData($data);
        }

        $this->addProviderParametersForm($form, $data['providerId']);
    }

    /**
     *
     */
    protected function addProviderParametersForm($form, $providerId, array $options = array())
    {
        if (!isset($options['label'])) {
            $options['label'] = false;
        }

        if (!isset($options['extra_fields_message'])) {
            $options['extra_fields_message'] = false;
        }

        if (!isset($options['invalid_message'])) {
            $options['invalid_message'] = false;
        }

        if ($providerId) {
            $type = $this->nodeContentProviders->get($providerId)->getForm();
        } else {
            $type = 'hidden';
            // $options['data'] = '';
        }

        $form->add('providerParameters', $type, $options);
    }
}
