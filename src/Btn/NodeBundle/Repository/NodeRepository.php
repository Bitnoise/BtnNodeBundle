<?php

namespace Btn\NodeBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Kunstmaan\NodeBundle\Entity\Node;

/**
 * NodeRepository
 */
class NodeRepository extends NestedTreeRepository
{
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
            ->setMaxResults(1);
        ;

        if (empty($url)) {
            $qb->andWhere('n.url = :url AND n.url IS NOT NULL');
        } else {
            $qb->andWhere('n.url = :url');
        }

        $qb->andWhere('n.lvl > 0');
        $qb->setParameter('url', $url);

        return $qb->getQuery()->getOneOrNullResult();
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
            ->setMaxResults(1);
        ;

        $qb->andWhere('n.slug = :slug');
        $qb->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
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

        return $qb->getQuery()->getResult();
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
