(function() {
    'use strict';

    angular
        .module('EeoApp')
        .controller('CourseController', CourseController);


    function CourseController($scope, $http) {
    		
    	$scope.create = function (e) {
    		console.log("Were not using this yet!");
    		// e.preventDefault();
		  //  $http.post("http://www.eeo.cn/partner/api/course.api.php?action=addCourse", {"SID":"", })
				// .then(successCallback, errorCallback);
		};

    }



})();

