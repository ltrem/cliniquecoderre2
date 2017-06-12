<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Coordinate;
use AppBundle\Entity\User;
use AppBundle\Form\UserProfileForm;
use AppBundle\Form\UserRegistrationForm;
use AppBundle\Form\UserRequestPasswordForm;
use AppBundle\Form\UserResetPasswordForm;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        // Add Client and default Coordinate to User
        $user = new User();
        $client = new Client();
        $coordinate = new Coordinate();
        $contact = new Contact();

        $coordinate->setIsPrimary(true);
        $client->addCoordinate($coordinate);
        $client->addContact($contact);
        $user->setClient($client);

        $form = $this->createForm(UserRegistrationForm::class, $user);
        $form->get('client')
            ->remove('birthdate')
            ->remove('gender')
            ->remove('coordinates')
            ->remove('picture');
        $form->get('client')->get('contacts')[0]
            ->remove('phoneHome')
            ->remove('phoneWork');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            // Assign ROLE_CLIENT
            $roles = array('ROLE_CLIENT');
            $user->setRoles($roles);

            // Assign User to Client
            $client = $user->getClient();
            $client->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome '.$user->getEmail());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profileAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this sections.');
        }

        $client = $user->getClient();
        if (!is_object($client) || !$client instanceof Client) {
            throw new AccessDeniedException('This client does not have access to this sections.');
        }

        // Old way to sort element
        /*  Old way to sort event
            $now = new \DateTime('now');
            $startTime7days = new \DateTime($now->modify('+1 days')->setTime(00, 00, 00)->format("Y-m-d H:i:s"));

            // Get Past Events
                $criteria7days = Criteria::create()
                    ->where(Criteria::expr()->lt('startTime', $startTime7days));
                $pastEvents = $user->getClient()->getEvents()->matching($criteria7days);

            // Get Upcoming Events
                //$criteria7days = Criteria::create()
                //    ->where(Criteria::expr()->gt('startTime', $startTime7days));
                //$events7days = $user->getClient()->getEvents()->matching($criteria7days);
        */

        $em = $this->getDoctrine()->getManager();

        // Appointment Availability Notification
        $appointmentAvailabilityNotification = $em->getRepository('AppBundle:AppointmentAvailabilityNotification')->findAppointmentAvailabilityFromClient($user->getClient());
        $notifToken = '';
        $eventOffered = '';
        if ($appointmentAvailabilityNotification) {
            $notifToken = $appointmentAvailabilityNotification->getToken();
            $eventOffered = $appointmentAvailabilityNotification->getEventFreed();
        }
        // Past events
        $pastEvents = $em->getRepository('AppBundle:Event')->findPastEvents($user->getClient(), 7);
        // Upcoming events
        $upcomingEvents = $em->getRepository('AppBundle:Event')->findUpcomingEvents($user->getClient());
        // Upcoming and not cancelled events
        $cancelledUpcomingEvents = $em->getRepository('AppBundle:Event')->findCancelledUpcomingEvents($user->getClient());
        // Find upcoming 7 next days
        $events7days = $em->getRepository('AppBundle:Event')->findXDaysUpcomingEvents(7);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'eventOffered' => $eventOffered,
            'notifToken' => $notifToken,
            'upcomingEvents' => $upcomingEvents,
            'cancelledUpcomingEvents' => $cancelledUpcomingEvents,
            'events7days' => $events7days,
            'pastEvents' => $pastEvents,
        ]);
    }

    /**
     * @Route("/profile/edit", name="user_profile_edit")
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this sections.');
        }

        // If User managed to register without Contact, add one to User Client
        if ($user->getClient()->getContacts()->isEmpty()) {
            $contact = new Contact();
            $user->getClient()->addContact($contact);
        }

        $form = $this->createForm(UserProfileForm::class, $user);
        $form->get('client')->remove('picture');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Edited '.$user->getEmail());

            return $this->redirectToRoute('user_profile');
        }


        return $this->render('user/profile_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/profile/request", name="user_request_password")
     */
    public function requestPasswordAction(Request $request)
    {
        $form = $this->createForm(UserRequestPasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();

            // Validate if user exist and send email with token
            if ($email) {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('AppBundle:User')->findUserFromEmail($email);

                if ($user instanceof User) {
                    $client = $user->getClient();
                    if ($client instanceof Client) {
                        // Generate Token
                        $token = rtrim(strtr(base64_encode(openssl_random_pseudo_bytes(32)), '+/', '-_'), '=');

                        // Get User and assign password and token
                        $user->setResetPasswordToken($token);
                        $user->setResetPasswordDate(null);

                        $password_reset_template= $this->renderView('user/password_reset.html.twig', array(
                            'confirmationUrl' =>  $this->generateUrl('user_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL)
                        ));

                        // Send email to new Client
                        // Create Communication
                        $communication = new Communication();
                        $communication->setType('email');
                        $communication->setDateSent(new \DateTime('now'));
                        $communication->setTitle('Cr�ation de profil sur Cliniquecoderre.com');
                        $communication->setContent($password_reset_template);
                        $communication->setEmail($user->getUsername());

                        $client->addCommunication($communication);

                        // Communication sent
                        // Send email
                        $message = \Swift_Message::newInstance()
                            ->setFrom('info@cliniquecoderre.com')
                            ->setTo($communication->getEmail())
                            ->setSubject(
                                $communication->getTitle()
                            )
                            ->setBody(
                                $communication->getContent(),
                                'text/html'
                            )
                        ;
                        $this->get('mailer')->send($message);

                        $em->flush();

                    }
                } else {
                    $this->addFlash('notice', 'Avez-vous bien inscrit votre courriel? : '. $email);
                }
            }


        }

        return $this->render('user/request_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // TODO: Vérifier pour ne pas permettre de changer de courriel au RESET de password
    /**
     * @Route("/profile/reset/{token}", name="user_reset_password")
     */
    public function resetPasswordAction(Request $request, $token = null)
    {
        $date = new \DateTime('now');

        $user = '';
        if ($token) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->findUserFromToken($token);
        }

        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('The token is either bad or expired.');
        }

        $user->setEmail('');

        $form = $this->createForm(UserResetPasswordForm::class, $user, array(
            'action' => $this->generateUrl('user_reset_password', array('token' => $token)),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Vous avez modifier votre mot de passe avec succes: '.$user->getEmail());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }


        return $this->render('user/reset_password.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
