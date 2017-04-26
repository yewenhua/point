angular.module('app')

.controller('OrderlistCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', '$stateParams', function($scope, CommonFunction, DataLoad, toaster, divpage, $modal, $stateParams) {
	$scope.haveData = true;
	$scope.loading = true;
	$scope.query = '';
	$scope.page = 1;
	$scope.totalPage = 1;
	$scope.perPage = 10;
	$scope.dataList = [];
	$scope.init = true;
	$scope.init_loading = true;
	$scope.selectAll = false;
	$scope.all_delete_disabled = true;
	$scope.all_delete_html = '删除';
	$scope.submit = false;
	$scope.radioModel = 99;
	
	if(!angular.isUndefined($stateParams.status)){
		$scope.radioModel = Number($stateParams.status);
	}
	else{
		$scope.radioModel = 99;
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
	
    if(!angular.isUndefined($stateParams.start) && !angular.isUndefined($stateParams.end)){
    	$scope.dateSelectRange = {
		    start: moment(new Date($stateParams.start)).utc().startOf('day'),
		    end: moment(new Date($stateParams.end)).utc().startOf('day')
	    };
    }
    else{
		$scope.dateSelectRange = {
		    start: moment($scope.monthAgo()).utc().startOf('day'),
		    end: moment().utc().startOf('day')
	    };
    }
    
    if(!angular.isUndefined($stateParams.comid)){
		$scope.comid = $stateParams.comid;
		$scope.type = $stateParams.type;
	}
	else{
		$scope.comid = 'empty';
		$scope.type = 'empty';
	}
	
	$scope.$watch('dateSelectRange', function(new_val, old_val){
		if(new_val != old_val){
			$scope.init = false;
			$scope.page = 1;
			$scope.getAllData();
		}
	}, true);
	
	$scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.getOrderPageData({
			  searchkey: $scope.query,
			  status: $scope.radioModel,
			  start: $scope.dateSelectRange.start.format('YYYY-MM-DD'),
			  end: $scope.dateSelectRange.end.format('YYYY-MM-DD'),
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage,
			  comid: $scope.comid,
			  type: $scope.type
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						angular.forEach($scope.dataList, function(value, key) {
							value.face_url = '/backend/uploads/' + value.goods_face;
					    });
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '订单列表', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '订单列表', returnData.message);
				}
				$scope.loading = false;
				$scope.init_loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', '订单列表', '获取数据出错！');
			  $scope.init_loading = false;
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
			  
			  var promise = DataLoad.deleteOrderBatch({
				  idlist: JSON.stringify($scope.idlist),
			  });
			  return promise.then(function (returnData) {
					$scope.all_disagree_disabled = false;
					$scope.all_delete_html = '删除';
					
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '订单列表', returnData.message);
						location.reload();
					} 
					else {
						$scope.submit = false;
						toaster.pop('error', '订单列表', returnData.message);
					}
			  }, function () {
				  $scope.submit = false;
				  $scope.all_disagree_disabled = false;
				  $scope.all_delete_html = '删除';
				  toaster.pop('error', '订单列表', '操作出错！');
			  });
		  }
	   }
	}
	
	$scope.statusClass = function(status){
		if(status == 0){
			return 'text-danger';
		}
		else if(status == 1){
			return 'text-warning';
		}
		else if(status == 2){
			return 'text-info';
		}
		else if(status == 3){
			return 'text-success';
		}
		else if(status == 4){
			return 'text-primary';
		}
		else if(status == 5){
			return 'text-seven';
		}
		else if(status == 6){
			return 'text-ten';
		}
		else if(status == 7){
			return 'text-eleven';
		}
		else{
			return 'text-default';
		}
	}
	
	$scope.todo = function (item, size) {
	    var lookModalInstance = $modal.open({
	        templateUrl: 'todoModalContent',
	        controller: 'todoModalInstanceCtrl',
	        size: size,
	        resolve: {
	            item: function () {
	                return item;
	            }
	        }
	    });

	    lookModalInstance.result.then(function (param) {
	    	if(angular.isDefined(param) && angular.isDefined(param['type']) && param['type'] == 'update'){
	    		$scope.getAllData();
	    	}
	    }, function () {
	        
	    });
	};
}]);


angular.module('app').controller('todoModalInstanceCtrl', ['$rootScope', '$scope', '$modalInstance', 'toaster', 'item', '$rootScope', 'DataLoad', function($rootScope, $scope, $modalInstance, toaster, item, $rootScope, DataLoad) {
	$scope.item = angular.copy(item);
	$scope.item.order_address = angular.copy(JSON.parse(item.order_address));
	if($scope.item.status == 1){
		$scope.item.order_address.logisticName = '';
		$scope.item.order_address.logisticNo = '';
		$scope.item.order_address.memo = '';
	}
	$scope.submit = false;
	$scope.submitBtnHtml = '提交';
	
	$scope.cancle = function() {
		var param = {type: 'cancel'};
	    $modalInstance.close(param);
    }
	
	$scope.update = function() {
		var param = {type: 'update'};
	    $modalInstance.close(param);
    }
	
	$scope.close_html = '关闭订单';
	$scope.close = function(){
		layer.confirm('待发货或者已发货的订单关闭，将会退回用户所用掉的积分和现金！', {
		    btn: ['确定','取消'], //按钮
		    title: '确定要关闭吗？',
		    icon: 3
		}, function(){
		    //layer.msg('正在关闭中…', {time: 1000});
			layer.closeAll('dialog');
		    if($scope.submit == false){
				  $scope.submit = true;
				  var value = 4;
				  $scope.close_html = '关闭订单中…';
				  var promise = DataLoad.changeOrderStatus({
					  id: $scope.item.id,
					  status: value
				  });
				  return promise.then(function (returnData) {
						$scope.close_html = '关闭订单';
						if (returnData && returnData.code == 0) {
							toaster.pop('success', '订单', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '订单', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.close_html = '关闭订单';
					  toaster.pop('error', '订单', '操作出错！');
				  });
			  }
		}, function(){
			//layer.closeAll('dialog');
		});
	}
	
	$scope.sure_html = '确认订单';
	$scope.sure = function(){
		layer.confirm('确定要确认订单吗？', {
		    btn: ['确定','取消'], //按钮
	    }, function(){
			 layer.closeAll('dialog');
			 if($scope.submit == false){
				  $scope.submit = true;
				  var value = 3;
				  $scope.sure_html = '确认订单中…';
				  var promise = DataLoad.changeOrderStatus({
					  id: $scope.item.id,
					  status: value
				  });
				  return promise.then(function (returnData) {
						$scope.sure_html = '确认订单';
						if (returnData && returnData.code == 0) {
							toaster.pop('success', '订单', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '订单', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.sure_html = '确认订单';
					  toaster.pop('error', '订单', '操作出错！');
				  });
			}
	    }, function(){
			//layer.closeAll('dialog');
		});
	}
	
	$scope.send_html = '发货';
	$scope.send = function(){
	   if(!$scope.item.order_address.logisticName){
		   toaster.pop('error', '订单', '物流公司不能为空');
		   return false;
	   }
	   
	   if(!$scope.item.order_address.logisticNo){
		   toaster.pop('error', '订单', '物流单号不能为空');
		   return false;
	   }
	   
	   //修改收货人信息
	   if(!$scope.item.order_address.userName){
		   toaster.pop('error', '订单', '收货人不能为空');
		   return false;
	   }
	   
	   if(!$scope.item.order_address.telNumber){
		   toaster.pop('error', '订单', '手机号不能为空');
		   return false;
	   }
	   
	   if(!$scope.item.order_address.detailInfo){
		   toaster.pop('error', '订单', '地址不能为空');
		   return false;
	   }
		
	   layer.confirm('确定要发货吗？', {
		    btn: ['确定','取消'], //按钮
	   }, function(){
			layer.closeAll('dialog');
			if($scope.submit == false){
				  $scope.submit = true;
				  var value = 2;
				  $scope.send_html = '提交发货中…';
				  var promise = DataLoad.submitLogisticsInfo({
					  id: $scope.item.id,
					  order_id: $scope.item.order_id,
					  order_address: angular.toJson($scope.item.order_address)
				  });
				  return promise.then(function (returnData) {
						$scope.send_html = '发货';
						if (returnData && returnData.code == 0) {
							$rootScope.diffNum_no_review--;
							toaster.pop('success', '订单', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '订单', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.send_html = '发货';
					  toaster.pop('error', '订单', '操作出错！');
				  });
			}
		}, function(){
			//layer.closeAll('dialog');
		});
	 }
	
	$scope.modify_html = '修改';
	$scope.modify = function(){
	   if($scope.item.status == 2){
		   if(!$scope.item.order_address.logisticName){
			   toaster.pop('error', '订单', '物流公司不能为空');
			   return false;
		   }
		   
		   if(!$scope.item.order_address.logisticNo){
			   toaster.pop('error', '订单', '物流单号不能为空');
			   return false;
		   }
	   }
	   else if($scope.item.status == 1){
		   if(!$scope.item.order_address.userName){
			   toaster.pop('error', '订单', '收货人不能为空');
			   return false;
		   }
		   
		   if(!$scope.item.order_address.telNumber){
			   toaster.pop('error', '订单', '电话号码不能为空');
			   return false;
		   }
		   
		   if(!$scope.item.order_address.detailInfo){
			   toaster.pop('error', '订单', '地址不能为空');
			   return false;
		   }
	   }
		
	   layer.confirm('确定要修改发货信息吗？', {
		    btn: ['确定','取消'], //按钮
	   }, function(){
			layer.closeAll('dialog');
			if($scope.submit == false){
				  $scope.submit = true;
				  $scope.modify_html = '提交修改中…';
				  var promise = DataLoad.modifyLogisticsInfo({
					  id: $scope.item.id,
					  order_id: $scope.item.order_id,
					  order_address: angular.toJson($scope.item.order_address)
				  });
				  return promise.then(function (returnData) {
						$scope.modify_html = '修改';
						if (returnData && returnData.code == 0) {
							toaster.pop('success', '订单', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '订单', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.modify_html = '修改';
					  toaster.pop('error', '订单', '操作出错！');
				  });
			}
		}, function(){
			//layer.closeAll('dialog');
		});
	}
	
	$scope.refund_html = '退款';
	$scope.refund = function(){
		layer.confirm('确定要退款吗？', {
		    btn: ['确定','取消'], //按钮
	    }, function(){
			 layer.closeAll('dialog');
			 if($scope.submit == false){
				  $scope.submit = true;
				  $scope.refund_html = '退款提交中…';
				  var promise = DataLoad.submitRefundOrder({
					  id: $scope.item.id,
					  order_id: $scope.item.order_id
				  });
				  return promise.then(function (returnData) {
						$scope.refund_html = '退款';
						if (returnData && returnData.code == 0) {
							toaster.pop('success', '订单', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '订单', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.refund_html = '退款';
					  toaster.pop('error', '订单', '操作出错！');
				  });
			}
	    }, function(){
			//layer.closeAll('dialog');
		});
	}
	
	$scope.agree_html = '同意退款';
	$scope.disagree_html = '拒绝退款';
	$scope.check = function(type){
		if(type == 'agree'){
			var title = '确定要同意退款吗？';
		}
		else{
			var title = '确定要拒绝退款吗？';
		}
		
		layer.confirm(title, {
		    btn: ['确定','取消'], //按钮
	    }, function(){
			 layer.closeAll('dialog');
			 if($scope.submit == false){
				  $scope.submit = true;
				  if(type == 'agree'){
				      $scope.agree_html = '提交中…';
				  }
				  else{
					  $scope.disagree_html = '提交中…';
				  }
				  var promise = DataLoad.checkRefundOrder({
					  id: $scope.item.id,
					  order_id: $scope.item.order_id,
					  type: type
				  });
				  return promise.then(function (returnData) {
					    $scope.agree_html = '同意退款';
				        $scope.disagree_html = '拒绝退款';
					    
						if (returnData && returnData.code == 0) {
							$rootScope.diffNum_no_refund--;
							toaster.pop('success', '订单', returnData.message);
							$scope.update();
						} 
						else {
							$scope.submit = false;
							toaster.pop('error', '订单', returnData.message);
						}
				  }, function () {
					  $scope.submit = false;
					  $scope.agree_html = '同意退款';
				      $scope.disagree_html = '拒绝退款';
					  toaster.pop('error', '订单', '操作出错！');
				  });
			}
	    }, function(){
			//layer.closeAll('dialog');
		});
	}
}]);