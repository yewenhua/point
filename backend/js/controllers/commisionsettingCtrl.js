angular.module('app')

.controller('CommisionsettingCtrl', ['$scope', '$rootScope', 'CommonFunction', 'DataLoad', 'toaster', function($scope, $rootScope, CommonFunction, DataLoad, toaster) {
	$scope.schedule = {};
	$scope.schedule.junior_first = '';
	$scope.schedule.junior_second = '';
	$scope.schedule.junior_third = '';
	
	$scope.schedule.middle_first = '';
	$scope.schedule.middle_second = '';
	$scope.schedule.middle_third = '';
	
	$scope.schedule.advanced_first = '';
	$scope.schedule.advanced_second = '';
	$scope.schedule.advanced_third = '';
	$scope.schedule.isNew = true;
	$scope.loading = true;
	$scope.submiting = false;
	$scope.submit_html = '提交';
	
	$scope.commisionSetting = function() {
		  if($scope.schedule.junior_first == ''){
			  toaster.pop('error', '佣金设置', "初级第一层分配比例不能为空！");
			  return false;
		  }
		  if($scope.schedule.middle_first == ''){
			  toaster.pop('error', '佣金设置', "中级第一层分配比例不能为空！");
			  return false;
		  }
		  if($scope.schedule.advanced_first == ''){
			  toaster.pop('error', '佣金设置', "高级第一层分配比例不能为空！");
			  return false;
		  }
		  
		  $scope.submiting = true;
		  $scope.submit_html = '提交中…';
		  var promise = DataLoad.commisionSetting({
			  id: $scope.schedule.isNew ? '' : $scope.schedule.id,
			  junior_first: $scope.schedule.junior_first,
			  junior_second: $scope.schedule.junior_second,
			  junior_third: $scope.schedule.junior_third,
			  middle_first: $scope.schedule.middle_first,
			  middle_second: $scope.schedule.middle_second,
			  middle_third: $scope.schedule.middle_third,
			  advanced_first: $scope.schedule.advanced_first,
			  advanced_second: $scope.schedule.advanced_second,
			  advanced_third: $scope.schedule.advanced_third
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if($scope.schedule.isNew == true){
						$scope.schedule.id = returnData.data;
						$scope.schedule.isNew = false;
					}	
					toaster.pop('success', '佣金设置', '保存成功！');
				} 
				else {
					toaster.pop('error', '佣金设置', returnData.message);
				}
				$scope.submiting = false;
				$scope.submit_html = '提交';
		  }, function () {
			  $scope.submiting = false;
			  $scope.submit_html = '提交';
			  toaster.pop('error', '佣金设置', '保存佣金设置出错！');
		  });
    };
    
    $scope.getCommisionSetting = function() {
    	  $scope.loading = true;
		  var promise = DataLoad.getCommisionSetting({

		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.schedule = returnData.data;
					$scope.schedule.isNew = false;
				} 
				else {
					$scope.schedule.isNew = true;
				}
				$scope.loading = false;
		  }, function () {
			  $scope.schedule.isNew = true;
			  $scope.loading = false;
			  toaster.pop('error', '佣金设置', '获取佣金设置出错！');
		  });
    }
    
    $scope.getCommisionSetting();
    
}]);