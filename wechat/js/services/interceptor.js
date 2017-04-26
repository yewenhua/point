'use strict';

/**
 * Interceptor
 */

angular.module('interceptor', []).factory('Interceptor',['$q', function($q) {
	var interceptor = {
		// 成功的请求方法 
		'request': function(config) {
			return config; // 或者 $q.when(config); 
		},
		// 响应成功
		'response': function(response) {
			if (response.data && response.data.code == 9999) {
				localStorage.removeItem('userInfo');
				window.location = '/index.php/mobile/#/access/signin';
				return;
			}
			return response;
		},
		'requestError': function(rejection) {
			// 请求发生了错误，如果能从错误中恢复，可以返回一个新的请求或promise 
			return response; // 或新的promise 
			// 或者，可以通过返回一个rejection来阻止下一步 
			// return $q.reject(rejection); 
		},
		'responseError': function(rejection) {
			// 请求发生了错误，如果能从错误中恢复，可以返回一个新的响应或promise 
			return rejection; // 或新的promise 
			// 或者，可以通过返回一个rejection来阻止下一步 
			// return $q.reject(rejection); 
		}
	};
	return interceptor;
}]);