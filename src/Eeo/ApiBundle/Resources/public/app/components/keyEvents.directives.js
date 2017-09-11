(function() {
    'use strict';

    angular
        .module('EeoApp')
        .directive('enterNext', enterNext);

    function enterNext() {
		return {
			restrict: 'A',
			link: function($scope,elem,attrs) {
				elem.bind('keydown', function(e) {
					var code = e.keyCode || e.which;
					if (code === 13) {
						e.preventDefault();
						elem.nextAll('input').first().focus();
					}
				});
			}
		}
    }

})();