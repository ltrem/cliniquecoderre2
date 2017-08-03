<?php
namespace AppBundle\Controller\EasyAdmin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminAppointmentType;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends BaseAdminController
{
    /**
     * @Route("/", name="easyadmin")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this sections.');
        }

        $this->initialize($request);

        // Get form
        $form_event = $this->createForm(AdminAppointmentType::class);

        if (null === $request->query->get('entity')) {

            return $this->render('easy_admin/dashboard.html.twig', array(
                'employe' => $user->getEmploye(),
                'form_event' => $form_event->createView(),
            ));
        }

        return parent::indexAction($request);
    }

}