<?php
namespace AppBundle\Service;


use AppBundle\Entity\Communication;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vresh\TwilioBundle\Service\TwilioWrapper;

class CommunicationMailerService
{
    private $container;
    private $mailer;
    private $twilio;
    private $phoneUtil;

    public function __construct(ContainerInterface $container, $mailer, TwilioWrapper $twilio, PhoneNumberUtil $phoneUtil)
    {
        $this->container = $container;
        $this->mailer = $mailer;
        $this->twilio = $twilio;
        $this->phoneUtil = $phoneUtil;
    }

    public function send(Communication $communication)
    {

        $type = explode(',',$communication->getType());
        $type = array_map('trim', $type);

        if (in_array('sms', $type)) {

            $phoneNumber = $communication->getPhone();
            if ($phoneNumber instanceof PhoneNumber) {
                $phoneNumber = $this->phoneUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164);
            }

            // Send SMS (with Vresh/TwilioBundle)
            $this->twilio->account->messages->sendMessage(
                $this->container->getParameter('twilio_from_phone'), // From a Twilio number in your account
                $phoneNumber, // Text any number
                $communication->getContent()
            );

        }

        if (in_array('email', $type)) {

            // Prepare and send email (With cspooSwiftmailerMailgunBundle)
            $message = \Swift_Message::newInstance()
                ->setFrom($this->container->getParameter('mailgun_from'))
                ->setTo($communication->getEmail())
                ->setSubject(
                    $communication->getTitle()
                )
                ->setBody(
                    $communication->getContent(),
                    'text/html'
                )
            ;
            $this->mailer->send($message);
        }

    }
}