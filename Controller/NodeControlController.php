<?php

namespace Btn\NodesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Btn\BaseBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Btn\NodesBundle\Entity\Node;
use Btn\NodesBundle\Form\NodeType;

/**
 * Nodes controller.
 *
 * @Route("/control/nodes")
 */
class NodeControlController extends BaseController
{
    /**
     * Lists all Nodes.
     *
     * @Route("/", name="cp_nodes")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Lists all Nodes.
     *
     * @Route("/tree", name="cp_nodes_tree")
     * @Template()
     */
    public function treeAction()
    {
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('BtnNodesBundle:Node');
        $topNodes = $repo->getRootNodes();

        return array('topNodes' => $topNodes);
    }


    /**
     * List all nodes for modal picker
     *
     * @Route("/list-modal", name="cp_nodes_list_modal")
     * @Template()
     **/
    public function listModalAction()
    {
        $em       = $this->getDoctrine()->getManager();
        $repo     = $em->getRepository('BtnNodesBundle:Node');
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
        $parent = $this->findEntity('BtnNodesBundle:Node', $request->get('id'));
        $node   = new Node();
        if ($parent) {
            $node->setParent($parent);
        }
        $form   = $this->createForm(new NodeType(), $node);
        $result = null;

        //form processing
        $result = $this->processForm($node, $form, $request);

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
        $node = $this->findEntityOr404('BtnNodesBundle:Node', $request->get('id'));
        $this->getManager()->remove($node);
        $this->getManager()->flush();

        $msg = $this->get('translator')->trans('node.removed');
        $this->get('session')->getFlashBag()->add('success', $msg);

        return $this->redirect($this->generateUrl('cp_nodes'));
    }

    /**
     * Edit node params
     *
     * @Route("/edit/{id}", name="cp_edit_node")
     * @Template()
     */
    public function editAction($id, Request $request)
    {
        $node   = $this->findEntityOr404('BtnNodesBundle:Node', $request->get('id'));
        $form   = $this->createForm(new NodeType(), $node);
        $result = null;

        //form processing
        $result = $this->processForm($node, $form, $request);

        //prepare content
        return array(
            'form' => $form->createView(),
            'node' => $node
        );
    }

    /**
     * Select node content
     *
     * @Route("/content", name="cp_content_for_node")
     * @Template()
     */
    public function contentAction(Request $request)
    {
        //get all content providers
        $providers = $this->getRepository('BtnNodesBundle:NodeService')->findAll();

        //prepare content
        $content = $this->renderView('BtnNodesBundle:NodeControl:_content.html.twig', array(
            'providers' => $providers,
            'node'      => $request->get('node')
        ));

        return $this->renderJson($content, 'succes');
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
        $provider = $this->getRepository('BtnNodesBundle:NodeService')->find($id);

        $form = $this->createForm($this->get($provider->getNodeProvider())->getForm());

        //form processing
        $result = $this->processContentForm($provider, $form, $request);

        //prepare content
        $content = $this->renderView('BtnNodesBundle:NodeControl:_assign_content.html.twig', array(
            'form'     => $form->createView(),
            'provider' => $provider,
            'node'     => $node
        ));

        return $this->resolveView($result, $content);
    }

    private function resolveView($result, $content)
    {
        //valid or without post
        if ($result === true || $result === null) {
            return $this->renderJson($content, 'success');
        //invalid
        } elseif ($result === false) {
            return $this->renderJson($content, 'error');
        }
    }

    private function processContentForm($provider, &$form, $request)
    {
        if ($request->getMethod() == 'POST' && $request->get($form->getName())) {
            $form->bind($request);

            if ($form->isValid()) {
                //get correct route name from service
                $service                = $this->get($provider->getNodeProvider());
                $route                  = $service->resolveRoute($form->getData());
                $routeParameters        = $service->resolveRouteParameters($form->getData());
                $controlRoute           = $service->resolveControlRoute($form->getData());
                $controlRouteParameters = $service->resolveControlRouteParameters($form->getData());

                //set routeName to the node
                $node = $this->getRepository('BtnNodesBundle:Node')->find($request->get('node'));
                $node->setRoute($route);
                $node->setRouteParameters($routeParameters);
                $node->setControlRoute($controlRoute);
                $node->setControlRouteParameters($controlRouteParameters);
                $node->setProvider($provider->getName());
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
