angular.module('app')

.controller('LogisticCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', '$stateParams', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal, $stateParams) {
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
	$scope.editing = false;
	$scope.creating = false;
	$scope.updateData = {};
	$scope.openData = {};
	$scope.openData.isNew = true;
	$scope.openData.addr_list = [];
    $scope.is_open = true;
	
    $scope.$watch('is_open', function(new_val, old_val){
	    if(new_val != old_val){
	    	$scope.page = 1;
	        $scope.getAllData();
	    }
    });
	
	$scope.itemData = {
	    name: '',
	    is_lock: true,
	    isNew: true,
	    send_list:[]
    };

	$scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.getLogisticPageData({
			  searchkey: $scope.query,
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage,
			  status: $scope.is_open ? 1 : 0
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						angular.forEach($scope.dataList, function(val, key) {
							$scope.dataList[key].send_list = angular.fromJson(val.template);
						});
						
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '邮费设置', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '邮费设置', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', '邮费设置', '获取数据出错！');
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
			  
			  var promise = DataLoad.deleteLogisticBatch({
				  idlist: JSON.stringify($scope.idlist),
			  });
			  return promise.then(function (returnData) {
					$scope.all_disagree_disabled = false;
					$scope.all_delete_html = '删除';
					
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '邮费设置', returnData.message);
						location.reload();
					} 
					else {
						$scope.submit = false;
						toaster.pop('error', '邮费设置', returnData.message);
					}
			  }, function () {
				  $scope.submit = false;
				  $scope.all_disagree_disabled = false;
				  $scope.all_delete_html = '删除';
				  toaster.pop('error', '邮费设置', '操作出错！');
			  });
		  }
	   }
	}
	
	$scope.createNew = function(){
		$scope.itemData = {
		    name: '',
		    is_lock: true,
		    isNew: true,
		    send_list:[
				{
					isDefault: true,
					isNew: false,
					select_all: true,
					first_weight: 1000,
					first_fee: 10,
					other_weight: 1000,
					other_fee: 8,
					addr_list:[
						
					]
				}
		    ]
	    };
		$scope.openData.isNew = true;
		$scope.openData.addr_list = [];
        $scope.creating = true;
    }
	  
    $scope.cancel = function(){
  	    $scope.editing = false;
	    $scope.creating = false;
    }
  
    $scope.updateItem = function(item){
	    $scope.editing = true;
        $scope.itemData = angular.copy(item);
        $scope.itemData.is_lock = $scope.itemData.is_lock == 1 ? true : false;
	    $scope.itemData.isNew = false;
	    $scope.itemData.template = angular.fromJson($scope.itemData.template);
	    $scope.itemData.send_list = $scope.itemData.template;
    }
    
    $scope.updateAddr = function(item, index){
    	$scope.openData = angular.copy(item);
    	$scope.openData.isNew = false;
    	$scope.openData.index = index;
    	$scope.open($scope.openData, 'lg');
    }
    
    $scope.insertAddr = function(){
    	$scope.openData = {};
    	$scope.openData.isDefault = false;
    	$scope.openData.isNew = true;
		$scope.openData.addr_list = [];
		$scope.open($scope.openData, 'lg');
    }
	
	$scope.open = function (item, size) {
	    var logisticModalInstance = $modal.open({
	        templateUrl: 'logisticModalContent',
	        controller: 'logisticModalInstanceCtrl',
	        size: size,
	        resolve: {
	            item: function () {
	                return item;
	            }
	        }
	    });

	    logisticModalInstance.result.then(function (param) {
	    	if(angular.isDefined(param)){
	    		var temp = angular.copy(param['data']);
	    		if(temp.isNew){
	    			temp.isNew = false;
	    			$scope.itemData.send_list.push(temp);
	    		}
	    		else{
	    			angular.forEach($scope.itemData.send_list, function(val, key) {
	    				if(key == temp.index){
	    					$scope.itemData.send_list[key] = temp;
	    				}
	    			});
	    		}
	    	}
	    }, function () {
	        
	    });
	};
	
	$scope.submitItem = function(){
		if($scope.submit == false){
			  $scope.submit = true;
			  
			  var promise = DataLoad.updateLogistic({
				    id: $scope.itemData.isNew ? '' : $scope.itemData.id,
				    name: $scope.itemData.name,
				    is_lock: $scope.itemData.is_lock ? 1 : 0,
				    template: angular.toJson($scope.itemData.send_list),
			  });
			  return promise.then(function (returnData) {
					$scope.all_disagree_disabled = false;
					$scope.all_delete_html = '删除';
					
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '邮费设置', returnData.message);
						$scope.editing = false;
						$scope.creating = false;
						$scope.getAllData();
					} 
					else {
						toaster.pop('error', '邮费设置', returnData.message);
					}
					$scope.submit = false;
			  }, function () {
				  $scope.submit = false;
				  $scope.all_disagree_disabled = false;
				  $scope.all_delete_html = '删除';
				  toaster.pop('error', '邮费设置', '操作出错！');
			  });
	   }
	}
}]);


angular.module('app').controller('logisticModalInstanceCtrl', ['$rootScope', '$scope', '$modalInstance', 'toaster', 'item', '$rootScope', 'DataLoad', function($rootScope, $scope, $modalInstance, toaster, item, $rootScope, DataLoad) {
	$scope.item = angular.copy(item);
	if($scope.item.isNew){
		$scope.item.first_weight = '1000';
		$scope.item.first_fee = '0';
		$scope.item.other_weight = '1000';
		$scope.item.other_fee = '0';
	}
	
	$scope.province_list = [
	    {name: '全国', selected: false}, {name: '北京市', selected: false}, {name: '天津市', selected: false}, 
	    {name: '上海市', selected: false}, {name: '重庆市', selected: false}, {name: '河北省', selected: false}, 
	    {name: '河南省', selected: false}, {name: '云南省', selected: false}, {name: '辽宁省', selected: false}, 
	    {name: '黑龙江省', selected: false}, {name: '湖南省', selected: false}, {name: '安徽省', selected: false}, 
	    {name: '山东省', selected: false}, {name: '新疆', selected: false}, {name: '江苏省', selected: false}, 
    	{name: '浙江省', selected: false}, {name: '江西省', selected: false}, {name: '湖北省', selected: false}, 
    	{name: '广西', selected: false}, {name: '甘肃省', selected: false}, {name: '山西省', selected: false}, 
    	{name: '内蒙古', selected: false}, {name: '陕西省', selected: false}, {name: '吉林省', selected: false}, 
    	{name: '福建省', selected: false}, {name: '贵州省', selected: false}, {name: '广东省', selected: false}, 
    	{name: '青海省',  selected: false}, {name: '西藏', selected: false}, {name: '四川省', selected: false}, 
    	{name: '宁夏', selected: false}, {name: '海南省', selected: false}, {name: '台湾省', selected: false}, 
    	{name: '香港', selected: false}, {name: '澳门', selected: false}
	];
	
	if($scope.item.addr_list.length > 0){
		angular.forEach($scope.province_list, function(outerVal, outerKey) {
			angular.forEach($scope.item.addr_list, function(innerVal, innerKey) {
				if(outerVal.name == innerVal.name){
					outerVal.selected = true;
				}
			});
	    });
	}
	
	if(angular.isUndefined($scope.item.select_all)){
		$scope.item.select_all = false;
	}
	$scope.submit = false;
	$scope.submitBtnHtml = '提交';
	
	if($scope.item.isDefault){
		//默认全国
		$scope.item.select_all = true;
		angular.forEach($scope.province_list, function(outerVal, outerKey) {
			outerVal.selected = true;
		});
	}
	
	$scope.$watch('item.select_all', function(new_val, old_val){
		if(new_val != old_val && $scope.province_list.length > 0){
			  if(new_val){
				  angular.forEach($scope.province_list, function(value, key) {
					  value.selected = true;
				  });
			  }
			  else{
				  $scope.count = 0;
				  $scope.all = 0;
				  angular.forEach($scope.province_list, function(value, key) {
					  if(value.selected){
						  $scope.count++;
					  }
					  $scope.all++;
				  });
				  if($scope.count == $scope.all){
					  angular.forEach($scope.province_list, function(value, key) {
						  value.selected = false;
					  });
				  }
			  }
		}
	});
	
	$scope.$watch('province_list', function(new_val, old_val){
		  if(new_val != old_val && $scope.province_list.length > 0){
			  $scope.num = 0;
			  $scope.all = 0;
			  angular.forEach($scope.province_list, function(value, key) {
					if(value.selected){
						$scope.num++;
					}
					$scope.all++;
			  });
			  
			  if($scope.num == $scope.all){
				  $scope.item.select_all = true;
			  }
			  else{
				  $scope.item.select_all = false;
			  }
		  }
	}, true); 
	
	$scope.addr_list = [];
	$scope.cancle = function() {
		angular.forEach($scope.province_list, function(value, key) {
			if(value.selected){
				$scope.addr_list.push(value);
			}
	    });
		$scope.item.addr_list = angular.copy($scope.addr_list);
		var param = {type: 'cancel', data: $scope.item};
	    $modalInstance.close(param);
    }
}]);
