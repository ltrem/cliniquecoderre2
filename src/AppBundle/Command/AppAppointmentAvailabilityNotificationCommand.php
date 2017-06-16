<?php

namespace AppBundle\Command;

use AppBundle\Entity\AppointmentAvailabilityNotification;
use AppBundle\Entity\Communication;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppAppointmentAvailabilityNotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:appointment_availability_notification')
            ->setDescription('...')
            ->addOption('event_id', null, InputOption::VALUE_REQUIRED, 'Option description')
            ->addOption('schedule_command_id', null, InputOption::VALUE_REQUIRED, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Get Appointment that was cancelled to notify about free space
        $event_id = $input->getOption('event_id');
        $schedule_command_id = $input->getOption('schedule_command_id');

        $eventFreed = $em->getRepository('AppBundle:Event')->find($event_id);

        // IT MIGHT BE FIXED, BUT TO BE TESTED PROFUSELY !
        // FIX THIS
        // FIX THIS
        // FIX THIS
        // FIX THIS
        // FIX THIS

            // Find if client are on emergency list, meaning:
            // - Upcoming event scheduled
            // - Not Cancelled
            // - Without Notification for this event
            $eligibleEmergency = $em->getRepository('AppBundle:Event')->findUpcomingEmergency();

        // FIX THIS
        // FIX THIS
        // FIX THIS
        // FIX THIS
        // FIX THIS

        // If eligibleEmergency is not null, and eventFreed is not expired, proceed with notification
        if ($eligibleEmergency !== null && $eventFreed->getEndTime() >= new \DateTime('now')) {

            // Set answer to last notification NO
            $lastNotificationSent = $em->getRepository('AppBundle:AppointmentAvailabilityNotification')->findLastAppointmentNotificationSent($eventFreed);
            if ($lastNotificationSent) {
                $lastNotificationSent->setAnswer(0);
            }

            // Prepare variable to nofity
            $user = $eligibleEmergency->getClient()->getUser();
            $client = $eligibleEmergency->getClient();
            $client_contacts = $client->getContacts();
            $phone = $this->getContainer()->get('libphonenumber.phone_number_util')->format($client_contacts[0]->getPhoneCell(), \libphonenumber\PhoneNumberFormat::E164);
            $carrier = $client_contacts[0]->getPhoneCellCarrier();
            $phone_to_email = str_replace('+1', '', $phone) . '@' . $carrier->getMailToSMS();

            // Generate Token
            $token = rtrim(strtr(base64_encode(openssl_random_pseudo_bytes(32)), '+/', '-_'), '=');

            // Create a communication
            $communication = new Communication();
            $communication->setEmail($user->getEmail());
            $communication->setPhone($phone_to_email);
            $communication->setDateSent(new \DateTime('now'));
            $communication->setTitle('Voulez-vous le rendez-vous?');
            $communication->setContent('Your call!');
            $communication->setType('sms,email');

            $notification_template= $this->getContainer()->get('templating')->render('event/appointment_notification_ajax.html.twig', array(
                'availabilityNotificationYesUrl' => $this->getContainer()->get('router')->generate('appointment_notification_answer', array('token' => $token, 'answer' => 1), UrlGeneratorInterface::ABSOLUTE_URL),
                'availabilityNotificationNoUrl' =>  $this->getContainer()->get('router')->generate('appointment_notification_answer', array('token' => $token, 'answer' => 0), UrlGeneratorInterface::ABSOLUTE_URL)
            ));
            $communication->setContent($notification_template);

            // Assign communication to client
            $client->addCommunication($communication);

            // Create notification
            $notification = new AppointmentAvailabilityNotification();
            $notification->setEventFreed($eventFreed);
            $notification->setEventToReplace($eligibleEmergency);
            $notification->setCommunication($communication);
            $notification->setToken($token);

            $em->persist($notification);
            $em->getConnection()->beginTransaction();
            $em->flush();

            // Send notification to user
            $this->getContainer()->get('app.communication_mailer')
                ->sendCommunication($communication);

            $output->writeln('Notification sent to ' . $communication->getEmail());

            $em->getConnection()->commit();

        } else {

            // If no eligible client, remove CRON
            $scheduledCommand = $em->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find($schedule_command_id);

            if ($scheduledCommand !== null) {

                $em->remove($scheduledCommand);

            }


            // FIX THIS
            // FIX THIS
            // FIX THIS
        }

        //$output->writeln('Command result.');
    }

}
