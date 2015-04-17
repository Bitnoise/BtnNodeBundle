<?php

namespace Btn\NodeBundle\Routing;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Catch all with lowest priority and pass it to the NodeController
 */
class Router implements RouterInterface
{
    /** @var RouteCollection */
    private $routeCollection;

    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var ContainerInterface */
    private $container;

    /** @var  RequestContext */
    private $context;

    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container       = $container;
        $this->routeCollection = new RouteCollection();

        $routerName = $this->container->getParameter('btn_node.router.name');
        $routerPrefix = $this->container->getParameter('btn_node.router.prefix');
        $controller = $this->container->getParameter('btn_node.router.controller');
        $locale = $this->container->getParameter('btn_node.router.locale');
        $defaultLocale = $this->container->getParameter('kernel.default_locale');

        $defaults = [
            '_controller' => $controller,
            'url'         => '',
        ];

        $requirements = [
            'url' => '[a-zA-Z0-9\-_\/]+',
        ];

        $path = $routerPrefix;
        if ($locale['enabled']) {
            $path .= '{_locale}/';
            $defaults['_locale'] = $defaultLocale;
            if ($locale['requirements']) {
                $requirements['_locale'] = $locale['requirements'];
            }
        }
        $path .= '{url}';

        $this->routeCollection->add($routerName, new Route($path, $defaults, $requirements));
    }

    /**
     * Match given urls via the context to the routes we defined.
     * This functionality re-uses the default Symfony way of routing and its components
     *
     * @param string $pathinfo
     *
     * @return array
     */
    public function match($pathinfo)
    {
        $requestAttributes = $this->container->get('request')->attributes;

        if ($requestAttributes->has('_controller')) {
            throw new ResourceNotFoundException('Routing is already done');
        }

        if (HttpKernelInterface::SUB_REQUEST === $requestAttributes->get('_request_type')) {
            throw new ResourceNotFoundException('Skipping subrequest');
        }

        $urlMatcher = new UrlMatcher($this->routeCollection, $this->getContext());

        // Inject default lang prefix to url if not present
        $locale = $this->container->getParameter('btn_node.router.locale');
        if ($locale['enabled'] && $locale['skip_default']) {
            $defaultLocale = $this->container->getParameter('kernel.default_locale');
            $routerPrefix = $this->container->getParameter('btn_node.router.prefix');
            if (!preg_match('~^'.preg_quote($routerPrefix).'[a-z]{2}\/~', $pathinfo)) {
                $pathinfo = preg_replace(
                    '~^'.preg_quote($routerPrefix).'~',
                    $routerPrefix.$defaultLocale.'/',
                    $pathinfo
                );
            }
        }

        $result = $urlMatcher->match($pathinfo);

        if (!empty($result)) {
            $nodeRepo       = $this->container->get('btn_node.provider.node')->getRepository();
            $node           = $nodeRepo->getNodeForUrl($result['url']);
            $result['node'] = $node;

            if (is_null($node)) {
                throw new ResourceNotFoundException(sprintf('No node found for url "%s"', $pathinfo));
            } elseif (!$node->getRoute() && !$node->getLink()) {
                throw new ResourceNotFoundException(sprintf('Empty route and link for url "%s"', $pathinfo));
            }
        }

        return $result;
    }

    /**
     * Generate an url for a supplied route
     *
     * @param string $name       The path
     * @param array  $parameters The route parameters
     * @param bool   $absolute   Absolute url or not
     *
     * @return null|string
     */
    public function generate($name, $parameters = [], $absolute = false)
    {
        $this->urlGenerator = new UrlGenerator($this->routeCollection, $this->context);

        $url = $this->urlGenerator->generate($name, $parameters, $absolute);

        // Remove default lang url if it should be skiped from url
        if (
            $name === $this->container->getParameter('btn_node.router.name') &&
            $this->container->getParameter('btn_node.router.locale')['skip_default']
        ) {
            $defaultLocale = $this->container->getParameter('kernel.default_locale');
            $routerPrefix = $this->container->getParameter('btn_node.router.prefix');
            if (
                array_key_exists('_locale', $this->context->getParameters()) &&
                $this->context->getParameters()['_locale'] === $defaultLocale
            ) {
                $url = preg_replace('~^'.preg_quote($routerPrefix.$defaultLocale).'\/~', $routerPrefix, $url);
            }
        }

        return $url;
    }

    /**
     * Getter for routeCollection
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        return $this->routeCollection;
    }

    /**
     * Sets the request context.
     *
     * @param RequestContext $context The context
     *
     * @api
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * Gets the request context.
     *
     * @return RequestContext The context
     *
     * @api
     */
    public function getContext()
    {
        if (!isset($this->context)) {
            /* @var Request $request */
            $request = $this->container->get('request');

            $this->context = new RequestContext();
            $this->context->fromRequest($request);
        }

        return $this->context;
    }
}
