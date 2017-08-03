<?php
namespace AppBundle\Service;


use AppBundle\Entity\Communication;

class CommunicationMailerService
{
    private $mailer;
    private $twilio;

    public function __construct($mailer, $twilio)
    {
        $this->mailer = $mailer;
        $this->twilio = $twilio;
    }

    public function send(Communication $communication)
    {

        // Send email
        $message = \Swift_Message::newInstance()
            ->setFrom('info@cliniquecoderre.com')
            ->setSubject(
                $communication->getTitle()
            )
            ->setBody(
                $communication->getContent(),
                'text/html'
            )
        ;

        $type = explode(',',$communication->getType());

        if (in_array('email', $type)) {
            $message->setTo($communication->getEmail());
            // Send message
            $this->mailer->send($message);
        }

        if (in_array('sms', $type)) {
            /* TWILIO SETUP
            $this->twilio->account->messages->sendMessage(
                '+15005550006', // From a Twilio number in your account
                '+18198188736', // Text any number
                "Hello monkey!"
            );
            */

            $message->setTo($communication->getPhone());
            // Send message
            $this->mailer->send($message);
        }

    }
}