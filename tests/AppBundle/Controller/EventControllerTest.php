<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\Tests\LoadEventData;
use AppBundle\DataFixtures\Tests\LoadUserData;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testUnauthorized()
    {
        $client = $this->makeClient();
        $client->request('GET', '/rendez-vous/nouveau');
        $this->assertStatusCode(302, $client);
    }

    public function testLogin()
    {
        $references = $this->loadFixtures([LoadUserData::class])->getReferenceRepository();
        $user = $references->getReference('user_cancel');

        $this->loginAs($user, 'main');

        $client = $this->makeClient();
        $client->request('GET', '/rendez-vous/nouveau');
        $this->assertStatusCode(200, $client);
    }

    public function testEventCancellation()
    {
        $references = $this->loadFixtures([
            LoadUserData::class,
            LoadEventData::class
        ])->getReferenceRepository();

        $event_replace= $references->getReference('event_replace');
        $event_to_cancel = $references->getReference('event_cancel');

        $user = $references->getReference('user_cancel');
        $this->loginAs($user, 'main');


        $client = $this->makeClient();
        $path = $this->getUrl('event_cancel', array('id' => $event_to_cancel->getId()));
        $crawler = $client->request('POST', $path, array(), array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
            ));

        $buttonValue = $this->getContainer()->get('translator')->trans('event.cancellation.submit');
        $form = $crawler->selectButton($buttonValue)->form();
        $form->setValues(['event_cancellation[reason]' => 'J\'ai cancellé mon rendez-vous']);

        $client->enableProfiler();
        $client->setServerParameter('HTTP_X-Requested-With', 'XMLHttpRequest');
        $client->submit($form);
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        // Assert the query succeeded and that email was sent
        $this->assertStatusCode(200, $client);
        $this->assertGreaterThan(1, $mailCollector->getMessageCount());

        // Assert that a command have been generated
        $em = $this->getContainer()->get('doctrine')->getManager();
        $commands = $em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->findAll();
        dump($commands);
        $this->assertCount(1, $commands);
    }

    public function testAppointmentNotificationCommand()
    {
        $this->verbosityLevel = 'debug';
        // $this->runCommand('schedule:execute');
    }
}
