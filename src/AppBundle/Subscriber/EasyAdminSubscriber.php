<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\Client;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Coordinate;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
           // 'easy_admin.post_new' => array('addCoordinate'),
        );
    }

    public function addCoordinate(GenericEvent $event)
    {
        $entity = $event->getSubject();

        // Add Client and default Coordinate to User
        $user = new User();
        $coordinate = new Coordinate();
        $contact = new Contact();

        $coordinate->setIsPrimary(true);
        $entity->addCoordinate($coordinate);
        $entity->addContact($contact);
        $user->setClient($entity);

        $event['entity'] = $entity;
    }
}