<?php

namespace Btn\NodeBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Btn\NodeBundle\Entity\Node;

/**
 * NodeRepository
 */
class NodeRepository extends NestedTreeRepository
{
    /** @var integer $cacheLifetime */
    protected $cacheLifetime;

    /**
     * @param integer $cacheLifetime
     */
    public function setCacheLifetime($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * @param string $slug The slug
     *
     * @return Node|null
     */
    public function getNodeForUrl($url)
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n')
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;

        if (empty($url)) {
            $qb->andWhere('n.url = :url AND n.url IS NOT NULL');
        } else {
            $qb->andWhere('n.url = :url');
        }

        $qb->andWhere('n.lvl > 0');
        $qb->setParameter('url', $url);

        $query = $qb->getQuery();

        if ($this->cacheLifetime) {
            $query->useResultCache(true, $this->cacheLifetime);
        }

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $slug The slug
     *
     * @return Node|null
     */
    public function getNodeForSlug($slug)
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n')
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;

        $qb->andWhere('n.slug = :slug');
        $qb->setParameter('slug', $slug);

        $query = $qb->getQuery();

        if ($this->cacheLifetime) {
            $query->useResultCache(true, $this->cacheLifetime);
        }

        return $query->getOneOrNullResult();
    }

    /**
     * @param int $root
     *
     * @return Node[]
     */
    public function getNodesForRoot($root)
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.root = :root')
            ->orderBy('n.lft', 'ASC')
            ->setParameter(':root', $root)
        ;

        $query = $qb->getQuery();

        if ($this->cacheLifetime) {
            $query->useResultCache(true, $this->cacheLifetime);
        }

        return $query->getResult();
    }

    /**
     * @param Node $node
     * @param Node $newParent
     *
     * @return void
     */
    public function setNewParent($node, $newParent)
    {
        $meta = $this->getClassMetadata();

        $this->listener
            ->getStrategy($this->_em, $meta->name)
            ->updateNode($this->_em, $node, $newParent)
        ;
    }
}
