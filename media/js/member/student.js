$(function(){
	$(".goback").click(function(){
		location.href = '/member/center';
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
    	$.ajax({
			url: '/index.php/member/student_data_by_page',
			type: 'POST',
			data: {
			    page: page,
			    num: num
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
								html += '<div class="weui-media-box weui-media-box_text exchange-point-item-top">';
									html += '<div class="title">用户编号：' + response.data.data[i]['mobile'] + '</div>';
								html += '</div>';
								
								html += '<div class="weui-media-box weui-media-box_text exchange-point-item student-point-item">';
								    html += '<div class="left">';
								        if(response.data.data[i]['name']){
								        	var name = response.data.data[i]['name'];
								        }
								        else{
								        	var name = '--';
								        }
										html += '<div class="value">' + name + '</div>';
									html += '</div>';
									html += '<div class="right">';
										html += '<div class="status">加入时间：' + response.data.data[i]['created_at'].substring(0,10) + '</div>';
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
    
    function loading(){
    	var index = layer.load(2, {
		    shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
    }
});