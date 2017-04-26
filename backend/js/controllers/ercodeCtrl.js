angular.module('app')

.controller('ErcodeCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', 'divpage', 'FileUploader', '$document', '$timeout', '$state', function($scope, CommonFunction, DataLoad, toaster, divpage, FileUploader, $document, $timeout, $state) {
	$scope.data = {}; 
	$scope.data.bgImage = '';
	$scope.data.bg_url = '';
	$scope.data.json_data = [];
	$scope.data.isNew = true;
	$scope.loading = true;
	$scope.haveData = false;

	$scope.posterData = function(){
		$scope.loading = true;
		var promise = DataLoad.posterData({
			type: 1
		});
		return promise.then(function (returnData) {
			$scope.loading = false;
			if (returnData && returnData.code == 0) {
				$scope.data = returnData.data;
				$scope.haveData = true;
				$scope.data.bgImage = $scope.data.bg_url;
				$scope.data.json_data = angular.fromJson($scope.data.json_data);
				for(var i=0; i<$scope.data.json_data.length; i++){
					var index = i + 1;
					var type = $scope.data.json_data[i].type;
					if($scope.data.json_data[i].type == 'head'){
						var innerItem = '<img src="/backend/img/lufei.jpeg" />';
						var obj = $('<div class="drag" style="left:' + $scope.data.json_data[i].left +'px; top:' + $scope.data.json_data[i].top +'px; height:' + $scope.data.json_data[i].height +'px; width:' + $scope.data.json_data[i].width +'px;" type="' + type +'" index="' + index +'" style="z-index:' + index+'">' + innerItem +'<div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"> </div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div></div>');
					}
					else if($scope.data.json_data[i].type == 'qr'){
						var innerItem = '<img src="/backend/img/qr.jpg" />';
						var obj = $('<div class="drag" style="left:' + $scope.data.json_data[i].left +'px; top:' + $scope.data.json_data[i].top +'px; height:' + $scope.data.json_data[i].height +'px; width:' + $scope.data.json_data[i].width +'px;" type="' + type +'" index="' + index +'" style="z-index:' + index+'">' + innerItem +'<div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"> </div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div></div>');
					}
					else if($scope.data.json_data[i].type == 'nickname'){
						var innerItem = '<div class="text" style="font-size:'+ $scope.data.json_data[i].fontSize +'; color:'+ $scope.data.json_data[i].color +';">昵称</div>';
						var obj = $('<div class="drag" color="'+ $scope.data.json_data[i].color +'" size="'+ $scope.data.json_data[i].fontSize +'" style="left:' + $scope.data.json_data[i].left +'px; top:' + $scope.data.json_data[i].top +'px; height:' + $scope.data.json_data[i].height +'px; width:' + $scope.data.json_data[i].width +'px;" type="' + type +'" index="' + index +'" style="z-index:' + index+'">' + innerItem +'<div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"> </div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div></div>');
					}

			        $('#poster').append(obj);
			        bindEvents(obj);
				}
				$scope.data.isNew = false;
			} 
			else {
				$scope.haveData = false;
				$scope.data.bgImage = '';
				$scope.data.json_data = [];
				$scope.data.isNew = true;
			}
		}, function () {
			$scope.loading = false;
			$scope.haveData = false;
			toaster.pop('error', '海报设置', '获取数据出错！');
		});
	}
	$scope.posterData();

	$scope.submitBtnHtml = '提交';
	$scope.submitActive = false;
	$scope.submitUpload = function() {
		if($scope.submitActive == false){
			  $scope.submitActive = true;
			  if($scope.uploader.queue.length <= 0 && !$scope.data.isNew){
				  $scope.editPoster();
			  }
			  else{
				  if($scope.uploader.queue.length >= 1){
				      uploader.uploadAll(); 
				  }
				  else{
					  toaster.pop('error', '海报设置', "海报背景不能为空");
				  }
			  }
		}
	}
	
	$scope.editPoster = function() {
		if($scope.data.isNew){
			if($scope.uploader.queue.length <= 0){
				$scope.submitActive = false;
				toaster.pop('error', '海报设置', "背景图片不能为空");
				return false;
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
		
		$scope.data.json_data = [];
		var dragArray = document.querySelectorAll('.drag');
		for(var i=0; i<dragArray.length; i++){
			var temp = {};
			var type = $(dragArray[i]).attr('type');
			var child = '';
			var itemData = {
				type: type,
				left: $(dragArray[i]).position().left,
				top: $(dragArray[i]).position().top
			};
			
			if(type == 'head' || type == 'qr'){
				child = $(dragArray[i]).children('img');
				itemData.width = $(dragArray[i]).width();
				itemData.height = $(dragArray[i]).height();
			}
			else if(type == 'desc' || type == 'nickname'){
				child = $(dragArray[i]).children('.text');
				itemData.fontSize = child.css('font-size');
				itemData.color = child.css('color');
				itemData.height = child.height();
			}
			
			$scope.data.json_data.push(itemData);
		}

		var promise = DataLoad.editPoster({
			id: $scope.data.isNew ? '' : $scope.data.id,
			json_data: $scope.data.json_data,
			bg_url: $scope.data.bg_url,
			type: 1
		});
		return promise.then(function (returnData) {
			$scope.submitActive = false;
			if (returnData && returnData.code == 0) {
				toaster.pop('success', '海报设置', "保存成功");
				//$scope.posterData();
			} 
			else {
				toaster.pop('error', '海报设置', returnData.message);
			}
		}, function () {
			$scope.submitActive = false;
			toaster.pop('error', '海报设置', '保存海报出错');
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
			    toaster.pop('error', '海报设置', "上传文件扩展名必须为：.jpg .png .jpeg .gif");
			    
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
			$scope.editPoster();
		}
		else{
			$scope.submitActive = false;
			toaster.pop('error', '海报设置', response.answer);
		}
	};
	
	function creatEle(type){
		if($scope.data.bgImage == ''){
        	layer.msg('海报背景不能为空');
            return;
        }
        
        var img = "";
        if(type == 'qr'){
            img = '<img src="/backend/img/qr.jpg" />';
        }
        else if(type == 'head'){
       	    img = '<img src="/backend/img/lufei.jpeg" />';
        }
        else if(type == 'nickname'){
            img = '<div class=text>昵称</div>';
        }
        else if(type == 'desc'){
            img = '<div class=text>描述</div>';
        }
        
        var index = $('#poster .drag').length + 1;
        var obj = $('<div class="drag" type="' + type +'" index="' + index +'" style="z-index:' + index+'">' + img+'<div class="rRightDown"> </div><div class="rLeftDown"> </div><div class="rRightUp"> </div><div class="rLeftUp"> </div><div class="rRight"> </div><div class="rLeft"> </div><div class="rUp"> </div><div class="rDown"></div></div>');
        
        $('#poster').append(obj);
        bindEvents(obj);
	}
	
	angular.element(document.querySelector('.btn-nickname')).on('click',function(evt) {
		var type = $(this).data('type');
		creatEle(type);
    });
	
	angular.element(document.querySelector('.btn-head')).on('click',function(evt) {
		var type = $(this).data('type');
		creatEle(type);
    });
	
	angular.element(document.querySelector('.btn-desc')).on('click',function(evt) {
		var type = $(this).data('type');
		//creatEle(type);
    });
	
	angular.element(document.querySelector('.btn-qr')).on('click',function(evt) {
		var type = $(this).data('type');
		creatEle(type);
    });

	function bindEvents(obj){
        var index = obj.attr('index');
        var rs = new Resize(obj, { Max: true, mxContainer: "#poster" });
        rs.Set($(".rRightDown",obj), "right-down");
        rs.Set($(".rLeftDown",obj), "left-down");
        rs.Set($(".rRightUp",obj), "right-up");
        rs.Set($(".rLeftUp",obj), "left-up");
        rs.Set($(".rRight",obj), "right");
        rs.Set($(".rLeft",obj), "left");
        rs.Set($(".rUp",obj), "up");
        rs.Set($(".rDown",obj), "down"); 
        rs.Scale = true;
        var type = obj.attr('type');
        if(type=='nickname' || type=='desc' || type=='head' || type=='qr'){
            rs.Scale = false;
        }

        new Drag(obj, { Limit: true, mxContainer: "#poster" });
        $('.drag .remove').unbind('click').click(function(){
            $(this).parent().remove();
        });  
    }

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