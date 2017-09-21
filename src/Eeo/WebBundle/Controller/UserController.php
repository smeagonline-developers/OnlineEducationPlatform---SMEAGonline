<?php
namespace Eeo\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eeo\ApiBundle\Controller\BaseController as EeoBaseController;
use Eeo\ApiBundle\Classes\EeoOAuthClient as EeoAuthentication;
use Eeo\ApiBundle\Controller\TeacherController as Teacher;

class UserController extends Controller
{

    public $base;

    public $parameters;

    public function __construct() {

        $this->base         = new EeoBaseController();
    }

    public function indexAction($name) {

        return $this->render('EeoWebBundle:User:account-assignment.html.twig', array('name' => $name));
    }

    public function userOverviewAction($account) {
    
        $userCourseList = $this->base->getUserCourseList($account);

//         foreach ( $userCourseList as $key => $course) {
//             if ($course->course_name == "金陈鎬Kim chenhao"){
//                   echo "<pre>";
//                             var_dump($course);
//                         echo "</pre>";
//                     }
//                 }        
     
// exit;


    	$teacher = new Teacher();
    	$getAllData = $teacher->getTeacherList($account);   

        return $this->render('EeoWebBundle:User:user-overview.html.twig', array( 'TeachersClass' => $userCourseList, 'data' => $getAllData['data'] ));
    }
}
