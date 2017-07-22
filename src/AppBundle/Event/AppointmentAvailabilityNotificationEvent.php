<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AppointmentAvailabilityNotificationEvent extends Event {

    const NAME = "app.appointement_availability_notification";

    /*
     * @var Event
     */
    private $appointment_notification;


    public function __construct(\AppBundle\Entity\AppointmentAvailabilityNotification $appointment_notification) {

        $this->appointment_notification = $appointment_notification;

    }

    /**
     * @return \AppBundle\Entity\AppointmentAvailabilityNotification
     */
    public function getAppointmentNotification()
    {
        return $this->appointment_notification;
    }
}