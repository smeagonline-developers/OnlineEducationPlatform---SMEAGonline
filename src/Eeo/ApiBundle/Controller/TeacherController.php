<?php
namespace Eeo\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eeo\ApiBundle\Classes\EeoOAuthClient as Eeo;

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
    
    public function getTeacherList() {

        $param = $this->eeo->getParameters();
        $createRequest = $this->eeo->buildRequest($this->apiFileAddress . "?action=getTeacherList", $param);

        $request = $this->eeo->postRequest();

        return $request->data;
    }

	public function addTeacherAction() {

	}
	public function editTeacherActrion() {

	}
}