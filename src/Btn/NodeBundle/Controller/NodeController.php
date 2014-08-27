<?php

namespace Btn\NodeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Btn\BaseBundle\Controller\AbstractController;
use Btn\NodeBundle\Entity\Node;

/**
 * Nodes resolver.
 *
 */
class NodeController extends AbstractController
{
    /**
     * Resolve slug router
     */
    public function resolveAction($url = null, Node $node = null)
    {
        $provider = $this->get('btn_node.provider.node');

        //resolve node by url
        if ($node || ($node = $provider->getRepository()->getNodeForUrl($url))) {
            //if node contains valid url - redirect
            $link = $node->getLink();
            if (!empty($link)) {
                return $this->redirect($link);
            }

            $uri = $this->get('router')->generate($node->getRoute(), $node->getRouteParameters());
            $uri = str_replace($this->get('request')->getBaseUrl(), '', $uri);
            $match = $this->get('router')->match($uri);

            //prevent recursive loop here
            if (isset($match['_controller']) && $match['_controller'] !== 'Btn\NodeBundle\Controller\NodeController::resolveAction') {

                //some additional controller attributes
                $context = array(
                    'url'  => $url,
                    'node' => $node,
                );

                //store as referrer
                $this->get('session')->set('_btn_node', $url);
                $response = $this->forward($match['_controller'], array_merge($match, $context));

                //something here?
                return $response;
            }
        }

        //nothing matched
        throw $this->createNotFoundException(sprintf('No page found for slug "%s"', $url));
    }
}
