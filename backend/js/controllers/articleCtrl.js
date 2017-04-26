app.controller('ArticleCtrl', ['$scope', '$state', '$timeout', 'toaster', 'DataLoad', 'divpage', 'FileUploader', function($scope, $state, $timeout, toaster, DataLoad, divpage, FileUploader) {
	$scope.dataList = [];
	$scope.branch = null;
	$scope.article = {};
	$scope.article.title = '';
	$scope.article.content = '<p>请输入内容！</p>';
	$scope.article.abstracts = '';
	$scope.article.img_url = '';
	$scope.article.isNew = false;
	$scope.haveData = true;
	$scope.editing = false;
	$scope.loading = true;
	$scope.query = '';
	$scope.page = 1;
    $scope.totalPage = 1;
    $scope.perPage = 10;
    $scope.init = true;
    $scope.all_delete_disabled = true;
    $scope.selectAll = false;
    $scope.all_delete_html = '删除';
    $scope.moveable = false;
    $scope.submit_html = '提交';
    $scope.submiting = false;
    $scope.data = {}; 
	$scope.data.bgImage = '';
	$scope.data.bg_url = '';
    
    $scope.monthAgo = function(){
		var nowObj = new Date();
		var month_ago_seconds = nowObj.getTime() - 30 * 24 * 60 * 60 * 1000;
		var monthAgoObj = new Date();
		monthAgoObj.setTime(month_ago_seconds);
		var date = moment(monthAgoObj).utc().startOf('day').format('YYYY-MM-DD');
		return date;
	}
	
    $scope.createArticle = function(){
    	$scope.editing = true;
    	$scope.article = {};
    	$scope.article.title = '';
    	$scope.article.content = '<p>请输入内容！</p>';
    	$scope.article.abstracts = '';
    	$scope.article.isNew = true;
    	$scope.article.img_url = '';
    	$scope.data.bgImage = '';
    	$scope.data.bg_url = '';
    	document.querySelector('#preview').src = '';
    }
    
    $scope.cancelArticle = function(){
    	$scope.editing = false;
    }
    
    $scope.updateArticle = function(index){
    	$scope.editing = true;
    	$scope.article.isNew = false;
    	$scope.article.title = angular.copy($scope.dataList[index].title);
    	$scope.article.content = angular.copy($scope.dataList[index].content);
    	$scope.article.abstracts = angular.copy($scope.dataList[index].abstracts);
    	$scope.article.id = angular.copy($scope.dataList[index].id);
    	$scope.article.img_url = angular.copy($scope.dataList[index].img_url);
    	$scope.data.bg_url = angular.copy($scope.article.img_url);
    	$scope.data.bgImage = angular.copy($scope.article.img_url);
    	if(!$scope.article.img_url){
    		document.querySelector('#preview').src = '';
    	}
    }
    
    $scope.editArticle = function() {
		  if($scope.article.title == ''){
			  toaster.pop('error', '文章管理', "标题不能为空！");
			  return false;
		  }
		  if($scope.article.content == ''){
			  toaster.pop('error', '文章管理', "内容不能为空！");
			  return false;
		  }
		  if($scope.article.abstracts == ''){
			  toaster.pop('error', '文章管理', "摘要不能为空！");
			  return false;
		  }
		  
		  if($scope.article.isNew){
			  if($scope.uploader.queue.length <= 0){
				  $scope.data.bg_url = '';
			  }
			  else{
				  angular.forEach($scope.uploader.queue, function(item) {
					  $scope.data.bg_url = item.file.url;
				  });
			  }
		  }
		  else{
			  if($scope.uploader.queue.length <= 0){
				  $scope.data.bg_url = '';
			  }
			  else{
				  angular.forEach($scope.uploader.queue, function(item) {
					  $scope.data.bg_url = item.file.url;
				  });
			  }
		  }

		  $scope.submiting = true;
		  $scope.submit_html = '提交中…';
		  var promise = DataLoad.editArticle({
			  id: $scope.article.isNew ? '' : $scope.article.id,
			  title: $scope.article.title,
			  content:  $scope.article.content,
			  abstracts: $scope.article.abstracts,
			  img_url: $scope.data.bg_url ? $scope.data.bg_url : $scope.article.img_url,
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					toaster.pop('success', '文章管理', "保存成功！");
					$scope.article.id = '';
					$scope.article.title = '';
					$scope.article.isNew = false;
					$scope.article.content = '<h3>请输入内容！</h3>';
					$scope.article.abstracts = '';
					$scope.editing = false;
					$scope.getAllData();
				} 
				else {
					toaster.pop('error', '文章管理', returnData.message);
				}
				$scope.submiting = false;
				$scope.submit_html = '提交';
		  }, function () {
			  $scope.submiting = false;
			  $scope.submit_html = '提交';
			  toaster.pop('error', '文章管理', ' 保存文章出错！');
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
    
    $scope.searchData = function() {
	    $scope.page = 1;
	    $scope.getAllData();
    }
    
    $scope.dateSelectRange = {
	    start: moment(new Date('2016-12-01')).utc().startOf('day'),
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
		$scope.editing = false;
		$scope.loading = true;
		  var promise = DataLoad.getArticlePageData({
			  start: $scope.dateSelectRange.start.format('YYYY-MM-DD'),
			  end: $scope.dateSelectRange.end.format('YYYY-MM-DD'),
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
						var $selector = $("#article-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						toaster.pop('error', '文章管理', "没有数据！");
						$scope.haveData = false;
						$scope.dataList = [];
					}
				} 
				else {
					$scope.haveData = false;
					toaster.pop('error', '文章管理', returnData.message);
				}
				$scope.loading = false;
		  }, function () {
			  $scope.loading = false;
			  $scope.haveData = false;
			  toaster.pop('error', '文章管理', '获取文章管理出错！');
		  });
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
				  if($scope.num == 1){
					  $scope.moveable = true;
				  }
				  else{
					  $scope.moveable = false;
				  }
			  }
			  else{
				  $scope.moveable = false;
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
	  
	$scope.submit = false;
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
			  
			  $scope.all_delete_disabled = true;
			  var promise = DataLoad.deleteArticleBatch({
				  idlist: JSON.stringify($scope.idlist),
			  });
			  return promise.then(function (returnData) {
					$scope.all_delete_disabled = false;
					$scope.all_delete_html = '删除';
					
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '文章管理', returnData.message);
						$scope.page = 1;
						$scope.getAllData();
					} 
					else {
						$scope.submit = false;
						toaster.pop('error', '文章管理', returnData.message);
					}
			  }, function () {
				  $scope.submit = false;
				  $scope.all_delete_disabled = false;
				  $scope.all_delete_html = '删除';
				  toaster.pop('error', '文章管理', '操作出错！');
			  });
		  }
	   }
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
				  toaster.pop('error', '文章管理', '没有上一条了');
			  }
			  else{
				  var type ='up';
				  $scope.changeSortEachother($scope.selectSortItem.id, $scope.selectSortItem.sort_id, $scope.offset, type);
			  }
		  }
		  else{
			  toaster.pop('error', '文章管理', '操作出错！');
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
			  toaster.pop('error', '文章管理', '操作出错！');
		  }
	  }
	  
	  $scope.top = function(){
		  $scope.selectSortItem = '';
		  angular.forEach($scope.dataList, function(value, key) {
			  if(value.selected){
				  $scope.selectSortItem = angular.copy(value);
			  }
		  });
		  
		  var promise = DataLoad.changeArticleSortTop({
			  id: $scope.selectSortItem.id
		  });
		  return promise.then(function (returnData) {
			  if (returnData && returnData.code == 0) {
				  $scope.page = 1;
				  $scope.getAllData();
			  } 
			  else {
			 	  toaster.pop('error', '文章管理', returnData.message);
			  }
		  }, function () {
			  toaster.pop('error', '文章管理', '操作出错！');
		  });
	  }
	  
	  $scope.changeSortEachother = function(id, sort_id, offset, type){
		  var promise = DataLoad.changeSortArticle({
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
			 	  toaster.pop('error', '文章管理', returnData.message);
			  }
		  }, function () {
			  toaster.pop('error', '文章管理', '操作出错！');
		  });
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
			angular.forEach($scope.uploader.queue, function(item) {
				var fileName = item.file.name;
				if(fileName.indexOf('.jpg') === -1 && fileName.indexOf('.jpeg') === -1 && fileName.indexOf('.png') === -1 && fileName.indexOf('.gif') === -1){
				    toaster.pop('error', '封面设置', "上传文件扩展名必须为：.jpg .png .jpeg .gif");
				    
				    //删除最后一个元素
				    $scope.uploader.queue.pop();
					return false;
				}
				else{
					if($scope.uploader.queue.length > 1){
				        $scope.uploader.queue.splice(0, 1);
				    }
				}
			});
	  };
		
	  uploader.onCompleteItem = function(fileItem, response, status, headers) {
			if(response.code == 0){
				$(".bootstrap-filestyle").children('input').val('');
				fileItem.file.url = '/backend/uploads/' + response.file;
				fileItem.file.save_file_name = response.file;
				$scope.editArticle();
			}
			else{
				toaster.pop('error', '封面设置', response.answer);
			}
	  };
		
	  angular.element(document.querySelector('#fileInput')).on('change',function(evt) {
	        var file = evt.currentTarget.files[0];
	        var reader = new FileReader();
	        reader.onload = function (evt) {
		        $scope.$apply(function($scope){
		            $scope.data.bgImage = evt.target.result;
		        });
	        };
	        reader.readAsDataURL(file);
	  });
	  
	  $scope.submitArticle = function(){
	    	if($scope.uploader.queue.length <= 0 && !$scope.article.isNew){
				$scope.editArticle();
			}
			else{
				if($scope.uploader.queue.length >= 1){
				    uploader.uploadAll(); 
				}
				else{
					$scope.editArticle();
				}
			}
	  };
}]);