<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Client;
use AppBundle\Entity\Employe;
use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{
    public function findXDaysUpcomingEvents($x = 7)
    {
        // Get Today's date
        $now = new \DateTime('now');
        $startTime = new \DateTime($now->modify('+'. $x .' days')->setTime(00, 00, 00)->format("Y-m-d H:i:s"));
        $endTime = new \DateTime($now->modify('+'. $x .' days')->setTime(23, 59, 59)->format("Y-m-d H:i:s"));

        return $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.startTime >= :startTime')
            ->andWhere('e.startTime <= :endTime')
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->orderBy('e.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingEvents(Client $client = null, Employe $employe = null)
    {
        // Get Today's date
        $now = new \DateTime('now');
        return $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.startTime >= :startTime')
            ->andWhere('e.cancellation is null')
            ->andWhere('e.client = :client or e.employe = :employe')
            ->setParameter('startTime', $now)
            ->setParameter('client', $client)
            ->setParameter('employe', $employe)
            ->orderBy('e.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCancelledUpcomingEvents(Client $client)
    {
        // Get Today's date
        $now = new \DateTime('now');
        return $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.startTime >= :startTime')
            ->andWhere('e.cancellation is not null')
            ->andWhere('e.client = :client')
            ->setParameter('startTime', $now)
            ->setParameter('client', $client)
            ->orderBy('e.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPastEvents(Client $client, $x = 7)
    {
        // Get Today's date
        $now = new \DateTime('now');
        return $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.startTime < :startTime')
            ->setParameter('startTime', $now)
            ->setParameter('client', $client)
            ->andWhere('e.client = :client')
            ->orderBy('e.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllBetweenDate(\DateTime $start, \DateTime $end)
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.startTime >= :startTime')
            ->andWhere('e.endTime <= :endTime')
            ->andWhere('e.cancellation IS NULL')
            ->setParameter('startTime', $start)
            ->setParameter('endTime', $end)
            ->orderBy('e.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneBetweenDate(\DateTime $start, \DateTime $end)
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.startTime >= :startTime')
            ->andWhere('e.endTime <= :endTime')
            ->andWhere('e.cancellation IS NULL')
            ->setParameter('startTime', $start)
            ->setParameter('endTime', $end)
            ->orderBy('e.startTime', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByEmployeBetweenDate(Employe $employe, \DateTime $start, \DateTime $end)
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.startTime >= :startTime')
            ->andWhere('e.endTime <= :endTime')
            ->andWhere('e.employe = :employe')
            ->setParameter('startTime', $start)
            ->setParameter('endTime', $end)
            ->setParameter('employe', $employe)
            ->orderBy('e.startTime', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUpcomingEmergency()
    {
        // Get Upcoming Event that have Emergency checked
        $now = new \DateTime('now');
        $startTime = new \DateTime($now->modify('+1days')->setTime(00, 00, 00)->format("Y-m-d H:i:s"));

        return $this->createQueryBuilder('e')
            ->select('e')
            ->leftJoin('e.appointmentAvailabilityNotifications', 'notif')
            ->leftJoin('notif.eventToReplace', 'etr')
            ->where('e.cancellation IS NULL')
            ->andWhere('e.emergency = 1')
            ->andWhere('e.startTime > :startTime')
            ->andWhere('etr.client IS NULL')
            ->setParameter('startTime', $startTime)
            ->orderBy('e.startTime', 'DESC')
            ->addOrderBy('e.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
