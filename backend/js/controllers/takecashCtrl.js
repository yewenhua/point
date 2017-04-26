angular.module('app')

.controller('TakecashCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', '$stateParams', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal, $stateParams) {
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
	$scope.submit_money = 0;
	$scope.get_money = 0;
	
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
		  var promise = DataLoad.getTakecashPageData({
			  searchkey: $scope.query,
			  status: $scope.radioModel,
			  start: $scope.dateSelectRange.start.format('YYYY-MM-DD'),
			  end: $scope.dateSelectRange.end.format('YYYY-MM-DD'),
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage,
			  userid: $scope.userid
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.submit_money = returnData.data.submit_money;
					$scope.get_money = returnData.data.get_money;
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '提现管理', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.submit_money = 0;
					$scope.get_money = 0;
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '提现管理', returnData.message);
				}
				$scope.loading = false;
				$scope.init_loading = false;
		  }, function () {
			  $scope.submit_money = 0;
			  $scope.get_money = 0;
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  $scope.init_loading = false;
			  toaster.pop('error', '提现管理', '获取数据出错！');
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
			  
			  var promise = DataLoad.deleteTakecashBatch({
				  idlist: JSON.stringify($scope.idlist),
			  });
			  return promise.then(function (returnData) {
					$scope.all_disagree_disabled = false;
					$scope.all_delete_html = '删除';
					
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '提现管理', returnData.message);
						location.reload();
					} 
					else {
						$scope.submit = false;
						toaster.pop('error', '提现管理', returnData.message);
					}
			  }, function () {
				  $scope.submit = false;
				  $scope.all_disagree_disabled = false;
				  $scope.all_delete_html = '删除';
				  toaster.pop('error', '提现管理', '操作出错！');
			  });
		  }
	   }
	}
	
	$scope.open = function (item, size) {
	    var takecashModalInstance = $modal.open({
	        templateUrl: 'takecashModalContent',
	        controller: 'takecashModalInstanceCtrl',
	        size: size,
	        resolve: {
	            item: function () {
	                return item;
	            }
	        }
	    });

	    takecashModalInstance.result.then(function (param) {
	    	if(angular.isDefined(param) && angular.isDefined(param['type']) && param['type'] == 'update'){
	    		$scope.getAllData();
	    	}
	    }, function () {
	        
	    });
	};
}]);


angular.module('app').controller('takecashModalInstanceCtrl', ['$rootScope', '$scope', '$modalInstance', 'toaster', 'item', 'DataLoad', function($rootScope, $scope, $modalInstance, toaster, item, DataLoad) {
	$scope.item = angular.copy(item);
	$scope.submit = false;
	$scope.cancle = function() {
		var param = {type: 'cancel'};
	    $modalInstance.close(param);
    }
	
	$scope.update = function() {
		var param = {type: 'update'};
	    $modalInstance.close(param);
    }
	
	$scope.agree_html = '通过';
	$scope.agree = function(){
		layer.confirm('确定通过吗', {
		    btn: ['确定','取消'], //按钮
		}, function(){
			layer.closeAll('dialog');
		    if($scope.submit == false){
				  $scope.submit = true;
				  var value = 1;
				  $scope.agree_html = '提交中…';
				  var promise = DataLoad.checkTakecash({
					  id: $scope.item.id,
					  status: value
				  });
				  return promise.then(function (returnData) {
						$scope.agree_html = '通过';
						if (returnData && returnData.code == 0) {
							toaster.pop('success', '提现管理', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '提现管理', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.agree_html = '通过';
					  toaster.pop('error', '提现管理', '操作出错！');
				  });
			  }
		}, function(){
			//layer.closeAll('dialog');
		});
	}
	
	$scope.disagree_html = '不通过';
	$scope.disagree = function(){
		layer.confirm('款项金额将退回至用户钱包', {
		    btn: ['确定','取消'], //按钮
		    title: '确定要不通过吗？',
		    icon: 3
		}, function(){
			layer.closeAll('dialog');
		    if($scope.submit == false){
				  $scope.submit = true;
				  var value = 2;
				  $scope.disagree_html = '提交中…';
				  var promise = DataLoad.checkTakecash({
					  id: $scope.item.id,
					  status: value
				  });
				  return promise.then(function (returnData) {
						$scope.disagree_html = '不通过';
						if (returnData && returnData.code == 0) {
							toaster.pop('success', '提现管理', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '提现管理', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.disagree_html = '不通过';
					  toaster.pop('error', '提现管理', '操作出错！');
				  });
			  }
		}, function(){
			//layer.closeAll('dialog');
		});
	}
}]);