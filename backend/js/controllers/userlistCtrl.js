angular.module('app')

.controller('UserlistCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal) {
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
    $scope.radioModel = 99;
    $scope.init_loading = true;
    $scope.is_manager = false;
    $scope.is_company = false;
	
    $scope.$watch('is_manager', function(new_val, old_val){
	    if(new_val != old_val){
	    	$scope.page = 1;
	        $scope.getAllData();
	    }
    });
    
    $scope.$watch('is_company', function(new_val, old_val){
	    if(new_val != old_val){
	    	$scope.page = 1;
	        $scope.getAllData();
	    }
    });
    
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
	
	$scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.getMemberPageData({
			  searchkey: $scope.query,
			  level: $scope.radioModel,
			  manager: $scope.is_manager ? 1 : 0,
			  company: $scope.is_company ? 1 : 0,
			  time: $scope.init ? '' : $scope.dateSelectSingle.end.format('YYYY-MM-DD'),
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						angular.forEach($scope.dataList, function(value, key) {
							if(value.is_manager == 1){
								value.manager = true;
							}
							else{
								value.manager = false;
							}
							
							if(value.is_company == 1){
								value.company = true;
							}
							else{
								value.company = false;
							}
							
							if(value.is_message == 1){
								value.message = true;
							}
							else{
								value.message = false;
							}
						});
						
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '会员列表', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '会员列表', returnData.message);
				}
				$scope.loading = false;
				$scope.init_loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  $scope.init_loading = false;
			  toaster.pop('error', '会员列表', '获取数据出错！');
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
			  
			  var promise = DataLoad.deleteUserBatch({
				  idlist: JSON.stringify($scope.idlist),
			  });
			  return promise.then(function (returnData) {
					$scope.all_disagree_disabled = false;
					$scope.all_delete_html = '删除';
					
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '会员列表', returnData.message);
						location.reload();
					} 
					else {
						$scope.submit = false;
						toaster.pop('error', '会员列表', returnData.message);
					}
			  }, function () {
				  $scope.submit = false;
				  $scope.all_disagree_disabled = false;
				  $scope.all_delete_html = '删除';
				  toaster.pop('error', '会员列表', '操作出错！');
			  });
		  }
	   }
	}
	
	$scope.open = function (item, size) {
	    var userModalInstance = $modal.open({
	        templateUrl: 'userModalContent',
	        controller: 'userModalInstanceCtrl',
	        size: size,
	        resolve: {
	            item: function () {
	                return item;
	            }
	        }
	    });

	    userModalInstance.result.then(function (param) {
	    	if(angular.isDefined(param) && angular.isDefined(param['type']) && param['type'] == 'update'){
	    		$scope.getAllData();
	    	}
	    	else if(angular.isDefined(param) && angular.isDefined(param['type']) && param['type'] == 'student'){
	    		$scope.student(param.item, 'lg');
	    	}
	    }, function () {
	        
	    });
	};
	
	$scope.student = function (item, size) {
	    var studentModalInstance = $modal.open({
	        templateUrl: 'studentModalContent',
	        controller: 'studentModalInstanceCtrl',
	        size: size,
	        resolve: {
	            item: function () {
	                return item;
	            }
	        }
	    });

	    studentModalInstance.result.then(function (param) {
	    	
	    }, function () {
	        
	    });
	};
}]);


angular.module('app').controller('userModalInstanceCtrl', ['$rootScope', '$scope', '$modalInstance', 'toaster', 'item', '$timeout', 'DataLoad', '$window', function($rootScope, $scope, $modalInstance, toaster, item, $timeout, DataLoad, $window) {
	$scope.item = angular.copy(item);
	$scope.item.new_level = '';
	$scope.item.pay_mobile = '';
	$scope.item.pay_consume_rate = '';
	$scope.submit = false;
	$scope.submitBtnHtml = '提交';
	$scope.upgrade = [];
	$scope.item.manager = $scope.item.is_manager == 1 ? true : false;
	$scope.item.company = $scope.item.is_company == 1 ? true : false;
	
	if($scope.item.level == 0 || $scope.item.level == 1 || $scope.item.level == 2){
		$scope.upgrade = [
            {level: 11, name:'初级服务中心'},
  		    {level: 12, name:'中级服务中心'},
  		    {level: 13, name:'高级服务中心'}
  		];
		$scope.item.new_level = 11;
	}
	else if($scope.item.level == 11){
		$scope.upgrade = [
		    {level: 12, name:'中级服务中心'},
		    {level: 13, name:'高级服务中心'}
		];
		$scope.item.new_level = 12;
	}
	else if($scope.item.level == 12){
		$scope.upgrade = [
		    {level: 13, name:'高级服务中心'}
		];
		$scope.item.new_level = 13;
	}
	
	$scope.cancle = function() {
		var param = {type: 'cancel'};
	    $modalInstance.close(param);
    }
	
	$scope.update = function() {
		var param = {type: 'update'};
	    $modalInstance.close(param);
    }
	
	$scope.student = function() {
		var param = {type: 'student', item: $scope.item};
	    $modalInstance.close(param);
    }
	
	$scope.detail = function(param){
		$modalInstance.close(param);
		if(param != 'charge' && param != 'declaration'){
		    $window.location.href = '/admin/#/app/'+ param +'?userid=' + $scope.item.id;
		}
		else if(param == 'charge'){
			$window.location.href = '/admin/#/app/declaration?userid=' + $scope.item.id + '&type=charge';
		}
        else if(param == 'declaration'){
        	$window.location.href = '/admin/#/app/declaration?userid=' + $scope.item.id + '&type=declaration';
		}
	}
	
	$scope.update_html = '提交修改';
	$scope.updateRecommend = function(){
	    if($scope.submit == false){
			  $scope.submit = true;
			  $scope.update_html = '提交中…';
			  var promise = DataLoad.changeRecommend({
				  id: $scope.item.id,
				  mobile: $scope.item.parent_mobile
			  });
			  return promise.then(function (returnData) {
					$scope.update_html = '提交修改';
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '会员管理', returnData.message);
						$scope.update();
					} 
					else {
						$scope.submit = false;
						toaster.pop('error', '会员管理', returnData.message);
					}
			  }, function () {
				  $scope.submit = false;
				  $scope.update_html = '提交修改';
				  toaster.pop('error', '会员管理', '操作出错！');
			  });
		}
	}
	
	$scope.upgrade_html = '提交升级';
	$scope.upgradeLevel = function(){
		if($scope.item.pay_mobile == ''){
			toaster.pop('error', '会员管理', '兑充人手机号码不能为空');
			return false;
		}
		
		if($scope.item.pay_consume_rate != '' && ($scope.item.pay_consume_rate < 0 || $scope.item.pay_consume_rate > 100)){
			toaster.pop('error', '会员管理', '抵扣消费积分比例必须在0-100之间');
			return false;
		}
		
		if(confirm("确定要升级吗?") == true){
		    if($scope.submit == false){
				  $scope.submit = true;
				  $scope.upgrade_html = '提交中…';
				  var promise = DataLoad.upgradeLevel({
					  id: $scope.item.id,
					  old_level: $scope.item.level,
					  new_level: $scope.item.new_level,
					  pay_mobile: $scope.item.pay_mobile,
					  pay_consume_rate: $scope.item.pay_consume_rate != '' ? $scope.item.pay_consume_rate : 0
				  });
				  return promise.then(function (returnData) {
						$scope.upgrade_html = '提交升级';
						if (returnData && returnData.code == 0) {
							toaster.pop('success', '会员管理', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '会员管理', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.upgrade_html = '提交升级';
					  toaster.pop('error', '会员管理', '操作出错！');
				  });
			}
		}
	}
	
	$scope.manager_status = function(item){
		 if(item.manager){
			 var value = 1;
		 }
		 else{
			 var value = 0;
		 }
		 
		 var promise = DataLoad.manager_status({
			 id: item.id,
			 value: value,
		 });
		 return promise.then(function (returnData) {
		     if (returnData && returnData.code == 0) {
				 toaster.pop('success', '会员管理', returnData.message);
				 $timeout(function(){
					 $scope.update();
				 }, 1000);
			 } 
			 else {
				 toaster.pop('error', '会员管理', returnData.message);
			 }
		 }, function () {
			 toaster.pop('error', '会员管理', '修改出错');
		 });
	 }
	
	$scope.company_status = function(item){
		 if(item.company){
			 var value = 1;
		 }
		 else{
			 var value = 0;
		 }
		 
		 var promise = DataLoad.company_status({
			 id: item.id,
			 value: value,
		 });
		 return promise.then(function (returnData) {
		     if (returnData && returnData.code == 0) {
				 toaster.pop('success', '会员管理', returnData.message);
				 $timeout(function(){
					 $scope.update();
				 }, 1000);
			 } 
			 else {
				 toaster.pop('error', '会员管理', returnData.message);
			 }
		 }, function () {
			 toaster.pop('error', '会员管理', '修改出错');
		 });
	 }
	
	 $scope.message_status = function(item){
		 if(item.message){
			 var value = 1;
		 }
		 else{
			 var value = 0;
		 }
		 
		 var promise = DataLoad.message_status({
			 id: item.id,
			 value: value,
		 });
		 return promise.then(function (returnData) {
		     if (returnData && returnData.code == 0) {
				 toaster.pop('success', '会员管理', returnData.message);
				 $timeout(function(){
					 $scope.update();
				 }, 1000);
			 } 
			 else {
				 toaster.pop('error', '会员管理', returnData.message);
			 }
		 }, function () {
			 toaster.pop('error', '会员管理', '修改出错');
		 });
	 }
}]);

angular.module('app').controller('studentModalInstanceCtrl', ['$rootScope', '$scope', '$modalInstance', 'toaster', 'item', '$rootScope', 'DataLoad', 'divpage', function($rootScope, $scope, $modalInstance, toaster, item, $rootScope, DataLoad, divpage) {
	$scope.item = angular.copy(item);	
	$scope.cancle = function() {
	    $modalInstance.close();
    }
	
	$scope.haveData = true;
	$scope.page = 1;
	$scope.totalPage = 1;
	$scope.perPage = 10;
	$scope.dataList = [];
	$scope.loading = true;
	
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
    
	$scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.getStudentPageData({
			  page: $scope.page,
			  num: $scope.perPage,
			  parent_id: $scope.item.id
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#student-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '会员列表', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '会员列表', returnData.message);
				}
				$scope.loading = false;
				$scope.init_loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  $scope.init_loading = false;
			  toaster.pop('error', '会员列表', '获取数据出错！');
		  });
    }
	$scope.getAllData();
}]);