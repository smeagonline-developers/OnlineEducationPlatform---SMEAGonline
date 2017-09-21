<?php
namespace Eeo\ApiBundle\Controller;

use Eeo\ApiBundle\Classes\Config\Parameters;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eeo\ApiBundle\Controller\BaseController as EeoBaseController;
use Eeo\ApiBundle\Controller\TeacherController as Teacher;
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

    public $teachers;

    public $parameters;
    
    public function __construct() {

        $this->base 		= new EeoBaseController();
        $this->parameters 	= new Parameters();
    }

    public function getCourseClassAction($courseId = null) {

    	$courseClass = $this->base->getList(SELF::NAME, array('param' => ['courseId' => $courseId]));
    	$courseClass = ($courseClass->error_info->errno == 106) ? null : $courseClass;

    	$teacher 		= new Teacher();
    	$teacher 		 = $teacher->getTeacherList();
		
		$this->parameters->setCourseId($courseId);
 		$lessonTeachers = $this->getUniqueArray($courseClass->data, 'teacher_account') ;
  
        return $this->render('EeoWebBundle:classIn:course-class.html.twig', [ 'courseId' => $courseId, 'courseClass' => $courseClass->data,
																	'teachers'	=> $teacher, 'lessonTeachers' =>$lessonTeachers
																]);
	}

	// public function getLessonFestivalList($courseId = null) {

	// 	$param = $this->eeo->getParameters();

 //        if (!is_null($courseId)) {
 //            $param["courseId"] = $courseId;
 //        }
 //      			   $this->eeo->buildRequest($this->apiFileAddress . "?action=getCourseList", $param);
 //        $request = $this->eeo->postRequest();

 //        return $request;
	// }

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


	public function getUniqueArray( $data, $getKeyOfArrayIndex ) { 
		$counters = 0; 
		$setKey						= []; 
	    $setTemporaryArrayObject 	= []; 
	   
	    foreach( $data as $val ) { 
	        if ( !in_array( $val->$getKeyOfArrayIndex, $setKey ) ) { 
	            $setKey[ $counters ] = $val->$getKeyOfArrayIndex; 

	            $val->parent_courseId = $this->parameters->getCourseId();

	            $setTemporaryArrayObject[ $counters ] = $val; 
	        } 
	        $counters++; 
	    } 
	    $getArrayOfUniqResult = $setTemporaryArrayObject;

	    return $getArrayOfUniqResult; 
	} 
	
}