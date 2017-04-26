'use strict';

app.controller('ChgpwdCtrl', ['$rootScope', '$scope', '$http', '$state', 'toaster', '$timeout', 'DataLoad', '$location', function($rootScope, $scope, $http, $state, toaster, $timeout, DataLoad, $location) {
    $scope.authError = null;
    $scope.user = {
		password: '',
		newpassword: '',
		renewpassword: '',
	};
    
	var active = false;
	$scope.chgpwd = function() {
        if($scope.user.newpassword != $scope.user.renewpassword){
			toaster.pop('error', '系统设置', " 两次输入密码不一致！");
			return false;
		}
		
		if (active == false && $scope.user.password != '' && $scope.user.newpassword != '' && $scope.user.renewpassword != '') {
			active = true;
			var promise = DataLoad.chgpwd({
				id: $rootScope.userInfo.id,
				password: $scope.user.password,
				newpassword: $scope.user.newpassword
			});
			return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					toaster.pop('success', '修改密码', '修改成功！');
					$scope.user = {
						password: '',
						newpassword: '',
						renewpassword: '',
					};
				} 
				else {
					toaster.pop('error', '修改密码', '修改密码出错！');
				}
				active = false;
			}, function () {
				active = false;
				toaster.pop('error', '修改密码', '未知错误，请刷新重试！');
			});
		}
    }
}]);