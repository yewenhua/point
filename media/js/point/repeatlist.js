$(function(){
	var page = 1;
    var type = 1;
    
	$('.weui-navbar__item').on('click', function () {
        $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
        type = $(this).data('type');
        page = 1;
        $("#list").html('');
        $(".list-bottom-last").hide();
		$(".load-more").hide();
        list_data(1, function(){
        	
        });
    });
	
	$(".goback").click(function(){
		history.back(-1);
    });
	
	$(document).on('click', ".disable", function(){
		layer.msg('不在有效期内');
    });
    
    $(document).on('click', ".in_active", function(){
		layer.msg('额度已用完');
    });
	
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
			url: '/index.php/point/repeat_data_by_page',
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
						html += '<div class="weui-panel">';
							html += '<div class="weui-panel__bd">';
								html += '<div class="weui-media-box weui-media-box_text point-item-top wait-item-top repeat-item-top">';
									html += '<div class="left">开始：' + response.data.data[i]['begin_time'].substring(0, 16) + '</div>';
									html += '<div class="right">结束：' + response.data.data[i]['end_time'].substring(0, 16) + '</div>';
									html += '<div class="clear"></div>';
								html += '</div>';
								
								html += '<div class="weui-media-box weui-media-box_text point-item repeat-item">';
								    html += '<div class="left relative">';
										html += '<div class="rate"><img src="/media/img/repeat_06.png"/></div>';
										html += '<div class="value">' + response.data.data[i]['rate'] + '</div>';
									html += '</div>';
									html += '<div class="middle relative">';
										html += '<div class="rect">'
											var width = (response.data.data[i]['already_shot_point']/response.data.data[i]['limit_point']) * 100 + '%';
											html += '<div class="rect-value" style="width:'+ width +'"></div>';
										html += '</div>';
										html += '<div class="max">额度：<span>' + response.data.data[i]['limit_point'] + '</span></div>';
										html += '<div class="now">已用：<span>' + response.data.data[i]['already_shot_point'] + '</span></div>';
										var rate = (response.data.data[i]['already_shot_point']/response.data.data[i]['limit_point']) * 100;
										var percent = rate != 0 ? rate.toFixed(1) : 0;
										html += '<div class="rate"><span>' + percent + '%</span></div>';
									html += '</div>';
									html += '<div class="right relative">';
									    if(type == 1){
									    	var href = '/point/repeat?key='+ response.data.data[i]['key'];
									    	var style = '';
									    	var repeat_class = 'able';
									    	if(response.data.data[i]['status'] == 1){
									    		style = 'opacity: 0.3;'; 
									    		href = 'javascript:;';
									    		repeat_class = 'in_active';
									    	}
									    }
									    else{
									    	var href = 'javascript:;';
									    	var style = 'opacity: 0.3;';
									    	var repeat_class = 'disable';
									    }
									    html += '<a href="' + href + '" style="'+ style +'" class="'+ repeat_class +'">';
										    html += '<div class="btn" style="'+ style +'"><img src="/media/img/repeat_09.png"/></div>';
										    html += '<div class="value">复投</div>';
									    html += '</a>';
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
						location.href = '/member/login';
					}, 1500);
				}
				else{
					$(".nodata").remove();
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
