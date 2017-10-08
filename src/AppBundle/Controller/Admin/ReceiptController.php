<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Event;
use AppBundle\Entity\Receipt;
use AppBundle\Form\ReceiptType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Receipt controller.
 *
 * @Route("admin/receipt")
 */
class ReceiptController extends Controller
{

    /**
     * Create a receipt for an event
     *
     * @Route("/new/{id}", name="admin_receipt_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Event $event)
    {

        $receipt = new Receipt();

        $form = $this->createForm(ReceiptType::class, $receipt);
        $form->handleRequest($request);

        // Verify if it's an Ajax call
        if($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {

                // TODO: Changer la logique ci bas... présentement, si la génération de PDF plante, le "reçus d'assurance ne sera pas créée.

                // Generate Receipt in PDF format in a temporary files (KnpSnappy will delete it)
                $tmp_file = base64_encode(random_bytes(10));
                $filename = '/tmp/'. $tmp_file . '.pdf';
                $this->get('knp_snappy.pdf')->generate('http://www.google.fr', $filename, array(
                    'orientation' => 'landscape',
                    'enable-javascript' => true,
                    'javascript-delay' => 1000,
                    'no-stop-slow-scripts' => true,
                    'no-background' => false,
                    'lowquality' => false,
                    'page-height' => 600,
                    'page-width'  => 1000,
                    'encoding' => 'utf-8',
                    'images' => true,
                    'cookie' => array(),
                    'dpi' => 300,
                    'enable-external-links' => true,
                    'enable-internal-links' => true
                ));

                // Create new uploaded file and assign it to receipt
                $newFile = new UploadedFile($filename, $filename, null, filesize($filename), false, true);
                $receipt->setImageFile($newFile);

                // Add receipt to Event
                $event->addReceipt($receipt);
                $em = $this->getDoctrine()->getManager();

                $em->persist($receipt);
                $em->getConnection()->beginTransaction();
                $em->flush();

                // TODO: Maybe send an email with the invoice by email by triggering an event like bellow
                    //$this->get('event_dispatcher')->dispatch(AppointmentCancelledEvent::NAME, new AppointmentCancelledEvent($event));

                $em->getConnection()->commit();

                return new Response(json_encode(array('status'=>'success')));
            }

            return $this->render('event/receipt/receipt_ajax.html.html.twig', array(
                'event' => $event,
                'form' => $form->createView(),
            ));
        }
    }

}
