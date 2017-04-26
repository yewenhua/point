angular.module('app')

.controller('PersonalCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', '$rootScope', function($scope, CommonFunction, DataLoad, toaster, $rootScope) {
	
	$scope.user = angular.copy($rootScope.userInfo);
	$scope.isNameExist = false;
	$scope.isMobileExist = false;
    $scope.isExist = function(param) {
		var promise = DataLoad.isExist(param);
		return promise.then(function (returnData) {
			if (returnData && returnData.code == 0 && returnData.data.id != $scope.user.id) {
				if(!angular.isUndefined(param.name)){
					$scope.isNameExist = true;
					toaster.pop('error', '信息修改', '用户名已经存在！');
				}
				else{
					$scope.isMobileExist = true;
					toaster.pop('error', '信息修改', '手机号码已经存在！');
				}
			} 
		    else {
		    	if(!angular.isUndefined(param.name)){
					$scope.isNameExist = false;
				}
				else{
					$scope.isMobileExist = false;
				}
			}
		}, function () {
			if(!angular.isUndefined(param.name)){
				$scope.isNameExist = true;
			}
			else{
				$scope.isMobileExist = true;
			}
			toaster.pop('error', '信息修改', '获取信息出错！');
		});
    }
    
    $scope.mobilePattern = function(mobile){
    	var rule = /^1[1|2|3|4|5|6|7|8|9][0-9]\d{4,8}$/;
    	if(rule.test(mobile)){
    		return true;
    	}
    	return false;
    }
    
    $scope.checkName = function(){
    	if($scope.user.name){
	    	$scope.isExist({
	    		name: $scope.user.name
	    	});
    	}
    }
    
    $scope.checkMobile = function(){
    	if($scope.user.mobile){
    		if($scope.mobilePattern($scope.user.mobile)){
		    	$scope.isExist({
		    		mobile: $scope.user.mobile
		    	});
    		}
    	}
    }
  	
	var active = false;
	$scope.chgPersonal = function() {
		if (!$scope.isNameExist && !$scope.isMobileExist) {
			if (active == false) {
				active = true;
				var promise = DataLoad.chgPersonal({
					id: $rootScope.userInfo.id,
					name: $scope.user.name,
					mobile: $scope.user.mobile
				});
				return promise.then(function (returnData) {
	
					if (returnData && returnData.code == 0) {
						$rootScope.userInfo.name = angular.copy($scope.user.name);
						$rootScope.userInfo.mobile = angular.copy($scope.user.mobile);
						toaster.pop('success', '信息修改', '修改成功！');
					} 
					else {
						toaster.pop('error', '信息修改', '修改出错！');
					}
					active = false;
				}, function () {
					active = false;
					toaster.pop('error', '信息修改', '未知错误，请刷新重试！');
				});
			}
		}
		else if ($scope.isNameExist){
			toaster.pop('error', '信息修改', '用户名已经存在！');
		}
		else{
			toaster.pop('error', '信息修改', '手机号码已经存在！');
		}
    }
}]);