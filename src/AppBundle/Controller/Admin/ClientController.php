<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Client;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Coordinate;
use AppBundle\Entity\User;
use AppBundle\Event\ClientCreatedEvent;
use AppBundle\Form\ClientType;
use AppBundle\Form\SearchClientType;
use AppBundle\Form\UserProfileForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller.
 *
 * @Route("admin/client")
 */
class ClientController extends Controller
{
    /**
     * Lists all event entities.
     *
     * @Route("/", name="admin_client")
     * @Method("GET")
     */
    public function clientAction(Request $request)
    {

        // PDF
        // GENERATE INVOICE PDF
        // PDF
        /*
        return new Response(
            $this->get('knp_snappy.pdf')->getOutput('http://www.dessinsdrummond.com'),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="file.pdf"'
            )
        );
        */

        $em = $this->getDoctrine()->getManager();

        // Prepare Search Filter
        {
            $search_client_form = $this->createForm(SearchClientType::class);
            $search_client_form->handleRequest($request);
            $filter = $search_client_form->getData();
        }

        // Filter query
        {
            $queryBuilder = $em->getRepository('AppBundle:Client')->createQueryBuilder('c')
                ->add('select', 'c, contacts')
                ->join('c.contacts', 'contacts');
            if ($filter['search_name']) {
                $queryBuilder
                    ->where('c.firstname LIKE :filter_name OR c.lastname LIKE :filter_name')
                    ->setParameter('filter_name', '%' . $filter['search_name'] . '%');
            }
            if ($filter['search_phone']) {
                $queryBuilder
                    ->where('contacts.phoneCell LIKE :filter_phoneCell')
                    ->setParameter('filter_phoneCell', '%' .$filter['search_phone'] . '%');
            }
            $query = $queryBuilder->getQuery();
        }

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15),
            array(
                'wrap-queries' => true
            )

        );

        return $this->render('admin/client/index.html.twig', array(
            'clients' => $result,
            'search_client_form' => $search_client_form->createView()
        ));
    }

    /**
     * Create new client.
     *
     * @Route("/new", name="admin_client_new")
     */
    public function clientNewAction(Request $request)
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

        $form = $this->createForm(UserProfileForm::class, $user);
        $form->remove('plainPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Assign ROLE_CLIENT
            $roles = array('ROLE_CLIENT');
            $user->setRoles($roles);

            // Assign User to Client
            $client = $user->getClient();
            $client->setUser($user);

            // Generate Token
            $token = rtrim(strtr(base64_encode(openssl_random_pseudo_bytes(32)), '+/', '-_'), '=');

            // Get User and assign password and token
            $user = $form->getData();
            $user->setPlainPassword(random_bytes(10));
            $user->setResetPasswordToken($token);
            $user->setResetPasswordDate(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->getConnection()->beginTransaction();
            $em->flush();

            // Send a notice to every client that are waiting for an appointment
            $this->get('event_dispatcher')->dispatch(ClientCreatedEvent::NAME, new ClientCreatedEvent($user));

            $this->addFlash('notice', 'Welcome '.$user->getEmail());

            $em->getConnection()->commit();

            return $this->redirectToRoute('admin_client_edit', array('id' => $client->getId()));

        }

        return $this->render('admin/client/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Displays a form to edit an existing client entity.
     *
     * @Route("/{id}", name="admin_client_edit")
     * @Method({"GET", "POST"})
     */
    public function clientEditAction(Request $request, Client $client)
    {
        $em = $this->getDoctrine()->getManager();

        if ($user = $client->getUser()) {
            // Event Availability Notification
            $appointmentAvailabilityNotification = $em->getRepository('AppBundle:AppointmentAvailabilityNotification')->findAppointmentAvailabilityFromClient($user->getClient());
            $notifToken = '';
            $eventOffered = '';
            if (null !== $appointmentAvailabilityNotification) {
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
        }

        $editForm = $this->createForm(ClientType::class, $client);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_client_edit', array('id' => $client->getId()));
        }

        return $this->render('admin/client/edit.html.twig', array(
            'client' => $client,
            'form' => $editForm->createView(),
            'eventOffered' => $eventOffered,
            'notifToken' => $notifToken,
            'upcomingEvents' => $upcomingEvents,
            'cancelledUpcomingEvents' => $cancelledUpcomingEvents,
            'events7days' => $events7days,
            'pastEvents' => $pastEvents,
        ));
    }
}
