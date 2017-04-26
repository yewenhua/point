angular.module('app')

.controller('QuickCtrl', ['$scope', '$rootScope', 'CommonFunction', 'DataLoad', 'toaster', function($scope, $rootScope, CommonFunction, DataLoad, toaster) {
	$scope.quick = {};
	$scope.quick.mobile = '';
	$scope.quick.money = '';
	$scope.submit = false;
	$scope.submit_html = '提交';
	
	$scope.quick = function() {
		  if($scope.quick.mobile == ''){
			  toaster.pop('error', '快速报单', "手机号码不能为空！");
			  return false;
		  }
		  if($scope.quick.money == ''){
			  toaster.pop('error', '快速报单', "报单金额不能为空！");
			  return false;
		  }
		  
		  if($scope.submit == false){
			  $scope.submit = true;
			  $scope.submit_html = '提交中…';
			  var promise = DataLoad.quickDeclaration({
				  mobile: $scope.quick.mobile,
				  money: $scope.quick.money,
			  });
			  return promise.then(function (returnData) {
					if (returnData && returnData.code == 0) {
						$scope.quick.mobile = '';
						$scope.quick.money = '';
						toaster.pop('success', '快速报单', '报单成功！');
					} 
					else {
						toaster.pop('error', '快速报单', returnData.message);
					}
					$scope.submit = false;
					$scope.submit_html = '提交';
			  }, function () {
				  $scope.submit = false;
				  $scope.submit_html = '提交';
				  toaster.pop('error', '快速报单', '报单出错！');
			  });
		  }
    };

    
}]);