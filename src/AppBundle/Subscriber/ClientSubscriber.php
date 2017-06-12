<?php

namespace AppBundle\Subscriber;

use AppBundle\Event\ClientCreatedEvent;
use AppBundle\Manager\ClientManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClientSubscriber implements EventSubscriberInterface {

    private $clientManager;

    public function __construct(ClientManager $clientManager)
    {
        $this->clientManager = $clientManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            ClientCreatedEvent::NAME => 'onClientCreate',
        ];
    }

    public function onClientCreate(ClientCreatedEvent $userCreated) {

        // Send registration confirmation email
        {
            $user = $userCreated->getUser();
            $notification_result = $this->clientManager->sendRegistrationConfirmation($user);
        }
    }
}