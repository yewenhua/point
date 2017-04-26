$(function(){
	$(".goback").click(function(){
		history.back(-1);
    });
	
    var page = 1;
	
	$(".load-more").click(function(){
    	var $this = $(this);
    	if(!$this.hasClass('on')){
    		$this.addClass('on');
    		page++;
    	    list_data(page, function(){
    	    	$this.removeClass('on');
    	    });
    	}
    });
    
    function list_data(page, callback){
    	var num = 10;
    	
    	$.ajax({
			url: '/index.php/point/consume_data_by_page',
			type: 'POST',
			data: {
			    page: page,
			    num: num,
			    type: type
			},
			dataType: 'json',
			cache: false,
			beforeSend: function() {
				loading();
			},
			success: function(response){
				if(response.code == 0 && response.data.data.length > 0){
					if(page == 1){
						$("#list").html('');
					}
					
					var html = '';
					for(var i=0; i<response.data.data.length; i++){
						html += '<div class="point-time-title">' + response.data.data[i]['created_at'] + '</div>';
						html += '<div class="weui-panel">';
							html += '<div class="weui-panel__bd">';
								html += '<div class="weui-media-box weui-media-box_text exchange-point-item-top">';
									html += '<div class="title">编号：' + response.data.data[i]['order_id'] + '</div>';
								html += '</div>';
								
								html += '<div class="weui-media-box weui-media-box_text exchange-point-item">';
									html += '<div class="left">';
									    if(response.data.data[i]['reason'] == 1){
									        html += '<div class="value">-' + response.data.data[i]['point'] + '</div>';
									    }
									    else{
									    	html += '<div class="value">+' + response.data.data[i]['point'] + '</div>';
									    }
									html += '</div>';
									html += '<div class="right">';
									    if(response.data.data[i]['type'] == 1){
									    	var typeName = '可用转入';
									    }
									    else if(response.data.data[i]['type'] == 2){
									    	var typeName = '积分转让';
									    }
									    else if(response.data.data[i]['type'] == 3){
									    	var typeName = '充值';
									    }
									    else if(response.data.data[i]['type'] == 4){
									    	var typeName = '兑换';
									    }
									    else if(response.data.data[i]['type'] == 5){
									    	var typeName = '升级';
									    }
									    else if(response.data.data[i]['type'] == 6){
									    	var typeName = '分享';
									    }
									    else if(response.data.data[i]['type'] == 7){
									    	var typeName = '报单';
									    }
									    else if(response.data.data[i]['type'] == 8){
									    	var typeName = '退款';
									    }
									    else if(response.data.data[i]['type'] == 9){
									    	var typeName = '推荐商家';
									    }
									    else if(response.data.data[i]['type'] == 10){
									    	var typeName = '兑充升级';
									    }
									    else if(response.data.data[i]['type'] == 11){
								    	    var typeName = '商家结算';
									    }
									    else if(response.data.data[i]['type'] == 12){
									    	var typeName = '订单结算';
									    }
									    else if(response.data.data[i]['type'] == 13){
									    	var typeName = '商品分享';
									    }
									    else if(response.data.data[i]['type'] == 14){
									    	var typeName = '分享结算';
									    }
									    else{
									    	var typeName = '--';
									    }
									    html += '<div class="status">' + typeName + '</div>';
									html += '</div>';
									html += '<div class="clear"></div>';
								html += '</div>';
							html += '</div>';
						html += '</div>';
					}
					$("#list").append(html);
					if(response.data.data.length == num){
						$(".list-bottom-last").hide();
						$(".load-more").show();
					}
					else{
						$(".list-bottom-last").show();
						$(".load-more").hide();
					}
				}
				else if(response.code == 9999 || response.code == 8888){
					layer.msg(response.message);
					setTimeout(function(){
						if(type == 'member'){
						    location.href = '/member/login';
						}
						else{
							location.href = '/service/login';
						}
					}, 1500);
				}
				else{
					html = '<div class="nodata">没有数据</div>';
					$("#list").append(html);
					$(".list-bottom-last").hide();
					$(".load-more").hide();
				}
				layer.closeAll('loading');
				callback();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				layer.msg('获取信息失败');
				layer.closeAll('loading');
				callback();
			}
		});
    }
    list_data(1, function(){
    	
    });
    
    function loading(){
    	var index = layer.load(2, {
		    shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
    }
});