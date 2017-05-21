<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AppointmentCancelledEvent extends Event {

    const NAME = "app.appointment_cancelled";

    /*
     * @var Appointment
     */
    private $appointment;


    public function __construct(\AppBundle\Entity\Event $appointment) {

        $this->appointment = $appointment;

    }

    /**
     * @return \AppBundle\Entity\Event
     */
    public function getAppointment()
    {
        return $this->appointment;
    }
}