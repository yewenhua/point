angular.module('app')

.controller('DeclarationsettingCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal) {
	$scope.config = {};
	$scope.config.least_money = '';
	$scope.config.most_money = '';
	$scope.config.use_consume_most_rate = '';
	$scope.config.wait_rate = '';
	$scope.config.first_level_rate = '';
	$scope.config.second_level_rate = '';
	$scope.config.third_level_rate = '';
	$scope.config.give_exchange_rate = '';
	$scope.config.give_consume_rate = '';
	$scope.config.give_share_rate = '';
	$scope.config.wait_period = '';
	$scope.config.first_level_must = 0;
	$scope.config.second_level_must = 2;
	$scope.config.third_level_must = 2;
	$scope.config.charge_repeat_rate = '';
	$scope.config.charge_repeat_period = '';
	$scope.config.merchant_repeat_rate = '';
	$scope.config.merchant_repeat_period = '';
	$scope.submit = false;
	$scope.loading = true;
	$scope.submit_html = '提交';
	
	$scope.getConfigDeclaration = function() {
		  var promise = DataLoad.getConfigDeclaration({

		  });
		  return promise.then(function (returnData) {
			  if (returnData && returnData.code == 0) {
				  $scope.config = returnData.data;
			  } 
			  $scope.loading = false;
		  }, function () {
			  $scope.loading = false;
		      toaster.pop('error', '报单设置', '获取报单设置出错');
		  });
    }
	$scope.getConfigDeclaration();
	
	$scope.submitConfig = function() {
		  if($scope.config.least_money == ''){
			  toaster.pop('error', '报单设置', "报单最低金额不能为空！");
			  return false;
		  }
		  if($scope.config.most_money == ''){
			  toaster.pop('error', '报单设置', "报单最高金额不能为空！");
			  return false;
		  }
		  if($scope.config.use_consume_most_rate == ''){
			  toaster.pop('error', '报单设置', "报单兑冲消费积分最高比例不能为空！");
			  return false;
		  }
		  if($scope.config.wait_rate == ''){
			  toaster.pop('error', '报单设置', "待用积分倍率不能为空！");
			  return false;
		  }
		  if($scope.config.first_level_rate == ''){
			  toaster.pop('error', '报单设置', "第一层分享比例不能为空！");
			  return false;
		  }
		  if($scope.config.second_level_rate == ''){
			  toaster.pop('error', '报单设置', "第二层分享比例不能为空！");
			  return false;
		  }
		  if($scope.config.third_level_rate == ''){
			  toaster.pop('error', '报单设置', "第三层分享比例不能为空！");
			  return false;
		  }
		  if($scope.config.give_exchange_rate == ''){
			  toaster.pop('error', '报单设置', "赠送兑换积分比例不能为空！");
			  return false;
		  }
		  if($scope.config.give_consume_rate == ''){
			  toaster.pop('error', '报单设置', "赠送消费积分比例不能为空！");
			  return false;
		  }
		  if($scope.config.give_share_rate == ''){
			  toaster.pop('error', '报单设置', "赠送分享积分比例不能为空！");
			  return false;
		  }
		  if($scope.config.wait_period == ''){
			  toaster.pop('error', '报单设置', "待用积分周期不能为空！");
			  return false;
		  }
		  if($scope.config.charge_repeat_rate == ''){
			  toaster.pop('error', '报单设置', "充值复投倍率不能为空！");
			  return false;
		  }
		  if($scope.config.charge_repeat_period == ''){
			  toaster.pop('error', '报单设置', "充值复投周期不能为空！");
			  return false;
		  }
		  if($scope.config.merchant_repeat_rate == ''){
			  toaster.pop('error', '报单设置', "商家复投倍率不能为空！");
			  return false;
		  }
		  if($scope.config.merchant_repeat_period == ''){
			  toaster.pop('error', '报单设置', "商家复投周期不能为空！");
			  return false;
		  }
		  
		  if($scope.submit == false){
			  $scope.submit = true;
			  $scope.submit_html = '提交中…';
			  var promise = DataLoad.configDeclaration({
				  config: $scope.config
			  });
			  return promise.then(function (returnData) {
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '报单设置', '设置成功');
					} 
					else {
						toaster.pop('error', '报单设置', returnData.message);
					}
					$scope.submit = false;
					$scope.submit_html = '提交';
			  }, function () {
				  $scope.submit = false;
				  $scope.submit_html = '提交';
				  toaster.pop('error', '报单设置', '设置出错');
			  });
		  }
    };
}]);
