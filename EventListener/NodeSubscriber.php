<?php

namespace Btn\NodeBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Btn\NodeBundle\Model\NodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Btn\NodeBundle\NodeEvents;

class NodeSubscriber implements EventSubscriber
{
    /** @var string $nodeClass */
    protected $nodeClass;
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
    protected $eventDispatcher;

    /**
     *
     */
    public function __construct($nodeClass, EventDispatcherInterface $eventDispatcher)
    {
        $this->nodeClass       = $nodeClass;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em  = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $computeChangeSet = false;

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof NodeInterface) {
                $changeSet = $uow->getEntityChangeSet($entity);

                if (isset($changeSet['providerId'])) {
                    $entity->setProviderEvent(NodeEvents::PROVIDER_CHANGED);
                } elseif (isset($changeSet['providerParameters'])) {
                    $entity->setProviderEvent(NodeEvents::PROVIDER_MODIFIED);
                }

                if (!empty($changeSet['slug']) || !empty($changeSet['parent'])) {
                    if ($this->updateChildrenUrls($entity)) {
                        $computeChangeSet = true;
                    }
                }
            }
        }

        // computeChangeSet to catch all changes from updateChildrenUrls() method
        if ($computeChangeSet) {
            $uow->computeChangeSets();
        }
    }

    /**
     *
     */
    protected function updateChildrenUrls(NodeInterface $entity)
    {
        $changed = false;
        foreach ($entity->getChildren() as $node) {
            if ($node->updateUrl()) {
                $changed = true;
            }
        }

        return $changed;
    }
}
