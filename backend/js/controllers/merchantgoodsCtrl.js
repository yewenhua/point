angular.module('app')

.controller('MerchantgoodsCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', '$stateParams', '$window', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal, $stateParams, $window) {
	$scope.haveData = true;
	$scope.loading = true;
	$scope.query = '';
	$scope.page = 1;
	$scope.totalPage = 1;
	$scope.perPage = 20;
	$scope.dataList = [];
	$scope.init = true;
	$scope.submit = false;
	$scope.total = {
		total_all: 0,
		total_release: 0,
		total_down: 0,
		total_recommend: 0,
		total_timebuy: 0
	};
	
	if(!angular.isUndefined($stateParams.userid)){
		$scope.userid = $stateParams.userid;
	}
	else{
		$scope.userid = 'empty';
	}
	
	$scope.dateSelectSingle = {
	    start: moment(new Date()).utc().startOf('day'),
	    end: '',
    };
	
    $scope.$watch('dateSelectSingle.start', function(new_val, old_val){
		if(new_val != old_val){
			$scope.init = false;
			$scope.page = 1;
			$scope.getAllData();
		}
	}, true);
    
    $scope.monthAgo = function(){
		var nowObj = new Date();
		var month_ago_seconds = nowObj.getTime() - 30 * 24 * 60 * 60 * 1000;
		var monthAgoObj = new Date();
		monthAgoObj.setTime(month_ago_seconds);
		var date = moment(monthAgoObj).utc().startOf('day').format('YYYY-MM-DD');
		return date;
	}
	
	$scope.dateSelectRange = {
	    start: moment(new Date('2016-12-01')).utc().startOf('day'),
	    end: moment().utc().startOf('day')
    };
	
	$scope.$watch('dateSelectRange', function(new_val, old_val){
		if(new_val != old_val){
			$scope.init = false;
			$scope.page = 1;
			$scope.getAllData();
		}
	}, true);
	
	$scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.getMerchantGoodsPageData({
			  searchkey: $scope.query,
			  start: $scope.dateSelectRange.start.format('YYYY-MM-DD'),
			  end: $scope.dateSelectRange.end.format('YYYY-MM-DD'),
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage,
			  userid: $scope.userid
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.total.total_all = returnData.data.total_all;
					$scope.total.total_release = returnData.data.total_release;
					$scope.total.total_down = returnData.data.total_down;
					$scope.total.total_recommend = returnData.data.total_recommend;
					$scope.total.total_timebuy = returnData.data.total_timebuy;
				
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '商家商品', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '商家商品', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', '商家商品', '获取数据出错！');
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
	
	$scope.goto_detail = function(item, type) {
	    var start = $scope.dateSelectRange.start.format('YYYY-MM-DD');
	    var end = $scope.dateSelectRange.end.format('YYYY-MM-DD');
	    var status = '';
	    if(type == 'total'){
	    	status = 99;
	    }
	    else if(type == 'release'){
	    	status = 1;
	    }
        else if(type == 'down'){
        	status = 2;
	    }
	    else if(type == 'recommend'){
	    	status = 3;
	    }
	    else if(type == 'time'){
	    	status = 4;
	    }
	    
  	    $window.location.href = "/admin#/app/goodslist?key=" + item.mobile + "&type="+ status +"&start=" + start + "&end=" + end;
    }
}]);
