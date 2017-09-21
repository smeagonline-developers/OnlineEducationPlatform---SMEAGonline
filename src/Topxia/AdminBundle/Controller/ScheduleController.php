<?php 
namespace Topxia\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends BaseController 
{

	public function indexAction() {

		return new Response("Schedule page is working!");

	}

}