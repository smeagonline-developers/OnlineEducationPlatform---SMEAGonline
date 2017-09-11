<?php
namespace Eeo\ApiBundle\Classes\Config;

use  Eeo\ApiBundle\Classes\EeoOAuthInterface as Eeo;

class Parameters implements Eeo
{
    /**
     * 
     * @author Richtermark M. Baay
     *
     */
	public $courseId;

	public $courseStatus;

	public $coursePage;

	public $coursePerPage;

	public $courseName;

	public $folderId;

	public $fileData;

	public $expiryTime;

	public $userAccount;

	public $courseStartTime;

	public $courseEndTime;

	public function setCourseId($courseId) {
		
		$this->courseId = $courseId;

		return $this->courseId;
	}

	public function getCourseId() {

		return $this->courseId;
	}

}