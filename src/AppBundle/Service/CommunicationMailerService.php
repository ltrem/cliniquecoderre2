<?php
namespace AppBundle\Service;


use AppBundle\Entity\Communication;

class CommunicationMailerService
{
    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendCommunication(Communication $communication)
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
            $message->setTo($communication->getPhone());
            // Send message
            $this->mailer->send($message);
        }

    }
}