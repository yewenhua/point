angular.module('app')

.controller('WechatsettingCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal) {
	$scope.haveData = true;
	$scope.loading = true;
	$scope.query = '';
	$scope.page = 1;
	$scope.totalPage = 1;
	$scope.perPage = 10;
	$scope.dataList = [];
	
	$scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.getMyDownloadPageData({
			  searchkey: $scope.query,
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '我的下载', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '我的下载', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', '我的下载', '获取数据出错！');
		  });
    }
	$scope.getAllData();
	
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
}]);
