<?php

namespace AppBundle\Subscriber;

use AppBundle\Event\AppointmentAvailabilityNotificationEvent;
use AppBundle\Event\AppointmentCancelledEvent;
use AppBundle\Manager\AppointmentManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppointmentSubscriber implements EventSubscriberInterface {

    private $appointmentManager;

    public function __construct(AppointmentManager $appointmentManager)
    {
        $this->appointmentManager = $appointmentManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            AppointmentCancelledEvent::NAME => 'onAppointmentCancel',
            AppointmentAvailabilityNotificationEvent::NAME => 'onAppointmentAvailabilityAnswer'
        ];
    }

    public function onAppointmentCancel(AppointmentCancelledEvent $event) {

        // Tell client that his Event have been cancelled
        {
            $user = $event->getAppointment()->getClient()->getUser();
            $appointment = $event->getAppointment();
            $notification_result = $this->appointmentManager->notifyClient($user, $appointment);
        }

        // Tell clients on Emergency list a spot have opened in the schedule
        {
            if ($notification_result) {
                $this->appointmentManager->notifyEmergencyList($appointment);
            }
        }
    }

    public function onAppointmentAvailabilityAnswer(AppointmentAvailabilityNotificationEvent $event) {

        // Replace client event with new, accepted, event.
        {
            $event_to_edit = $event->getAppointmentNotification();
            $from_this_client = $event->getAppointmentNotification()->getEventFreed()->getClient();
            $to_this_client = $event->getAppointmentNotification()->getEventToReplace()->getClient();

            if ($event_to_edit->getAnswer() == 1) {
                $notification_result = $this->appointmentManager->swapClientAppointmentAvailabilityAnswer($event_to_edit, $from_this_client, $to_this_client);
            }
        }
    }
}