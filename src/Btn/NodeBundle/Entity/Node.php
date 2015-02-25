<?php

namespace Btn\NodeBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Btn\NodeBundle\Model\NodeInterface;
use Btn\BaseBundle\Util\Text;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="btn_node", indexes={
 *     @ORM\Index(name="idx_slug", columns={"slug"}),
 *     @ORM\Index(name="idx_url", columns={"url"}),
 * })
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Btn\NodeBundle\Repository\NodeRepository")
 *
 * @todo Do something with this Complexity
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Node implements NodeInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=64)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(name="slug", type="string", length=64, nullable=true)
     */
    private $slug;

    /**
     * @Gedmo\TreeLeft()
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel()
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight()
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot()
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumn(name="root", referencedColumnName="id", onDelete="CASCADE")
     */
    private $rootEntity;

    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @ORM\Column(name="route", type="string", nullable=true)
     */
    private $route;

    /**
     * @ORM\Column(name="route_parameters", type="array", nullable=true)
     */
    private $routeParameters;

    /**
     * @ORM\Column(name="control_route_parameters", type="array", nullable=true)
     */
    private $controlRouteParameters;

    /**
     * @ORM\Column(name="control_route", type="string", nullable=true)
     */
    private $controlRoute;

    /**
     * @ORM\Column(name="provider_id", type="string", nullable=true)
     */
    private $providerId;

    /**
     * @ORM\Column(name="provider_parameters", type="array", nullable=true)
     */
    private $providerParameters;

    /**
     * @var string $providerEvent
     */
    private $providerEvent;

    /**
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(name="meta_title", type="string", nullable=true)
     */
    private $metaTitle;

    /**
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(name="meta_keywords", type="text", nullable=true)
     */
    private $metaKeywords;

    /**
     * @ORM\Column(name="og_title", type="string", nullable=true)
     */
    private $ogTitle;

    /**
     * @ORM\Column(name="og_description", type="text", nullable=true)
     */
    private $ogDescription;

    /**
     * @ORM\Column(name="og_image", type="string", nullable=true)
     */
    private $ogImage;

    /**
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;

    /**
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private $link;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface $router
     */
    private $router;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->visible  = true;
        $this->router   = null;
    }

    /**
     *
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     *
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     *
     */
    public function setSlug($slug)
    {
        return $this->slug = Text::slugify($slug);
    }

    /**
     *
     */
    public function setParent(NodeInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     *
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     *
     */
    public function clearChildren()
    {
        $this->children = new ArrayCollection();

        return $this;
    }

    /**
     * Set lft
     *
     * @param  integer $lft
     * @return Node
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param  integer $lvl
     * @return Node
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param  integer $rgt
     * @return Node
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param  integer $root
     * @return Node
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Is root
     *
     * @return bool
     */
    public function isRoot()
    {
        return !$this->getParent() ? true : false;
    }

    /**
     * Set route
     *
     * @param  string $route
     * @return Node
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return Node
     */
    public function getRootEntity()
    {
        return $this->rootEntity;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set routeParameters
     *
     * @param  array $routeParameters
     * @return Node
     */
    public function setRouteParameters(array $routeParameters)
    {
        $this->routeParameters = $routeParameters;

        return $this;
    }

    /**
     * Get routeParameters
     *
     * @return array
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * Add children
     *
     * @param  \Btn\NodeBundle\Entity\Node $children
     * @return Node
     */
    public function addChildren(NodeInterface $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Btn\NodeBundle\Entity\Node $children
     */
    public function removeChildren(NodeInterface $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * @return string
     */
    public function getFullSlug($withoutThisNode = false)
    {
        $slug       = '';
        $parentNode = $this->getParent();
        if (null !== $parentNode) {
            $parentSlug = $parentNode->getFullSlug();
            if (!empty($parentSlug)) {
                $slug = rtrim($parentSlug, '/').'/';
            }
        }

        if (!$withoutThisNode) {
            $slug = $this->getLvl() !== 0 ? $slug.$this->getSlug() : '';
        }

        return $slug;
    }

    /**
     * Set url
     *
     * @param  string $url
     * @return Node
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * Update full url for this node
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateUrl()
    {
        $result = false;

        //don't update url for root nodes
        $parentNode = $this->getParent();
        if (null !== $parentNode) {
            $currentUrl = $this->getUrl();
            $fullSlug   =  $this->getFullSlug();
            if ($fullSlug !== $currentUrl) {
                $this->setUrl($fullSlug);
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Set controlRoute
     *
     * @param  string $controlRoute
     * @return Node
     */
    public function setControlRoute($controlRoute)
    {
        $this->controlRoute = $controlRoute;

        return $this;
    }

    /**
     * Get controlRoute
     *
     * @return string
     */
    public function getControlRoute()
    {
        return $this->controlRoute;
    }

    /**
     * Set providerId
     *
     * @param  string $providerId
     * @return Node
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * Get providerId
     *
     * @return string
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * Set providerParameters
     *
     * @param  string $providerParameters
     * @return Node
     */
    public function setProviderParameters(array $providerParameters = null)
    {
        $this->providerParameters = $providerParameters;

        return $this;
    }

    /**
     * Get providerParameters
     *
     * @return string
     */
    public function getProviderParameters()
    {
        return $this->providerParameters;
    }

    /**
     *
     */
    public function setProviderEvent($providerEvent)
    {
        $this->providerEvent = $providerEvent;

        return $this;
    }

    /**
     *
     */
    public function getProviderEvent()
    {
        return $this->providerEvent;
    }

    /**
     *
     */
    public function getName()
    {
        return $this->title;
    }

    /**
     *
     */
    public function setRouter(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     *
     */
    public function generateUrl()
    {
        return $this->router ? $this->router->generate('_btn_node', array('url' => $this->getUrl())) : $this->getUrl();
    }

    /**
     *
     */
    public function getOptions()
    {
        if (!$this->getRoute()) {
            return array();
        } elseif ($this->getUrl() || ('' === $this->getUrl() && '' === $this->getSlug() && 1 === $this->getLvl())) {
            return array(
                'uri' => $this->generateUrl(),
            );
        } else {
            return array(
                'route'           => $this->getRoute(),
                'routeParameters' => is_array($this->getRouteParameters()) ? $this->getRouteParameters() : array(),
            );
        }
    }

    /**
     * Set routeParameters
     *
     * @param  string $routeParameters
     * @return Node
     */
    public function setControlRouteParameters(array $controlRouteParameters)
    {
        $this->controlRouteParameters = $controlRouteParameters;

        return $this;
    }

    /**
     * Get routeParameters
     *
     * @return string
     */
    public function getControlRouteParameters()
    {
        return $this->controlRouteParameters;
    }

    /**
     * Set metaTitle
     *
     * @param  string $metaTitle
     * @return Node
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set metaDescription
     *
     * @param  string $metaDescription
     * @return Node
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set metaKeywords
     *
     * @param  string $metaKeywords
     * @return Node
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set ogTitle
     *
     * @param  string $ogTitle
     * @return Node
     */
    public function setOgTitle($ogTitle)
    {
        $this->ogTitle = $ogTitle;

        return $this;
    }

    /**
     * Get ogTitle
     *
     * @return string
     */
    public function getOgTitle()
    {
        return $this->ogTitle;
    }

    /**
     * Set ogDescription
     *
     * @param  string $ogDescription
     * @return Node
     */
    public function setOgDescription($ogDescription)
    {
        $this->ogDescription = $ogDescription;

        return $this;
    }

    /**
     * Get ogDescription
     *
     * @return string
     */
    public function getOgDescription()
    {
        return $this->ogDescription;
    }

    /**
     * Set ogImage
     *
     * @param  string $image
     * @return Node
     */
    public function setOgImage($ogImage = null)
    {
        $this->ogImage = $ogImage;

        return $this;
    }

    /**
     * Get ogImage
     *
     * @return string
     */
    public function getOgImage()
    {
        return $this->ogImage;
    }

    /**
     * Set visible
     *
     * @param  boolean $visible
     * @return Node
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set link
     *
     * @param  string $link
     * @return Node
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     *
     */
    public function __toString()
    {
        return str_pad($this->title, strlen($this->title) + $this->lvl, "_", STR_PAD_LEFT);
    }
}
