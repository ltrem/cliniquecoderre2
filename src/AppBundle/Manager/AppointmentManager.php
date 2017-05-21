<?php
namespace AppBundle\Manager;

use AppBundle\Entity\AppointmentAvailabilityNotification;
use AppBundle\Entity\Client;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Event;
use AppBundle\Entity\EventCancellation;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use AppBundle\Service\CommunicationMailerService;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class AppointmentManager {

    private $em;
    private $template;
    private $session;
    private $communicationMailer;
    private $phoneUtil;

    public function __construct(EntityManager $entityManager, EngineInterface $template, Session $session, CommunicationMailerService $communicationMailerService, PhoneNumberUtil $phoneNumberUtil)
    {
        $this->em = $entityManager;
        $this->template = $template;
        $this->session = $session;
        $this->communicationMailer = $communicationMailerService;
        $this->phoneUtil = $phoneNumberUtil;
    }

    public function notifyClient(User $user, Event $appointment)
    {
        // Prepare variable
        $client = $appointment->getClient();
        $client_contacts = $client->getContacts();
        $phone = $this->phoneUtil->format($client_contacts[0]->getPhoneCell(), \libphonenumber\PhoneNumberFormat::E164);
        $carrier = $client_contacts[0]->getPhoneCellCarrier();
        $phone_to_email = str_replace('+1', '', $phone) . '@' . $carrier->getMailToSMS();

        // Send user that his appointment was cancelled
        {
            // Create a communication
            $communication = new Communication();
            $communication->setEmail($user->getEmail());
            $communication->setPhone($phone_to_email);
            $communication->setDateSent(new \DateTime('now'));
            $communication->setTitle($appointment->getName());
            $communication->setContent($appointment->getCancellation()->getReason());
            $communication->setType('sms,email');
            $communication_template = $this->template->render('communication/communication_template.html.twig', array(
                'communication' => $communication
            ));
            $communication->setContent($communication_template);

            // Assign communication to client
            $client->addCommunication($communication);

            // Add communication to database
            $this->em->persist($communication);
            $this->em->getConnection()->beginTransaction();
            $this->em->flush();

            // Add flash message
            $this->session->getFlashBag()->add('notice', 'Appointment cancelled ' . $appointment->getName());

            // Send notification to user
            $this->communicationMailer->sendCommunication($communication);

            $this->em->getConnection()->commit();
        }

        return true;

    }

    public function notifyEmergencyList(Event $appointment)
    {

        // Create a CRON that will handle notification
        $scheduledCommand = new ScheduledCommand();
        $scheduledCommand->setName('Appointment Availability Notification - Event #' . $appointment->getId());
        $scheduledCommand->setCommand('app:appointment_availability_notification');
        $scheduledCommand->setArguments('--event_id=' . $appointment->getId());
        //$scheduledCommand->setCronExpression('*/1 7-21 * * *');
        $scheduledCommand->setCronExpression('* * * * *');
        $scheduledCommand->setExecuteImmediately(true);
        $scheduledCommand->setPriority(1);
        $scheduledCommand->setDisabled(false);
        $scheduledCommand->setLocked(false);
        $this->em->persist($scheduledCommand);
        $this->em->flush();

        $scheduledCommand->setArguments('--event_id=' . $appointment->getId() . ' --schedule_command_id=' . $scheduledCommand->getId());
        $this->em->flush();

    }

    public function swapClientAppointmentAvailabilityAnswer(AppointmentAvailabilityNotification $appointmentAvailabilityNotification, Client $from_this_client, Client $to_this_client)
    {

        $eventExist = $this->em->getRepository('AppBundle:Event')->findOneBetweenDate($appointmentAvailabilityNotification->getEventFreed()->getStartTime(), $appointmentAvailabilityNotification->getEventFreed()->getEndTime());

        if ($eventExist === null) {
            $newEvent = new Event();
            $newEvent->setClient($to_this_client);
            $newEvent->setName($appointmentAvailabilityNotification->getEventToReplace()->getName());
            $newEvent->setStartTime($appointmentAvailabilityNotification->getEventFreed()->getStartTime());
            $newEvent->setEndTime($appointmentAvailabilityNotification->getEventFreed()->getEndTime());
            $newEvent->setEmergency(false);
            $newEvent->setEmploye($appointmentAvailabilityNotification->getEventFreed()->getEmploye());

            // Add new event to database
            $this->em->persist($newEvent);
            $this->em->getConnection()->beginTransaction();
            $this->em->flush();

            // Add flash message
            $this->session->getFlashBag()->add('notice', 'Vous avez confirmez votre nouveau rendez-vous qui ce tiendra le : '.$appointmentAvailabilityNotification->getEventFreed()->getStartTime()->format('d/m/Y H:i'));

            $this->em->getConnection()->commit();
        }
    }
}