<?php

namespace Metronic\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Common\ArrayToolkit;
use Topxia\Service\Util\CloudClientFactory;
use Topxia\Service\CloudPlatform\CloudAPIFactory;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
         $result = CloudAPIFactory::create('leaf')->get('/me');

        $hidden = array();
        if(isset($result['thirdCopyright']) and $result['thirdCopyright'] == '1'){
            $hidden = array(
                'cloud_notice' => '1',
                'system_status' => '1',
            );
        }

        if(isset($result['copyright']) and $result['copyright'] == '1'){  
            $hidden = array(
                'cloud_notice' => '1'
            );
        }       

        return $this->render('MetronicAdminBundle:Default:index.html.twig',array(
            'hidden' => $hidden
        ));
    }
}
