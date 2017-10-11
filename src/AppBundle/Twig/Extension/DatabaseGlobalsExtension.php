<?php

namespace AppBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;

class DatabaseGlobalsExtension extends \Twig_Extension
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getGlobals()
    {
        return array (
            "eventsss" => $this->em->getRepository('AppBundle:Event')->find(2),
        );
    }

    public function getName()
    {
        return "AppBundle:DatabaseGlobalsExtension";
    }

}