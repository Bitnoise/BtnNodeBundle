<?php

namespace Btn\NodeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Btn\BaseBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Btn\NodeBundle\Entity\Node;
use Btn\NodeBundle\Form\NodeType;

/**
 * Nodes controller.
 *
 * @Route("/control/node")
 */
class NodeControlController extends BaseController
{
    /**
     * Lists all Nodes.
     *
     * @Route("/", name="cp_node")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Lists all Nodes.
     *
     * @Route("/tree", name="cp_node_tree")
     * @Template()
     */
    public function treeAction(Request $request)
    {
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BtnNodeBundle:Node');
        $topNodes = $repo->getRootNodes();

        $node = null;
        $path = array();
        if ($request->get('id') !== null) {
            $node = $this->findEntity('BtnNodeBundle:Node', $request->get('id'));
            // temporary solution
            foreach ($repo->getPath($node) as $item) {
                $path[] = $item->getId();
            }
        }

        return array('topNodes' => $topNodes, 'currentNode' => $node, 'pathToCurrentNode' => $path);
    }

    /**
     * List all nodes for modal picker
     *
     * @Route("/list-modal", name="cp_node_list_modal")
     * @Template()
     **/
    public function listModalAction()
    {
        $em       = $this->getDoctrine()->getManager();
        $repo     = $em->getRepository('BtnNodeBundle:Node');
        $topNodes = $repo->getRootNodes();

        return array(
            'topNodes' => $topNodes,
            'expanded' => false,
            'isModal' => true
        );
    }

    /**
     * Add new node
     *
     * @Route("/add", name="cp_add_node")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $parent = $this->findEntity('BtnNodeBundle:Node', $request->get('id'));
        $node   = new Node();
        if ($parent) {
            $node->setParent($parent);
        }
        $form   = $this->createForm(new NodeType(), $node);
        $result = null;

        //form processing
        $result = $this->processForm($node, $form, $request);

        //if success - redirect to edit mode
        if ($result === true) {
            return $this->redirect($this->generateUrl('cp_edit_node', array('id' => $node->getId())));
        }

        //prepare content
        return array(
            'form'   => $form->createView(),
            'node'   => $node,
            'parent' => $parent
        );
    }

    /**
     * Add new node
     *
     * @Route("/remove", name="cp_remove_node")
     */
    public function removeAction(Request $request)
    {
        $node = $this->findEntityOr404('BtnNodeBundle:Node', $request->get('id'));
        $this->getManager()->remove($node);
        $this->getManager()->flush();

        $msg = $this->get('translator')->trans('node.removed');
        $this->get('session')->getFlashBag()->add('success', $msg);

        return $this->redirect($this->generateUrl('cp_node'));
    }

    /**
     * Edit node params
     *
     * @Route("/edit/{id}", name="cp_edit_node")
     * @Template()
     */
    public function editAction($id, Request $request)
    {
        $node   = $this->findEntityOr404('BtnNodeBundle:Node', $request->get('id'));
        $form   = $this->createForm(new NodeType(), $node);
        $result = null;

        //form processing
        $result = $this->processForm($node, $form, $request);

        // get content providers
        $providers = $this->get('btn_node.content_providers')->getProviders();

        //prepare content
        return array(
            'form'      => $form->createView(),
            'node'      => $node,
            'providers' => $providers
        );
    }

    /**
     * assignContent node content
     *
     * @Route("/assign_content/{id}/{node}", name="cp_assign_content_for_node")
     * @Template()
     */
    public function assignContentAction($id, $node, Request $request)
    {
        //get all content providers
        $provider = $this->get($id);
        // replace id with object - nasty piece of shit here but don't want to break something
        $node     = $this->findEntityOr404('BtnNodeBundle:Node', $node);

        $form = $this->createForm($this->get($id)->getForm());

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

    private function processForm($entity, &$form, $request)
    {
        if ($request->getMethod() == 'POST' && $request->get($form->getName())) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getManager();
                $em->persist($entity);
                //fix url for all entity childrens?
                foreach ($entity->getChildren() as $node) {
                    $node->setUrl($node->getFullSlug());
                    $em->persist($node);
                }
                $em->flush();

                return true;
            } else {
                return false;
            }
        }
    }
}
