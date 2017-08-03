<?php
// src/AppBundle/Command/SendEventReminderCommand.php

namespace AppBundle\Command;

use AppBundle\Entity\EventReminder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


use AppBundle\Entity\Event;
use AppBundle\Entity\Communication;

// Send email and/or SMS to remind users they have an appointment.
class SendEventReminderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        // ...
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:send-event-reminder')

            // the short description shown while running "php bin/console list"
            ->setDescription('Envoyer un rappel pour les rendez-vous.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("Cette commande vous permet d'envoyer un rappel � tous les clients ayant un rendez-vous prochainement...")
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lineBegin = '|* ';

        // ...
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '|*******************************************************************|',
            '|* Send reminder to all client. ************************************|',
            '|*******************************************************************|'
        ]);

        // Get upcoming Events
        $em = $this->getContainer()->get('doctrine')->getManager();
        $events1days = $em->getRepository('AppBundle:Event')->findXDaysUpcomingEvents(7);
        // $events7days = $em->getRepository('AppBundle:Event')->findXDaysUpcomingEvents(7);

        $arrEvents['1'] = $events1days;
        // $arrEvents['7'] = $events7days;

        // Loop events and send Communication reminders
        foreach ($arrEvents as $key => $events) {

            $output->writeln([
                $lineBegin . $key . ' jours avant le rendez-vous',
            ]);

            foreach ($events as $event) {
                // Echo Output
                $output->writeln(
                    $lineBegin . $event->getClient()->getFirstName() . ' ' . $event->getClient()->getLastName() . ' (' . $event->getName() . ') - ' . $event->getStartTime()->format('Y-m-d H:i:s')
                );

                // Get entities
                $client = $event->getClient();
                $user = $client->getUser();
                $contacts = $client->getContacts()[0];

                // Create Communication
                $communication = new Communication();
                $communication->setType('sms,email');
                $communication->setDateSent(new \DateTime('now'));
                $communication->setTitle('Rappel de rendez-vous');
                $communication->setContent('Contenu du message');
                $communication->setEmail($user->getUsername());
                if ($contacts->getPhoneCellCarrier()) {
                    $communication->setPhone(preg_replace('/\D/', '', $contacts->getPhoneCell()) . '@' . $contacts->getPhoneCellCarrier()->getMailToSMS());
                }

                // Create EventReminder
                $reminder = new EventReminder();
                $reminder->setCommunication($communication);
                $reminder->setEvent($event);

                // Send communication
                $this->getContainer()->get('app.communication_mailer')->send($communication);

                // Persist
                $em->persist($reminder);
                $em->flush();
            }
        }


        // // outputs a message without adding a "\n" at the end of the line
        // $output->write('You are about to ');
        // $output->write('create a user.');

        // // outputs a message followed by a "\n"
        $output->writeln($lineBegin);
        $output->writeln('|* Reminder succesfully sent! **************************************|');
        $output->writeln('|*******************************************************************|');
        $output->writeln('');

    }
}