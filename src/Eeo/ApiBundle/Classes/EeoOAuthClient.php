<?php
namespace Eeo\ApiBundle\Classes;

use Eeo\ApiBundle\Classes\Config\RequestParameter;
use Eeo\ApiBundle\Classes\Config\Parameters as Fields;

class EeoOAuthClient implements EeoOAuthInterface
{   
    
    /**
     * 
     * @author Richtermark M. Baay
     *
     */
    private $curl;

    private $requestUri;

    private $requiredFields;

    CONST NAME = "api_package";
    CONST REQUEST_HOST = "http://www.eeo.cn/partner/api";
    
    public function getParameters() {

        $apiRequest = new RequestParameter();
        $getDefaultParam = $apiRequest->getParameters();

        return $getDefaultParam;
    }

    public function buildRequest($uri, array $data) {

        $transformRequirements = "";
        foreach ($data as $key => $value) {
       
            $this->requiredFields[] .= $key."=".$value;    
        }

        foreach ($this->requiredFields as $key => $value) {
            $connector = ($key > 0) ? "&" : "";
            $transformRequirements .= $connector . $value;
        }
        
        $this->requiredFields = $transformRequirements; 
        $this->requestUri = SELF::REQUEST_HOST . $uri;
        
        return $this->requestUri;
    }

    public function postRequest() {
        $this->startProcess();
        curl_setopt_array($this->curl, array(  
                CURLOPT_URL => $this->requestUri . "",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $this->requiredFields ."",
                CURLOPT_HTTPHEADER => array( "cache-control: no-cache", 
                                             "content-type: application/x-www-form-urlencoded",
                                             "postman-token: 54139bfd-df04-29ec-e08b-df9168a9e7f6"
                                    ),
        ));

        $response = $this->getResponse($this->curl);
        $err = $this->getError($this->curl);
        $this->endProcess($this->curl);

        return (!$err) ? json_decode($response) : "cURL Error #:" . $err ;
    }

    public function getResponse( $checkInitialization ) {

        $curlInit = $checkInitialization;

        return curl_exec(($curlInit));
    }

    public function getError( $checkInitialization ) {
        
        $curlInit = $checkInitialization;

        return curl_error($curlInit);
    }

    public function startProcess () {
         
         $this->curl = curl_init();

         return $this->curl;
    }

    public function endProcess( $checkInitialization ) {
        
        $curlInit = $checkInitialization;

        return curl_close($curlInit);
    }

    public function test() {

        return $this->apiRequest->getParameters();
    }
}

