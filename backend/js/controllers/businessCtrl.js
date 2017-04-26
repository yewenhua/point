angular.module('app')

.controller('BusinessCtrl', ['$scope', '$rootScope', 'CommonFunction', 'DataLoad', 'toaster', function($scope, $rootScope, CommonFunction, DataLoad, toaster) {
	$scope.quick = {};
	$scope.quick.mobile = '';
	$scope.company_get_type = 2;
	$scope.quick.point = '';
	$scope.quick.mark = '';
	$scope.submit = false;
	$scope.submit_html = '提交';
	
	$scope.quick = function() {
		  if($scope.quick.mobile == ''){
			  toaster.pop('error', '商家结算', "手机号码不能为空！");
			  return false;
		  }
		  if($scope.quick.point == ''){
			  toaster.pop('error', '商家结算', "获利积分不能为空！");
			  return false;
		  }
		  
		  if($scope.submit == false){
			  $scope.submit = true;
			  $scope.submit_html = '提交中…';
			  var promise = DataLoad.quickBusiness({
				  mobile: $scope.quick.mobile,
				  company_get_type: $scope.company_get_type,
				  point: $scope.quick.point,
				  mark: $scope.quick.mark
			  });
			  return promise.then(function (returnData) {
					if (returnData && returnData.code == 0) {
						$scope.quick.mobile = '';
						$scope.quick.point = '';
						$scope.quick.mark = '';
						toaster.pop('success', '商家结算', '结算成功！');
					} 
					else {
						toaster.pop('error', '商家结算', returnData.message);
					}
					$scope.submit = false;
					$scope.submit_html = '提交';
			  }, function () {
				  $scope.submit = false;
				  $scope.submit_html = '提交';
				  toaster.pop('error', '商家结算', '结算出错！');
			  });
		  }
    };

    
}]);