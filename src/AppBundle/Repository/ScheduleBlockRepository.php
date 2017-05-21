<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Schedule;
use Doctrine\ORM\EntityRepository;

class ScheduleBlockRepository extends EntityRepository
{
    public function findScheduleBlocks(Schedule $schedule)
    {
        return $this->createQueryBuilder('sb')
            ->select('sb')
            ->where('sb.schedule = :schedule')
            ->setParameter('schedule', $schedule)
            ->getQuery()
            ->getResult();
    }

    public function findOneBetweenDate(\DateTime $dateFrom, \DateTime $dateTo)
    {
        return $this->createQueryBuilder('sb')
            ->select('sb')
            ->where('sb.dateFrom >= :dateFrom')
            ->andWhere('sb.dateTo <= :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->orderBy('sb.dateFrom', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}