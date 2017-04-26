angular.module('app').controller('UserimportCtrl', ['$rootScope', '$scope', 'toaster', '$state', 'DataLoad', 'FileUploader', '$timeout', function($rootScope, $scope, toaster, $state, DataLoad, FileUploader, $timeout) {

	$scope.submitBtnHtml = '提交导入';
	$scope.importDisabled = true;
	$scope.importData = {};
	$scope.importData.url = '';
	  
	$scope.uploadBatch = function() {
		  angular.forEach($scope.uploader.queue, function(item) {
			  $scope.importData.url = 'backend/uploads/' + item.file.save_file_name;
		  });
		  
		  var promise = DataLoad.batchMemberImport({
			  url: $scope.importData.url,
		  });
		  return promise.then(function (returnData) {
			  $scope.submitBtnHtml = '提交导入';
			  $this.removeClass('btn_disable').addClass('btn-success');
			  if (returnData && returnData.code == 0) {
				  toaster.pop('success', '批量导入', returnData.message);
				  $timeout(function(){
					  $state.go('app.userlist');
				  }, 2000);
			  } 
			  else {
				  toaster.pop('error', '批量导入', returnData.message);
			  }
		  }, function () {
			  $scope.submitBtnHtml = '提交导入';
			  $this.removeClass('btn_disable').addClass('btn-success');
			  toaster.pop('error', '批量导入', '批量导入出错！');
		  });
    }
	
	$scope.submitUpload = function(e) {
		  if($scope.uploader.queue.length <= 0){
			  toaster.pop('error', '批量导入', "导入文件不能为空！");
			  return false;
		  }
		  else{
			  if(confirm("确定要导入吗?") == true){
				  $scope.submitBtnHtml = '导入中…';
				  $this = $(e.currentTarget);
				  $this.removeClass('btn-success').addClass('btn_disable');
				  
			      uploader.uploadAll(); 
			  }
		  }
	}
	
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

    // CALLBACKS
    uploader.onAfterAddingFile = function(fileItem) {
        if($scope.uploader.queue.length > 1){
	        $scope.uploader.queue.splice(0, 1);
        }
	    
        angular.forEach($scope.uploader.queue, function(item) {
		    var fileName = item.file.name;
		    if(fileName.indexOf('.xls') === -1 && fileName.indexOf('.xlsx') === -1){
		    	$scope.importDisabled = true;
		    	toaster.pop('error', '批量导入', "导入文件格式必须为EXCEL（xls或xlsx后缀结尾）！");
				return false;
		    }
		    else{
		    	$scope.importDisabled = false;
		    }
	    });
    };
  
    uploader.onCompleteItem = function(fileItem, response, status, headers) {
	    if(response.code == 0){
		    $(".bootstrap-filestyle").children('input').val('');
		    fileItem.file.save_file_name = response.file;
		    $scope.uploadBatch();
	    }
	    else{
		    toaster.pop('error', '批量导入', response.answer);
		    $scope.submitBtnHtml = '提交导入';
			$this.removeClass('btn_disable').addClass('btn-success');
	    }
    };

}]);