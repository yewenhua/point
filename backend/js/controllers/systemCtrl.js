angular.module('app')

.controller('SystemCtrl', ['$scope', '$rootScope', 'CommonFunction', 'DataLoad', 'toaster', function($scope, $rootScope, CommonFunction, DataLoad, toaster) {
	$scope.system = {};
	$scope.system.title = '';
	$scope.system.privilege_key = '';
	$scope.system.site_name = '';
	$scope.system.site_www = '';
	$scope.system.keywords = '';
	$scope.system.description = '';
	$scope.month_check_list = [];
	for(i=1; i<=28; i++){
		$scope.month_check_list.push({id:i, value: i+'日'});
	}
	
	$scope.system.isNew = true;
	
	$scope.editSystem = function() {
		  if($scope.system.title == ''){
			  toaster.pop('error', '系统设置', "系统名称不能为空！");
			  return false;
		  }
		  if($scope.system.author == ''){
			  toaster.pop('error', '系统设置', "版权归属不能为空！");
			  return false;
		  }
		  if($scope.system.privilege_key == ''){
			  toaster.pop('error', '系统设置', "授权绑定不能为空！");
			  return false;
		  }
		  if($scope.system.site_name == ''){
			  toaster.pop('error', '系统设置', "站点名称不能为空！");
			  return false;
		  }
		  if($scope.system.site_www == ''){
			  toaster.pop('error', '系统设置', "站点域名不能为空！");
			  return false;
		  }
		  
		  var promise = DataLoad.editSystem({
			  id: $scope.system.isNew ? '' : $scope.system.id,
			  title: $scope.system.title,
			  author: $scope.system.author,
			  privilege_key: $scope.system.privilege_key,
			  site_name: $scope.system.site_name,
			  site_www: $scope.system.site_www,
			  keywords: $scope.system.keywords,
			  description: $scope.system.description
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if($scope.system.isNew == true){
						$scope.system.id = returnData.data;
						$scope.system.isNew = false;
					}	
					$rootScope.systemInfo.title = angular.copy($scope.system.title);
					$rootScope.systemInfo.signin_title = $rootScope.systemInfo.title;
					toaster.pop('success', '系统设置', '保存成功！');
				} 
				else {
					toaster.pop('error', '系统设置', returnData.message);
				}
				
		  }, function () {
			  toaster.pop('error', '系统设置', '保存系统设置出错！');
		  });
    };
    
    $scope.getSystemData = function() {
		  var promise = DataLoad.getSystemData({

		  });
		  return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					$scope.system = returnData.data;
					$scope.system.isNew = false;
					$rootScope.systemInfo = angular.copy(returnData.data);
				} 
				else {
					$scope.system.isNew = true;
					$scope.system.title = '';
					$scope.system.privilege_key = '';
					$scope.system.site_name = '';
					$scope.system.site_www = '';
					$scope.system.keywords = '';
					$scope.system.description = '';
					toaster.pop('error', '系统设置', returnData.message);
				}
		  }, function () {
			  $scope.system.isNew = true;
			  $scope.system.title = '';
		      $scope.system.privilege_key = '';
		      $scope.system.site_name = '';
		  	  $scope.system.site_www = '';
		  	  $scope.system.keywords = '';
		  	  $scope.system.description = '';
			  toaster.pop('error', '系统设置', '获取系统设置出错！');
		  });
    }
    
    $scope.getSystemData();
    
}]);