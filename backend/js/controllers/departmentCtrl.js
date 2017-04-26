angular.module('app')

.controller('DepartmentCtrl', ['$rootScope', '$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', '$modal', '$log', '$interval', '$timeout', '$document', function($rootScope, $scope, CommonFunction, DataLoad, toaster, divpage, $modal, $log, $interval, $timeout, $document) {
	  $scope.itemData = {
		  name: '',
		  is_lock: '',
		  isNew: true
	  };
      $scope.editing = false;
	  $scope.creating = false;  
	  $scope.haveData = true;
	  $scope.loading = true;
	  $scope.query = '';
	  $scope.page = 1;
	  $scope.totalPage = 1;
	  $scope.perPage = 10;
	  $scope.dataList = [];
	  $scope.originalData = '';
	  $scope.submit = false;
	  $scope.selectAll = false;
	  $scope.all_disagree_disabled = true;
	  $scope.all_disagree_html = '批量删除';
	  $scope.moveable = false;
	  
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
		  $scope.loading = true;
		  var promise = DataLoad.departmentPageData({
			  searchkey: $scope.query,
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage
		  });
		  return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.originalData = returnData.data;
						$scope.dataList = returnData.data.data;
						angular.forEach($scope.dataList, function(value, key) {
							value.agreehtml = '修改';
							value.disagreehtml = '删除';
							value.disabled = false;
							value.selected = false;
						});
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						$scope.originalData = '';
						toaster.pop('error', ' 结算报表', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.originalData = '';
					$scope.haveData = false;
					toaster.pop('error', ' 结算报表', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.originalData = '';
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', ' 结算报表', ' 获取产品出错！');
		  });
      }
	  
	  $scope.getAllData();
	  
	  $scope.createNew = function(){
		  $scope.itemData = {
			  name: '',
			  is_lock: '',
			  isNew: true
		  };
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
	  }
	  
	  $scope.active = false;
      $scope.submitEditItem = function() {
		  if($scope.itemData.name == ''){
				toaster.pop('error', '部门管理', "部门名称不能为空！");
				return false;
		  }
		  if($scope.itemData.sort_id == ''){
				toaster.pop('error', '部门管理', "排序号不能为空！");
				return false;
		  }
			
		  if ($scope.active == false) {
				$scope.active = true;
				var promise = DataLoad.editDepartment({
					id: $scope.itemData.isNew ? '' : $scope.itemData.id,
					name: $scope.itemData.name,
					is_lock: $scope.itemData.is_lock ? 1 : 0
				});
				return promise.then(function (returnData) {

					if (returnData && returnData.code == 0) {
						toaster.pop('success', '部门管理', '修改成功！');
						if($scope.itemData.isNew == true){
							$scope.itemData.id = returnData.data;
							$scope.itemData.isNew = false;
						}
						$scope.itemData = {
							name: '',
							sort_id: '',
							is_lock: false,
							isNew: false
						};
						$scope.editing = false;
						$scope.creating = false;
						$scope.haveData = true;
						$scope.loading = true;
						$scope.getAllData();
					} 
					else {
						toaster.pop('error', '部门管理', '部门管理出错！');
					}
					$scope.active = false;
				}, function () {
					$scope.active = false;
					toaster.pop('error', '部门管理', '未知错误，请刷新重试！');
				});
		  }
	  }

	  $scope.deleteItem = function(item){
		  if(confirm("确定要删除吗?") == true){
			  if($scope.submit == false){
				  $scope.submit = true;
				  item.disagreehtml = '提交中…';
				  item.disabled = true;
				  $scope.deleteItemById(item);
			  }
		  }
	  }
	  
	  $scope.deleteItemById = function(item){
		  var promise = DataLoad.deleteDepartmentById({
			  id: item.id,
		  });
		  return promise.then(function (returnData) {
			    $scope.submit = false;
			    item.agreehtml = '修改';
			    item.disagreehtml = '删除';
			    item.disabled = false;
				if (returnData && returnData.code == 0) {
					toaster.pop('success', '部门管理', returnData.message);
					location.reload();
				} 
				else {
					toaster.pop('error', '部门管理', returnData.message);
				}
				
		  }, function () {
			  $scope.submit = false;
			  item.agreehtml = '修改';
			  item.disagreehtml = '删除';
			  item.disabled = false;
			  toaster.pop('error', '部门管理', ' 操作出错！');
		  });
	  }
	  
	  $scope.deleteBatch = function(){
		  if(confirm("确定要删除吗?") == true){
			  if($scope.submit == false){
				  $scope.submit = true;
				  $scope.all_disagree_disabled = true;
				  $scope.all_disagree_html = '删除中…';
				  $scope.deleteDepartmentBatch();
			  }
		  }
	  }
	  
	  $scope.idlist = [];
	  $scope.deleteDepartmentBatch = function(){
		  $scope.idlist = [];
		  angular.forEach($scope.dataList, function(value, key) {
			  if(value.selected == true){
				  $scope.idlist.push(value.id);
			  }
		  });
		  
		  var promise = DataLoad.deleteDepartmentBatch({
			  idlist: JSON.stringify($scope.idlist),
		  });
		  return promise.then(function (returnData) {
				$scope.all_disagree_disabled = false;
				$scope.all_disagree_html = '删除';
				
				if (returnData && returnData.code == 0) {
					toaster.pop('success', '部门管理', returnData.message);
					location.reload();
				} 
				else {
					$scope.submit = false;
					toaster.pop('error', '部门管理', returnData.message);
				}
				
		  }, function () {
			  $scope.submit = false;
			  $scope.all_disagree_disabled = false;
			  $scope.all_disagree_html = '删除';
			  toaster.pop('error', '部门管理', ' 操作出错！');
		  });
	  }
	  
	  $scope.batchLock = function(){
		  if(confirm("确定要批量锁定吗?") == true){
			  if($scope.submit == false){
				  $scope.submit = true;
				  $scope.all_disagree_disabled = true;
				  $scope.lockDepartmentBatch();
			  }
		  }
	  }
	  
	  $scope.lockDepartmentBatch = function(){
		  $scope.idlist = [];
		  angular.forEach($scope.dataList, function(value, key) {
			  if(value.selected == true){
				  $scope.idlist.push(value.id);
			  }
		  });
		  
		  var promise = DataLoad.lockDepartmentBatch({
			  idlist: JSON.stringify($scope.idlist),
		  });
		  return promise.then(function (returnData) {
				$scope.all_disagree_disabled = false;
				
				if (returnData && returnData.code == 0) {
					toaster.pop('success', '部门管理', returnData.message);
					location.reload();
				} 
				else {
					$scope.submit = false;
					toaster.pop('error', '部门管理', returnData.message);
				}
				
		  }, function () {
			  $scope.submit = false;
			  $scope.all_disagree_disabled = false;
			  toaster.pop('error', '部门管理', '操作出错！');
		  });
	  }
	  
	  $scope.up = function(){
		  $scope.selectSortItem = '';
		  $scope.selectSortIndex = '';
		  angular.forEach($scope.dataList, function(value, key) {
			  if(value.selected){
				  $scope.selectSortItem = angular.copy(value);
				  $scope.selectSortIndex = angular.copy(key);
			  }
		  });
		  
		  if($scope.selectSortItem != '' && $scope.selectSortIndex !== ''){
			  $scope.offset = ($scope.page - 1) * $scope.perPage + $scope.selectSortIndex - 1;
			  if($scope.offset < 0){
				  toaster.pop('error', '部门管理', '没有上一条了');
			  }
			  else{
				  var type ='up';
				  $scope.changeSortEachother($scope.selectSortItem.id, $scope.selectSortItem.sort_id, $scope.offset, type);
			  }
		  }
		  else{
			  toaster.pop('error', '部门管理', '操作出错！');
		  }
	  }
	  
	  $scope.down = function(){
		  $scope.selectSortItem = '';
		  $scope.selectSortIndex = '';
		  angular.forEach($scope.dataList, function(value, key) {
			  if(value.selected){
				  $scope.selectSortItem = angular.copy(value);
				  $scope.selectSortIndex = angular.copy(key);
			  }
		  });
		  
		  if($scope.selectSortItem != '' && $scope.selectSortIndex !== ''){
			  var type ='down';
			  $scope.offset = ($scope.page - 1) * $scope.perPage + $scope.selectSortIndex + 1;
			  $scope.changeSortEachother($scope.selectSortItem.id, $scope.selectSortItem.sort_id, $scope.offset, type);
		  }
		  else{
			  toaster.pop('error', '部门管理', '操作出错！');
		  }
	  }
	  
	  $scope.changeSortEachother = function(id, sort_id, offset, type){
		  var promise = DataLoad.changeSortDep({
			  id: id,
			  sort_id: sort_id,
			  offset: offset,
			  type: type
		  });
		  return promise.then(function (returnData) {
			  if (returnData && returnData.code == 0) {
				  $scope.getAllData();
			  } 
			  else {
			 	  toaster.pop('error', '部门管理', returnData.message);
			  }
			
		  }, function () {
			  toaster.pop('error', '部门管理', '操作出错！');
		  });
	  }
	  
	  $scope.$watch('dataList', function(new_val, old_val){
		  if(new_val != old_val){
			  $scope.num = 0;
			  $scope.all = 0;
			  angular.forEach($scope.dataList, function(value, key) {
					if(value.selected){
						$scope.num++;
					}
					$scope.all++;
			  });
			  
			  if($scope.num > 0){
				  $scope.all_disagree_disabled = false;
				  if($scope.num == 1){
					  $scope.moveable = true;
				  }
				  else{
					  $scope.moveable = false;
				  }
			  }
			  else{
				  $scope.moveable = false;
				  $scope.all_disagree_disabled = true;
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
		  if(new_val != old_val){
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
	
}]);
