'use strict';

app.controller('AdminSettingCtrl', ['$rootScope', '$scope', '$http', '$state', 'toaster', '$timeout', 'DataLoad', '$location', 'divpage', 'CommonFunction', function($rootScope, $scope, $http, $state, toaster, $timeout, DataLoad, $location, divpage, CommonFunction) {
	$scope.roleData = [];
	$scope.admin = {};
	$scope.admin.name = '';
	$scope.admin.dep_id = '';
	$scope.admin.mobile = '';
	$scope.admin.password = '';
	$scope.admin.role_id = '';
	$scope.admin.is_lock = false;
	$scope.admin.isNew = true;
	$scope.editing = false;
	$scope.creating = false;
	$scope.haveData = true;
	$scope.loading = true;
	$scope.query = '';
	$scope.page = 1;
	$scope.totalPage = 1;
	$scope.perPage = 10;
	$scope.dataList = [];
	$scope.departmentList = [];
	$scope.departmentLoading = true;
	$scope.nextId = '';
	
	$scope.getAllRoleData = function() {
		  var promise = DataLoad.getAllRoleData({
			  
		  });
		  return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					if (returnData.data.length != 0) {
						$scope.roleData = returnData.data;
					} else {
						$scope.roleData = [];
						toaster.pop('success', '管理员设置', "没有数据！");
					}
				} 
				else {
					$scope.roleData = [];
					toaster.pop('error', '管理员设置', returnData.message);
				}
		  }, function () {
			  $scope.roleData = [];
			  toaster.pop('error', '管理员设置', ' 获取角色列表出错！');
		  });
    }
	
	$scope.getAllRoleData();
	
	$scope.searchData = function() {
		  $scope.page = 1;
		  $scope.getAllData();
	}
	
	$scope.getAllData = function() {
		  var promise = DataLoad.getAdminPageData({
			  searchkey: $scope.query,
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						$scope.nextId = Number(returnData.last.id) + 1;
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', ' 管理员设置', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', ' 管理员设置', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', ' 管理员设置', ' 获取数据出错！');
		  });
    }
	  
	$scope.getAllData();
	
	$scope.departmentAllData = function() {
		  $scope.departmentLoading = true;
		  var promise = DataLoad.departmentAllData({

		  });
		  return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					if (returnData.data.length != 0) {
						$scope.departmentLoading = false;
						$scope.departmentList = returnData.data;
						angular.forEach($scope.departmentList, function(value, key) {
							value.selected = false;
						});
					} else {
						$scope.departmentList = [];
						toaster.pop('success', ' 管理员设置', "没有数据！");
					}
				} 
				else {
					$scope.departmentList = [];
					toaster.pop('error', ' 管理员设置', returnData.message);
				}
		  }, function () {
			  $scope.departmentList = [];
			  toaster.pop('error', ' 管理员设置', ' 获取数据出错！');
		  });
    }
	$scope.departmentAllData();
	
	$scope.active = false;
	$scope.editAdmin = function() {
		/*
		if($scope.admin.name == ''){
			toaster.pop('error', '管理员设置', "名称不能为空！");
			return false;
		}
		if($scope.admin.mobile == ''){
			toaster.pop('error', '管理员设置', "手机不能为空！");
			return false;
		}
		if($scope.admin.isNew == true && $scope.admin.password == ''){
			toaster.pop('error', '管理员设置', "密码不能为空！");
			return false;
		}
		*/
		if($scope.admin.role_id == ''){
			toaster.pop('error', '管理员设置', "角色不能为空！");
			return false;
		}
		
		var depIdList = '';
		angular.forEach($scope.departmentList, function(value, key) {
			if(value.selected){
				if(depIdList == ''){
					depIdList = value.id;
				}
				else{
					depIdList += ',' + value.id;
				}
			}
		});
		
		if(depIdList == ''){
			toaster.pop('error', '管理员设置', "管辖部门不能为空！");
			return false;
		}
		else{
			$scope.admin.dep_id = depIdList;
		}
		
		if ($scope.active == false) {
			$scope.active = true;
			var promise = DataLoad.editAdmin({
				id: $scope.admin.isNew ? '' : $scope.admin.id,
				name: $scope.admin.name,
				mobile: $scope.admin.mobile,
				password: $scope.admin.password,
				role_id: $scope.admin.role_id,
				dep_id: $scope.admin.dep_id,
				is_lock: $scope.admin.is_lock ? 1 : 0
			});
			return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					toaster.pop('success', '管理员设置', '保存成功！');
					if($scope.admin.isNew == true){
						$scope.admin.id = returnData.data;
						$scope.admin.isNew = false;
					}
					$scope.admin = {
						name: '',
						mobile: '',
						password: '',
						role_id: '',
						dep_id: '',
						is_lock : false,
						isNew: false
					};
					$scope.editing = false;
					$scope.creating = false;
					$scope.haveData = true;
					$scope.loading = true;

					$scope.getAllData();
				} 
				else {
					toaster.pop('error', '管理员设置', '管理员生成出错！');
				}
				$scope.active = false;
			}, function () {
				$scope.active = false;
				toaster.pop('error', '管理员设置', '未知错误，请刷新重试！');
			});
		}
    }
	
	$scope.createAdmin = function(){
		$scope.admin = {
			name: '',
			mobile: '',
			password: '',
			role_id: '',
			dep_id: '',
			is_lock: false,
			isNew: true
		};
        $scope.creating = true;
        angular.forEach($scope.departmentList, function(value, key) {
			value.selected = false;
		});
    }
	
	$scope.cancel = function(){
		$scope.editing = false;
		$scope.creating = false;
	}
	
	$scope.updateAdmin = function(admin){
		$scope.editing = true;
	    $scope.admin = angular.copy(admin);
		$scope.admin.isNew = false;
		$scope.admin.password = '';
		$scope.admin.is_lock = $scope.admin.is_lock == 1 ? true : false;
		
		var depIdList = [];
		if($scope.admin.dep_id && $scope.admin.dep_id.indexOf(',') !== -1){
			//多个部门
			depIdList = $scope.admin.dep_id.split(',');
		}
		else if($scope.admin.dep_id && $scope.admin.dep_id.indexOf(',') === -1){
			//一个部门
			depIdList = [$scope.admin.dep_id];
		}
		angular.forEach($scope.departmentList, function(value, key) {
			if(depIdList.length > 0){
				if(CommonFunction.isInArray(depIdList, value.id)){
					value.selected = true;
				}
				else{
					value.selected = false;
				}
			}
			else{
			    value.selected = false;
			}
		});
	}
	  
	$scope.deleteAdmin = function(admin){
		if(confirm("确定要删除吗?") == true){
			$scope.deleteAdminNow(admin);
		}
	}
	
	$scope.deleteAdminNow = function(admin){
		var promise = DataLoad.deleteAdmin({
			id: admin.id,
		});
		return promise.then(function (returnData) {

		    if (returnData && returnData.code == 0) {
				toaster.pop('success', '管理员设置', returnData.message);
				$scope.getAllData();
			} 
			else {
				toaster.pop('error', '管理员设置', returnData.message);
			}
				
		}, function () {
			  toaster.pop('error', '管理员设置', ' 删除管理员设置出错！');
		});
	}
	
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
	 
}]);