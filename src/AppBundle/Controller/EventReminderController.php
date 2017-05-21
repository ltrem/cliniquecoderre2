<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EventReminder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Eventreminder controller.
 *
 * @Route("eventreminder")
 */
class EventReminderController extends Controller
{
    /**
     * Lists all eventReminder entities.
     *
     * @Route("/", name="eventreminder_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $eventReminders = $em->getRepository('AppBundle:EventReminder')->findAll();

        return $this->render('eventreminder/index.html.twig', array(
            'eventReminders' => $eventReminders,
        ));
    }

    /**
     * Creates a new eventReminder entity.
     *
     * @Route("/new", name="eventreminder_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $eventReminder = new Eventreminder();
        $form = $this->createForm('AppBundle\Form\EventReminderType', $eventReminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventReminder);
            $em->flush($eventReminder);

            return $this->redirectToRoute('eventreminder_show', array('id' => $eventReminder->getId()));
        }

        return $this->render('eventreminder/new.html.twig', array(
            'eventReminder' => $eventReminder,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a eventReminder entity.
     *
     * @Route("/{id}", name="eventreminder_show")
     * @Method("GET")
     */
    public function showAction(EventReminder $eventReminder)
    {
        $deleteForm = $this->createDeleteForm($eventReminder);

        return $this->render('eventreminder/show.html.twig', array(
            'eventReminder' => $eventReminder,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing eventReminder entity.
     *
     * @Route("/{id}/edit", name="eventreminder_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EventReminder $eventReminder)
    {
        $deleteForm = $this->createDeleteForm($eventReminder);
        $editForm = $this->createForm('AppBundle\Form\EventReminderType', $eventReminder);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('eventreminder_edit', array('id' => $eventReminder->getId()));
        }

        return $this->render('eventreminder/edit.html.twig', array(
            'eventReminder' => $eventReminder,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a eventReminder entity.
     *
     * @Route("/{id}", name="eventreminder_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EventReminder $eventReminder)
    {
        $form = $this->createDeleteForm($eventReminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($eventReminder);
            $em->flush($eventReminder);
        }

        return $this->redirectToRoute('eventreminder_index');
    }

    /**
     * Creates a form to delete a eventReminder entity.
     *
     * @param EventReminder $eventReminder The eventReminder entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EventReminder $eventReminder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('eventreminder_delete', array('id' => $eventReminder->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
