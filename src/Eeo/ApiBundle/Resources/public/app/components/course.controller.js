(function() {
    'use strict';

    angular
        .module('EeoApp')
        .controller('CourseController', CourseController);


    function CourseController($scope, $http) {
    		console.log($scope);
    	$scope.editCourseClass = function (e) {
    		
    		// e.preventDefault();
		  //  $http.post("http://www.eeo.cn/partner/api/course.api.php?action=addCourse", {"SID":"", })
				// .then(successCallback, errorCallback);
		};

    }



})();

