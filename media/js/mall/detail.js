$(function(){
	var can_buy = false;
	function buy(type){
		if(can_buy){
			layer.msg('提交中…');
			if(type == 1){
				var num = Number($("#num").val());
			}
			else{
				var num = Number($("#num-second").val());
			}
			
			var buy_goods_attr = $("#buy_goods_attr").val();
			var buy_goods_key = $("#buy_goods_key").val();
			$("#buy_num").val(num);
			
			if(num && buy_goods_key){
				var monthTotal = already_buy + num;
				if(limit_buy == 0 || monthTotal <= limit_buy){
					if(level == 99 || level > 0){
						//99为没登录，大于0不是普通会员
			            $("#submit_form").submit();
					}
					else{
						//登录为普通会员
						layer.msg('普通会员不能购买');
					}
				}
				else{
					var msg = '该商品每位用户限购' + limit_buy + '份';
					layer.msg(msg);
				}
			}
			else{
				layer.msg('参数错误');
			}
		}
		else{
			layer.msg('该商品暂未开放购买');
		}
	}
	
	$("#submit").click(function(){
		if(can_buy){
			if(options && options.is_option == 1){
				is_share(2);
				actionsheet();
			}
			else{
				buy(1);
			}
		}
		else{
			layer.msg('该商品暂未开放购买');
		}
	});
	
	$("#iosActionsheetCancel").click(function(){
		var buy_goods_attr = $("#buy_goods_attr").val();
		if(buy_goods_attr){
			var goods_attr_arr = [];
			if(buy_goods_attr.indexOf(';') !== -1){
				goods_attr_arr = buy_goods_attr.split(';');
			}
			else{
				goods_attr_arr = [buy_goods_attr];
			}
			
			for(var i=0; i<options.options.length; i++){
				var flag = false;
				for(var j=0; j<goods_attr_arr.length; j++){
					var firstWord = goods_attr_arr[j][0];
					var attrSortNum = i + 1;
					if(firstWord == attrSortNum){
						flag = true;
					}
				}
				
				if(!flag){
					layer.msg('请选择' + options.options[i].title);
					return false;
				}
			}
		}
		else{
			layer.msg('请选择' + options.options[0].title);
			return false;
		}
		
		hideActionSheet();
		buy(2);
	});
	
	$("#submit-two").click(function(){
		layer.msg('没有库存了');
	});
	
	$(".goback").click(function(){
		history.back();
    });
	
	var width = $(".alternate-content-detail li").width();
	$(".alternate-content").height(width);
	//$("body").css({'height':'auto'});
	
	
	var $iosActionsheet = $('#iosActionsheet');
    var $iosMask = $('#iosMask');

    function hideActionSheet() {
        $iosActionsheet.removeClass('weui-actionsheet_toggle');
        $iosMask.fadeOut(200);
        $("#guige-img").hide();
    }

    $iosMask.on('click', hideActionSheet);
    $(".goods-size-select").on("click", function(){
    	if(can_buy){
    		is_share(2);
    	    actionsheet();
    	}
    	else{
    		layer.msg('该商品暂未开放购买');
    	}
    });
    
    function actionsheet(){
    	$("#guige-img").show();
        $iosActionsheet.addClass('weui-actionsheet_toggle');
        $iosMask.fadeIn(200);
    }
    
    //sku管理
    var focusNum = 0;
    $(".decrease").click(function(){
		var total = $("#num-second").data('total');
		if(total >= 1){
	    	var num = Number($("#num-second").val());
	        if(num <= 1){
	        	$(this).addClass('disabled');
	        	focusNum = num;
	        }
	        else {
	        	var new_num = num - 1;
	        	if(new_num > 1){
	        		$(this).removeClass('disabled');
	        	}
	        	else{
	        		$(this).addClass('disabled');
	        	}
	        	
	        	$(".increase").removeClass('disabled');
	        	$("#num-second").val(new_num);
	        	focusNum = num;
	        }
    	}
    	else{
    		$(".increase").removeClass('disabled');
    		$(this).removeClass('disabled');
    		$("#num-second").val(0);
    		focusNum = 1;
    	}
	});
    
    $(".increase").click(function(){
    	var total = $("#num-second").data('total');
    	if(total >= 1){
    		var num = Number($("#num-second").val());
	        if(num < total){
	        	var new_num = num + 1;
	        	if(new_num < total){
	        		$(this).removeClass('disabled');
	        	}
	        	else{
	        		$(this).addClass('disabled');
	        	}
	        	
	        	$(".decrease").removeClass('disabled');
	        	$("#num-second").val(new_num);
	        }
	        else {
	        	$(this).addClass('disabled');
	        	$("#num-second").val(num)
	        }
	        focusNum = num;
    	}
    	else{
    		$(".decrease").removeClass('disabled');
    		$(this).removeClass('disabled');
    		$("#num-second").val(0);
    		focusNum = 1;
    	}
	});
    
    $("#num-second").change(function(){
    	var total = $("#num-second").data('total');
    	var inputNum = Number($(this).val());
    	if(inputNum >= 1 && inputNum <= total){
    		if(inputNum == total){
    			$(".increase").addClass('disabled');
    		}
    		else{
    			$(".increase").removeClass('disabled');
    		}
    		
    		if(inputNum > 1){
    			$(".decrease").removeClass('disabled');
    		}
    		else{
    			$(".decrease").addClass('disabled');
    		}
    	}
    	else{
    		$(".decrease").addClass('disabled');
    		$(".increase").addClass('disabled');
    	}
	});
    
    $("#num-second").keyup(function(){
    	var total = $("#num-second").data('total');
    	var inputNum = Number($(this).val());
    	if(inputNum > total){
    		if(focusNum >= total){
    			$(".increase").addClass('disabled');
    		}
    		else{
    			$(".increase").removeClass('disabled');
    		}
    		
    		if(focusNum > 1){
    			$(".decrease").removeClass('disabled');
    		}
    		else{
    			$(".decrease").addClass('disabled');
    		}
    		
    		$("#num-second").val(1);
    		layer.msg('不在合法购买数量以内');
    	}
	});
    
    
    
    var data = skuData;

    //保存最后的组合结果信息
    var SKUResult = {};
    //获得对象的key
    function getObjKeys(obj) {
        if (obj !== Object(obj)) throw new TypeError('Invalid object');
        var keys = [];
        for (var key in obj){
            if (Object.prototype.hasOwnProperty.call(obj, key)){
                keys[keys.length] = key;
            }
        }
        return keys;
    }

    //把组合的key放入结果集SKUResult
    function add2SKUResult(combArrItem, sku) {
  	    var key = combArrItem.join(";");
        if(SKUResult[key]) {//SKU信息key属性·
            SKUResult[key].count += sku.count;
        } else {
            SKUResult[key] = {
                count : sku.count
            };
        }
    }

    //初始化得到结果集
    function initSKU() {
        var i, j, skuKeys = getObjKeys(data);
        for(i = 0; i < skuKeys.length; i++) {
            var skuKey = skuKeys[i];//一条SKU信息key
            var sku = data[skuKey];	//一条SKU信息value
            var skuKeyAttrs = skuKey.split(";"); //SKU信息key属性值数组
  		    skuKeyAttrs.sort(function(value1, value2) {
  			    return parseInt(value1) - parseInt(value2);
  		    });

            //对每个SKU信息key属性值进行拆分组合
  		    var combArr = combInArray(skuKeyAttrs);
  		    for(j = 0; j < combArr.length; j++) {
  			     add2SKUResult(combArr[j], sku);
  		    }

            //结果集接放入SKUResult
            SKUResult[skuKeyAttrs.join(";")] = {
                count:sku.count
            }
        }
    }

    /**
     * 从数组中生成指定长度的组合
     * 方法: 先生成[0,1...]形式的数组, 然后根据0,1从原数组取元素，得到组合数组
     */
    function combInArray(aData) {
	  	if(!aData || !aData.length) {
	  		return [];
	  	}
	
	  	var len = aData.length;
	  	var aResult = [];
	
	  	for(var n = 1; n < len; n++) {
	  		var aaFlags = getCombFlags(len, n);
	  		while(aaFlags.length) {
	  			var aFlag = aaFlags.shift();
	  			var aComb = [];
	  			for(var i = 0; i < len; i++) {
	  				aFlag[i] && aComb.push(aData[i]);
	  			}
	  			aResult.push(aComb);
	  		}
	  	}
	  	
	  	return aResult;
    }


    /**
     * 得到从 m 元素中取 n 元素的所有组合
     * 结果为[0,1...]形式的数组, 1表示选中，0表示不选
     */
    function getCombFlags(m, n) {
	  	if(!n || n < 1) {
	  		return [];
	  	}
	
	  	var aResult = [];
	  	var aFlag = [];
	  	var bNext = true;
	  	var i, j, iCnt1;
	
	  	for (i = 0; i < m; i++) {
	  		aFlag[i] = i < n ? 1 : 0;
	  	}
	
	  	aResult.push(aFlag.concat());
	
	  	while (bNext) {
	  		iCnt1 = 0;
	  		for (i = 0; i < m - 1; i++) {
	  			if (aFlag[i] == 1 && aFlag[i+1] == 0) {
	  				for(j = 0; j < i; j++) {
	  					aFlag[j] = j < iCnt1 ? 1 : 0;
	  				}
	  				aFlag[i] = 0;
	  				aFlag[i+1] = 1;
	  				var aTmp = aFlag.concat();
	  				aResult.push(aTmp);
	  				if(aTmp.slice(-n).join("").indexOf('0') == -1) {
	  					bNext = false;
	  				}
	  				break;
	  			}
	  			aFlag[i] == 1 && iCnt1++;
	  		}
	  	}
	  	return aResult;
    } 

  
    if(options && options.is_option == 1){
        initSKU();
    }
    
	$('.sku').each(function() {
		var self = $(this);
		var attr_id = self.attr('attr_id');
		if(!SKUResult[attr_id]) {
			self.addClass('gray');
		}
	}).click(function() {
		var self = $(this);
		if(self.hasClass('gray')){
			return false;
		}

		//选中自己，兄弟节点取消选中
		self.toggleClass('bh-sku-selected').siblings().removeClass('bh-sku-selected');
		
		//已经选择的节点
		var selectedObjs = $('.bh-sku-selected');

		if(selectedObjs.length) {
			//获得组合key库存
			var selectedIds = [];
			selectedObjs.each(function() {
				selectedIds.push($(this).attr('attr_id'));
			});
			selectedIds.sort(function(value1, value2) {
			    //按照数值的大小对数字进行排序  升序
				return parseInt(value1) - parseInt(value2);
			});
			
			var len = selectedIds.length;
			var count = Number(SKUResult[selectedIds.join(';')].count);
			
			
			//追加begin
			updateDisplay(count, 1);
			//追加end
			
			
			//用已选中的节点验证待测试节点 underTestObjs
			$(".sku").not(selectedObjs).not(self).each(function() {
				var siblingsSelectedObj = $(this).siblings('.bh-sku-selected');
				var testAttrIds = [];//从选中节点中去掉选中的兄弟节点
				if(siblingsSelectedObj.length) {
					var siblingsSelectedObjId = siblingsSelectedObj.attr('attr_id');
					for(var i = 0; i < len; i++) {
						(selectedIds[i] != siblingsSelectedObjId) && testAttrIds.push(selectedIds[i]);
					}
				} else {
					testAttrIds = selectedIds.concat();
				}
				testAttrIds = testAttrIds.concat($(this).attr('attr_id'));
				testAttrIds.sort(function(value1, value2) {
					return parseInt(value1) - parseInt(value2);
				});
				if(!SKUResult[testAttrIds.join(';')]) {
					$(this).addClass('gray').removeClass('bh-sku-selected');
				} else {
					$(this).removeClass('gray');
				}
			});
		} else {
			//设置默认库存
			
			//追加begin
			updateDisplay(defaultTotal, 2);
			//追加end
			
			//设置属性状态
			$('.sku').each(function() {
				SKUResult[$(this).attr('attr_id')] ? $(this).removeClass('gray') : $(this).addClass('gray').removeClass('bh-sku-selected');
			})
		}
	});
	
	function updateDisplay(num, type){
		$('.actionsheet-kucun').html(num);
		$("#num-second").data('total', num);
		var now_num = $("#num-second").val();
		if(num >= now_num ){
			$(".increase").removeClass('disabled');
		}
		else{
			$("#num-second").val(1);
			$(".decrease").addClass('disabled');
		}
		
		var sel_option = '';
		var sel_attr = '';
		if(type == 1){
			sel_option = '已选择 ';
			var selectedObjs = $('.bh-sku-selected');
			selectedObjs.each(function() {
				sel_option = sel_option + ' "' + $(this).html() + '"';
				sel_attr = sel_attr ? (sel_attr + ';' + $(this).attr('attr_id')) : $(this).attr('attr_id');
			});
		}
		else{
			sel_option = '请选择';
			sel_attr = '';
			$(".guige-child-title").each(function(){
				var self = $(this);
				var title = self.html();
				sel_option = sel_option + ' ' + title;
			});
		}
		$('.guige-selected-title').html(sel_option);
		$("#out-guige-sel").html(sel_option);
		$("#buy_goods_attr").val(sel_attr);
	}
	
	function timeup(){
		var time = Math.ceil((buy_time_seconds * 1000 - (new Date()).getTime())/1000);
	    if(time >= 0){
		    var day = Math.floor(time/(24 * 60 * 60));
		    var hour = Math.floor((time - (24 * 60 * 60) * day)/(60 * 60));
		    var minute = Math.floor((time - (24 * 60 * 60) * day - (60 * 60) * hour)/60);
		    var second = Math.floor(time - (24 * 60 * 60) * day - (60 * 60) * hour - minute * 60);
		    var day_html = day >= 10 ? day : '0' + day;
		    var hour_html = hour >= 10 ? hour : '0' + hour;
		    var minute_html = minute >= 10 ? minute : '0' + minute;
		    var second_html = second >= 10 ? second : '0' + second;
		    can_buy = false;
	    }
	    else{
		    var day_html = '00';
		    var hour_html = '00';
		    var minute_html = '00';
		    var second_html = '00';
 	    }
	    $('.day').html(day_html);
	    $('.hour').html(hour_html);
	    $('.minute').html(minute_html);
	    $('.second').html(second_html);
	    
	    if(time < 0){
	    	$('.buy_time_detail_page').remove();
	    	can_buy = true;
	    }
	}
	
	if(is_time_buy == 1){
		timeup();
		var temp = setInterval(function(){
			timeup();
		}, 1000);
	}
	else{
		can_buy = true;
	}
	
	$(".share-content").click(function(){
		if(can_buy){
			is_share(1);
			actionsheet();
		}
		else{
			layer.msg('该商品暂未开放购买');
		}
	});
	
	function is_share(type){
		if(type == 1){
			$("#share_price").show();
			$("#submitShareAction").show();
			$("#goods_price").hide();
			$("#iosActionsheetCancel").hide();
			$("#guige_kucun").hide();
			$("#share_kucun").show();
			$(".guige-selected-title").hide();
			$(".category").hide();
			$("#num-select-normal").hide();
			$("#num-select-share").show();
		}
		else{
			$("#share_price").hide();
			$("#submitShareAction").hide();
			$("#goods_price").show();
			$("#iosActionsheetCancel").show();
			$("#guige_kucun").show();
			$("#share_kucun").hide();
			$(".guige-selected-title").show();
			$(".category").show();
			$("#num-select-normal").show();
			$("#num-select-share").hide();
		}
	}
	
	$('#submitShareAction').on('click', function(){
		$("#share_num").val($("#num-share").val());
		hideActionSheet();
		layer.msg('提交中…');
		$("#share_form").submit();
	});
	
	var focusNumShare = 0;
    $(".decrease-share").click(function(){
		var total = $("#num-share").data('total');
		if(total >= 1){
	    	var num = Number($("#num-share").val());
	        if(num <= 1){
	        	$(this).addClass('disabled');
	        	focusNumShare = num;
	        }
	        else {
	        	var new_num = num - 1;
	        	if(new_num > 1){
	        		$(this).removeClass('disabled');
	        	}
	        	else{
	        		$(this).addClass('disabled');
	        	}
	        	
	        	$(".increase-share").removeClass('disabled');
	        	$("#num-share").val(new_num);
	        	focusNumShare = num;
	        }
    	}
    	else{
    		$(".increase-share").removeClass('disabled');
    		$(this).removeClass('disabled');
    		$("#num-share").val(0);
    		focusNumShare = 1;
    	}
	});
    
    $(".increase-share").click(function(){
    	var total = $("#num-share").data('total');
    	if(total >= 1){
    		var num = Number($("#num-share").val());
	        if(num < total){
	        	var new_num = num + 1;
	        	if(new_num < total){
	        		$(this).removeClass('disabled');
	        	}
	        	else{
	        		$(this).addClass('disabled');
	        	}
	        	
	        	$(".decrease-share").removeClass('disabled');
	        	$("#num-share").val(new_num);
	        }
	        else {
	        	$(this).addClass('disabled');
	        	$("#num-share").val(num)
	        }
	        focusNumShare = num;
    	}
    	else{
    		$(".decrease-share").removeClass('disabled');
    		$(this).removeClass('disabled');
    		$("#num-share").val(0);
    		focusNumShare = 1;
    	}
	});
    
    $("#num-share").change(function(){
    	var total = $("#num-share").data('total');
    	var inputNum = Number($(this).val());
    	if(inputNum >= 1 && inputNum <= total){
    		if(inputNum == total){
    			$(".increase-share").addClass('disabled');
    		}
    		else{
    			$(".increase-share").removeClass('disabled');
    		}
    		
    		if(inputNum > 1){
    			$(".decrease-share").removeClass('disabled');
    		}
    		else{
    			$(".decrease-share").addClass('disabled');
    		}
    	}
    	else{
    		$(".decrease-share").addClass('disabled');
    		$(".increase-share").addClass('disabled');
    	}
	});
    
    $("#num-share").keyup(function(){
    	var total = $("#num-share").data('total');
    	var inputNum = Number($(this).val());
    	if(inputNum > total){
    		if(focusNumShare >= total){
    			$(".increase-share").addClass('disabled');
    		}
    		else{
    			$(".increase-share").removeClass('disabled');
    		}
    		
    		if(focusNumShare > 1){
    			$(".decrease-share").removeClass('disabled');
    		}
    		else{
    			$(".decrease-share").addClass('disabled');
    		}
    		
    		$("#num-share").val(1);
    		layer.msg('不在合法购买数量以内');
    	}
	});
});