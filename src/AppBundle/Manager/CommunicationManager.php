<?php
namespace AppBundle\Manager;

use AppBundle\Entity\Communication;
use AppBundle\Service\CommunicationMailerService;
use Doctrine\ORM\EntityManager;

class CommunicationManager
{
    private $em;
    private $communicationMailer;

    public function __construct(EntityManager $entityManager, CommunicationMailerService $communicationMailerService)
    {
        $this->em = $entityManager;
        $this->communicationMailer = $communicationMailerService;
    }

    public function sendCommunication(Communication $communication)
    {

        if ($clients = $communication->getClients()) {
            foreach ($clients as $id) {
                $client = $this->em->getRepository('AppBundle:Client')->find($id);
                $client->addCommunication($communication);


                $this->em->getConnection()->beginTransaction();
                $this->em->flush();

                // Send communication
                $communication->setEmail($client->getEmail());
                $communication->setPhone($client->getPhoneCell());
                $this->communicationMailer->send($communication);

                $this->em->getConnection()->commit();

            }
        }

    }
}