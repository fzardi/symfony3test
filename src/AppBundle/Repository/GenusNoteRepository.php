<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use Doctrine\ORM\EntityRepository;

class GenusNoteRepository extends EntityRepository
{
    /**
    * @param Genus $genus
    * @return GenusNote[]
    */
    public function findAllRecentNotesForGenus(Genus $genus)
    {
        return $this->createQueryBuilder('gn')
//            ->select('count(gn.id)')
            ->andWhere('gn.genus = :genus')
            ->setParameter('genus', $genus)
            ->andWhere('gn.createdAt > :recentDate')
            ->setParameter('recentDate', new \DateTime('-3 months'))
            ->getQuery()
            ->execute();
    }
}