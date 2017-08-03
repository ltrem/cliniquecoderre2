<?php

namespace AppBundle\Subscriber;

use AppBundle\Event\ClientCreatedEvent;
use AppBundle\Event\CommunicationSentEvent;
use AppBundle\Manager\ClientManager;
use AppBundle\Manager\CommunicationManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommunicationSubscriber implements EventSubscriberInterface {

    private $communicationManager;

    public function __construct(CommunicationManager $communicationManager)
    {
        $this->communicationManager = $communicationManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            CommunicationSentEvent::NAME => 'onCommunicationSent',
        ];
    }

    public function onCommunicationSent(CommunicationSentEvent $communicationSent) {

        // Send communication
        {
            $communication = $communicationSent->getCommunication();
            $this->communicationManager->sendCommunication($communication);
        }
    }
}