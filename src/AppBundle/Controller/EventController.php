<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppointmentAvailabilityNotification;
use AppBundle\Entity\Event;
use AppBundle\Entity\EventCancellation;
use AppBundle\Event\AppointmentAvailabilityNotificationEvent;
use AppBundle\Event\AppointmentCancelledEvent;
use AppBundle\Form\AppointmentAvailabilityNotificationAnswerType;
use AppBundle\Form\EventCancellationType;
use AppBundle\Form\EventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Event controller.
 *
 * @Route("rendez-vous")
 * @Security("has_role('ROLE_CLIENT')")
 */
class EventController extends Controller
{
    /**
     * Lists all event entities.
     *
     * @Route("/", name="event_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('AppBundle:Event')->findAll();
        $dql = "SELECT e FROM AppBundle:Event e";
        $query = $em->createQuery($dql);

        $queryBuilder = $em->getRepository('AppBundle:Event')->createQueryBuilder('e');

        if ($request->query->getAlnum('filter')) {
            $queryBuilder
                ->where('e.name LIKE :name')
                ->setParameter('name', '%' . $request->query->getAlnum('filter') . '%');
        }

        $query = $queryBuilder->getQuery();

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15)
        );

        return $this->render('event/index.html.twig', array(
            'events' => $result,
        ));
    }

    /**
     * Creates a new event entity.
     *
     * @Route("/nouveau", name="event_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $event = new Event();

        // Assign user to event
        $user = $this->getUser();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        // Add 1 hour to StartTime (timezone problem?)
        $startTime = $form->get('startTime')->getData();

        if ($startTime) {
            // Round to nearest lowest hour
            $startTime = new \DateTime($startTime->format("Y-m-d H:00:00"));
            $event->setStartTime($startTime);

            // Add 1 hour to endTime
            $endTime = new \DateTime($startTime->format("Y-m-d H:00:00"));
            $event->setEndTime($endTime->modify("+1 hour"));
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $event->setClient($user->getClient());

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('event.message.create.success')
            );

            return $this->redirectToRoute('user_profile');
        }

        // Get all employes
        $em = $this->getDoctrine()->getManager();
        $employes = $em->getRepository('AppBundle:Employe')->findAll();

        return $this->render('event/new.html.twig', array(
            'employes' => $employes,
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     */
    public function showAction(Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);

        return $this->render('event/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing event entity.
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('AppBundle\Form\EventType', $event);
        $editForm->handleRequest($request);


        // Add 1 hour to StartTime (timezone problem?)
        $startTime = $editForm->get('startTime')->getData();

        // Round to nearest lowest hour
        $startTime = new \DateTime($startTime->format("Y-m-d H:00:00"));
        $event->setStartTime($startTime);

        // Add 1 hour to endTime
        $endTime = new \DateTime($startTime->format("Y-m-d H:00:00"));
        $event->setEndTime($endTime->modify("+1 hour"));

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'notice',
                'Event modified'
            );

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     *
     * @Route("/{id}", name="event_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Event $event)
    {
        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush($event);
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Event $event The event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Cancel the Event.
     *
     * @Route("/{id}/cancel", name="event_cancel")
     * @Method({"GET", "POST"})
     */
    public function cancelAction(Request $request, Event $event)
    {

        if ($event->getCancellation()) {
            return $this->redirectToRoute('user_profile');
        }

        $eventCancellation = new EventCancellation();

        $form = $this->createForm(EventCancellationType::class, $eventCancellation);
        $form->handleRequest($request);

        // Verify if it's an Ajax call
        if($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {

                $event->setCancellation($eventCancellation);
                $em = $this->getDoctrine()->getManager();

                $em->persist($eventCancellation);
                $em->getConnection()->beginTransaction();
                $em->flush();

                // Send a notice to every client that are waiting for an appointment
                $this->get('event_dispatcher')->dispatch(AppointmentCancelledEvent::NAME, new AppointmentCancelledEvent($event));

                $em->getConnection()->commit();

                return new Response(json_encode(array('status'=>'success')));
            }

            return $this->render('event/cancel_ajax.html.twig', array(
                'event' => $event,
                'form' => $form->createView(),
            ));
        }

        return $this->render('event/new.html.twig', array(
            'eventCancellation' => $eventCancellation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Answer from Event Availability Notification
     *
     * @Route("/proposition-de-rendez-vous/{token}/{answer}", name="appointment_notification_answer", defaults={"answer" = null})
     * @Method({"GET", "POST"})
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function availabilityNotificationAnswerAction(Request $request, string $token, $answer)
    {
        $em = $this->getDoctrine()->getManager();

        $availabilityNotification = '';
        if ($token) {
            $availabilityNotification = $em->getRepository('AppBundle:AppointmentAvailabilityNotification')->findAppointmentAvailabilityFromToken($token);
        }

        if (!is_object($availabilityNotification) || !$availabilityNotification instanceof AppointmentAvailabilityNotification) {
            // throw new AccessDeniedException('The token is either bad or expired.');
            return $this->render('event/appointment_notification_expired.html.twig');
        }

        $form = $this->createForm(AppointmentAvailabilityNotificationAnswerType::class, $availabilityNotification, array(
            'action' => $this->generateUrl('appointment_notification_answer', array('token' => $token)),
        ));
        $form->handleRequest($request);

        // Verify if it's an Ajax call
        if($request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $em->getConnection()->beginTransaction();
                $em->flush();

                // Send a notice to every client that are waiting for an appointment
                $this->get('event_dispatcher')->dispatch(
                    AppointmentAvailabilityNotificationEvent::NAME,
                    new AppointmentAvailabilityNotificationEvent($availabilityNotification)
                );

                $em->getConnection()->commit();

                return new Response(json_encode(array('status'=>'success')));
            }

            return $this->render('event/ajax_appointment_notification_answer.html.twig', array(
                'availabilityNotification' => $availabilityNotification,
                'form' => $form->createView(),
            ));

        } else {
            if ($form->isSubmitted() && $form->isValid()) {

                $em->getConnection()->beginTransaction();
                $em->flush();

                // Send a notice to every client that are waiting for an appointment
                $this->get('event_dispatcher')->dispatch(
                    AppointmentAvailabilityNotificationEvent::NAME,
                    new AppointmentAvailabilityNotificationEvent($availabilityNotification)
                );

                $em->getConnection()->commit();

                return $this->redirectToRoute('user_profile');
            }
        }

        return $this->render('event/appointment_notification_answer.html.twig', [
            'form' => $form->createView(),
            'availabilityNotification' => $availabilityNotification,
        ]);

    }
}
