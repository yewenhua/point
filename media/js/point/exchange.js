$(function(){
	$(".goback").click(function(){
		location.href = '/member/center';
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
			url: '/index.php/point/exchange_data_by_page',
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
									    html += '<div class="value">+ ' + response.data.data[i]['point'] + '</div>';
									html += '</div>';
									html += '<div class="right">';
									    if(response.data.data[i]['type'] == 1){
									    	html += '<div class="status">充值</div>';
									    }
									    else if(response.data.data[i]['type'] == 2){
									    	html += '<div class="status">兑换商品</div>';
									    }
									    else if(response.data.data[i]['type'] == 3){
									    	html += '<div class="status">升级到服务中心</div>';
									    }
									    else if(response.data.data[i]['type'] == 4){
									    	html += '<div class="status">退款</div>';
									    }
									    else{
									    	html += '<div class="status">--</div>';
									    }
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