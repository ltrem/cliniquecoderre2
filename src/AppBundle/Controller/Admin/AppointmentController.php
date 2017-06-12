<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Client;
use AppBundle\Entity\Employe;
use AppBundle\Entity\Event;
use AppBundle\Entity\Receipt;
use AppBundle\Form\AdminAppointmentType;
use AppBundle\Form\EventType;
use AppBundle\Form\SearchEventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


// TODO: Add new appointment availability validation mechanism

/**
 * Admin Appointment Controller
 *
 * @Route("admin/event")
 */
class AppointmentController extends Controller
{

    /**
     * Lists all event entities.
     *
     * @Route("/", name="admin_event")
     * @Method({"GET"})
     */
    public function eventAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Prepare Search Filter
        {
            $search_event_form = $this->createForm(SearchEventType::class);
            $search_event_form->handleRequest($request);
            $filter = $search_event_form->getData();
        }

        // Filter query
        {
            $queryBuilder = $em->getRepository('AppBundle:Event')->createQueryBuilder('e')
                ->innerJoin('e.client', 'client')
                ->leftJoin('client.contacts', 'contacts');
            if ($filter['search_client']) {
                $queryBuilder
                    ->andWhere('client.id = :filter_name')
                    ->setParameter('filter_name', $filter['search_client']);
            }
            if ($filter['search_phone']) {
                $queryBuilder
                    ->andWhere('contacts.phoneCell LIKE :filter_phoneCell')
                    ->setParameter('filter_phoneCell', '%' .$filter['search_phone'] . '%');
            }
            if ($filter['search_emergency']) {
                $queryBuilder
                    ->andWhere('e.emergency = 1');
            }
            $query = $queryBuilder->getQuery();
        }

        // Paginator
        {
            $paginator = $this->get('knp_paginator');
            $result = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 15),
                array(
                    'wrap-queries' => true
                )

            );
        }

        return $this->render('admin/event/index.html.twig', array(
            'events' => $result,
            'search_event_form' => $search_event_form->createView(),
        ));
    }

    /**
     * Create new event.
     *
     * @Route("/new", name="admin_event_new")
     * @Method({"GET", "POST"})
     */
    public function eventNewAction(Request $request)
    {

        $client = '';
        $employe = '';

        $event = new Event();

        if ($request->query->get('client'))
        {
            $client = $request->query->get('client');
            $client = $this->getDoctrine()->getManager()->getRepository('AppBundle:Client')->find($client);
            $event->setClient($client);
        }
        if ($request->query->get('employe'))
        {
            $employe = $request->query->get('employe');
            $employe = $this->getDoctrine()->getManager()->getRepository('AppBundle:Employe')->find($employe);
            $event->setEmploye($employe);
        }

        $form = $this->createForm(AdminAppointmentType::class, $event);
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


        // Verify if it's an Ajax call
        if($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($event);
                $em->flush();

                $flash_message = $this->renderView('flash_message.html.twig', array(
                    'flash_message' => $this->get('translator')->trans('admin.event.edit.success')
                ));


                //return $this->redirectToRoute('admin_event');
                return new Response(json_encode(array('status'=> $flash_message)));
            }

            return $this->render('admin/event/event_ajax.html.twig', array(
                'client'    => $client,
                'employe'   => $employe,
                'form' => $form->createView(),
            ));
        }

        return $this->render('admin/event/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing event entity.
     *
     * @Route("/{id}", name="admin_event_edit")
     * @Method({"GET", "POST"})
     */
    public function eventEditAction(Request $request, Event $event)
    {
        $editForm = $this->createForm(AdminAppointmentType::class, $event);
        $editForm->handleRequest($request);

        // Add 1 hour to StartTime (timezone problem?)
        $startTime = $editForm->get('startTime')->getData();

        if ($startTime) {
            // Round to nearest lowest hour
            $startTime = new \DateTime($startTime->format("Y-m-d H:00:00"));
            $event->setStartTime($startTime);

            // Add 1 hour to endTime
            $endTime = new \DateTime($startTime->format("Y-m-d H:00:00"));
            $event->setEndTime($endTime->modify("+1 hour"));
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('admin.event.edit.success')
            );

            return $this->redirectToRoute('admin_event');
        }

        return $this->render('admin/event/edit.html.twig', array(
            'event' => $event,
            'form' => $editForm->createView(),
        ));
    }

}