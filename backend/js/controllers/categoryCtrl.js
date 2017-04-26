app.controller('CategoryCtrl', ['$scope', '$timeout', 'toaster', 'DataLoad', 'CommonFunction', 'FileUploader', function($scope, $timeout, toaster, DataLoad, CommonFunction, FileUploader) {

	$scope.rightColShow = false;
	$scope.editing = false;
	$scope.branch = {};
	$scope.branch.label = "";
	$scope.branch.path = "";
	$scope.branch.isNew = false;
	$scope.editItem = {};
	$scope.routerBranchNum = 0;
	$scope.loading = true;
	$scope.data = {}; 
	$scope.data.bgImage = '';
	$scope.data.bg_url = '';
	
    var tree;
    $scope.my_tree_handler = function(branch) {
    	  $scope.rightColShow = true;
    	  $scope.editing = true;
    	  $scope.tab(1);
          $scope.editItem.label = angular.copy(branch.label);
          $scope.editItem.node_id = angular.copy(branch.node_id);
          $scope.editItem.path = angular.copy(branch.path);
          $scope.editItem.selected = angular.copy(branch.selected);
          $scope.editItem.orderby = angular.copy(branch.orderby);
          $scope.editItem.open = branch.is_open == 1 ? true : false;
          $scope.editItem.img_url = angular.copy(branch.img_url);
          $scope.editItem.url = angular.copy(branch.url);
          $scope.data.bgImage = angular.copy(branch.img_url);
          if(!branch.img_url){
        	  document.querySelector('#preview').src = '';
          }
          
          $scope.editItem.isNew = false;
    	  $scope.branch = branch;
    	  
	      var _ref;
	      $scope.output = "当前分支: " + branch.label;
	      if ((_ref = branch.data) != null ? _ref.description : void 0) {
	          return $scope.output += '(' + branch.data.description + ')';
	      } 
    };

    $scope.my_data = [];
    $scope.my_tree = tree = {};
    $scope.editItem = {
		label: '新栏目',
        path:'',
        is_root:0,
        isNew: true,
        top_nav: false,
	    bottom_nav: false,
	    content_nav: false,
	    open: false,
	    orderby:'',
    };
    $scope.try_adding_a_branch = function() {
        var selected_branch = tree.get_selected_branch();
        var new_branch = angular.copy($scope.editItem);
        return tree.add_branch(selected_branch, new_branch);
    };
    
    $scope.adding_a_root_branch = function() {
    	var new_branch = angular.copy($scope.editItem);
        return tree.add_root_branch(new_branch);
    };
    
    $scope.delete_branch = function() {
    	var branch;
    	var parent;
    	branch = tree.get_selected_branch();
        parent = tree.get_parent_branch(branch);

        if(angular.isUndefined(branch)){
      	    toaster.pop('error', '栏目管理', '请先选择分支！');
      	    return false;
        }
        if(angular.isUndefined(parent)){
        	tree.delete_root_branch(branch);
        }
        else{
        	tree.delete_branch(parent, branch);
        }
    }
    
    $scope.expand_all = function() {
    	tree.expand_all();
    }
    
    $scope.insertTree = function(){
    	$scope.tab(0);
    	$scope.editing = true;
    	var b = tree.get_selected_branch();
    	if(angular.isUndefined(b) || b == null){
      	  toaster.pop('error', '栏目管理', '请先选择分支！');
      	  return false;
        }

    	var temp = String(angular.copy(b.path));
        if(temp.split('/').length >= 4){
      	  toaster.pop('error', '栏目管理', '不能再新建子分支了！');
      	  return false;
        }
        
    	var branch = tree.get_selected_branch();
    	$scope.editItem.label = '新栏目';
    	$scope.editItem.node_id = '';
    	$scope.editItem.isNew = true;
    	$scope.editItem.isRoot = false;
    	$scope.editItem.top_nav = false;
    	$scope.editItem.bottom_nav = false;
    	$scope.editItem.content_nav = false;
    	$scope.editItem.img_url = '';
    	$scope.editItem.url = '';
    	$scope.editItem.open = false;
        $scope.data.bgImage = '';
        document.querySelector('#preview').src = '';
    	$scope.editItem.orderby = Number(branch.children.length) + 1;;
    	$scope.editItem.parent_path = branch.path;
    	
    };
    
    $scope.insertRootTree = function(){
    	$scope.tab(2);
    	$scope.editing = true;
        
    	$scope.editItem.label = '新根栏目';
    	$scope.editItem.node_id = '';
    	$scope.editItem.isNew = true;
    	$scope.editItem.isRoot = true;
    	$scope.editItem.top_nav = false;
    	$scope.editItem.bottom_nav = false;
    	$scope.editItem.content_nav = false;
    	$scope.editItem.open = false;
    	$scope.editItem.orderby = $scope.routerBranchNum + 1;
    	$scope.editItem.parent_path = '';
    	$scope.editItem.img_url = '';
    	$scope.editItem.url = '';
        $scope.data.bgImage = '';
        document.querySelector('#preview').src = '';
    	
    };
    
    $scope.updatetTree = function(){
    	$scope.tab(1);
    	$scope.editing = true;
    	var branch = tree.get_selected_branch();
    	$scope.editItem.label = angular.copy(branch.label);
    	$scope.editItem.node_id = angular.copy(branch.node_id);
    	$scope.editItem.isNew = false;
    	$scope.editItem.orderby = angular.copy(branch.orderby);
    	if(branch.is_root){
    		$scope.editItem.isRoot = true;
    	}
    	else{
    		$scope.editItem.isRoot = false;
    	}
    	$scope.editItem.parent_path = angular.copy(branch.path);
    	$scope.editItem.open = branch.is_open == 1 ? true : false;
    	$scope.editItem.img_url = angular.copy(branch.orderby);
    	$scope.editItem.url = angular.copy(branch.url);
    	$scope.data.bgImage = angular.copy(branch.img_url);
    	if(!branch.img_url){
      	    document.querySelector('#preview').src = '';
        }
    }
    
    $scope.deleteNote = function(){
    	if(confirm("确定要删除吗?") == true){
	    	var branch = tree.get_selected_branch();
	        if(angular.isUndefined(branch)){
	      	    toaster.pop('error', '栏目管理', '请先选择分支！');
	      	    return false;
	        }
	        $scope.deleteTree(branch);
    	}
    }
    
    $scope.submitBranch = function(){
    	if($scope.uploader.queue.length <= 0 && !$scope.editItem.isNew){
			$scope.editTree();
		}
		else{
			if($scope.uploader.queue.length >= 1){
			    uploader.uploadAll(); 
			}
			else{
				//toaster.pop('error', '栏目管理', "栏目推广封面不能为空");
				$scope.editTree();
			}
		}
    };
    
    $scope.tabs = [true, false, false, false];
    $scope.tab = function(index){
        angular.forEach($scope.tabs, function(i, v) {
            $scope.tabs[v] = false;
        });
        $scope.tabs[index] = true;
    }
    
    $scope.originalData = [];
    $scope.getAllData = function() {
    	  $scope.my_data = [];
    	  $scope.loading = true;
		  var promise = DataLoad.treeAllData({
				
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if (returnData.data.length != 0) {
						$scope.originalData = returnData.data;
						
						//depth = 1
						var rootArray = [];
						angular.forEach($scope.originalData, function(value, key) {
							value.children = [];
							if(value.is_root == 1){
								rootArray.push(value);
							}
						});
						
						//depth = 2
						for(var i=0; i<rootArray.length; i++){
							angular.forEach($scope.originalData, function(value, key) {
								if(value.is_root != 1){
									var tempPath = value.path.split('/');
								    if(tempPath.length == 2 && tempPath[0] == rootArray[i].path){
								    	rootArray[i].children.push(value);
								    }
								}
							});
						}
						
						//depth = 3
						for(var i=0; i<rootArray.length; i++){
							angular.forEach($scope.originalData, function(value, key) {
								var tempPath = value.path.split('/');
								if(value.is_root != 1 && tempPath.length == 3){
									if(rootArray[i].children.length > 0){
										for(var j=0; j<rootArray[i].children.length; j++){
											var tempParentPath = rootArray[i].children[j].path.split('/');
											var pathString = tempPath[0] + tempPath[1];
											var parentPathString = tempParentPath[0] + tempParentPath[1];
										    if(pathString == parentPathString){
										    	rootArray[i].children[j].children.push(value);
										    }
										}
									}
								}
							});
						}
						
						//depth = 4
						for(var i=0; i<rootArray.length; i++){
							angular.forEach($scope.originalData, function(value, key) {
								var tempPath = value.path.split('/');
								if(value.is_root != 1 && tempPath.length == 4){
									if(rootArray[i].children.length > 0){
										for(var j=0; j<rootArray[i].children.length; j++){
											if(rootArray[i].children[j].children.length > 0){
												for(var k=0; k<rootArray[i].children[j].children.length; k++){
													var tempParentPath = rootArray[i].children[j].children[k].path.split('/');
													var pathString = tempPath[0] + tempPath[1] + tempPath[2];
													var parentPathString = tempParentPath[0] + tempParentPath[1] + tempParentPath[2];
												    if(pathString == parentPathString){
												    	rootArray[i].children[j].children[k].children.push(value);
												    }
												}
											}
										}
									}
								}
							});
						}

						$scope.my_data = rootArray;
						$scope.routerBranchNum = rootArray.length;
					} else {
						toaster.pop('error', '栏目管理', "没有数据！");
					}
				} 
				else {
					toaster.pop('error', '栏目管理', returnData.message);
				}
				$scope.loading = false;
				$timeout(function() {
					$scope.expand_all();
				}, 200);
		  }, function () {
			  $scope.loading = false;
			  toaster.pop('error', '栏目管理', ' 获取栏目管理出错！');
		  });
    }
    
    $scope.getAllData();
    
    $scope.active = false;
    $scope.editTree = function() {
		  if($scope.editItem.label == ''){
			  toaster.pop('error', '栏目管理', "分支不能为空！");
			  return false;
		  }

		  if($scope.editItem.orderby == ''){
			  toaster.pop('error', '栏目管理', "导航顺序不能为空！");
			  return false;
		  }
		  
		  if($scope.editItem.isNew){
			  if($scope.uploader.queue.length <= 0){
				  $scope.data.bg_url = '';
				  //toaster.pop('error', '栏目管理', "栏目推广封面不能为空");
				  //return false;
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

		  if(!$scope.active){
			  $scope.active = true;
			  var promise = DataLoad.editTree({
				  node_id: $scope.editItem.isNew ? '' : $scope.editItem.node_id,
				  label: $scope.editItem.label,
				  path:  $scope.editItem.isNew ? $scope.editItem.parent_path : $scope.editItem.path,
				  is_root: $scope.editItem.isRoot ? 1 : 0,
				  orderby: $scope.editItem.orderby,
				  img_url: $scope.data.bg_url ? $scope.data.bg_url : $scope.editItem.img_url,
				  is_open: $scope.editItem.open ? 1 : 0
			  });
			  return promise.then(function (returnData) {
				    $scope.active = false;
					if (returnData && returnData.code == 0) {
						if (returnData.data.length != 0) {
							toaster.pop('success', '栏目管理', "保存成功！");
							if($scope.editItem.isNew == true){
								$scope.editItem.node_id = angular.copy(returnData.data.node_id);
								$scope.editItem.path = angular.copy(returnData.data.path);
								$scope.editItem.isNew = false;
								if($scope.editItem.isRoot == false){
									$scope.editItem.is_root = 0;
									$scope.try_adding_a_branch();
									$scope.branch.is_root = 0;
								}
								else{
									$scope.editItem.is_root = 1;
									$scope.adding_a_root_branch();
									$scope.branch.is_root = 1;
									$scope.routerBranchNum = $scope.routerBranchNum + 1;//根节点数量加1
								}
								$scope.tab(1);
								$scope.editItem.url = angular.copy(returnData.data.url);
							}
							else{
								$scope.branch.orderby = angular.copy($scope.editItem.orderby);
							    $scope.branch.label = angular.copy($scope.editItem.label);
							    $scope.branch.is_root = $scope.editItem.isRoot ? 1 : 0;
							}
						} else {
							toaster.pop('error', '栏目管理', "没有数据！");
						}
					} 
					else {
						toaster.pop('error', '栏目管理', returnData.message);
					}
			  }, function () {
				  $scope.active = false;
				  toaster.pop('error', '栏目管理', ' 保存栏目管理出错！');
			  });
		  }
    }
    
    $scope.deleteTree = function(branch) {

		  var promise = DataLoad.deleteTree({
			  node_id: branch.path,
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					toaster.pop('success', '栏目管理', "删除成功！");
					$scope.delete_branch();
				} 
				else {
					toaster.pop('error', '栏目管理', returnData.message);
				}
		  }, function () {
			  toaster.pop('error', '栏目管理', ' 栏目管理出错！');
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
			$scope.editTree();
		}
		else{
			$scope.submitActive = false;
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
    
}]);