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

    /**
     * Redirect to connected user assigned employe
     *
     * @Route("/profile", name="admin_profile")
     */
    public function profileAction(Request $request)
    {

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this sections.');
        }

        // redirect to the employe page of the logged used
        return $this->redirectToRoute('admin_employe_edit', array('id' => $user->getEmploye()->getId()));

    }

}
