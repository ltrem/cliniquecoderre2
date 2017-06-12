<?php
namespace AppBundle\Manager;

use AppBundle\Entity\Communication;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use AppBundle\Service\CommunicationMailerService;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class ClientManager {

    private $em;
    private $template;
    private $session;
    private $communicationMailer;
    private $phoneUtil;
    private $router;
    private $translator;

    public function __construct(EntityManager $entityManager, EngineInterface $template, Session $session, CommunicationMailerService $communicationMailerService, PhoneNumberUtil $phoneNumberUtil, Router $router, TranslatorInterface $translator)
    {
        $this->em = $entityManager;
        $this->template = $template;
        $this->session = $session;
        $this->communicationMailer = $communicationMailerService;
        $this->phoneUtil = $phoneNumberUtil;
        $this->router = $router;
        $this->translator = $translator;
    }

    public function sendRegistrationConfirmation(User $user)
    {

        // Assign ROLE_CLIENT
        $roles = array('ROLE_CLIENT');
        $user->setRoles($roles);

        // Assign User to Client
        $client = $user->getClient();
        $client->setUser($user);

        $token = $user->getResetPasswordToken();

        $password_reset_template= $this->template->render('user/email_registration_confirmation.html.twig', array(
            'confirmationUrl' =>  $this->router->generate('user_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL)
        ));

        // Send email to new Client
        // Create Communication
        $communication = new Communication();
        $communication->setType('email');
        $communication->setDateSent(new \DateTime('now'));
        $communication->setTitle($this->translator->trans('registration.emailConfirmation.subject'));
        $communication->setContent($password_reset_template);
        $communication->setEmail($user->getUsername());

        $client->addCommunication($communication);

        // Add communication to database
        $this->em->persist($communication);
        $this->em->getConnection()->beginTransaction();
        $this->em->flush();

        // Add flash message
        //$this->session->getFlashBag()->add('notice', 'Appointment cancelled ' . $appointment->getName());

        // Send notification to user
        $this->communicationMailer->sendCommunication($communication);

        $this->em->getConnection()->commit();

        return true;
    }
}