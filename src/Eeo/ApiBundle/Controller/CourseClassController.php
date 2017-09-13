<?php
namespace Eeo\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eeo\ApiBundle\Controller\BaseController as EeoBaseController;
use Eeo\ApiBundle\Classes\EeoOAuthClient as EeoAuthentication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CourseClassController extends BaseController
{  

	/**
	 * 
	 * @author Richtermark M. Baay
	 *
	 */

	CONST NAME = "course_class";

	public $eeo;

    public $base;
    
    public function __construct() {

        $this->base = new EeoBaseController();
    }

    public function getCourseClassAction($courseId) {

    	$courseClass = $this->base->getList(SELF::NAME, array('param' => ['courseId' => $courseId]));
    	$courseClass = ($courseClass->error_info->errno == 106) ? null : $courseClass->data ;

        return $this->render('EeoWebBundle:classIn:course-class.html.twig', ['courseClass' => $courseClass,'courseId' => $courseId]);
	}

	public function editCourseClassAction($courseId, $classId) {

		$this->eeo 	= new EeoAuthentication();
		$request 	= Request::createFromGlobals();

		$data  		= $request->query->all();

		$param 				= $this->eeo->getParameters();
		$param["courseId"]  = $courseId;
        $param["classId"]   = $classId;
        $param["className"] = $data['n'];

        $createRequest = $this->eeo->buildRequest($this->base->apiFileAddress . "?action=editCourseClass", $param);
        $request = $this->eeo->postRequest();

        var_dump($request);exit;
        return $this->getCourseClassAction($courseId);
	}

	public function addCourseClassAction() {


	}

	public function addCourseClassMultipleActrion() {}
	 
	/*
	*	Obtain information section lesson
	*/
	public function getClassInfo($courseId, $classId){

		$param = $this->eeo->getParameters();

        $param["courseId"] = $courseId;
        $param["classId"] = $classId;

      			   $this->eeo->buildRequest($this->apiFileAddress . "?action=getClassInfo", $param);
        $request = $this->eeo->postRequest();

        return $request->data;
	}

	/*
	*	Acquisition time in class to class members 
	*/
	public function getClassMemberTimeAction(){}

	/*
	*	Replace the course teacher
	*/
	public function modifyCourseTeacherAction(){}

	/*
	*	Add more classes next lesson the students
	*/
	public function addCourseClassStudentAction(){}

	/*
	*	Get information at the time a member of the class festival attendance
	*/
	public function getClassMemberTimeDetailsAction(){}	
	
}