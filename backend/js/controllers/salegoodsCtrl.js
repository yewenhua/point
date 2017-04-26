angular.module('app')

.controller('SalegoodsCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', '$stateParams', '$window', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal, $stateParams, $window) {
	$scope.haveData = true;
	$scope.loading = true;
	$scope.query = '';
	$scope.page = 1;
	$scope.totalPage = 1;
	$scope.perPage = 20;
	$scope.dataList = [];
	$scope.init = true;
	$scope.submit = false;
	$scope.sort = {
		word: 'num',
        type: 'desc'
	}
	$scope.total = {
		total_num: 0,
		total_exchange_point: 0,
		total_consume_point: 0,
		total_cash: 0,
		total_logistic_fee: 0
	};
	
	$scope.thList = [
	    {
	    	name : '数量', sort : 'fa-sort-desc', selected: true
	    },
	    {
	    	name : '消费积分', sort : 'fa-sort', selected: false
	    },
	    {
	    	name : '购物券', sort : 'fa-sort', selected: false
	    },
	    {
	    	name : '现金', sort : 'fa-sort', selected: false
	    },
	    {
	    	name : '邮费', sort : 'fa-sort', selected: false
	    }
	];
	
	$scope.changeSort = function(item){
		if(item.selected){
			if(item.sort == 'fa-sort-asc'){
				item.sort = 'fa-sort-desc';
				$scope.sort.type = 'desc';
			}
			else{
				item.sort = 'fa-sort-asc';
				$scope.sort.type = 'asc';
			}
		}
		else{
			item.selected = true;
			item.sort = 'fa-sort-desc';
			$scope.sort.type = 'desc';
			angular.forEach($scope.thList, function(value, key) {
				if(item.name != value.name){
					value.selected = false;
					value.sort = 'fa-sort';
				}
		    });
		}
		
		if(item.name == '数量'){
		    $scope.sort.word = 'num';
		}
		else if(item.name == '消费积分'){
		    $scope.sort.word = 'consume_point';
		}
		else if(item.name == '购物券'){
		    $scope.sort.word = 'exchange_point';
		}
		else if(item.name == '现金'){
		    $scope.sort.word = 'total_cash_price';
		}
		else if(item.name == '邮费'){
		    $scope.sort.word = 'logistic_fee';
		}
		else{
			$scope.sort.word = 'num';
		}
	}
	
	$scope.$watch('sort', function(new_val, old_val){
		if(new_val != old_val){
			$scope.page = 1;
			$scope.getAllData();
		}
	}, true);
	
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
	    start: moment($scope.monthAgo()).utc().startOf('day'),
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
		  var promise = DataLoad.getGoodsStatisticPageData({
			  searchkey: $scope.query,
			  start: $scope.dateSelectRange.start.format('YYYY-MM-DD'),
			  end: $scope.dateSelectRange.end.format('YYYY-MM-DD'),
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage,
			  userid: $scope.userid,
			  sort_word: $scope.sort.word,
			  sort_type: $scope.sort.type
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.total.total_num = returnData.total.total_num;
					$scope.total.total_exchange_point = returnData.total.total_exchange_point;
					$scope.total.total_consume_point = returnData.total.total_consume_point;
					$scope.total.total_cash = returnData.total.total_cash;
					$scope.total.total_logistic_fee = returnData.total.total_logistic_fee;
				
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '销量排行', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '销量排行', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', '销量排行', '获取数据出错！');
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
	
	$scope.detail = function(item) {
	    var start = $scope.dateSelectRange.start.format('YYYY-MM-DD');
	    var end = $scope.dateSelectRange.end.format('YYYY-MM-DD');
  	    //$window.location.href = "/admin#/app/orderlist?key=" + item.user_id + "&status=3&start=" + start + "&end=" + end + "&type=merchant";
    }
}]);
