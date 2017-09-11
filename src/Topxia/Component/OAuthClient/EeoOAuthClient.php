<?php
namespace Topxia\Component\OAuthClient;

use Topxia\Service\Common\ServiceKernel;
use \InvalidArgumentException;

class EeoOAuthClient implements EeoOAuthInterface
{   

    public $authorizedUri;
    public clientAuthorizationKey = array();

    CONST EPOCH = '1503989680';
    CONST AUTHORIZE_URL = 'http://www.eeo.cn/partner/api/course.api.php?';


    public function __construct() {

        // $this->clientAuthorizationKey = array('SID' => '2357082', 'client_secret' => '3AVToi0y');

        // return $this->clientAuthorizationKey;
    }

    public function getRedirectUri($uri) {

        $this->authorizedUri = $uri;

        return $this->authorizedUri;
   }


    public function getConvertedTimeStamp() {
        $epoch = SELF::EPOCH;

        $dateTime = new DateTime("@epoch");  // convert UNIX timestamp to PHP DateTime
        $currentTimeStamp =  $dateTime->format('Y-m-d H:i:s'); // output = 2017-01-01 00:00:00
    
        return $currentTimeStamp;

    }

    <?php

// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => "?action=getCourseList",
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => "",
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 30,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => "POST",
//   CURLOPT_POSTFIELDS => "SID=2357082&safeKey=a2e546b2960bb19d9ee6d13d0e0b4fac&timeStamp=1504165849",
//   CURLOPT_HTTPHEADER => array(
//     "cache-control: no-cache",
//     "content-type: application/x-www-form-urlencoded",
//     "postman-token: 54139bfd-df04-29ec-e08b-df9168a9e7f6"
//   ),
// ));

// $response = curl_exec($curl);
// $err = curl_error($curl);

// curl_close($curl);

// if ($err) {
//   echo "cURL Error #:" . $err;
// } else {
//   echo $response;
// }
}

