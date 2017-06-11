<?php

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Event;
use AppBundle\Entity\Schedule;
use AppBundle\Entity\User;
use AppBundle\Form\EventType;
use AppBundle\Form\ScheduleType;
use AppBundle\Form\SearchEventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller.
 *
 * @Route("admin/dashboard")
 */
class AdminController extends Controller
{
    /**
     * Lists all client entities.
     *
     * @Route("/", name="admin_dashboard")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('admin/homepage.html.twig');
    }

    // TODO: Fix admin profile
    /**
     * List the profile.
     *
     * @Route("/profile", name="admin_profile")
     */
    public function profileAction(Request $request)
    {
        $schedule = new Schedule();

        $schedule_form = $this->createForm(ScheduleType::class, $schedule, array(
            'action' => $this->generateUrl('admin_schedule_new'),
        ));

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this sections.');
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
            'schedule_form' => $schedule_form->createView(),
            'schedules' => $result,
        ));
    }

}
