<?php
namespace Eeo\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eeo\ApiBundle\Controller\BaseController as EeoBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eeo\ApiBundle\Classes\EeoOAuthClient as EeoAuthentication;

class CourseController extends BaseController
{   

    /**
     * 
     * @author Richtermark M. Baay
     *
     */
    CONST NAME = "course";

    public $base;

    public $eeo;
    
    public function __construct() {

        $this->base = new EeoBaseController();

    }

    /*
    *    Get a list of courses
    */
    public function getCourseListAction(){

        $course = $this->base->getList(SELF::NAME);

        return $this->render('EeoWebBundle:classIn:course-list.html.twig',
                    array( SELF::NAME => $course->data
                ));
    }

    /*
	*	Obtain course information
	*/
	public function getCourseInfoAction(){}

    /*
    *    Creating a course
    */
    public function addCourseAction() {
        
        $formRequest = Request::createFromGlobals();
        $courseName = $formRequest->query->get('name');
      
        if(isset($courseName) && $courseName != null) {
            
            $param = $this->eeo->getParameters();
            $param["courseName"] = $courseName;

            $this->eeo->buildRequest("/course.api.php?action=addCourse", $param);
            $request = $this->eeo->postRequest();

            $this->get('session')->getFlashBag()->add('notice', array('type' => 'success', 'title' => 'Course Created!', 'message' => 'You have successfully created new course!'));

            $newCreatedCourseId = $request->data;
            $response = $request->error_info->errno;

            return $this->render('TopxiaAdminBundle:Eeo:course-create.html.twig'); 
        }

        return $this->render('TopxiaAdminBundle:Eeo:add-course.html.twig'); 
    }

    /*
    *    Editing course
    */
    public function editCourseAction($courseId) {
        
        $this->eeo = new EeoAuthentication();
        
        $param = $this->eeo->getParameters();
        $param["courseId"] = $courseId;

        $createRequest = $this->eeo->buildRequest("/course.api.php?action=editCourse", $param);
        $request = $this->eeo->postRequest();

        return $this->getCourseListAction();
    }
    
    /*
    *    Delete Course
    */
    public function delCourseAction($courseId) {

        $this->eeo = new EeoAuthentication();

        $param = $this->eeo->getParameters();
        $param["courseId"] = $courseId;

        $createRequest = $this->eeo->buildRequest("/course.api.php?action=delCourse", $param);
        $request = $this->eeo->postRequest();

        $getResponse = $request->error_info->errno;
        if ($getResponse == 149) {

            return $this->getCourseListAction();
        }
    }

}