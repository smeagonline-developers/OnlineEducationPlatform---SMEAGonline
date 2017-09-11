<?php

namespace FOS\RestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FOSRestBundle:Default:index.html.twig', array('name' => $name));
    }
}
