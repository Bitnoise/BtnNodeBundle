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
     * @Route("/new", name="btn_node_nodecontrol_new")
     * @Route("/create", name="btn_node_nodecontrol_create")
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
     * @Route("/{id}/assign-content/{provider}", name="btn_node_nodecontrol_assigncontent", requirements={"id" = "\d+", "provider" = "[a-zA-Z0-9\._]+"})
     * @Template()
     */
    public function assignContentAction($id, $provider, Request $request)
    {
        //get all content providers
        $provider = $this->get($provider);
        // replace id with object - nasty piece of shit here but don't want to break something
        $entity   = $this->findEntityOr404($this->entityProvider()->getClass(), $id);

        $form = $this->createForm($provider->getForm());

        //form processing
        $result = $this->processContentForm($provider, $form, $request);

        //prepare content
        return array(
            'form'     => $form->createView(),
            'provider' => $provider,
            'id'       => $id,
            'node'     => $node
        );
    }

    private function processContentForm($service, &$form, $request)
    {
        if ($request->getMethod() == 'POST' && $request->get($form->getName())) {
            $form->bind($request);

            if ($form->isValid()) {
                //get correct route name from service
                $route                  = $service->resolveRoute($form->getData());
                $routeParameters        = $service->resolveRouteParameters($form->getData());
                $controlRoute           = $service->resolveControlRoute($form->getData());
                $controlRouteParameters = $service->resolveControlRouteParameters($form->getData());

                //set routeName to the node
                $node = $this->getRepository('BtnNodeBundle:Node')->find($request->get('node'));
                $node->setRoute($route);
                $node->setRouteParameters($routeParameters);
                $node->setControlRoute($controlRoute);
                $node->setControlRouteParameters($controlRouteParameters);
                $node->setProvider($service->getName());
                $this->getManager()->persist($node);
                $this->getManager()->flush();

                return true;
            } else {
                return false;
            }
        }
    }
}
