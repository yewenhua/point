angular.module('app')

.controller('LogCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', function($scope, CommonFunction, DataLoad, toaster, divpage) {
	  $scope.haveData = true;
	  $scope.loading = true;
	  $scope.init = true;
	  $scope.dateSelectSingle = {
		  start: moment(new Date()).utc().startOf('day'),
		  end: '',
	  };
	  $scope.query = '';
	  $scope.page = 1;
	  $scope.totalPage = 1;
	  $scope.perPage = 10;
	  $scope.catchList = [];
	  $scope.originalData = '';
	  $scope.colors = ['primary', 'info', 'success', 'warning', 'danger', 'dark'];
	  
	  //首页上一页是否灰显
	  $scope.isFirst = function(index){
	    	if(index == 1){
	    		return true;
	    	}
	    	return false;
	  }
	    
	  //下一页尾页是否灰显
	  $scope.isDisable = function(index){
	    	if(index == 1 && $scope.totalPage == 1){
	    		return true;
	    	}
	    	else if(index < $scope.totalPage){
	    		return false;
	    	}
	    	
	    	return true;
	  }
	    
	  //获取每页信息
	  $scope.getInfoPage = function(index){
	    	if(index <= $scope.totalPage && index > 0){
		    	$scope.page = index;
		    	$scope.getAllData();
	    	}
	  }
	    
	  $scope.divpageByPage = function(e){
	    	var value = $(e.currentTarget).html();
	    	var page = Number(value);
	    	if(angular.isNumber(page) && page > 0){
	    		$scope.getInfoPage(page);
	    	}
	  };
	  
	  $scope.searchData = function() {
		  $scope.page = 1;
		  $scope.getAllData();
	  }
	  
	  $scope.getAllData = function() {
		  var promise = DataLoad.logPageData({
			  time: $scope.init ? '' : $scope.dateSelectSingle.end.format('YYYY-MM-DD'),
			  searchkey: $scope.query,
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage
		  });
		  return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.catchList = returnData.data.data;
						$scope.originalData = returnData;
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.catchList = [];
						$scope.originalData = '';
						toaster.pop('error', ' 申请提现', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.catchList = [];
					$scope.originalData = '';
					$scope.haveData = false;
					toaster.pop('error', ' 申请提现', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.catchList = [];
			  $scope.originalData = '';
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', ' 申请提现', ' 获取提现记录出错！');
		  });
      }
	  
	  $scope.getAllData();  
	  $scope.$watch('dateSelectSingle.start', function(new_val, old_val){
		  if(new_val != old_val){
			  $scope.init = false;
			  $scope.page = 1;
			  $scope.getAllData();
		  }
	  }, true);
	
}]);