<?php
namespace AppBundle\Controller\EasyAdmin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminAppointmentType;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JMose\CommandSchedulerBundle\Entity\ScheduledCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

        // Get all Appointment availability notification ScheduledCommand
        {
            $commands = $this->get('doctrine')->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->findByCommand('app:appointment_availability_notification');
            $arrayNotification = array();

            foreach($commands as $command) {
                $arguments = $command->getArguments(true);
                $params = [];
                foreach($arguments as $key => $value) {
                    $params[substr($key, 2)] = $value;
                }

                $event = $this->get('doctrine')->getRepository('AppBundle:Event')->find($params['event_id']);
                if ($event->getEndTime() >= new \DateTime('now')) {
                    $arrayNotification[] = array(
                        'command_id' => $params['schedule_command_id'],
                        'event' => $event,
                        'last' => $this->get('doctrine')->getRepository('AppBundle:AppointmentAvailabilityNotification')->findLastAppointmentNotificationSent($params['event_id']),
                        'sent' => $this->get('doctrine')->getRepository('AppBundle:AppointmentAvailabilityNotification')->findAppointmentNotificationSent($params['event_id']),
                    );
                }
            }
        }

        // Get appointment form
        $form_event = $this->createForm(AdminAppointmentType::class);

        if (null === $request->query->get('entity')) {

            return $this->render('easy_admin/dashboard.html.twig', array(
                'employe' => $user->getEmploye(),
                'form_event' => $form_event->createView(),
                'form_event_edit' => $form_event->createView(),
                'availabilityNotifications' => $arrayNotification,
            ));
        }

        return parent::indexAction($request);
    }

    /**
     * Cancel the appointment availability notification command.
     *
     * @Route("/cancel-appointment-notification/{id}", name="admin_appointment_notification_answer")
     * @Method({"GET", "POST"})
     */
    public function cancelAppointmentNotificationAction(Request $request, int $id)
    {
        $scheduledCommand = $this->get('doctrine')->getRepository('JMoseCommandSchedulerBundle:ScheduledCommand')->find($id);

        if ($scheduledCommand !== null) {
            $this->get('doctrine')->getManager()->remove($scheduledCommand);
            $this->get('doctrine')->getManager()->flush($scheduledCommand);

            $this->addFlash('success', 'Notification automatique de disponibilitées supprimé!');
        } else {
            $this->addFlash('error', 'L\'annulation de notification automatique de disponibilitée n\'a pas fonctionnée!');
        }

        return $this->redirectToRoute('easyadmin');
    }
}