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

        return $this->render('TopxiaAdminBundle:Eeo:course-lesson-list.html.twig', ['courseClass' => $courseClass->data,'courseId' => $courseId]);
	}

	public function editCourseClassAction($courseId, $classId) {

		$request = Request::createFromGlobals();
		$data  = $request->query->all();

		$this->eeo = new EeoAuthentication();

		$param 				= $this->eeo->getParameters();
		$param["courseId"]  = $courseId;
        $param["classId"]   = $classId;
        $param["className"] = $data['n'];

        $createRequest = $this->eeo->buildRequest($this->base->apiFileAddress . "?action=editCourseClass", $param);
        $request = $this->eeo->postRequest();

        return $this->getCourseClassAction($courseId);
	}

	public function addCourseClassAction() {}

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