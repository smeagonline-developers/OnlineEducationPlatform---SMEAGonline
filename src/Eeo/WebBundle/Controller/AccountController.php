<?php
namespace Eeo\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
   public function indexAction($name)
    {
        return $this->render('EeoWebBundle:Custom:account-assignment.html.twig', array('name' => $name));
    }
}
