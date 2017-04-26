'use strict';

app.controller('RoleCtrl', ['$rootScope', '$scope', '$http', '$state', 'toaster', '$timeout', 'DataLoad', '$location', 'divpage', function($rootScope, $scope, $http, $state, toaster, $timeout, DataLoad, $location, divpage) {
    $scope.role = {
    	name: '',
		desc: '',
		privilege_list: [],
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
	$scope.privilegeData = [];
	
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
	
	$scope.getAllPrivilegeData = function() {
		  var promise = DataLoad.getAllPrivilegeData({
			  
		  });
		  return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					if (returnData.data.length != 0) {
						$scope.privilegeData = returnData.data;
						angular.forEach($scope.privilegeData, function(privilege) {
							privilege.readSelected = false;
							privilege.writeSelected = false;
						});
					} else {
						$scope.privilegeData = [];
						toaster.pop('success', ' 角色设置', "没有数据！");
					}
				} 
				else {
					$scope.privilegeData = [];
					toaster.pop('error', ' 角色设置', returnData.message);
				}
		  }, function () {
			  $scope.privilegeData = [];
			  toaster.pop('error', ' 角色设置', ' 获取权限列表出错！');
		  });
    }
	$scope.getAllPrivilegeData();
	
	$scope.searchData = function() {
		  $scope.page = 1;
		  $scope.getAllData();
	}
	
	$scope.getAllData = function() {
		  var promise = DataLoad.getRolePageData({
			  searchkey: $scope.query,
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage
		  });
		  return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						angular.forEach($scope.dataList, function(item, key) {
							var tempArray = JSON.parse(item.privilege_list);
							var privilegeName = '';
							for(var i=0; i<tempArray.length; i++){
								if(privilegeName == ''){
									privilegeName = tempArray[i].page_name;
								}
								else{
									privilegeName += " " + tempArray[i].page_name;
								}
							}
							$scope.dataList[key].privilegeName = privilegeName;
						});
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', ' 角色设置', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', ' 角色设置', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', ' 角色设置', ' 获取数据出错！');
		  });
    }
	  
	$scope.getAllData();
    
    $scope.active = false;
    $scope.submitHtml = '提交';
	$scope.editRole = function() {
		if($scope.role.name == ''){
			toaster.pop('error', '角色设置', "页面名称不能为空！");
			return false;
		}
		if($scope.role.desc == ''){
			toaster.pop('error', '角色设置', "页面URL不能为空！");
			return false;
		}
		
		$scope.role.privilege_list = [];
		angular.forEach($scope.privilegeData, function(privilege) {
			if(privilege.readSelected || privilege.writeSelected){
				var temp = {};
				temp.id = privilege.id;
				temp.page_name = privilege.page_name;
				temp.page_url = privilege.page_url;
				temp.page_state = privilege.page_state;
				//temp.page_desc = privilege.page_desc;
				if(privilege.readSelected){
					privilege.readSelected = 1;
				}
				else{
					privilege.readSelected = 0;
				}
				
				if(privilege.writeSelected){
					privilege.writeSelected = 1;
				}
				else{
					privilege.writeSelected = 0;
				}
				temp.readSelected = privilege.readSelected;
				temp.writeSelected = privilege.writeSelected;
				$scope.role.privilege_list.push(temp);
			}
		});
		
		if($scope.role.privilege_list.length == 0){
			toaster.pop('error', '角色设置', "角色权限不能为空！");
			return false;
		}

		
		if ($scope.active == false) {
			$scope.active = true;
			$scope.submitHtml = '正在提交……';
			var promise = DataLoad.editRole({
				id: $scope.role.isNew ? '' : $scope.role.id,
				name: $scope.role.name,
				desc: $scope.role.desc,
				privilege_list: JSON.stringify($scope.role.privilege_list)
			});
			return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					toaster.pop('success', '角色设置', '修改成功！');
					if($scope.role.isNew == true){
						$scope.role.id = returnData.data;
						$scope.role.isNew = false;
					}
					$scope.role = {
						name: '',
						desc: '',
						privilege_list: [],
						isNew: false
					};
					$scope.editing = false;
					$scope.creating = false;
					$scope.haveData = true;
					$scope.loading = true;
					angular.forEach($scope.privilegeData, function(privilege) {
						privilege.readSelected = false;
						privilege.writeSelected = false;
					});
					$scope.getAllData();
				} 
				else {
					toaster.pop('error', '角色设置', '角色设置出错！');
				}
				$scope.active = false;
				$scope.submitHtml = '提交';
			}, function () {
				$scope.active = false;
				$scope.submitHtml = '提交';
				toaster.pop('error', '角色设置', '未知错误，请刷新重试！');
			});
		}
    }
	
	$scope.createRole = function(){
		$scope.role = {
			name: '',
			desc: '',
			privilege_list: [],
			isNew: true
		};
        $scope.creating = true;
        angular.forEach($scope.privilegeData, function(privilege) {
        	privilege.readSelected = false;
			privilege.writeSelected = false;
		});
    }
	
	$scope.updateRole = function(role){
		$scope.editing = true;
	    $scope.role = angular.copy(role);
		$scope.role.isNew = false;
		$scope.role.privilege_list = JSON.parse($scope.role.privilege_list);
		angular.forEach($scope.privilegeData, function(privilegeValue, privilegeKey) {
			privilegeValue.readSelected = false;
			privilegeValue.writeSelected = false;
		});
		
		angular.forEach($scope.role.privilege_list, function(item, key) {
			angular.forEach($scope.privilegeData, function(privilegeValue, privilegeKey) {
				if(item.id == privilegeValue.id){
					if(item.readSelected == 1){
						privilegeValue.readSelected = true;
					}
					else{
						privilegeValue.readSelected = false;
					}
					
					if(item.writeSelected == 1){
						privilegeValue.writeSelected = true;
					}
					else{
						privilegeValue.writeSelected = false;
					}
				}
			});
		});
	}
	  
	$scope.deleteRole = function(role){
		if(confirm("确定要删除吗?") == true){
			$scope.deleteRoleNow(role);
		}
	}
	
	$scope.deleteRoleNow = function(role){
		var promise = DataLoad.deleteRole({
			id: role.id,
		});
		return promise.then(function (returnData) {

		    if (returnData && returnData.code == 0) {
				toaster.pop('success', '角色设置', returnData.message);
				$scope.getAllData();
			} 
			else {
				toaster.pop('error', '角色设置', returnData.message);
			}
				
		}, function () {
			  toaster.pop('error', '角色设置', ' 删除角色设置出错！');
		});
	}
	
	$scope.changeRead = function(item){
		if(!item.readSelected){
			item.writeSelected = false;
		}
	}
	
	$scope.changeWrite = function(item){
		if(item.writeSelected){
			item.readSelected = true;
		}
	}
	  
	$scope.goods = function(item){
		if(item.page_state == 'app.goodslist' || item.page_state == 'app.category' || item.page_state == 'app.banner' || item.page_state == 'app.article' || item.page_state == 'app.merchantgoods'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.orderlist = function(item){
		if(item.page_state == 'app.orderlist' || item.page_state == 'app.rankorder' || item.page_state == 'app.sharemanager'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.member = function(item){
		if(item.page_state == 'app.userlist' || item.page_state == 'app.upgrade' || item.page_state == 'app.userimport' || item.page_state == 'app.quick' || item.page_state == 'app.center' || item.page_state == 'app.business' || item.page_state == 'app.repeatcompensation'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.financial_detail = function(item){
		if(item.page_state == 'app.declaration' || item.page_state == 'app.cashlist' || item.page_state == 'app.commisionlist' || item.page_state == 'app.exchangelist' || item.page_state == 'app.consumelist' || item.page_state == 'app.useablelist' || item.page_state == 'app.sharelist' || item.page_state == 'app.wallet'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.financial_manager = function(item){
		if(item.page_state == 'app.waitlist' || item.page_state == 'app.takecash' || item.page_state == 'app.repeatmanager'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.statistic = function(item){
		if(item.page_state == 'app.shareholder' || item.page_state == 'app.merchant' || item.page_state == 'app.salerank' || item.page_state == 'app.salegoods' || item.page_state == 'app.consumerank' || item.page_state == 'app.declarationrank' || item.page_state == 'app.memberrank'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.operate = function(item){
		if(item.page_state == 'app.declarationsetting' || item.page_state == 'app.commisionsetting' || item.page_state == 'app.servicesetting' || item.page_state == 'app.logistic' || item.page_state == 'app.ercode'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.plugin = function(item){
		if(item.page_state == 'app.message' || item.page_state == 'app.smstemplate'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.system = function(item){
		if(item.page_state == 'app.system' || item.page_state == 'app.adminsetting' || item.page_state == 'app.department' || item.page_state == 'app.privilege' || item.page_state == 'app.role' || item.page_state == 'app.wechatsetting' || item.page_state == 'app.smssetting' || item.page_state == 'app.log'){
			return true;
		}
		else{
			return false;
		}
	}
	
	$scope.my = function(item){
		if(item.page_state == 'app.personal' || item.page_state == 'app.chgpwd' || item.page_state == 'app.welcome'){
			return true;
		}
		else{
			return false;
		}
	}
}]);