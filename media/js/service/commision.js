$(function(){
	$(".goback").click(function(){
		location.href = '/member/wallet/service';
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
			url: '/index.php/service/commision_data_by_page',
			type: 'POST',
			data: {
			    page: page,
			    num: num,
			    categoty: categoty
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
						html += '<div class="weui-panel">';
							html += '<div class="weui-panel__bd">';
								html += '<div class="weui-media-box weui-media-box_text point-item-top record-item-top">';
									html += '<div class="left">编号：' + response.data.data[i]['order_id'] + '</div>';
									html += '<div class="right">' + response.data.data[i]['created_at'].substring(0, 16) + '</div>';
									html += '<div class="clear"></div>';
								html += '</div>';
								
								html += '<div class="weui-media-box weui-media-box_text point-item commision-item">';
									html += '<div class="left">';
									    html += '<div class="title">佣金</div>';
									    html += '<div class="value">+ ' + response.data.data[i]['commision'] + '</span></div>';
									html += '</div>';
									html += '<div class="middle">';
									    html += '<div class="title">报单金额</div>';
									    html += '<div class="value">&yen; ' + response.data.data[i]['order_money'] + '</span></div>';
									html += '</div>';
									html += '<div class="right">';
									    html += '<div class="name">报单人：' + response.data.data[i]['name'] + '</div>';
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
						location.href = '/service/login';
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