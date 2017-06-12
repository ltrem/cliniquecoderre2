<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ClientCreatedEvent extends Event {

    const NAME = "app.client_created";

    /*
     * @var User
     */
    private $user;


    public function __construct(\AppBundle\Entity\User $user) {

        $this->user = $user;

    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}