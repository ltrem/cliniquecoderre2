<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\Communication;
use AppBundle\Entity\User;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::PRE_PERSIST => 'onPrePersist',
        ];
    }

    public function onPrePersist(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if ($entity instanceof Communication) {
            $user = $this->tokenStorage->getToken()->getUser();
            if (!$user instanceof User) {
                $user = null;
            }
            $entity->setSentBy($user);
        }
    }
}