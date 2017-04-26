angular.module('app')

.controller('RankorderCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', '$stateParams', '$window', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal, $stateParams, $window) {
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
		total_order: 0,
		total_wait_send: 0,
		total_already_send: 0,
		total_completed: 0,
		total_closed: 0,
		total_cash: 0,
		total_refunded: 0
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
		  var promise = DataLoad.getMerchantOrderPageData({
			  searchkey: $scope.query,
			  start: $scope.dateSelectRange.start.format('YYYY-MM-DD'),
			  end: $scope.dateSelectRange.end.format('YYYY-MM-DD'),
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage,
			  userid: $scope.userid
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.total.total_order = returnData.data.total_order;
					$scope.total.total_wait_send = returnData.data.total_wait_send;
					$scope.total.total_already_send = returnData.data.total_already_send;
					$scope.total.total_completed = returnData.data.total_completed;
					$scope.total.total_closed = returnData.data.total_closed;
					$scope.total.total_refunded = returnData.data.total_refunded;
				
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '商家订单', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '商家订单', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', '商家订单', '获取数据出错！');
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
	    else if(type == 'wait_send'){
		    status = 1;
	    }
        else if(type == 'already_send'){
    	    status = 2;
	    }
	    else if(type == 'completed'){
		    status = 3;
	    }
	    else if(type == 'closed'){
		    status = 4;
	    }
	    else if(type == 'refunded'){
		    status = 6;
	    }
  	    $window.location.href = "/admin#/app/orderlist?comid=" + item.uid + "&status="+ status +"&start=" + start + "&end=" + end;
    }
}]);
