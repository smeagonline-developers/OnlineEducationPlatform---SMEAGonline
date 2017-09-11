<?php
namespace Eeo\ApiBundle\Classes\Config;

use  Eeo\ApiBundle\Classes\EeoOAuthInterface as Eeo;

class RequestParameter implements Eeo
{
    /**
     * 
     * @author Richtermark M. Baay
     *
     */
	CONST NAME = "default_parameter";

	private $sid;

	private $secret;

	private $safeKey;

	private $timeStamp;

 	private $accessAuthentication;

	/*
	*    Build Client Credentials
	*/
	public function __construct() {

		$this->accessAuthentication = array( "sid" => "2357082", "secret" => "3AVToi0y" );

		return $this->buildDefault();
	}

	public function buildDefault() {
		
		$this->accessAuthentication =  array(  "SID" 		=> $this->getSid(), 
										"safeKey" 	=> $this->getSafeKey(), 
								"timeStamp" 	=> $this->getTimeStamp()
							);
		return $this->getParameters();
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

    public function getTimeStamp() {
	    
	    $this->timeStamp =  time();
	    
	    return $this->timeStamp;
    }

    public function getName() {

    	return SELF::NAME;

    }
}