<?php

namespace Eeo\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('EeoWebBundle:Default:index.html.twig', array('name' => $name));
    }
}
