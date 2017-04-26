$(function(){
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
			url: '/index.php/article/getNewsList',
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
						html += '<div class="weui-panel weui-panel_access">';
							html += '<div class="weui-panel__bd">';
							    html += '<div class="weui-media-box weui-media-box_text">';
									html += '<h4 class="weui-media-box__title">' + response.data.data[i].title + '</h4>';
									if(response.data.data[i].img_url){
										html += '<a href="/article/detail?key='+ response.data.data[i].key +'">';
									        html += '<div class="article-img-face"><img src="' + response.data.data[i].img_url + '"/></div>';
									    html += '</a>';
									}
									html += '<p class="weui-media-box__desc">' + response.data.data[i].abstracts + '</p>';
							    html += '</div>';
							html += '</div>';
							html += '<div class="weui-panel__ft">';
								html += '<a href="/article/detail?key='+ response.data.data[i].key +'" class="weui-cell weui-cell_access weui-cell_link relative">';
									html += '<div class="weui-cell__bd time">' + response.data.data[i].created_at + '</div>';
									html += '<div class="detail">阅读全文</div>';
									html += '<span class="weui-cell__ft"></span>';
								html += '</a>';
							html += '</div>';
						html += '</div>';
					}
					$("#list").append(html);
					var size = $('.news_record .weui-panel').size();
					if(response.data.count > size){
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