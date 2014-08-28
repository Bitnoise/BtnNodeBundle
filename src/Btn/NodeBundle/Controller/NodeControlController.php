<?php

namespace Btn\NodeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Btn\AdminBundle\Controller\AbstractControlController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Btn\NodeBundle\Event\NodeEvent;
use Btn\NodeBundle\NodeEvents;
use Btn\AdminBundle\Annotation\EntityProvider;
use Btn\NodeBundle\Model\NodeInterface;

/**
 * @Route("/node")
 * @EntityProvider()
 */
class NodeControlController extends AbstractControlController
{
    /**
     * @Route("/", name="btn_node_nodecontrol_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/new", name="btn_node_nodecontrol_new", methods={"GET"})
     * @Route("/create", name="btn_node_nodecontrol_create", methods={"POST"})
     * @Template()
     */
    public function createAction(Request $request)
    {
        $entityProvider = $this->getEntityProvider();
        $entity         = $entityProvider->create();

        if ($request->query->has('parent') && $request->query->get('parent')) {
            $parent = $this->findEntityOr404($entityProvider->getClass(), $request->query->getInt('parent'));
            $entity->setParent($parent);
        }

        $this->checkPermissionsOrThrowException($entity);

        $form = $this->createForm('btn_node_form_node_control', $entity, array(
            'action' => $this->generateUrl(
                'btn_node_nodecontrol_create',
                array('parent' => $request->query->get('parent'))
            ),
        ));

        if ($this->get('btn_node.form.handler.node')->handle($form, $request)) {
            $this->setFlash('btn_admin.flash.created');

            $this->get('event_dispatcher')->dispatch(NodeEvents::NODE_CREATED, new NodeEvent($entity));

            return $this->redirect($this->generateUrl('btn_node_nodecontrol_edit', array('id' => $entity->getId())));
        }

        //prepare content
        return array(
            'form'   => $form->createView(),
            'entity' => $entity,
        );
    }

    /**
     * @Route("/{id}/edit", name="btn_node_nodecontrol_edit", requirements={"id" = "\d+"}, methods={"GET"})
     * @Route("/{id}/update", name="btn_node_nodecontrol_update", requirements={"id" = "\d+"}, methods={"POST"})
     * @Template()
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->findEntityOr404($this->getEntityProvider()->getClass(), $id);

        $this->checkPermissionsOrThrowException($entity);

        $form   = $this->createForm('btn_node_form_node_control', $entity, array(
            'action' => $this->generateUrl('btn_node_nodecontrol_update', array('id' => $id)),
        ));

        //form processing
        if ($this->get('btn_node.form.handler.node')->handle($form, $request)) {
            $this->setFlash('btn_admin.flash.updated');

            // trigger update event
            $this->get('event_dispatcher')->dispatch(NodeEvents::NODE_UPDATED, new NodeEvent($entity));
            // trigger provider specific event
            if ($entity->getProviderEvent()) {
                $this->get('event_dispatcher')->dispatch($entity->getProviderEvent(), new NodeEvent($entity));
            }

            return $this->redirect($this->generateUrl('btn_node_nodecontrol_edit', array('id' => $entity->getId())));
        }

        //prepare content
        return array(
            'form'   => $form->createView(),
            'entity' => $entity,
        );
    }

    /**
     * Delete route
     *
     * @Route("/{id}/delete/{csrf_token}", name="btn_node_nodecontrol_delete",
     *     requirements={"id" = "\d+"}, methods={"GET"}
     * )
     */
    public function deleteAction(Request $request, $id, $csrf_token)
    {
        $this->validateCsrfTokenOrThrowException('btn_node_nodecontrol_delete', $csrf_token);

        $entityProvider = $this->getEntityProvider();
        $entity         = $this->findEntityOr404($entityProvider->getClass(), $id);

        $this->checkPermissionsOrThrowException($entity);

        $this->get('event_dispatcher')->dispatch(NodeEvents::NODE_DELETED, new NodeEvent($entity));

        $entityProvider->delete($entity);

        $this->setFlash('btn_admin.flash.deleted');

        return $this->redirect($this->generateUrl('btn_node_nodecontrol_index'));
    }

    /**
     * Lists all Nodes.
     *
     * @Route("/tree", name="btn_node_nodecontrol_tree")
     * @Template()
     */
    public function treeAction(Request $request)
    {
        $repo     = $this->getEntityProvider()->getRepository();
        $topNodes = $repo->getRootNodes();

        $node = null;
        $path = array();
        if ($request->get('id') !== null) {
            $node = $repo->find($request->get('id'));
            // temporary solution
            foreach ($repo->getPath($node) as $item) {
                $path[] = $item->getId();
            }
        }

        $this->get('btn_base.asset_loader')->load('btn_admin_jstree');

        return array(
            'topNodes'          => $topNodes,
            'currentNode'       => $node,
            'pathToCurrentNode' => $path,
        );
    }

    /**
     * List all nodes for modal picker
     *
     * @Route("/list-modal", name="btn_node_nodecontrol_listmodal")
     * @Template()
     **/
    public function listModalAction()
    {
        $topNodes = $this->getEntityProvider()->getRepository()->getRootNodes();

        return array(
            'topNodes' => $topNodes,
            'expanded' => false,
            'isModal'  => true,
        );
    }

    /**
     *
     */
    protected function checkPermissionsOrThrowException(NodeInterface $node)
    {
        if ($node->isRoot() && !$this->get('security.context')->isGranted('ROLE_NODE_ROOT_MANAGEMENT')) {
            throw $this->createAccessDeniedException('You don\'t have permission to manage menu root elements');
        }
    }
}
