<?php

namespace Eeo\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eeo\ApiBundle\Classes\EeoOAuthClient as EeoAuthentication;

class BaseController extends Controller
{

    /**
     * 
     * @author Richtermark M. Baay
     *
     */
	private $eeo;

	private $course;

	private $teacher;

	private $students;

	private $courseClass;

	public $apiFileAddress;

	CONST NAME = "base";

    CONST COURSE_FILE = "/course.api.php";

    /*
    *   初始化的Eeo OAuth客户端类
    */
    public function __construct() {

    	# Access authentication
        $this->eeo 				= new EeoAuthentication();

        # Initialize request path
        $this->apiFileAddress 	= SELF::COURSE_FILE;
    }

    public function getApiFileAddress() {
        return $this->apiFileAddress;
    }



    /*
    * Get the list of : student, teacher, course, course
    * 
    * @param $request
    * @param array $actionBuilder
    * @param array $get
    * @return \Symfony\Component\HttpFoundation\Response
    *
    *       $this->getList() {}
    *       -------------------
    *           It require parameter for your request
    *           Parameters are : course, course_class, teacher, student
    *                           ->getList("student")
    *                           ->getList("teacher")
    *                           ->getList("course")
    *                           ->getList("course_class", array('param' => ['courseId' => '708663']))
    */
    public function getList($request, $get = null, $actionBuilder = "?action="){

    	$param = $this->eeo->getParameters();
    	
    	switch ( $request ) {
    		case 'course':
    				$sendRequest = $actionBuilder . "getCourseList" ;
    				$this->eeo->buildRequest($this->apiFileAddress . $sendRequest, $param);
    			break;
    		case 'course_class':
    				if ( is_null($get) ) {
    					return "Request Failed! Parameters needed!";
    				} else 
    					$param['courseId'] = $get['param']['courseId'];
	    				$sendRequest = $actionBuilder . "getCourseClass" ;
	    				
	    				$this->eeo->buildRequest($this->apiFileAddress . $sendRequest, $param);
    			break;
    		case 'teacher':
    				$sendRequest = $actionBuilder . "getTeacherList" ;
    				$this->eeo->buildRequest($this->apiFileAddress . $sendRequest, $param);
    			break;
    		case 'student':
    				$sendRequest = $actionBuilder . "getStudentList" ;

    				$this->eeo->buildRequest($this->apiFileAddress . $sendRequest, $param);
    			break;	
    		default:
    			break;
    	};

        return $this->eeo->postRequest();
    }

    /*
    * Get the list of courseClass
    * 
    * @param $courseId
    */
    public function getCourseClass($courseId) {

		$param = $this->eeo->getParameters();
        $param["courseId"] = $courseId;

      			   $this->eeo->buildRequest($this->apiFileAddress . "?action=getCourseClass", $param);
        $request = $this->eeo->postRequest();

        return $request->data;
	}

	public function getTeacherList() {

		$param = $this->eeo->getParameters();
        $createRequest = $this->eeo->buildRequest($this->apiFileAddress . "?action=getTeacherList", $param);

        $request = $this->eeo->postRequest();

        return array($request->data);
	}

    public function dataTransformer() {

    }
    
}
