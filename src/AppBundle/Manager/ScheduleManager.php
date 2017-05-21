<?php
namespace src\AppBundle\Manager;


use AppBundle\Entity\Schedule;
use Doctrine\ORM\EntityManager;

class ScheduleManager
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function generateScheduleBlock(Schedule $schedule, array $block_startTime, array $block_endTime)
    {

    }
}