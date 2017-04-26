app.controller('GoodlistCtrl', ['$scope', '$state', '$timeout', 'toaster', 'DataLoad', 'divpage', '$rootScope', '$modal', 'CommonFunction', '$stateParams', 'FileUploader', function($scope, $state, $timeout, toaster, DataLoad, divpage, $rootScope, $modal, CommonFunction, $stateParams, FileUploader) {
	$scope.dataList = [];
	$scope.branch = null;
	$scope.haveData = true;
	$scope.loading = true;
	$scope.doing_async = true;
	$scope.query = '';
	$scope.page = 1;
    $scope.totalPage = 1;
    $scope.perPage = 10;
    $scope.editing = false;
    $scope.moveable = false;
    $scope.imgData = []; 
    $scope.goodsData = {};
    $scope.goodsData.name = '';
    $scope.goodsData.is_option = false;
    $scope.goodsData.options = [];
    $scope.goodsData.optionList = [];
    $scope.goodsData.total = '';
    $scope.goodsData.market_price = 0;
    $scope.goodsData.point_price = '';
    $scope.goodsData.cash_price = '';
    $scope.goodsData.desc = '';
    $scope.goodsData.imgData = '';
    $scope.goodsData.is_release = true;
    $scope.goodsData.is_time_buy = false;
    $scope.dateSelectBuy = '';
    $scope.goodsData.is_share = false;
    $scope.goodsData.share_price = '';
    $scope.goodsData.model = 1;
	$scope.goodsData.url = '';
	$scope.goodsData.send_method = '';
	$scope.goodsData.weight = '';
	$scope.goodsData.limit_buy = '';
	$scope.goodsData.company_id = '';
	$scope.goodsData.company_get_type = 1;
	$scope.goodsData.company_useable_point = 0;
	$scope.goodsData.send_type = {};
	$scope.goodsData.send_type.logistic = false;
	$scope.goodsData.send_type.self = false;
    $scope.init = true;
    $scope.selectAll = false;
	$scope.all_delete_disabled = true;
	$scope.all_delete_html = '删除';
	$scope.radioModel = 99;
	$scope.init_loading = true;
	$scope.click_modify_cancle = false;
	$scope.merchantModel = 99;
	
	if(!angular.isUndefined($stateParams.type)){
		$scope.radioModel = Number($stateParams.type);
	}
	else{
		$scope.radioModel = 99;
	}
	
	$scope.init_logistic = {
		loading: true,
		error: false,
		data: []
	};
	$scope.modify = {
		doing: false,
		branch: null,
		page: 1
	};
	
	$scope.init_company = {
		loading: true,
		error: false,
		data: []
	};
	
	$scope.$watch('radioModel', function(new_val, old_val){
	    if(new_val != old_val){
		    if(!$scope.init_loading){
			    $scope.page = 1;
		        $scope.getAllData();
		    }
	    }
    });
	
	$scope.$watch('merchantModel', function(new_val, old_val){
	    if(new_val != old_val){
		    if(!$scope.init_loading){
			    $scope.page = 1;
		        $scope.getAllData();
		    }
	    }
    });
	
	if(!angular.isUndefined($stateParams.key)){
		$scope.query = $stateParams.key;
	}
	
	$scope.dateSelectSingle = {
	    start: moment(new Date()).utc().startOf('day'),
	    end: '',
    };
	
	var tree;
    $scope.my_tree_handler = function(branch) {
    	  $scope.branch = branch;
    	  
	      var _ref;
	      $scope.output = "您选择了: " + branch.label;
	      if ((_ref = branch.data) != null ? _ref.description : void 0) {
	          return $scope.output += '(' + branch.data.description + ')';
	      } 
	      
	      if(!$scope.editing && !$scope.modify.doing){
	    	  $scope.haveData = true;
	    	  $scope.page = 1;
	    	  $scope.totalPage = 1;
	    	  $scope.perPage = 10;
	    	  $scope.getAllData();
	    	  $scope.initItemData();
	      }
	      else if($scope.click_modify_cancle){
	    	  $scope.modify.doing = false;
	    	  $scope.click_modify_cancle = false;
	      }
    };

    $scope.my_data = [];
    $scope.my_tree = tree = {};
    $scope.originalData = [];
    $scope.getTreeData = function() {
    	  $scope.doing_async = true;
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
						//$scope.getAllData();
					} else {
						toaster.pop('error', '商品管理', "没有数据！");
					}
				}
				else if(returnData == null){
					sessionStorage.removeItem('userInfo');
					$state.go('access.signin');
					return false;
				}
				else {
					toaster.pop('error', '商品管理', returnData.message);
				}
				$scope.doing_async = false;
				$timeout(function() {
					$scope.expand_all();
				}, 200);
		  }, function () {
			  $scope.doing_async = false;
			  toaster.pop('error', '商品管理', ' 获取菜单出错！');
		  });
    }
    
    $scope.getTreeData();
    
    $scope.expand_all = function() {
    	tree.expand_all();
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
    
    $scope.$watch('dateSelectSingle.start', function(new_val, old_val){
		if(new_val != old_val){
			//$scope.init = false;
			//$scope.page = 1;
			//$scope.getAllData();
		}
	}, true);
    
    if(!angular.isUndefined($stateParams.start) && !angular.isUndefined($stateParams.end)){
    	$scope.dateSelectRange = {
		    start: moment(new Date($stateParams.start)).utc().startOf('day'),
		    end: moment(new Date($stateParams.end)).utc().startOf('day')
	    };
    }
    else{
	    $scope.dateSelectRange = {
		    start: moment(new Date('2016-12-01')).utc().startOf('day'),
		    end: moment().utc().startOf('day')
	    };
    }
    
    $scope.$watch('dateSelectRange', function(new_val, old_val){
		if(new_val != old_val){
			if(!$scope.init_loading){
				$scope.init = false;
				$scope.page = 1;
				$scope.getAllData();
			}
		}
	}, true);

	$scope.getAllData = function() {
		  $scope.loading = true;
		  
		  var promise = DataLoad.getGoodsPageData({
			  path: $scope.branch == null ? '' : $scope.branch.path,
			  start: $scope.dateSelectRange.start.format('YYYY-MM-DD'),
			  end: $scope.dateSelectRange.end.format('YYYY-MM-DD'),
			  searchkey: $scope.query,
			  offset: ($scope.page - 1) * $scope.perPage,
			  num: $scope.perPage,
			  status: $scope.radioModel,
			  is_company: $scope.merchantModel
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					if (returnData.data.data.length != 0) {
						$scope.haveData = true;
						$scope.dataList = returnData.data.data;
						angular.forEach($scope.dataList, function(value, key) {
							if(value.is_release == 1){
								value.open = true;
							}
							else{
								value.open = false;
							}
							
							if(value.is_recommend == 1){
								value.recommend = true;
							}
							else{
								value.recommend = false;
							}
						});
						
						$scope.totalPage = Math.ceil(returnData.data.count/$scope.perPage);
						var $selector = $("#divpage-page");
						divpage.getready($selector, $scope.totalPage, $scope.page);
					} else {
						$scope.dataList = [];
						toaster.pop('success', '商品管理', "没有数据！");
						$scope.haveData = false;
					}
				} 
				else {
					$scope.dataList = [];
					$scope.haveData = false;
					toaster.pop('error', '商品管理', "没有数据！");
				}
				$scope.loading = false;
				$scope.init_loading = false;
		  }, function () {
			  $scope.dataList = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  $scope.init_loading = false;
			  toaster.pop('error', '商品管理', '获取数据出错！');
		  });
     }
	
	 $scope.initItemData = function(){
		 $scope.goodsData.name = '';
		 $scope.goodsData.is_option = false;
		 $scope.goodsData.options = [];
		 $scope.goodsData.optionList = [];
	     $scope.goodsData.total = '';
	     $scope.goodsData.market_price = 0;
	     $scope.goodsData.point_price = '';
	     $scope.goodsData.cash_price = '';
	     $scope.goodsData.desc = '';
	     $scope.goodsData.imgData = '';
	     $scope.goodsData.is_release = true;
	     $scope.goodsData.is_time_buy = false;
	     $scope.goodsData.is_share = false;
	     $scope.goodsData.share_price = '';
	     $scope.goodsData.model = 1;
	     $scope.goodsData.weight = '';
	     $scope.goodsData.limit_buy = '';
	     if(!$scope.init_logistic.loading && !$scope.init_logistic.error){
	         $scope.goodsData.send_method = $scope.init_logistic.data[0].id;
	     }
	     else{
	    	 $scope.goodsData.send_method = '';
	     }
	     
	     if(!$scope.init_company.loading && !$scope.init_company.error){
	         $scope.goodsData.company_id = $scope.init_company.data[0].id;
	     }
	     else{
	    	 $scope.goodsData.company_id = '';
	     }
	     $scope.goodsData.company_get_type = 1;
	     $scope.goodsData.company_useable_point = 0;
	     $scope.imgData = [];
	     $scope.goodsData.send_type = {};
	 	 $scope.goodsData.send_type.logistic = false;
	 	 $scope.goodsData.send_type.self = false;
	 }
	 
	 $scope.add = function(){
		 if($scope.init_logistic.loading){
			 toaster.pop('error', '商品管理', '正在加载配送方案');
			 return false;
		 }
		 if($scope.init_logistic.error){
			 toaster.pop('error', '商品管理', '获取配送方案出错');
			 return false;
		 }
		 if($scope.init_logistic.data <= 0){
			 toaster.pop('error', '商品管理', '未设置配送方案');
			 return false;
		 }
		 $scope.imgData = []; 
		 $scope.goodsData.send_method = $scope.init_logistic.data[0].id;
		 $scope.goodsData.weight = '';
		 $scope.goodsData.limit_buy = '';
		 $scope.goodsData.desc = '';
		 $scope.goodsData.is_release = true;
		 $scope.goodsData.is_time_buy = false;
		 $scope.goodsData.is_share = false;
		 $scope.goodsData.share_price = '';
		 $scope.goodsData.model = 1;
		 $scope.goodsData.url = '';
		 $scope.goodsData.send_type = {};
		 $scope.goodsData.send_type.logistic = true;
		 $scope.goodsData.send_type.self = true;
		 
		 if(!$scope.init_company.loading && !$scope.init_company.error){
	         $scope.goodsData.company_id = $scope.init_company.data[0].id;
	     }
	     else{
	    	 $scope.goodsData.company_id = '';
	     }
		 $scope.goodsData.company_get_type = 1;
	     $scope.goodsData.company_useable_point = 0;
		 
		 $scope.editing = true;
		 $scope.goodsData.is_new = true;
	 }
	 
	 $scope.cancel = function(){
		 $scope.goodsData = {}; 
	     $scope.goodsData.name = '';
	     $scope.goodsData.is_option = false;
	     $scope.goodsData.options = [];
	     $scope.goodsData.optionList = [];
	     $scope.goodsData.total = '';
	     $scope.goodsData.market_price = 0;
	     $scope.goodsData.point_price = '';
	     $scope.goodsData.cash_price = '';
	     $scope.goodsData.desc = '';
	     $scope.goodsData.imgData = '';
	     $scope.goodsData.is_release = true;
	     $scope.goodsData.is_time_buy = false;
	     $scope.goodsData.is_share = false;
	     $scope.goodsData.share_price = '';
	     $scope.goodsData.model = 1;
		 $scope.goodsData.url = '';
		 $scope.goodsData.send_method = '';
		 $scope.goodsData.company_get_type = 1;
		 $scope.goodsData.company_useable_point = 0;
		 $scope.editing = false;
		 $scope.click_modify_cancle = true;
		 $scope.goodsData.send_type = {};
		 $scope.goodsData.send_type.logistic = false;
		 $scope.goodsData.send_type.self = false;
		 
		 if($scope.modify.doing){
			 $scope.branch = $scope.modify.branch;
			 $scope.my_tree.select_branch($scope.branch);
		 }
	 }
	 
	 $scope.updateGoods = function(item){
		 if($scope.init_logistic.loading){
			 toaster.pop('error', '商品管理', '正在加载配送方案');
			 return false;
		 }
		 if($scope.init_logistic.error){
			 toaster.pop('error', '商品管理', '获取配送方案出错');
			 return false;
		 }
		 if($scope.init_logistic.data <= 0){
			 toaster.pop('error', '商品管理', '未设置配送方案');
			 return false;
		 }

		 $scope.modify = {
			 doing: true,
			 branch: angular.copy($scope.branch),
			 page: angular.copy($scope.page)
		 };

		 var temp = angular.copy(item);
		 var optionData = temp.options ? angular.fromJson(temp.options) : null;
		 $scope.editing = true;
		 $scope.goodsData = {}; 
		 $scope.goodsData.id = temp.id;
		 $scope.goodsData.is_new = false;
	     $scope.goodsData.name = temp.name;
	     $scope.goodsData.is_option = optionData && optionData.is_option == 1 ? true : false;
	     $scope.goodsData.options = optionData ? optionData.options : [];
	     $scope.goodsData.optionList = optionData ? optionData.optionList : [];
	     
	     $scope.goodsData.total = temp.total;
	     $scope.goodsData.market_price = temp.market_price;
	     $scope.goodsData.point_price = temp.point_price;
	     $scope.goodsData.cash_price = temp.cash_price;
	     
	     $scope.goodsData.desc = temp.description;
	     $scope.goodsData.imgData = angular.fromJson(temp.img_data);
	     $scope.goodsData.is_release = temp.is_release == 1 ? true : false;
	     $scope.goodsData.is_time_buy = temp.is_time_buy == 1 ? true : false;	     
	     $scope.dateSelectBuy = temp.buy_time;
	     $scope.goodsData.is_share = temp.is_share == 1 ? true : false;
	     $scope.goodsData.share_price = temp.share_price;
	     $scope.goodsData.model = temp.model;
		 $scope.goodsData.url = temp.url;
		 $scope.goodsData.weight = temp.weight;
		 $scope.goodsData.limit_buy = temp.limit_buy;
		 $scope.goodsData.company_get_type = temp.company_get_type;
		 $scope.goodsData.company_useable_point = temp.company_useable_point;
		 var send_type_array = temp.send_type.split(',');
		 $scope.goodsData.send_type = {};
		 $scope.goodsData.send_type.logistic = false;
		 $scope.goodsData.send_type.self = false;
		 for(var i=0; i<send_type_array.length; i++){
			 if(send_type_array[i] == 1){
				 $scope.goodsData.send_type.logistic = true;
			 }
			 else if(send_type_array[i] == 2){
				 $scope.goodsData.send_type.self = true;
			 }
		 }
		 
		 if(temp.send_method && temp.send_method > 0){
			 $scope.goodsData.send_method = temp.send_method;
		 }
		 else{
			 $scope.goodsData.send_method = $scope.init_logistic.data[0].id;
		 }
		 
		 if(temp.company_id && temp.company_id > 0){
			 $scope.goodsData.company_id = temp.company_id;
		 }
		 else{
			 $scope.goodsData.company_id = $scope.init_company.data[0].id;
		 }
		 
	     $scope.imgData = [];
	     for(var i=0; i<$scope.goodsData.imgData.length; i++){
	    	 var url = '/backend/uploads/' + $scope.goodsData.imgData[i].file;
	    	 var file = {};
	    	 file.url = url;
	    	 file.name = $scope.goodsData.imgData[i].file;
	    	 file.save_file_name = $scope.goodsData.imgData[i].file;
	    	 $scope.imgData.push({file: file, selected: $scope.goodsData.imgData[i].selected});
	     }
	     
	     var findRes = false;
	     for(var i=0; i<$scope.my_data.length; i++){
	    	 if(findRes){
	    		 break;
	    	 }
	    	 
	    	 for(var j=0; j<$scope.my_data[i].children.length; j++){
	    		 if(findRes){
		    		 break;
		    	 }
	    		 
	    		 for(var k=0; k<$scope.my_data[i].children[j].children.length; k++){
	    			 if(findRes){
	    	    		 break;
	    	    	 }
	    			 
	    			 for(var m=0; m<$scope.my_data[i].children[j].children[k].children.length; m++){
	    				 if(findRes){
	    		    		 break;
	    		    	 }
	    				 
			    		 //第4层
			    		 if($scope.my_data[i].children[j].children[k].children[m].path == item.category){
			    			 $scope.branch = $scope.my_data[i].children[j].children[k].children[m];
			    			 findRes = true;
			    			 break;
			    		 }
			    	 }
	    			 
		    		 //第3层
		    		 if($scope.my_data[i].children[j].children[k].path == item.category){
		    			 $scope.branch = $scope.my_data[i].children[j].children[k];
		    			 findRes = true;
		    			 break;
		    		 }
		    	 }

	    		 //第2层
	    		 if($scope.my_data[i].children[j].path == item.category){
	    			 findRes = true;
	    			 $scope.branch = $scope.my_data[i].children[j];
	    			 break;
	    		 }
	    	 }
	    	 
	         if($scope.my_data[i].path == item.category){
	        	 //第一层
	        	 findRes = true;
	        	 $scope.branch = $scope.my_data[i];
	        	 break;
	         }
	     }
	     if(findRes){
	    	 $scope.my_tree.select_branch($scope.branch);
	     }
	     
	     if($scope.modify.doing){
		     //点击修改之前的那级
		     for(var i=0; i<$scope.my_data.length; i++){
		    	 if($scope.my_data[i].node_id == $scope.modify.branch.node_id){
		    		 //第一层
		    		 $scope.modify.branch = $scope.my_data[i];
		    		 break;
		    	 }
		    	 
	             for(var j=0; j<$scope.my_data[i].children.length; j++){
	            	 if($scope.my_data[i].children[j].node_id == $scope.modify.branch.node_id){
	            		 //第2层
	    	    		 $scope.modify.branch = $scope.my_data[i].children[j];
	    	    		 break;
	            	 }
	            	 
	            	 for(var k=0; k<$scope.my_data[i].children[j].children.length; k++){
	            		 if($scope.my_data[i].children[j].children[k].node_id == $scope.modify.branch.node_id){
	                		 //第3层
	        	    		 $scope.modify.branch = $scope.my_data[i].children[j].children[k];
	        	    		 break;
	                	 }
	            		 
	            		 for(var m=0; m<$scope.my_data[i].children[j].children[k].children.length; m++){
	                		 if($scope.my_data[i].children[j].children[k].children[m].node_id == $scope.modify.branch.node_id){
	                    		 //第3层
	            	    		 $scope.modify.branch = $scope.my_data[i].children[j].children[k].children[m];
	            	    		 break;
	                    	 }
	                	 }
	            	 }
		    	 }
		     }
	     }
	 }
	 
	 $scope.deleteGoods = function(item){
		 if(confirm("确定要删除吗?") == true){
			var promise = DataLoad.deleteGoods({
				id: item.id,
			});
			return promise.then(function (returnData) {
			    if (returnData && returnData.code == 0) {
					toaster.pop('success', '商品管理', returnData.message);
					$timeout(function(){
						location.reload();
					}, 1500);
				} 
				else {
					toaster.pop('error', '商品管理', returnData.message);
				}
			}, function () {
				  toaster.pop('error', '商品管理', '删除出错');
			});
		 }
	 }
	 
	 
	 
	 //文件上传
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
		    if($scope.uploader.queue.length > 10){
		    	//删除最后一个元素
			    $scope.uploader.queue.pop();
			    toaster.pop('error', '商品管理', "最多可上传10个文件");
				return false;
		    }
		    else{
		    	angular.forEach($scope.uploader.queue, function(item) {
					var fileName = item.file.name;
					if(fileName.indexOf('.jpg') === -1 && fileName.indexOf('.jpeg') === -1 && fileName.indexOf('.png') === -1){
					    toaster.pop('error', '商品管理', "上传文件扩展名必须为：.jpg .png .jpeg");
					      
					    //删除最后一个元素
					    $scope.uploader.queue.pop();
						return false;
					}
				});
		    }
	 };
		
	 uploader.onCompleteItem = function(fileItem, response, status, headers) {
			if(response.code == 0){
				$(".bootstrap-filestyle").children('input').val('');
				fileItem.file.url = '/backend/uploads/' + response.file;
				fileItem.file.save_file_name = response.file;
			}
			else{
				toaster.pop('error', '商品管理', response.answer);
			}
			
			fileItem.selected = false;
			$scope.imgData.push(fileItem);
	 };
	 
	 $scope.deleteImg = function($event, item){
		 $event.stopPropagation();
		 if(confirm("确定要删除吗?") == true){
			var promise = DataLoad.deleteImg({
				id: $scope.goodsData.is_new ? '' : $scope.goodsData.id,
				file: item.file.save_file_name,
			});
			return promise.then(function (returnData) {
			    if (returnData && returnData.code == 0) {
			    	$scope.imgData.splice($scope.imgData.indexOf(item), 1);
			    	var index = $scope.uploader.queue.indexOf(item);
			    	if(index !== -1){
			    		$scope.uploader.queue.splice($scope.uploader.queue.indexOf(item), 1);
			    	}
					toaster.pop('success', '商品管理', returnData.message);
				} 
				else {
					toaster.pop('error', '商品管理', returnData.message);
				}
			}, function () {
				  toaster.pop('error', '商品管理', '删除出错');
			});
		 }
	 }
	 
	 $scope.submitBtnHtml = '提交';
	 $scope.save = function() {
		$scope.submitBtnHtml = '提交中…';
		var imgUrlArray = [];
		angular.forEach($scope.imgData, function(item) {
			imgUrlArray.push({file: item.file.save_file_name, selected: item.selected});
		});
		
		if($scope.goodsData.is_release){
			var is_release = 1;
		}
		else{
			var is_release = 0;
		}
		
		if($scope.goodsData.is_time_buy){
			var is_time_buy = 1;
		}
		else{
			var is_time_buy = 0;
		}
		
		if($scope.goodsData.is_share){
			var is_share = 1;
		}
		else{
			var is_share = 0;
		}
		
		if($scope.goodsData.send_type.logistic && $scope.goodsData.send_type.self){
			var send_type = '1,2';
		}
		else if($scope.goodsData.send_type.logistic && !$scope.goodsData.send_type.self){
			var send_type = '1';
		}
		else if(!$scope.goodsData.send_type.logistic && $scope.goodsData.send_type.self){
			var send_type = '2';
		}
		else{
			toaster.pop('error', '商品管理', '请选择配送方式');
			$scope.submitBtnHtml = '提交';
			return false;
		}
		var optionData = {
			is_option: $scope.goodsData.is_option ? 1 : 0,
			options: $scope.goodsData.options,
			optionList: $scope.goodsData.optionList,
		};
		  		  
		var promise = DataLoad.saveGoods({
			id: $scope.goodsData.is_new ? '' : $scope.goodsData.id,
			category: $scope.branch == null ? '' : $scope.branch.path,
			name: $scope.goodsData.name,
			options: angular.toJson(optionData),
			desc: $scope.goodsData.desc,
			market_price: $scope.goodsData.market_price,
			point_price: $scope.goodsData.point_price,
			cash_price: $scope.goodsData.cash_price,
			total: $scope.goodsData.total,
			model: $scope.goodsData.model,
			is_release: is_release,
			img_data: angular.toJson(imgUrlArray),
			send_method: $scope.goodsData.send_method,
			weight: $scope.goodsData.weight,
			limit_buy: $scope.goodsData.limit_buy,
			company_id: $scope.goodsData.company_id,
			company_get_type: $scope.goodsData.company_get_type,
			company_useable_point: $scope.goodsData.company_useable_point,
			send_type: send_type,
			is_time_buy: is_time_buy,
			buy_time: $scope.dateSelectBuy,
			is_share: is_share,
			share_price: $scope.goodsData.share_price
		});
		
		return promise.then(function (returnData) {
			$scope.submitBtnHtml = '提交';
			if (returnData && returnData.code == 0) {
				toaster.pop('success', '商品管理', returnData.message);
				$scope.goodsData.name = '';
				$scope.goodsData.is_option = false;
			    $scope.goodsData.options = [];
			    $scope.goodsData.optionList = [];
				$scope.goodsData.market_price = 0;
				$scope.goodsData.point_price = '';
				$scope.goodsData.cash_price = '';
				$scope.goodsData.total = '';
				$scope.uploader.queue = [];
				$scope.uploader.progress = 0;
				$timeout(function(){
					if($scope.modify.doing){
						 //修改
						 $scope.branch = $scope.modify.branch;
						 $scope.my_tree.select_branch($scope.branch);
						 $scope.page = angular.copy($scope.modify.page);
						 $scope.getAllData();
					}
					else{
						 //新增
				         $scope.getAllData();
					}
				    $scope.editing = false;
				}, 1500);
			} 
			else {
				toaster.pop('error', '商品管理', returnData.message);
			}
		}, function () {
			$scope.submitBtnHtml = '提交';
			toaster.pop('error', '商品管理', '图片上传出错！');
		});
	 }
	 
	 $scope.submit = function(e) {
		if($scope.uploader.queue.length <= 0 && $scope.imgData.length <= 0){
			toaster.pop('error', '商品管理', "商品图片不能为空！");
			return false;
		}
		
		if(!$scope.goodsData.total && !$scope.goodsData.is_option){
			toaster.pop('error', '商品管理', "商品库存不能为空！");
			return false;
		}
		
		if(!$scope.goodsData.point_price){
			toaster.pop('error', '商品管理', "商品积分价格不能为空！");
			return false;
		}
		
		if($scope.goodsData.model == 3 && !$scope.goodsData.cash_price){
			toaster.pop('error', '商品管理', "商品现金价格不能为空！");
			return false;
		}
		
		if(!$scope.goodsData.desc){
			toaster.pop('error', '商品管理', "商品描述不能为空！");
			return false;
		}
		
		if(!$scope.goodsData.model){
			toaster.pop('error', '商品管理', "兑换模式不能为空！");
			return false;
		}
		
		if($scope.goodsData.is_share && !$scope.goodsData.share_price){
			toaster.pop('error', '商品管理', "分享价格不能为空！");
			return false;
		}
		
		var has_face = false;
		angular.forEach($scope.imgData, function(value, key) {
			if(value.selected){
				has_face = true;
			}
		});
		if(!has_face){
			toaster.pop('error', '商品管理', "请选项商品封面");
			return false;
		}
		
		var flag = false
		angular.forEach($scope.uploader.queue, function(value, key) {
			if(angular.isDefined(value.file.url)){
				flag = true;
			}
		});
		  
		if(flag || $scope.imgData.length > 0){
			$scope.goodsData.imgData = angular.copy($scope.imgData);
			$scope.save();
		}
		else{
			toaster.pop('error', '文件上传', "请先点击上传按钮上传文件");
		}
	 }
	 $scope.setFace = function($event, item) {
		 $event.stopPropagation();
		 var old_val = angular.copy(item.selected);
		 angular.forEach($scope.imgData, function(value, key) {
			 value.selected = false;
		 });
		 if(old_val){
			 item.selected = false;
		 }
		 else{
			 item.selected = true;
		 }
	 }
	 
	 $scope.price = function(item) {
		 if(item.model == 1 || item.model == 2){
			 var priceValue = item.point_price + '分';
		 }
		 else if(item.model == 3){
			 var priceValue = item.point_price + '分 + ' + item.cash_price + '元';
		 }
		 return priceValue;
	 }
	 
	 $scope.switch_status = function(item){
		 if(item.open){
			 var value = 1;
		 }
		 else{
			 var value = 0;
		 }
		 
		 var promise = DataLoad.switch_status({
			 id: item.id,
			 value: value,
		 });
		 return promise.then(function (returnData) {
		     if (returnData && returnData.code == 0) {
				 toaster.pop('success', '商品管理', returnData.message);
			 } 
			 else {
				 toaster.pop('error', '商品管理', returnData.message);
			 }
		 }, function () {
			 toaster.pop('error', '商品管理', '修改出错');
		 });
	 }
	 
	 $scope.recommend_status = function(item){
		 if(item.recommend){
			 var value = 1;
		 }
		 else{
			 var value = 0;
		 }
		 
		 var promise = DataLoad.recommend_status({
			 id: item.id,
			 value: value,
		 });
		 return promise.then(function (returnData) {
		     if (returnData && returnData.code == 0) {
				 toaster.pop('success', '商品管理', returnData.message);
			 } 
			 else {
				 toaster.pop('error', '商品管理', returnData.message);
			 }
		 }, function () {
			 toaster.pop('error', '商品管理', '修改出错');
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
	  
	$scope.submitFlag = false;
	$scope.deleteBatch = function(){
	   if(confirm("确定要删除吗?") == true){
		  if($scope.submitFlag == false){
			  $scope.submitFlag = true;
			  $scope.idlist = [];
			  angular.forEach($scope.dataList, function(value, key) {
				  if(value.selected == true){
					  $scope.idlist.push(value.id);
				  }
			  });
			  
			  var promise = DataLoad.deleteGoodsBatch({
				  idlist: JSON.stringify($scope.idlist),
			  });
			  return promise.then(function (returnData) {
					$scope.all_disagree_disabled = false;
					$scope.all_delete_html = '删除';
					
					if (returnData && returnData.code == 0) {
						toaster.pop('success', '商品管理', returnData.message);
						location.reload();
					} 
					else {
						$scope.submitFlag = false;
						toaster.pop('error', '商品管理', returnData.message);
					}
			  }, function () {
				  $scope.submitFlag = false;
				  $scope.all_disagree_disabled = false;
				  $scope.all_delete_html = '删除';
				  toaster.pop('error', '商品管理', '操作出错！');
			  });
		  }
	   }
	}
	
	$scope.getLogisticList = function() {
		  $scope.init_logistic = {
			  loading: true,
			  error: false,
			  data: []
		  };
		  var promise = DataLoad.getLogisticList({
			  
		  });
		  return promise.then(function (returnData) {
			  $scope.init_logistic.loading = false;
			  if (returnData && returnData.code == 0) {
				  $scope.init_logistic.error = false;
				  $scope.init_logistic.data = returnData.data;
			  } 
			  else {
				  $scope.init_logistic.error = true;
			  }
		  }, function () {
			  $scope.init_logistic.loading = false;
			  $scope.init_logistic.error = true;
			  toaster.pop('error', '商品管理', '获取配送方式出错');
		  });
     }
	 $scope.getLogisticList();
	 
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
				  toaster.pop('error', '商品管理', '没有上一条了');
			  }
			  else{
				  var type ='up';
				  $scope.changeSortEachother($scope.selectSortItem.id, $scope.selectSortItem.sort_id, $scope.offset, type);
			  }
		  }
		  else{
			  toaster.pop('error', '商品管理', '操作出错！');
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
			  toaster.pop('error', '商品管理', '操作出错！');
		  }
	  }
	  
	  $scope.top = function(){
		  $scope.selectSortItem = '';
		  angular.forEach($scope.dataList, function(value, key) {
			  if(value.selected){
				  $scope.selectSortItem = angular.copy(value);
			  }
		  });
		  
		  var promise = DataLoad.changeSortTop({
			  id: $scope.selectSortItem.id
		  });
		  return promise.then(function (returnData) {
			  if (returnData && returnData.code == 0) {
				  $scope.page = 1;
				  $scope.getAllData();
			  } 
			  else {
			 	  toaster.pop('error', '商品管理', returnData.message);
			  }
		  }, function () {
			  toaster.pop('error', '商品管理', '操作出错！');
		  });
	  }
	  
	  $scope.changeSortEachother = function(id, sort_id, offset, type){
		  var promise = DataLoad.changeSortGoods({
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
			 	  toaster.pop('error', '商品管理', returnData.message);
			  }
		  }, function () {
			  toaster.pop('error', '商品管理', '操作出错！');
		  });
	  }
	  
	  $scope.selectCompanyData = function() {
		  $scope.init_company = {
			  loading: true,
			  error: false,
			  data: []
		  };
		  
		  var promise = DataLoad.selectCompanyData({
			  
		  });
		  return promise.then(function (returnData) {
			  $scope.init_company.loading = false;
			  if (returnData && returnData.code == 0) {
				  $scope.init_company.error = false;
				  $scope.init_company.data = returnData.data;
				  $scope.init_company.data.unshift({id:0, name:'无', mobile:''});
				  $scope.goodsData.company_id = $scope.init_company.data[0].id;
			  }
			  else{
				  $scope.init_company.error = true;
				  toaster.pop('error', '商品管理', '未设置商家');
			  }
		  }, function () {
			  $scope.init_company.loading = false;
			  $scope.init_company.error = true;
			  toaster.pop('error', '商品管理', '获取商家信息出错');
		  });
     }
	 $scope.selectCompanyData();
	 
	 $scope.insertOption = function(){
		 var newRowKey = ($scope.goodsData.options.length + 1) + '' + 1;
		 $scope.goodsData.options.push({
			 title: '',
			 child: [
			     {
				     title: '',
				     attr_id: newRowKey
			     }
			 ]
		 });
	 }
	 
     $scope.deleteOption = function(index){
    	 $scope.goodsData.options.splice(index, 1);
	 }
     
     function sortNumber(a, b)
     {
         return a - b;
     }
	 
     $scope.insertItemOption = function(index, item){
    	 if(item.child.length == 0){
    	     var newRowKey = (index + 1) + '' + 1;
    	 }
    	 else{
    		 var itemIndexArr = [];
    		 for(var i=0; i<item.child.length; i++){
    			 var attr_string = item.child[i].attr_id;
    			 var itemIndex = Number(attr_string.substring(1));
    			 itemIndexArr.push(itemIndex);
    		 }
    		 itemIndexArr.sort(sortNumber);
    		 var last = itemIndexArr.length - 1;
    		 var max = itemIndexArr[last] + 1;
    		 var newRowKey = (index + 1) + '' + max;
    	 }
    	 
    	 item.child.push(
			 {
			     title: '',
			     attr_id: newRowKey
		     }
    	 );
	 }
     
     $scope.deleteItemOption = function(item, index){
    	 item.splice(index, 1);
	 }
     
     $scope.goodsData.optionList = [];
     $scope.$watch('goodsData.options', function(new_val, old_val){
 	    if(new_val != old_val && $scope.goodsData.options.length > 0){
 	    	if($scope.goodsData.options.length == 1 && $scope.goodsData.options[0].child.length > 0){
 	    		var tempData = angular.copy($scope.goodsData.optionList);
 	    		$scope.goodsData.optionList = [];
 	    		angular.forEach($scope.goodsData.options[0].child, function(value, key){
 	    			if(value.title){
 	    				var num = 0;
 	    				for(var i=0; i<tempData.length; i++){
 	    					if(tempData[i].title == value.title){
 	    						num = tempData[i].num;
 	    					}
 	    				}
	 	    			$scope.goodsData.optionList.push({
	 	    				title: value.title,
	 	    				subTitle: null,
	 	    				num: num,
	 	    				attr_id: value.attr_id
	 	    			});
 	    			}
 	 		    });
 	    	}
 	    	else if($scope.goodsData.options.length == 2 && $scope.goodsData.options[0].child.length > 0 && $scope.goodsData.options[1].child.length > 0){
 	    		var tempData = angular.copy($scope.goodsData.optionList);
 	    		$scope.goodsData.optionList = [];
 	    		angular.forEach($scope.goodsData.options[0].child, function(firstValue, firstKey){
 	    			angular.forEach($scope.goodsData.options[1].child, function(secondValue, secondKey){
 	    				if(firstValue.title && secondValue.title){
 	    					var num = 0;
 	 	    				for(var i=0; i<tempData.length; i++){
 	 	    					if(tempData[i].title == firstValue.title && tempData[i].subTitle == secondValue.title){
 	 	    						num = tempData[i].num;
 	 	    					}
 	 	    				}
 	 	    				var new_attr_id = firstValue.attr_id + ';' + secondValue.attr_id;
		 	    			$scope.goodsData.optionList.push({
		 	    				title: firstValue.title,
		 	    				subTitle: secondValue.title,
		 	    				num: num,
		 	    				rowspan: $scope.goodsData.options[1].child.length,
		 	    				attr_id: new_attr_id
		 	    			});
 	    				}
 	    			});
 	 		    });
 	    	}
 	    }
     }, true);
     
     $scope.isDisplayNew = function(){
    	 if($scope.goodsData.is_option && $scope.goodsData.options.length <= 1){
    		 return true;
    	 }
    	 else{
    		 return false;
    	 }
     }
     
     $scope.rowTitle = function(index){
    	 if(index !== 0 && $scope.goodsData.optionList[index].title == $scope.goodsData.optionList[index-1].title){
    		 return '';
    	 }
    	 else{
    		 return $scope.goodsData.optionList[index].title;
    	 }
     }
          
     $scope.$watch('dateSelectBuy', function(new_val, old_val){
    	 if(new_val != old_val){
    		 
    	 }
     });
}]);
