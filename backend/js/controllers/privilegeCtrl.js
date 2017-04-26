'use strict';

app.controller('PrivilegeCtrl', ['$rootScope', '$scope', '$http', '$state', 'toaster', '$timeout', 'DataLoad', '$location', 'divpage', function($rootScope, $scope, $http, $state, toaster, $timeout, DataLoad, $location, divpage) {
    $scope.privilege = {
    	page_name: '',
		page_url: '',
		page_desc: '',
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
	
	$scope.cancel = function(){
		$scope.editing = false;
		$scope.creating = false;
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
	
	$scope.searchData = function() {
		  $scope.page = 1;
		  $scope.getAllData();
	}
	
	$scope.getAllData = function() {
		  var promise = DataLoad.getPrivilegePageData({
			  searchkey: $scope.query,
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage
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
						toaster.pop('success', ' 权限设置', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', ' 权限设置', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', ' 权限设置', ' 获取权限列表出错！');
		  });
    }
	  
	$scope.getAllData();
    
    $scope.active = false;
	$scope.editPrivilege = function() {
		if($scope.privilege.name == ''){
			toaster.pop('error', '权限设置', "页面名称不能为空！");
			return false;
		}
		if($scope.privilege.state == ''){
			toaster.pop('error', '权限设置', "页面URL不能为空！");
			return false;
		}
		if($scope.privilege.desc == ''){
			toaster.pop('error', '权限设置', "页面描述不能为空！");
			return false;
		}
		
		if ($scope.active == false) {
			$scope.active = true;
			var promise = DataLoad.editPrivilege({
				id: $scope.privilege.isNew ? '' : $scope.privilege.id,
				page_name: $scope.privilege.page_name,
				page_url: $scope.privilege.page_url,
				page_desc: $scope.privilege.page_desc
			});
			return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					toaster.pop('success', '权限设置', '修改成功！');
					if($scope.privilege.isNew == true){
						$scope.privilege.id = returnData.data;
						$scope.privilege.isNew = false;
					}
					$scope.privilege = {
						page_name: '',
						page_url: '',
						page_desc: '',
						isNew: false
					};
					$scope.editing = false;
					$scope.creating = false;
					$scope.haveData = true;
					$scope.loading = true;
					$scope.getAllData();
				} 
				else {
					toaster.pop('error', '权限设置', '权限设置出错！');
				}
				$scope.active = false;
			}, function () {
				$scope.active = false;
				toaster.pop('error', '权限设置', '未知错误，请刷新重试！');
			});
		}
    }
	
	$scope.createPrivilege = function(){
		$scope.privilege = {
			page_name: '',
			page_url: '',
			page_desc: '',
			isNew: true
		};
        $scope.creating = true;
    }
	
	$scope.updatePrivilege = function(privilege){
		$scope.editing = true;
	    $scope.privilege = angular.copy(privilege);
		$scope.privilege.isNew = false;
	}
	  
	$scope.deletePrivilege = function(privilege){
		if(confirm("确定要删除吗?") == true){
			$scope.deletePrivilegeNow(privilege);
		}
	}
	
	$scope.deletePrivilegeNow = function(privilege){
		var promise = DataLoad.deletePrivilege({
			id: privilege.id,
		});
		return promise.then(function (returnData) {

		    if (returnData && returnData.code == 0) {
				toaster.pop('success', '权限设置', returnData.message);
				$scope.getAllData();
			} 
			else {
				toaster.pop('error', '权限设置', returnData.message);
			}
				
		}, function () {
			  toaster.pop('error', '权限设置', ' 删除权限设置出错！');
		});
	}
	  
	  
}]);