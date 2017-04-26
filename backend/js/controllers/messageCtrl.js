angular.module('app')

.controller('MessageCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', '$stateParams', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal, $stateParams) {
	$scope.haveData = true;
	$scope.loading = true;
	$scope.query = '';
	$scope.page = 1;
	$scope.totalPage = 1;
	$scope.perPage = 10;
	$scope.dataList = [];
	$scope.init = true;
	$scope.selectAll = false;
	$scope.all_delete_disabled = true;
	$scope.all_delete_html = '删除';
	$scope.submit = false;
	$scope.init_loading = true;
	$scope.radioModel = 99;
	
	if(!angular.isUndefined($stateParams.userid)){
		$scope.userid = $stateParams.userid;
	}
	else{
		$scope.userid = 'empty';
	}
	
    $scope.$watch('radioModel', function(new_val, old_val){
	    if(new_val != old_val){
		    if(!$scope.init_loading){
			    $scope.page = 1;
		        $scope.getAllData();
		    }
	    }
    });
	
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
    
    $scope.$watch('status', function(new_val, old_val){
		if(new_val != old_val){
			$scope.page = 1;
			$scope.getAllData();
		}
	});
	
	$scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.getMessagePageData({
			  searchkey: $scope.query,
			  status: $scope.radioModel,
			  time: $scope.init ? '' : $scope.dateSelectSingle.end.format('YYYY-MM-DD'),
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage,
			  userid: $scope.userid
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
						toaster.pop('success', '短信记录', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '短信记录', returnData.message);
				}
				$scope.loading = false;
				$scope.init_loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  $scope.init_loading = false;
			  toaster.pop('error', '短信记录', '获取数据出错！');
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
	
	$scope.$watch('dataList', function(new_val, old_val){
		  if(new_val != old_val && $scope.dataList.length > 0){
			  $scope.num = 0;
			  $scope.all = 0;
			  angular.forEach($scope.dataList, function(value, key) {
					if(value.selected){
						$scope.num++;
					}
					$scope.all++;
			  });
			  
			  if($scope.num > 0){
				  $scope.all_delete_disabled = false;
			  }
			  else{
				  $scope.all_delete_disabled = true;
			  }
			  
			  if($scope.num == $scope.all){
				  $scope.selectAll = true;
			  }
			  else{
				  $scope.selectAll = false;
			  }
		  }
	}, true); 
	  
	$scope.$watch('selectAll', function(new_val, old_val){
		  if(new_val != old_val && $scope.dataList.length > 0){
			  if(new_val){
				  angular.forEach($scope.dataList, function(value, key) {
					  value.selected = true;
				  });
			  }
			  else{
				  $scope.count = 0;
				  $scope.all = 0;
				  angular.forEach($scope.dataList, function(value, key) {
					  if(value.selected){
						  $scope.count++;
					  }
					  $scope.all++;
				  });
				  if($scope.count == $scope.all){
					  angular.forEach($scope.dataList, function(value, key) {
						  value.selected = false;
					  });
				  }
			  }
		  }
	});
	  
	$scope.deleteBatch = function(){
	   if(confirm("确定要删除吗?") == true){
		  if($scope.submit == false){
			  $scope.submit = true;
			  $scope.idlist = [];
			  angular.forEach($scope.dataList, function(value, key) {
				  if(value.selected == true){
					  $scope.idlist.push(value.id);
				  }
			  });
			  
			  var promise = DataLoad.deleteMessageBatch({
				  idlist: JSON.stringify($scope.idlist),
			  });
			  return promise.then(function (returnData) {
					$scope.all_disagree_disabled = false;
					$scope.all_delete_html = '删除';
					
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '短信记录', returnData.message);
						location.reload();
					} 
					else {
						$scope.submit = false;
						toaster.pop('error', '短信记录', returnData.message);
					}
			  }, function () {
				  $scope.submit = false;
				  $scope.all_disagree_disabled = false;
				  $scope.all_delete_html = '删除';
				  toaster.pop('error', '短信记录', '操作出错！');
			  });
		  }
	   }
	}
}]);
