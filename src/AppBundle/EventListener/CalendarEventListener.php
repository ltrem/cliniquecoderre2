<?php
// src/AppBundle/EventListener/CalendarEventListener.php

namespace AppBundle\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use AppBundle\Entity\Event;
use AppBundle\Repository\EventRepository;
use Doctrine\ORM\EntityManager;
use libphonenumber\PhoneNumberUtil;

class CalendarEventListener
{
    private $entityManager;
    private $phoneNumberUtil;

    public function __construct(EntityManager $entityManager, PhoneNumberUtil $phoneNumberUtil)
    {
        $this->entityManager = $entityManager;
        $this->phoneUtil = $phoneNumberUtil;
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {

        // The original request so you can get filters from the calendar
        // Use the filter in your query for example
        $request = $calendarEvent->getRequest();
        $isEmploye = $request->get('isEmploye');
        $selectedEmploye = $request->get('employe');

        if ($selectedEmploye != 'all') {
            $selectedEmploye = $this->entityManager->getRepository('AppBundle:Employe')->find($selectedEmploye);
        }

        $isClient = $request->get('isClient');
        if ($isSchedule = $request->get('isSchedule')) {
            // Load edited schedule
            $schedule = $this->entityManager->getRepository('AppBundle:Schedule')->findOneByEmployee($selectedEmploye);
        }


        // TODO: Make sure the Schedule calendar is working
        // One employee will have One Schedule.
        // One Schedule will have unlimited amount of blocks
        // - Maybe blocks can have type (availability, non-availability, vacation, etc)

        // load events using your custom logic here,
        // for instance, retrieving events from a repository
        //
        // Create EventEntity instances and populate it's properties with data
        // from your own entities/database values.

        // Build array with schedule blocks
        if ($isSchedule) {

            $scheduleBlock = $this->entityManager->getRepository('AppBundle:ScheduleBlock')->findScheduleBlocks($schedule);

            dump($scheduleBlock);
            foreach ($scheduleBlock as $block) {
                $blockEvent = new EventEntity("Plage horaire", new \DateTime($block->getDateFrom()->format("Y-m-d H:i:s")), new \DateTime($block->getDateTo()->format("Y-m-d H:i:s")));

                //optional calendar event settings
                $blockEvent->setAllDay(false); // default is false, set to true if this is an all day event
                $blockEvent->setBgColor('green'); //set the background color of the event's label
                $blockEvent->setFgColor('#FFFFFF'); //set the foreground color of the event's label
                #$blockEvent->setUrl('http://www.google.com'); // url to send user to when event label is clicked
                $blockEvent->setCssClass(''); // a custom class you may want to apply to event labels

                $calendarEvent->addEvent($blockEvent);
            }
        }

        // Build array with empty schedule slots
        if ($isClient) {

            $startDate = $calendarEvent->getStartDatetime();
            $endDate = $calendarEvent->getEndDatetime();

            $interval = $startDate->diff($endDate->modify('+1 days'));
            $dayRange = $interval->format('%a');

            $blockPerDays = 12;

            $dayStartTime = new \DateTime($startDate->format("Y-m-d H:i:s"));
            $dayStartTime->setTime(06, 00, 00);

            for($i = 0; $i < $dayRange; $i++) {

                $eventStart = new \DateTime($dayStartTime->format("Y-m-d H:i:s"));
                $eventEnd = new \DateTime($dayStartTime->format("Y-m-d H:i:s"));

                for($y = 0; $y <= $blockPerDays; $y++) {
                    $blockEvents[] = new EventEntity("Libre " . $i . $y, new \DateTime($eventStart->format("Y-m-d H:i:s")), new \DateTime($eventEnd->modify('+1 hour')->format("Y-m-d H:i:s")));
                    $eventStart->modify('+1 hour');
                }

                $dayStartTime->modify('+1 days');
            }

            foreach ($blockEvents as $blockEvent) {
                unset($companyEvent);
                unset($startTime);
                unset($endTime);

                $eventClass = 'available';

                $startTime = new \DateTime($blockEvent->getStartDatetime()->format("Y-m-d H:i:s"));
                $endTime = new \DateTime($blockEvent->getEndDateTime()->format("Y-m-d H:i:s"));

                $companyEvent = $this->entityManager->getRepository('AppBundle:Event')->findOneByEmployeBetweenDate($selected_employe, $startTime, $endTime);

                if (is_object($companyEvent) && $companyEvent instanceof Event) {
                    $blockEvent->setTitle(utf8_encode('R�serv�'));

                    if ($companyEvent->getEmploye()->getId() == 2) {
                        $eventClass = 'unavailable julee-unavailable';
                    } else {
                        $eventClass = 'unavailable simon-unavailable';
                    }
                }

                //optional calendar event settings
                $blockEvent->setAllDay(false); // default is false, set to true if this is an all day event
                $blockEvent->setBgColor($eventClass); //set the background color of the event's label
                $blockEvent->setFgColor('#FFFFFF'); //set the foreground color of the event's label
                #$blockEvent->setUrl('http://www.google.com'); // url to send user to when event label is clicked
                $blockEvent->setCssClass($eventClass); // a custom class you may want to apply to event labels

                //finally, add the event to the CalendarEvent for displaying on the calendar
                $calendarEvent->addEvent($blockEvent);
            }
        }

        // Build array with empty schedule slots
        if ($isEmploye) {

            $startDate = $calendarEvent->getStartDatetime();
            $endDate = $calendarEvent->getEndDatetime();

            $interval = $startDate->diff($endDate->modify('+1 days'));
            $dayRange = $interval->format('%a');

            $blockPerDays = 12;

            $dayStartTime = new \DateTime($startDate->format("Y-m-d H:i:s"));
            $dayStartTime->setTime(06, 00, 00);

            for($i = 0; $i < $dayRange; $i++) {

                $eventStart = new \DateTime($dayStartTime->format("Y-m-d H:i:s"));
                $eventEnd = new \DateTime($dayStartTime->format("Y-m-d H:i:s"));

                for($y = 0; $y <= $blockPerDays; $y++) {
                    $blockEvents[] = new EventEntity("Libre " . $i . $y, new \DateTime($eventStart->format("Y-m-d H:i:s")), new \DateTime($eventEnd->modify('+1 hour')->format("Y-m-d H:i:s")));
                    $eventStart->modify('+1 hour');
                }

                $dayStartTime->modify('+1 days');
            }

            foreach ($blockEvents as $blockEvent) {
                $alreadyAdded = false;
                unset($companyEvent);
                unset($startTime);
                unset($endTime);

                $eventClass = 'available';

                $startTime = new \DateTime($blockEvent->getStartDatetime()->format("Y-m-d H:i:s"));
                $endTime = new \DateTime($blockEvent->getEndDateTime()->format("Y-m-d H:i:s"));

                if ($selectedEmploye != 'all') {
                    $companyEvent = $this->entityManager->getRepository('AppBundle:Event')->findOneByEmployeBetweenDate($selectedEmploye, $startTime, $endTime);

                    // If Event scheduled
                    if (is_object($companyEvent) && $companyEvent instanceof Event) {

                        $client_contacts = $companyEvent->getClient()->getContacts();
                        $phone = $this->phoneUtil->format($client_contacts[0]->getPhoneCell(), \libphonenumber\PhoneNumberFormat::NATIONAL);
                        $titleContent = utf8_encode($companyEvent->getClient()->getFullname());
                        $titleContent .= ' - ' . $phone;
                        $titleContent .= ' - ' . $companyEvent->getClient()->getUser()->getEmail();
                        $blockEvent->setTitle($titleContent);

                        if ($companyEvent->getEmploye()->getId() == 2) {
                            $eventClass = 'unavailable julee-unavailable';
                        } else {
                            $eventClass = 'unavailable simon-unavailable';
                        }

                        //optional calendar event settings
                        $blockEvent->setAllDay(false); // default is false, set to true if this is an all day event
                        $blockEvent->setBgColor($eventClass); //set the background color of the event's label
                        $blockEvent->setFgColor('#FFFFFF'); //set the foreground color of the event's label
                        #$blockEvent->setUrl('http://www.google.com'); // url to send user to when event label is clicked
                        $blockEvent->setCssClass($eventClass); // a custom class you may want to apply to event labels

                        //finally, add the event to the CalendarEvent for displaying on the calendar
                        $calendarEvent->addEvent($blockEvent);
                        $alreadyAdded = true;
                    }
                } elseif ($selectedEmploye == 'all') {
                    $companyEvents = $this->entityManager->getRepository('AppBundle:Event')->findAllBetweenDate($startTime, $endTime);
                    if ($companyEvents) {
                        foreach ($companyEvents as $companyEvent) {
                            unset($blockEvent);
                            $blockEvent = new EventEntity('', $companyEvent->getStartTime(), $companyEvent->getEndTime());
                            $client_contacts = $companyEvent->getClient()->getContacts();
                            $phone = $this->phoneUtil->format($client_contacts[0]->getPhoneCell(), \libphonenumber\PhoneNumberFormat::NATIONAL);
                            $titleContent = utf8_encode($companyEvent->getClient()->getFullname());
                            $titleContent .= ' - ' . $phone;
                            $titleContent .= ' - ' . $companyEvent->getClient()->getUser()->getEmail();
                            $blockEvent->setTitle($titleContent);

                            if ($companyEvent->getEmploye()->getId() == 2) {
                                $eventClass = 'unavailable julee-unavailable';
                            } else {
                                $eventClass = 'unavailable simon-unavailable';
                            }

                            //optional calendar event settings
                            $blockEvent->setAllDay(false); // default is false, set to true if this is an all day event
                            $blockEvent->setBgColor($eventClass); //set the background color of the event's label
                            $blockEvent->setFgColor('#FFFFFF'); //set the foreground color of the event's label
                            #$blockEvent->setUrl('http://www.google.com'); // url to send user to when event label is clicked
                            $blockEvent->setCssClass($eventClass); // a custom class you may want to apply to event labels

                            //finally, add the event to the CalendarEvent for displaying on the calendar
                            $calendarEvent->addEvent($blockEvent);
                            $alreadyAdded = true;
                        }
                    }
                }

                if (!$alreadyAdded) {
                    //optional calendar event settings
                    $blockEvent->setAllDay(false); // default is false, set to true if this is an all day event
                    $blockEvent->setBgColor($eventClass); //set the background color of the event's label
                    $blockEvent->setFgColor('#FFFFFF'); //set the foreground color of the event's label
                    #$blockEvent->setUrl('http://www.google.com'); // url to send user to when event label is clicked
                    $blockEvent->setCssClass($eventClass); // a custom class you may want to apply to event labels

                    //finally, add the event to the CalendarEvent for displaying on the calendar
                    $calendarEvent->addEvent($blockEvent);
                }
            }
        }

        /*
        foreach ($blockEvents as $blockEvent) {
            unset($companyEvent);
            unset($startTime);
            unset($endTime);

            $eventClass = 'available';

            $startTime = new \DateTime($blockEvent->getStartDatetime()->format("Y-m-d H:i:s"));
            $endTime = new \DateTime($blockEvent->getEndDateTime()->format("Y-m-d H:i:s"));

            $companyEvent = $this->entityManager->getRepository('AppBundle:Event')->findOneBetweenDate($startTime, $endTime);

            if (is_object($companyEvent) && $companyEvent instanceof Event) {

                $blockEvent->setTitle('Reserver ' . $companyEvent->getName());
                $eventClass = 'unavailable';
                $z++;
            }

            //optional calendar event settings
            $blockEvent->setAllDay(false); // default is false, set to true if this is an all day event
            $blockEvent->setBgColor($eventClass); //set the background color of the event's label
            $blockEvent->setFgColor('#FFFFFF'); //set the foreground color of the event's label
            #$blockEvent->setUrl('http://www.google.com'); // url to send user to when event label is clicked
            $blockEvent->setCssClass($eventClass); // a custom class you may want to apply to event labels

            //finally, add the event to the CalendarEvent for displaying on the calendar
            $calendarEvent->addEvent($blockEvent);
        }
        */
    }
}