<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {


        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('homepage.html.twig');

    }

    /**
     * @Route("/randomtest", name="randomtest")
     */
    public function randomTestAction(Request $request)
    {
        // Send email
        $message = \Swift_Message::newInstance()
            ->setFrom('info@sandbox1762d322d00b43cf95d08ef04f8151fc.mailgun.org')
            ->setSubject(
                'My subject'
            )
            ->setBody(
                'This is a test',
                'text/html'
            )
        ;

        $message->setTo('lautrem2@hotmail.com');
        $this->get('mailer')->send($message);

        return 'woops';
    }
}
