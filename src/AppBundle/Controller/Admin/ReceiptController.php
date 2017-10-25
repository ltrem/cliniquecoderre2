<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Communication;
use AppBundle\Entity\Event;
use AppBundle\Entity\Receipt;
use AppBundle\Event\CommunicationSentEvent;
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
     * Finds and displays a receipt entity.
     *
     * @Route("/{id}", name="admin_receipt_show")
     * @Method("GET")
     */
    public function showAction(Receipt $receipt)
    {
        return $this->render('event/receipt/receipt_ajax.html.html.twig', array(
            'receipt' => $receipt,
        ));
    }

    /**
     * Finds and displays a receipt entity.
     *
     * @Route("/send/{id}", name="admin_receipt_send")
     * @Method("GET")
     */
    public function sendAction(Receipt $receipt)
    {

        $communication = new Communication();
        $communication->setDateSent(new \DateTime('now'));
        $communication->setType('email');
        $communication->addClient($receipt->getEvent()->getClient());
        $communication->setTitle('Reçu d\'assurance');
        $communication->setContent(
            $this->renderView(
                'event/receipt/receipt.html.twig',
                array(
                    'receipt'  => $receipt
                )
            )
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($communication);
        $em->getConnection()->beginTransaction();
        $em->flush();

        // Dispatch an event to send the communication
        $this->get('event_dispatcher')->dispatch(CommunicationSentEvent::NAME, new CommunicationSentEvent($communication));

        $em->getConnection()->commit();

        return $this->render('event/receipt/receipt_ajax.html.html.twig', array(
            'receipt' => $receipt,
        ));
    }

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

                // Add receipt to Event
                $event->addReceipt($receipt);
                $em = $this->getDoctrine()->getManager();

                // Generate Receipt in PDF format in a temporary files (KnpSnappy will delete it)
                $tmp_file = base64_encode(random_bytes(10));
                $filename = '/tmp/'. $tmp_file . '.pdf';

                $this->get('knp_snappy.pdf')->generateFromHtml(
                    $this->renderView(
                        'event/receipt/receipt.html.twig',
                        array(
                            'receipt'  => $receipt
                        )
                    ),
                    $filename
                );

                /*
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
                */
                // Create new uploaded file and assign it to receipt
                $newFile = new UploadedFile($filename, $filename, null, filesize($filename), false, true);
                $receipt->setImageFile($newFile);

                $em->persist($receipt);
                $em->getConnection()->beginTransaction();
                $em->flush();

                if ($request->attributes->has('saveAndSend')) {
                    $this->sendAction($receipt);
                }

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
