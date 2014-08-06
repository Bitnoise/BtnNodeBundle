<?php

namespace Btn\NodesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NodeService
 *
 * @ORM\Table(name="node_service")
 * @ORM\Entity
 */
class NodeService
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="node_provider", type="string", length=255)
     */
    private $nodeProvider;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="bundle", type="string", length=255)
     */
    private $bundle;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nodeProvider
     *
     * @param  string      $nodeProvider
     * @return NodeService
     */
    public function setNodeProvider($nodeProvider)
    {
        $this->nodeProvider = $nodeProvider;

        return $this;
    }

    /**
     * Get nodeProvider
     *
     * @return string
     */
    public function getNodeProvider()
    {
        return $this->nodeProvider;
    }

    /**
     * Set active
     *
     * @param  boolean     $active
     * @return NodeService
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set bundle
     *
     * @param  string      $bundle
     * @return NodeService
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;

        return $this;
    }

    /**
     * Get bundle
     *
     * @return string
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * Set name
     *
     * @param  string      $name
     * @return NodeService
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
