angular.module('app')

.controller('CenterCtrl', ['$scope', '$rootScope', 'CommonFunction', 'DataLoad', 'toaster', function($scope, $rootScope, CommonFunction, DataLoad, toaster) {
	$scope.quick = {};
	$scope.quick.service_mobile = '';
	$scope.quick.vip_mobile = '';
	$scope.quick.point = '';
	$scope.quick.money = '';
	$scope.submit = false;
	$scope.submit_html = '提交';
	
	$scope.quick = function() {
		  if($scope.quick.service_mobile == ''){
			  toaster.pop('error', '服务中心报单', "服务中心号码不能为空！");
			  return false;
		  }
		  if($scope.quick.vip_mobile == ''){
			  toaster.pop('error', '服务中心报单', "会员编号不能为空！");
			  return false;
		  }
		  if($scope.quick.money == ''){
			  toaster.pop('error', '服务中心报单', "报单金额不能为空！");
			  return false;
		  }
		  
		  if($scope.submit == false){
			  $scope.submit = true;
			  $scope.submit_html = '提交中…';
			  var promise = DataLoad.serviceDeclaration({
				  service_mobile: $scope.quick.service_mobile,
				  vip_mobile: $scope.quick.vip_mobile,
				  money: $scope.quick.money,
				  point: $scope.quick.point
			  });
			  return promise.then(function (returnData) {
					if (returnData && returnData.code == 0) {
						$scope.quick.service_mobile = '';
						$scope.quick.vip_mobile = '';
						$scope.quick.point = '';
						$scope.quick.money = '';
						toaster.pop('success', '服务中心报单', '报单成功！');
					} 
					else {
						toaster.pop('error', '服务中心报单', returnData.message);
					}
					$scope.submit = false;
					$scope.submit_html = '提交';
			  }, function () {
				  $scope.submit = false;
				  $scope.submit_html = '提交';
				  toaster.pop('error', '服务中心报单', '报单出错！');
			  });
		  }
    };

    
}]);