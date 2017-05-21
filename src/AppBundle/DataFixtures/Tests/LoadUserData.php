<?php
/**
 * Created by PhpStorm.
 * User: LTrem
 * Date: 2017-05-06
 * Time: 16:53
 */

namespace AppBundle\DataFixtures\Tests;


use AppBundle\Entity\CellCarrier;
use AppBundle\Entity\Client;
use AppBundle\Entity\Contact;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
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

        // FIRST USER AND CLIENT EMERGENCY 1
        $cellCarrier = new CellCarrier();
        $cellCarrier->setAvailable(1);
        $cellCarrier->setName('Telus');
        $cellCarrier->setMailToSMS('msg.telus.com');
        $manager->persist($cellCarrier);
        $manager->flush();

        $user = new User();
        $user->setEmail('lautrem2@hotmail.com');
        $user->setPlainPassword('123');
        $user->setRoles(['ROLE_CLIENT']);
        $this->setReference('user_emergency', $user);
        $manager->persist($user);
        $manager->flush();

        $contact = new Contact();
        $contact->setPhoneCell($this->container->get('libphonenumber.phone_number_util')->parse('8198188736', 'CA'));
        $contact->setPhoneCellCarrier($cellCarrier);
        $manager->persist($contact);
        $manager->flush();

        $client = new Client();
        $client->setFirstName('Laurence 3');
        $client->setLastname('Tremblay 3');
        $client->setBirthdate(new \DateTime('2017-01-07 00:00:00'));
        $client->setGender('Homme');
        $client->setUser($user);
        $client->addContact($contact);
        $manager->persist($client);
        $manager->flush();

        $this->setReference('client_emergency', $client);


        // SECOND USER AND CLIENT EMERGENCY 2
        $user = new User();
        $user->setEmail('joblo@emptyness21342131.com');
        $user->setPlainPassword('123');
        $user->setRoles(['ROLE_CLIENT']);
        $this->setReference('user_cancel', $user);
        $manager->persist($user);
        $manager->flush();

        $contact = new Contact();
        $contact->setPhoneCell($this->container->get('libphonenumber.phone_number_util')->parse('8198188736', 'CA'));
        $contact->setPhoneCellCarrier($cellCarrier);
        $manager->persist($contact);
        $manager->flush();

        $client = new Client();
        $client->setFirstName('Nothing 23');
        $client->setLastname('Grybnik 23');
        $client->setBirthdate(new \DateTime('2017-01-07 00:00:00'));
        $client->setGender('Homme');
        $client->setUser($user);
        $client->addContact($contact);
        $manager->persist($client);
        $manager->flush();

        $this->setReference('client_emergency_2', $client);



        // THIRD USER AND CLIENT CANCEL 1
        $user = new User();
        $user->setEmail('lautrem2@gmail.com');
        $user->setPlainPassword('123');
        $user->setRoles(['ROLE_CLIENT']);
        $this->setReference('user_cancel', $user);
        $manager->persist($user);
        $manager->flush();

        $contact = new Contact();
        $contact->setPhoneCell($this->container->get('libphonenumber.phone_number_util')->parse('8198188736', 'CA'));
        $contact->setPhoneCellCarrier($cellCarrier);
        $manager->persist($contact);
        $manager->flush();

        $client = new Client();
        $client->setFirstName('Laurence ');
        $client->setLastname('Tremblay 2');
        $client->setBirthdate(new \DateTime('2017-01-07 00:00:00'));
        $client->setGender('Homme');
        $client->setUser($user);
        $client->addContact($contact);
        $manager->persist($client);
        $manager->flush();

        $this->setReference('client_cancel', $client);
    }
}