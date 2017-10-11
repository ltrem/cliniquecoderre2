<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Event;
use AppBundle\Entity\ScheduleBlock;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFixtures implements FixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager, [
            'providers' => [$this]
        ]);

        $this->loadEvents($manager);
        $this->loadScheduleBlocks($manager);

    }

    public function loadEvents(ObjectManager $manager) {

        $events = [];

        $i = 250;
        while ($i-- >= 0) {

            $exist = true;
            while ($exist) {
                $randomEvent = $this->getRandomEventTime();
                $month = $randomEvent['month'];
                $day = $randomEvent['day'];
                $hourStart = $randomEvent['hourStart'];
                $hourEnd = $randomEvent['hourEnd'];

                if (!in_array("2017-$month-$day $hourStart:00:00", $events)) {

                    $currentEventStart = "2017-$month-$day $hourStart:00:00";
                    $currentEventEnd = "2017-$month-$day $hourEnd:00:00";
                    $events[] = $currentEventStart;
                    $exist = false;
                }
            }

            $event = new Event();
            $event->setName("Rendez-vous $currentEventStart");
            $event->setStartTime(new \DateTime($currentEventStart));
            $event->setEndTime(new \DateTime($currentEventEnd));
            $event->setEmergency(0);
            $client = $manager->getRepository('AppBundle:Client')->find(rand(1, 3));
            $event->setClient($client);
            $employe = $manager->getRepository('AppBundle:Employe')->find(1);
            $event->setEmploye($employe);

            $manager->persist($event);
            $manager->flush();
        }

        $i = 500;
        while ($i-- >= 250) {
            $exist = true;
            while ($exist) {
                $randomEvent = $this->getRandomEventTime();
                $month = $randomEvent['month'];
                $day = $randomEvent['day'];
                $hourStart = $randomEvent['hourStart'];
                $hourEnd = $randomEvent['hourEnd'];

                if (!in_array("2017-$month-$day $hourStart:00:00", $events)) {

                    $currentEventStart = "2017-$month-$day $hourStart:00:00";
                    $currentEventEnd = "2017-$month-$day $hourEnd:00:00";
                    $events[] = $currentEventStart;
                    $exist = false;
                }
            }

            $event = new Event();
            $event->setName("Rendez-vous $currentEventStart");
            $event->setStartTime(new \DateTime($currentEventStart));
            $event->setEndTime(new \DateTime($currentEventEnd));
            $event->setEmergency(1);
            $client = $manager->getRepository('AppBundle:Client')->find(rand(1,3));
            $event->setClient($client);
            $employe = $manager->getRepository('AppBundle:Employe')->find(1);
            $event->setEmploye($employe);

            $manager->persist($event);
            $manager->flush();
        }

    }

    public function loadScheduleBlocks(ObjectManager $manager) {

        $scheduleBlocks = [];

        $schedule = 3;

        while ($schedule-- >= 0) {

            $day = 300;
            while ($day-- >= 0) {

                $dateFrom = new \DateTime('now');
                $dateFrom->setTime('08', '00')->modify('+' . $day . ' day');

                $dateTo = new \DateTime('now');
                $dateTo->setTime('17', '00')->modify('+' . $day . ' day');

                $scheduleBlock = new ScheduleBlock();
                $scheduleBlock->setDateFrom($dateFrom);
                $scheduleBlock->setDateTo($dateTo);

                $objSchedule = $manager->getRepository('AppBundle:Schedule')->find($schedule+1);
                $scheduleBlock->setSchedule($objSchedule);

                $manager->persist($scheduleBlock);
                $manager->flush();
            }
        }

    }

    public function getRandomEventTime() {

        $hourStart = rand(8, 18);
        $event = array(
            'month' => rand(9, 11),
            'day' => rand(1, 29),
            'hourStart' => $hourStart,
            'hourEnd' => $hourStart + 1
        );

        return $event;
    }

    public function phoneNumber()
    {
        $phone = $this->container->get('libphonenumber.phone_number_util')->parse('8198188736', 'CA');
        return $phone;
    }
}
