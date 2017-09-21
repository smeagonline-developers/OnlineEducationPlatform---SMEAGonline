<?php
namespace Eeo\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eeo\ApiBundle\Classes\EeoOAuthClient as Eeo;
use Symfony\Component\HttpFoundation\JsonResponse;

class TeacherController extends Controller
{  

    /**
     * 
     * @author Richtermark M. Baay
     *
     */
	public $eeo;
	public $apiFileAddress;

    CONST COURSE_FILE = "/course.api.php";

    /*
    *   初始化的Eeo OAuth客户端类
    */
    public function __construct() {

        $this->eeo 				= new Eeo;
        $this->apiFileAddress 	= SELF::COURSE_FILE;
    }
    
    public function getTeacherList($account = null) {
        $response = array();

        $param = $this->eeo->getParameters();
        $createRequest = $this->eeo->buildRequest($this->apiFileAddress . "?action=getTeacherList", $param);

        $request = $this->eeo->postRequest();

        if (!is_null($account)) {
            $getTeacher = array();

            foreach ($request->data as $value) {
                if ( $value->teacher_account == $account ) {
                    $getTeacher['data'] = $value;
                }
            }
             $response = $getTeacher;
        } else {
            $response = $request->data; 
        }

        return $response;
    }

    public function getTeacherListViewAction() {

        return $this->render('EeoWebBundle:classIn:teacher-list.html.twig', [ 'teachers' => $this->getTeacherList() ]);
    }

    public function modifyCourseTeacherAction($courseId, $account) {

        $param               = $this->eeo->getParameters();
        $param["courseId"]      = $courseId;
        $param["teacherAccount"]   = $account;

        $createRequest = $this->eeo->buildRequest($this->apiFileAddress . "?action=modifyCourseTeacher", $param);
        $request = $this->eeo->postRequest();

        echo "<pre>";
        
        var_dump($request);exit;
        echo "</pre>";
        return new Response($this->getCourseClassAction($courseId));
    }

	public function addTeacherAction(Request $request) {

        if ($request->query->get('accountNo')) {
            var_dump($request->query->all());exit;
        }
     
        // if($request->request->get('new_teacher')) {
        //     $request =  $request->query->get('new_teacher');

        //     var_dump($request);exit;

        //     return new Response(json_encode($request));
        // }

  
        return $this->render('EeoWebBundle:teacher:add-teacher.html.twig');
	}
	public function editTeacherActrion() {

	}
}