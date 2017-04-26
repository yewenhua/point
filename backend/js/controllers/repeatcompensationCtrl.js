angular.module('app')

.controller('RepeatcompensationCtrl', ['$scope', '$rootScope', 'CommonFunction', 'DataLoad', 'toaster', function($scope, $rootScope, CommonFunction, DataLoad, toaster) {
	$scope.data = {};
	$scope.data.mobile = '';
	$scope.data.limit_point = '';
	$scope.data.rate = '';
	$scope.data.day = '';
	$scope.auto_to_consume = false;
	$scope.dateSelectBeginTime = {
	    start: moment(new Date()).utc().startOf('day'),
	    end: moment(new Date()).utc().startOf('day')
    };
	
	$scope.dateSelectEndTime = {
	    start: moment(new Date()).utc().startOf('day'),
	    end: moment(new Date()).utc().startOf('day')
    };
	
	$scope.insertCompensation = function() {
		  if($scope.data.mobile == ''){
			  toaster.pop('error', '复投补偿', "手机号码不能为空！");
			  return false;
		  }
		  if($scope.data.limit_point == ''){
			  toaster.pop('error', '复投补偿', "限额不能为空！");
			  return false;
		  }
		  if($scope.data.rate == ''){
			  toaster.pop('error', '复投补偿', "倍率不能为空！");
			  return false;
		  }
		  if($scope.data.day == ''){
			  toaster.pop('error', '复投补偿', "周期不能为空！");
			  return false;
		  }
		  
		  var promise = DataLoad.insertCompensation({
			  mobile: $scope.data.mobile,
			  limit_point: $scope.data.limit_point,
			  rate: $scope.data.rate,
			  day: $scope.data.day,
			  begin_time: $scope.dateSelectBeginTime.start.format('YYYY-MM-DD'),
			  end_time: $scope.dateSelectEndTime.start.format('YYYY-MM-DD'),
			  auto_to_consume: $scope.auto_to_consume ? 1 : 0
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.data.mobile = '';
					$scope.data.limit_point = '';
					$scope.data.rate = '';
					$scope.data.day = '';
					$scope.auto_to_consume = false;
					toaster.pop('success', '复投补偿', '保存成功！');
				} 
				else {
					toaster.pop('error', '复投补偿', returnData.message);
				}
				
		  }, function () {
			  toaster.pop('error', '复投补偿', '保存复投补偿出错！');
		  });
    };
    
}]);