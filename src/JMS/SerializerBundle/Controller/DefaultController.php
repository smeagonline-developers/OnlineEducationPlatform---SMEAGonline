<?php

namespace JMS\SerializerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JMSSerializerBundle:Default:index.html.twig', array('name' => $name));
    }
}
