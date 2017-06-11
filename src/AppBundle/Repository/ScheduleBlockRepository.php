<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Employe;
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

    public function findAvailabilityBetweenDate(\DateTime $dateFrom, \DateTime $dateTo, Schedule $schedule = null)
    {
        $query = $this->createQueryBuilder('sb')
            ->select('sb')
            ->where('sb.dateFrom >= :dateFrom')
            ->andWhere('sb.dateTo <= :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->orderBy('sb.dateFrom', 'ASC');

        if ($schedule instanceof Schedule) {
            $query
                ->andWhere('sb.schedule = :schedule')
                ->setParameter('schedule', $schedule);
        }

        return $query->getQuery()->getResult();
    }

    public function findAllBetweenDate(\DateTime $start, \DateTime $end)
    {
        return $this->createQueryBuilder('sb')
            ->select('sb')
            ->where('sb.dateFrom >= :dateFrom')
            ->andWhere('sb.dateTo <= :dateTo')
            ->setParameter('dateFrom', $start)
            ->setParameter('dateTo', $end)
            ->orderBy('sb.dateFrom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}