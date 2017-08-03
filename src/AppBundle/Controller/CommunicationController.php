<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\Communication;
use AppBundle\Entity\Employe;
use AppBundle\Entity\User;
use AppBundle\Form\CommunicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Communication controller.
 *
 * @Route("communication")
 * @Security("has_role('ROLE_ADMIN')")
 */
class CommunicationController extends Controller
{

    /**
     * Send a communication.
     *
     * @Route("/new", name="communication_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $client = '';
        $employee = '';

        if ($request->query->get('client'))
        {
            $client = $request->query->get('client');
            $client = $this->getDoctrine()->getManager()->getRepository('AppBundle:Client')->find($client);
            $email = $client->getUser()->getUsername();
        }
        if ($request->query->get('employe'))
        {
            $employee = $request->query->get('employe');
            $employee = $this->getDoctrine()->getManager()->getRepository('AppBundle:Employe')->find($employee);
            $email = $employee->getUser()->getUsername();
        }

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this sections.');
        }

        $communication = new Communication();
        $communication->setEmail($email);

        $form = $this->createForm(CommunicationType::class, $communication);
        $form->handleRequest($request);

        // Verify if it's an Ajax call
        if($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {

                // Render and generate Communication Template
                $communication->setDateSent(new \DateTime('now'));
                $communication_template = $this->renderView('communication/communication_template.html.twig', array(
                    'communication' =>  $communication
                ));
                $communication->setContent($communication_template);

                // Add the communication to the Client
                if ($client instanceof Client) {
                    $client->addCommunication($communication);
                }

                // Add the communication to the Employe
                if ($employee instanceof Employe) {
                    $employee->addCommunication($communication);
                }

                // Send Communication
                $mailerService = $this->get('app.communication_mailer');
                $mailerService->send($communication);

                // Save the communication
                $em = $this->getDoctrine()->getManager();
                $em->persist($communication);
                $em->flush();

                $flash_message = $this->renderView('flash_message.html.twig', array(
                    'flash_message' => $this->get('translator')->trans('communication.message.create.success')
                ));

                return new Response(json_encode(array('status'=> $flash_message)));
            }

            return $this->render('communication/communication_ajax.html.twig', array(
                'communication' => $communication,
                'client'    => $client,
                'employe'   => $employee,
                'form' => $form->createView(),
            ));
        }

        return $this->render('event/new.html.twig', array(
            'communication' => $communication,
            'form' => $form->createView(),
        ));
    }
}
