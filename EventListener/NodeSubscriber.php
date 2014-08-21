<?php

namespace Btn\NodeBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Btn\NodeBundle\Model\NodeInterface;

class NodeSubscriber implements EventSubscriber
{
    protected $nodeClass;

    /**
     *
     */
    public function __construct($nodeClass)
    {
        $this->nodeClass = $nodeClass;
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
    public function preUpdate(LifecycleEventArgs $event)
    {
        $this->handleEvent($event);
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
                if ($this->updateChildren($entity)) {
                    $computeChangeSet = true;
                }
            }
        }

        // computeChangeSet to catch all changes from updateChildren() method
        if ($computeChangeSet) {
            $uow->computeChangeSets();
        }
    }

    /**
     *
     */
    protected function handleEvent(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof NodeInterface) {
            $this->updateChildren($entity);
        }
    }

    /**
     *
     */
    protected function updateChildren(NodeInterface $entity)
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
