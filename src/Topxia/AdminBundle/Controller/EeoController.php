<?php
namespace Topxia\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eeo\ApiBundle\Classes\EeoOAuthClient as Eeo;
// use Eeo\ApiBundle\Form\CourseCreateType;

class EeoController extends BaseController
{   
    public $eeo;

    /*
    *   初始化的Eeo OAuth客户端类
    */
    public function __construct() {

        $this->eeo = new Eeo;
    }

    /*
    *    课程列表
    */
    public function courseManageIndexAction(){

        $param = $this->eeo->getParameters();
        $createRequest = $this->eeo->buildRequest("/course.api.php?action=getCourseList", $param);

        $request = $this->eeo->postRequest();

        return $this->render('TopxiaAdminBundle:Eeo:course-manage.html.twig',array(
            'courses' => $request->data
        ));
    }

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
    
    /*
    *    课程列表
    */
    public function courseLessonListAction($courseId){

        $param = $this->eeo->getParameters();
        $param["courseId"] = $courseId;

        $createRequest = $this->eeo->buildRequest("/course.api.php?action=getCourseClass", $param);

        $request = $this->eeo->postRequest();

        return $this->render('TopxiaAdminBundle:Eeo:course-lesson-list.html.twig',
                        array( 'courseClasses' => $request->data,'courseId' => $courseId ));
    }

    public function courseLessonRemoveAction($classId, $courseId){
     
        $param = $this->eeo->getParameters();
        
        $param["courseId"]  = $courseId;
        $param["classId"]   = $classId;

    
        $createRequest = $this->eeo->buildRequest("/course.api.php?action=delCourseClass", $param);
        $request = $this->eeo->postRequest();

        return new Response(json_encode($remove));
    }
   
    
    public function getCloudResources(){

        $param = $this->eeo->getParameters();
        $createRequest = $this->eeo->buildRequest("/cloud.api.php?action=getFolderList", $param);

        return $this->eeo->postRequest();
     }


 
}