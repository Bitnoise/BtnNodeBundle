<?php

namespace Btn\NodeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Btn\AdminBundle\Controller\AbstractControlController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Btn\AdminBundle\Annotation\EntityProvider;

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
        $ep     = $this->getEntityProvider();
        $entity = $ep->create();
        $parent = null;

        if ($request->query->has('parent')) {
            $parent = $this->findEntityOr404($ep->getClass(), $request->query->getInt('parent'));
            $entity->setParent($parent);
        }

        $form = $this->createForm('btn_node_form_node_control', $entity, array(
            'action' => $this->generateUrl('btn_node_nodecontrol_create'),
        ));

        if ($this->handleForm($form, $request)) {
            $this->setFlash('btn_admin.flash.created');

            return $this->redirect($this->generateUrl('btn_node_nodecontrol_edit', array('id' => $entity->getId())));
        }

        //prepare content
        return array(
            'form'   => $form->createView(),
            'entity'   => $entity,
            'parent' => $parent,
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
        $form   = $this->createForm('btn_node_form_node_control', $entity, array(
            'action' => $this->generateUrl('btn_node_nodecontrol_update', array('id' => $id)),
        ));

        //form processing
        if ($this->handleForm($form, $request)) {
            $this->setFlash('btn_admin.flash.updated');

            return $this->redirect($this->generateUrl('btn_node_nodecontrol_edit', array('id' => $entity->getId())));
        }

        // get content providers
        $providers = $this->get('btn_node.content_providers')->getProviders();

        //prepare content
        return array(
            'form'      => $form->createView(),
            'entity'    => $entity,
            'providers' => $providers,
        );
    }

    /**
     * Delete route
     *
     * @Route("/{id}/delete/{csrf_token}", name="btn_node_nodecontrol_delete", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function removeAction(Request $request, $id, $csrf_token)
    {
        $this->validateCsrfTokenOrThrowException('btn_node_nodecontrol_delete', $csrf_token);

        $entityProvider = $this->getEntityProvider();
        $entity         = $this->findEntityOr404($entityProvider->getClass(), $id);

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
     * assignContent node content
     *
     * @Route("/{id}/assign-content/{providerId}", name="btn_node_nodecontrol_assigncontent", requirements={"id" = "\d+", "providerId" = "[a-zA-Z0-9\._]+"}, methods={"GET", "POST"})
     * @Template()
     */
    public function assignContentAction(Request $request, $id, $providerId)
    {
        if (!$this->has($providerId)) {
            throw $this->createNotFoundException(sprintf('Unable to find node content provider service with id "%s"', $providerId));
        }

        $entity   = $this->findEntityOr404($this->getEntityProvider()->getClass(), $id);
        $entity->setProviderId($providerId);

        $provider = $this->get($providerId);

        $form = $this->createForm('btn_node_form_node_content_provider', $entity, array(
            'action'        => $this->generateUrl('btn_node_nodecontrol_assigncontent', array('id' => $id, 'providerId' => $providerId)),
            'provider_form' => $provider->getForm(),
        ));

        //form processing
        $formHandler = $this->get('btn_node.form.handler.node_content_provider')->setNodeContentProvider($provider);

        if ($formHandler->handle($form, $request)) {
            $this->setFlash('btn_admin.flash.updated');

            return $this->redirect($this->generateUrl('btn_node_nodecontrol_edit', array('id' => $id)));
        }

        //prepare content
        return array(
            'form'       => $form->createView(),
            'providerId' => $providerId,
            'entity'     => $entity,
        );
    }
}
