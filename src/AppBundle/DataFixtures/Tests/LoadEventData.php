<?php
/**
 * Created by PhpStorm.
 * User: LTrem
 * Date: 2017-05-06
 * Time: 16:53
 */

namespace AppBundle\DataFixtures\Tests;

use AppBundle\Entity\CellCarrier;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Employe;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadEventData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    function load (ObjectManager $manager)
    {
        $cellCarrier = new CellCarrier();
        $cellCarrier->setAvailable(1);
        $cellCarrier->setName('Telus');
        $cellCarrier->setMailToSMS('msg.telus.com');
        $manager->persist($cellCarrier);
        // EMPLOYE
        $contact = new Contact();
        $contact->setPhoneCell($this->container->get('libphonenumber.phone_number_util')->parse('8198188736', 'CA'));
        $contact->setPhoneCellCarrier($cellCarrier);
        $manager->persist($contact);
        $manager->flush();

        $employe = new Employe();
        $employe->setFirstname('Simon');
        $employe->setLastname('Coderre');
        $employe->setBirthdate(new \DateTime('2017-01-07 00:00:00'));
        $employe->setGender('Homme');
        $employe->addContact($contact);
        $manager->persist($employe);
        $manager->flush();

        // EMERGENCY 1
        $event = new Event();
        $event->setName('Rendez-vous À REMPLACER 1.');
        $event->setStartTime(new \DateTime('2017-07-30 13:00:00'));
        $event->setEndTime(new \DateTime('2017-07-30 14:00:00'));

        $client_1 = $manager->getRepository('AppBundle:Client')->find(1);
        $event->setClient($client_1);
        $event->setEmergency(1);

        $this->setReference('event_replace', $event);

        $manager->persist($event);
        $manager->flush();

        // EMERGENCY 2
        $event = new Event();
        $event->setName('Rendez-vous À REMPLACER 2.');
        $event->setStartTime(new \DateTime('2017-08-02 13:00:00'));
        $event->setEndTime(new \DateTime('2017-08-02 14:00:00'));

        $client_2 = $manager->getRepository('AppBundle:Client')->find(2);
        $event->setClient($client_2);
        $event->setEmergency(1);

        $this->setReference('event_replace_2', $event);

        $manager->persist($event);
        $manager->flush();

        // TO CANCEL 1
        $event = new Event();
        $event->setName('Rendez-vous à canceller 1.');
        $event->setStartTime(new \DateTime('2017-07-01 13:00:00'));
        $event->setEndTime(new \DateTime('2017-07-01 14:00:00'));

        $client_1 = $manager->getRepository('AppBundle:Client')->find(3);
        $event->setClient($client_1);
        $event->setEmergency(0);

        $this->setReference('event_cancel', $event);

        $manager->persist($event);
        $manager->flush();
    }
}