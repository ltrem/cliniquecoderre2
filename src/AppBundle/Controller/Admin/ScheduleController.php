<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Employe;
use AppBundle\Entity\Schedule;
use AppBundle\Entity\ScheduleBlock;
use AppBundle\Entity\User;
use AppBundle\Form\ScheduleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin controller.
 *
 * @Route("admin/schedule")
 */
class ScheduleController extends Controller
{

    /**
     * New schedule.
     *
     * @Route("/new/{employe}", options={"expose"=true}, name="admin_schedule_new")
     * @Method("POST")
     */
    public function scheduleNewAction(Request $request, Employe $employe)
    {
        $schedule = new Schedule();

        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        // Verify if it's an Ajax call
        if($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                // Get Block startTime and endTime
                $block_startTime = $request->get('start');
                $block_endTime = $request->get('end');

                // Set Date Range of schedule by setting dateFrom and dateTo
                $schedule->setDateFrom(new \DateTime(min($block_startTime)));
                $schedule->setDateTo(new \DateTime(max($block_endTime)));
                $schedule->setEmploye($employe);

                // Create ScheduleBlock
                foreach ($block_startTime as $key => $date) {
                    $scheduleBlock = new ScheduleBlock();
                    $scheduleBlock->setDateFrom(new \DateTime($block_startTime[$key]));
                    $scheduleBlock->setDateTo(new \DateTime($block_endTime[$key]));
                    $scheduleBlock->setSchedule($schedule);

                    $em->persist($scheduleBlock);
                }

                // Persist Schedule to database
                $em->persist($schedule);
                $em->flush();

                return new Response(json_encode(array('status'=>'success')));
            }

            return $this->render('admin/schedule/schedule_ajax.html.twig', array(
                'schedule' => $schedule,
                'employe' => $employe,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * Displays a form to edit an existing schedule entity.
     *
     * @Route("/{id}", options={"expose"=true}, name="admin_schedule_edit")
     * @Method({"GET", "POST"})
     */
    public function scheduleEditAction(Request $request, Schedule $schedule)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this sections.');
        }

        $employe = $schedule->getEmploye();

        $editForm = $this->createForm(ScheduleType::class, $schedule);
        $editForm->handleRequest($request);

        // Verify if it's an Ajax call
        if($request->isXmlHttpRequest()) {

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();

                // Save ScheduleBlock
                {
                    // Delete all block from the Schedule so we can recreate them
                    {
                        foreach ($schedule->getBlocks() as $key => $block) {
                            $em->remove($block);
                        }
                        $this->getDoctrine()->getManager()->flush();
                    }

                    // Get Block startTime and endTime
                    $block_startTime = $request->get('start');
                    $block_endTime = $request->get('end');

                    // Set Date Range of schedule by setting dateFrom and dateTo
                    $schedule->setDateFrom(new \DateTime(min($block_startTime)));
                    $schedule->setDateTo(new \DateTime(max($block_endTime)));
                    $schedule->setEmploye($employe);

                    // Create ScheduleBlock
                    foreach ($block_startTime as $key => $date) {
                        $scheduleBlock = new ScheduleBlock();
                        $scheduleBlock->setDateFrom(new \DateTime($block_startTime[$key]));
                        $scheduleBlock->setDateTo(new \DateTime($block_endTime[$key]));
                        $scheduleBlock->setSchedule($schedule);

                        $em->persist($scheduleBlock);
                    }

                    // Insert in database;
                    $this->getDoctrine()->getManager()->flush();
                }

                return new Response(json_encode(array('status'=>'success')));
                //return $this->redirect($request->headers->get('referer'));
            }

            return $this->render('admin/schedule/schedule_ajax.html.twig', array(
                'schedule' => $schedule,
                'form' => $editForm->createView(),
                'employe' => $employe
            ));
        }

        $em = $this->getDoctrine()->getManager();

        // Filter query
        {
            $queryBuilder = $em->getRepository('AppBundle:Schedule')->createQueryBuilder('s');
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

        return $this->render('admin/user/profile.html.twig', array(
            'user' => $user,
            'schedule_form' => $editForm->createView(),
            'schedules' => $result,
            'employe' => $employe
        ));
    }


    // TODO: Get this to work from the AJAX call
    /**
     * Deletes a schedule block entity.
     *
     * @Route("/block/{id}", options={"expose"=true}, name="admin_schedule_block_delete")
     * @Method("DELETE")
     */
    public function deleteScheduleBlockAction(Request $request, ScheduleBlock $scheduleBlock)
    {

        $form = $this->createScheduleBlockDeleteForm($scheduleBlock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($scheduleBlock);
            $em->flush($scheduleBlock);
            return new Response(json_encode(array('status'=>'success')));
        }

        return new Response(json_encode(array('status'=>'nothing')));
    }

    /**
     * Creates a form to delete a scheduleBlock entity.
     *
     * @param ScheduleBlock $scheduleBlock The schedule block
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createScheduleBlockDeleteForm(ScheduleBlock $scheduleBlock)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_schedule_block_delete', array('id' => $scheduleBlock->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
