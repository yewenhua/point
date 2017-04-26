'use strict';

angular.module('app').controller('SigninFormController', ['$scope', 'DataLoad', '$location', 'toaster', function($scope, DataLoad, $location, toaster) {
	
	$scope.user = {
		name: '',
		password: '',
	};
	
	var active = false;
	$scope.loginHtml = '登  录';
	$scope.login = function() {
		if (active == false && $scope.user.name != '' && $scope.user.password != '') {
			active = true;
			$scope.loginHtml = '登录中……';
			var promise = DataLoad.signin({
				name: $scope.user.name,
				password: $scope.user.password
			});
			return promise.then(function (returnData) {
				if (returnData && returnData.code == 0 && returnData.data != null) {
					$scope.loginHtml = '登录成功';
					sessionStorage.setItem('userInfo', JSON.stringify(returnData.data));
	                $location.path("/");
				} 
				else {
					$scope.loginHtml = '登录';
					toaster.pop('error', '登录', '用户名或密码错误！');
				}
				active = false;
			}, function () {
				active = false;
				$scope.loginHtml = '登  录';
				toaster.pop('error', '登录', '未知错误，请刷新重试！');
			});
		}
    }
	
}]);
