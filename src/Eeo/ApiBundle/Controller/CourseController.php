<?php
namespace Eeo\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eeo\ApiBundle\Controller\BaseController as EeoBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends BaseController
{   

    /**
     * 
     * @author Richtermark M. Baay
     *
     */
    CONST NAME = "course";

    public $base;
    
    public function __construct() {

        $this->base = new EeoBaseController();
    }

    /*
    *    课程列表
    */
    public function getCourseListAction(){

        $course = $this->base->getList(SELF::NAME);

        return $this->render('TopxiaAdminBundle:Eeo:course-manage.html.twig',
                    array( SELF::NAME => $course->data
                ));
    }

    /*
	*	Obtain course information
	*/
	public function getCourseInfoAction(){}

    /*
    *    创建课程
    */
    public function courseCreateAction() {
        
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

        return $this->render('TopxiaAdminBundle:Eeo:course-create.html.twig'); 
    }

    /*
    *    课程编辑
    */
    public function courseEditAction($courseId) {

        $param = $this->eeo->getParameters();
        $param["courseId"] = $courseId;

        $createRequest = $this->eeo->buildRequest("/course.api.php?action=getCourseInfo", $param);
        $request = $this->eeo->postRequest();

        return $this->render('TopxiaAdminBundle:Eeo:course-edit.html.twig', array('course' => $request->data, 'resources' => "a"));
    }

    
    /*
    *    课程删除
    */
    public function courseDeleteAction($courseId) {

        $param = $this->eeo->getParameters();
        $param["courseId"] = $courseId;

        $createRequest = $this->eeo->buildRequest("/course.api.php?action=delCourse", $param);
        $request = $this->eeo->postRequest();

        $getResponse = $request->error_info->errno;
        if ($getResponse == 149) {

            return $this->redirect($this->generateUrl('admin_partner_courseManage'));
        }
    }
    
}