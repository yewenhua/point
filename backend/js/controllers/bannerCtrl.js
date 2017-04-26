angular.module('app')

.controller('BannerCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'FileUploader', function($scope, CommonFunction, DataLoad, toaster, FileUploader) {
	  $scope.bannerList = [];
	  $scope.editing = false;
	  $scope.banner = {};
	  $scope.banner.name = '';
	  $scope.banner.url = '';
	  $scope.banner.order_id = '';
	  $scope.banner.link = '';
	  $scope.banner.isNew = false;
	  $scope.haveData = false;
	  $scope.loading = true;
	  
	  var uploader = $scope.uploader = new FileUploader({
	        url: '/backend/upload_soft.php'
	  });

	  // FILTERS
	  uploader.filters.push({
	       name: 'customFilter',
	       fn: function(item /*{File|FileLikeObject}*/, options) {
	            return this.queue.length < 10;
	       }
	  });

	  uploader.onAfterAddingFile = function(fileItem) {
	      if($scope.uploader.queue.length > 1){
		      $scope.uploader.queue.splice(0, 1);
		  }
	  };

	  uploader.onCompleteItem = function(fileItem, response, status, headers) {
		  if(response.code == 0){
			  $(".bootstrap-filestyle").children('input').val('');
			  fileItem.file.url = '/backend/uploads/' + response.file;
			  $scope.editBanner();
		  }
		  else{
			  $scope.submitActive = false;
			  toaster.pop('error', '广告管理', response.answer);
		  }
	  };
	  
	  $scope.add = function() {
		  $scope.editing = true;
		  $scope.banner = {};
		  $scope.banner.name = '';
		  $scope.banner.url = '';
		  $scope.banner.order_id = '';
		  $scope.banner.link = '';
		  $scope.banner.isNew = true;
		  uploader.clearQueue();
	  }
	  
	  $scope.update = function(item){
		  $scope.editing = true;
		  uploader.clearQueue();
		  $scope.banner = angular.copy(item);
		  $scope.banner.isNew = false;
	  }
	  
	  $scope.deleteBanner = function(item){
		  if(confirm("确定要删除吗?") == true){
			  var promise = DataLoad.deleteBanner({
				  id: item.id,
			  });
			  return promise.then(function (returnData) {

					if (returnData && returnData.code == 0) {
						toaster.pop('success', '广告管理', returnData.message);
						$scope.getAllData();
					} 
					else {
						toaster.pop('error', '广告管理', returnData.message);
					}
					
			  }, function () {
				  toaster.pop('error', '广告管理', ' 删除图片出错！');
			  });
		  }
	  }
	  
	  $scope.cancle = function(){
		  $scope.editing = false;
	  }
	  
	  $scope.submitActive = false;
	  $scope.submitUpload = function() {
		  if($scope.submitActive == false){
			  if($scope.banner.name == ''){
				  toaster.pop('error', '广告管理', "轮播名称不能为空！");
				  return false;
			  }
			  if($scope.banner.link == ''){
				  toaster.pop('error', '广告管理', "链接不能为空！");
				  return false;
			  }
			  
			  $scope.submitActive = true;
			  if($scope.uploader.queue.length <= 0 && !$scope.banner.isNew){
				  $scope.editBanner();
			  }
			  else{
				  uploader.uploadAll(); 
			  }
		  }
	  }
	  
	  $scope.editBanner = function() {
		  if($scope.banner.isNew){
			  if($scope.uploader.queue.length <= 0){
				  $scope.submitActive = false;
				  toaster.pop('error', '广告管理', "图片不能为空！");
				  return false;
			  }
			  else{
				  angular.forEach($scope.uploader.queue, function(item) {
					  $scope.banner.url = item.file.url;
				  });
			  }
		  }
		  else{
			  if($scope.uploader.queue.length <= 0){
				  $scope.banner.url = '';
			  }
			  else{
				  angular.forEach($scope.uploader.queue, function(item) {
					  $scope.banner.url = item.file.url;
				  });
			  }
		  }
		  
		  var promise = DataLoad.editBanner({
			  id: $scope.banner.isNew ? '' : $scope.banner.id,
			  name: $scope.banner.name,
			  url: $scope.banner.url,
			  order_id: $scope.banner.order_id,
			  link: $scope.banner.link
		  });
		  return promise.then(function (returnData) {
			  $scope.submitActive = false;
			  if (returnData && returnData.code == 0) {
				  toaster.pop('success', '广告管理', "保存成功！");
				  $scope.editing = false;
				  $scope.getAllData();
			  } 
			  else {
				  toaster.pop('error', '广告管理', returnData.message);
			  }
		  }, function () {
			  $scope.submitActive = false;
			  toaster.pop('error', '广告管理', ' 保存广告出错！');
		  });
      }
	  
	  $scope.getAllData = function() {
		  $scope.loading = true;
		  var promise = DataLoad.bannerAllData({
				
		  });
		  return promise.then(function (returnData) {

				if (returnData && returnData.code == 0) {
					if (returnData.data.length != 0) {
						$scope.haveData = true;
						$scope.bannerList = returnData.data;
						angular.forEach($scope.bannerList, function(item) {
					        item.selected = false;
					    });
					}
					else{
						$scope.haveData = false;
						$scope.bannerList = [];
					}
				} 
				else {
					$scope.haveData = false;
					toaster.pop('error', '广告管理', '没有数据');
				}
				$scope.loading = false;
		  }, function () {
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', '广告管理', '获取出错！');
		  });
      }
	  
	  $scope.getAllData();
}]);