<!-- http://www.eeo.cn/partner/api/course.api.php?action=getStudentList -->
<?php
namespace Eeo\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eeo\ApiBundle\Classes\EeoOAuthClient as Eeo;

class StudentController extends BaseController
{  
	/*
	* 	Students get course / attend
	*/
	public function getCourseStudentAction() {

	}

	/*
	* 	Under Add course student / sit (single)
	*/
	public function addCourseStudentAction() {

	}

	/*
	*	Remove students under the program / sit (single)
	*/
	public function delCourseStudentActrion() {

	}

	/*
	*	Add Student / attend (multiple) under the program
	*/
	public function addCourseStudentMultipleAction(){

	}

	/*
	*	Remove students under course / attend (more)
	*/
	public function delCourseStudentMultipleAction(){

	}

	/*
	*	Add students after class section (more)
	*/
	public function addClassStudentMultipleAction(){

	}

	/*
	*	Remove students after class section (more)
	*/
	public function delClassStudentMultipleAction(){

	}
	
}