angular.module('app')

.controller('SmsTemplateCtrl', ['$scope', 'DataLoad', 'toaster', function($scope, DataLoad, toaster) {
	  $scope.templateList = [];
	  $scope.template = {};
	  $scope.template.name = '';
	  $scope.template.content = '';
	  $scope.template.type = '';
	  $scope.template.isNew = true;
	  $scope.loading = true;
	  
	  $scope.editTemplate = function(item){
		  $scope.template = angular.copy(item);
		  $scope.updateTemplate();
	  }
	  
	  $scope.updateTemplate = function(){
		  var promise = DataLoad.updateTemplate({
			  id: $scope.template.isNew ? '' : $scope.template.id,
			  type: $scope.template.type,
			  content: $scope.template.content,
			  name: $scope.template.name
		  });
		  
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.getAllData();
					toaster.pop('success', '短信模板', '更新成功！');
				} 
				else {
					toaster.pop('error', '短信模板', returnData.message);
				}
		  }, function () {
			  toaster.pop('error', '短信模板', '更新出错！');
		  });
	  }
	  
	  $scope.switch_status = function(item){
		  if(item.open){
			  var value = 1;
		  }
		  else{
			  var value = 0;
		  }
		 
		  var promise = DataLoad.switchTemplateStatus({
			  id: item.id,
			  status: value
		  });
		  return promise.then(function (returnData) {
		      if (returnData && returnData.code == 0) {
				  toaster.pop('success', '短信模板', returnData.message);
			  } 
			  else {
				  toaster.pop('error', '短信模板', returnData.message);
			  }
		  }, function () {
			  toaster.pop('error', '短信模板', '修改出错');
		  });
	  }

	  $scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.smsTemplate({

		  });
		  return promise.then(function (returnData) {
			    $scope.loading = false;
				if (returnData && returnData.code == 0) {
					if (returnData.data.length != 0) {
						$scope.templateList = returnData.data;
						angular.forEach($scope.templateList, function(item, key) {
							var status = angular.copy(item.status);
							item.open = status == 1 ? true : false;
						});
					} else {
						$scope.templateList = [];
						toaster.pop('success', ' 短信模板', "没有数据！");
					}
				} 
				else {
					$scope.templateList = [];
					toaster.pop('error', ' 短信模板', returnData.message);
				}
		  }, function () {
			  $scope.loading = false;
			  $scope.templateList = [];
			  toaster.pop('error', ' 短信模板', ' 获取短信模板出错！');
		  });
      }
	  
	  $scope.getAllData();
	
}]);