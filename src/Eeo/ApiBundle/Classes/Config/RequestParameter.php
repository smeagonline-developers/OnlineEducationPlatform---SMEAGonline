<?php
namespace Eeo\ApiBundle\Classes\Config;

use  Eeo\ApiBundle\Classes\EeoOAuthInterface as Eeo;
use  Eeo\ApiBundle\Classes\EeoOAuthClient as EeoAuth;

class RequestParameter implements Eeo
{
    /**
     * 
     * @author Richtermark M. Baay
     *
     */
	CONST NAME = "default_parameter";

	CONST USER_GLOBAL_PASSWORD = '123456';

	private $sid;

	private $secret;

	private $safeKey;

	private $timeStamp;

	private $telephone;

	private $password;

 	private $accessAuthentication;

    private $countryPhoneNumber;

	/*
	*    Build Client Credentials
	*/
	public function __construct() {

		$this->accessAuthentication = array( "sid" => "2357082", "secret" => "3AVToi0y" );
        $this->countryPhoneNumber = [ 'code' => 15, 'total_length' => 11, 'random_length' => 9 ];

		return $this->buildDefault();
	}

	public function buildDefault() {
		
		$this->accessAuthentication =  array(  "SID" 		=> $this->getSid(), 
										"safeKey" 	=> $this->getSafeKey(), 
								"timeStamp" 	=> $this->getTimeStamp()
							);
		return $this->getParameters();
	}

	public function getClassInUserRegistration($requestUri = "/course.api.php?action=register") {

        $param = $this->getParameters();

        $param['telephone'] = $this->getTelephone();
        $param['md5pass'] = $this->getMd5Password( SELF::USER_GLOBAL_PASSWORD );

        $eeoApi 	= new EeoAuth();
        $build 		= $eeoApi->buildRequest($requestUri, $param);
        $request 	= $eeoApi->postRequest();
        
        $classIn = ($request->error_info->errno == 1) ? ['classIn_id' => $request->data, 'classIn_telephone' => $param['telephone']] : "ClassIn Registration Failed!";
        
        return $classIn;
    }

    public function setTelephone($telephone) {

    	$this->telephone = $telephone;

    	return $this->telephone;
    }

    public function getTelephone() {

    	$this->telephone = (!is_null($this->telephone)) ? $this->telephone : $this->generatePhoneNumber() ;

    	return (int) $this->telephone;
    }

    public function generatePhoneNumber() {

        return  $this->countryPhoneNumber['code'] . rand(pow(10, $this->countryPhoneNumber['random_length']-1), pow(10, $this->countryPhoneNumber['random_length'])-1);
    }

	public function getParameters(){

		return $this->accessAuthentication;
	}

	public function getSid() {
		
		$this->sid = $this->accessAuthentication['sid'];

		return $this->sid;
	}
	
	public function getSecret() {
		
		$this->secret = $this->accessAuthentication['secret'];

		return $this->secret;
	}

	public function getSafeKey() {

       	$this->safeKey = md5( $this->getSecret() . $this->getTimeStamp() );

        return $this->safeKey;
    }

    public function getPlainPassword($password) {
    	
    	$this->password = $password;

    	return $this->password;
    }

    public function getMd5Password($password) {

    	return md5($password);
    }

    public function getTimeStamp() {
	    
	    $this->timeStamp =  time();
	    
	    return $this->timeStamp;
    }

    public function getNickname() {
    	return "Hwang Sunjae";
    }
  
    public function getSubscribersPasswordType() {
    	
    	return [ 'plain' => '123456', 'md5' => md5('123456')];
    }

    public function getName() {

    	return SELF::NAME;

    }
}