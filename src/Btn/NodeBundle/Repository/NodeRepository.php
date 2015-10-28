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
    /** @var integer $tree */
    protected $tree;

    /**
     * @param integer $cacheLifetime
     */
    public function setCacheLifetime($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * @param integer $tree
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param string $slug The slug
     *
     * @return Node|null
     */
    public function getNodeForUrl($url, $cache = null)
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

        if ($this->resolveCacheLifetime($cache)) {
            $query->useResultCache(true, $this->resolveCacheLifetime($cache));
        }

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $slug The slug
     *
     * @return Node|null
     */
    public function getNodeForSlug($slug, $cache = null)
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n')
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;

        $qb->andWhere('n.slug = :slug');
        $qb->setParameter('slug', $slug);

        $query = $qb->getQuery();

        if ($this->resolveCacheLifetime($cache)) {
            $query->useResultCache(true, $this->resolveCacheLifetime($cache));
        }

        return $query->getOneOrNullResult();
    }

    /**
     * @param int $root
     *
     * @return Node[]
     */
    public function getNodesForRoot($root, $cache = null)
    {
        $qb = $this->createQueryBuilder('n')
            ->select('n')
            ->andWhere('n.root = :root')
            ->orderBy('n.lft', 'ASC')
            ->setParameter(':root', $root)
        ;

        $query = $qb->getQuery();

        if ($this->resolveCacheLifetime($cache)) {
            $query->useResultCache(true, $this->resolveCacheLifetime($cache));
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

    /**
     *
     */
    public function createQueryBuilder($alias)
    {
        $qb = parent::createQueryBuilder($alias);

        if ('n' === $alias && $this->tree) {
            $qb->andWhere('n.tree = :tree');
            $qb->setParameter('tree', $this->tree);
        }

        return $qb;
    }

    /**
     *
     */
    private function resolveCacheLifetime($cacheLifetime)
    {
        if (true === $cacheLifetime) {
            return $this->cacheLifetime;
        } elseif (is_int($cacheLifetime)) {
            return $cacheLifetime;
        }

        return 0;
    }
}
