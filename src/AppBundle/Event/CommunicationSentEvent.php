<?php

namespace AppBundle\Event;

use AppBundle\Entity\Communication;
use Symfony\Component\EventDispatcher\Event;

class CommunicationSentEvent extends Event {

    const NAME = "app.communication_sent";

    /*
     * @var Communication
     */
    private $communication;

    public function __construct(Communication $communication) {

        $this->communication = $communication;

    }

    /**
     * @return \AppBundle\Entity\Communication
     */
    public function getCommunication()
    {
        return $this->communication;
    }
}