$(function(){
	var hash = location.hash;
	var sess_html = sessionStorage.center_html;
	var page = 1;
	var isLoading = false;
	var goods_width = $(".weui-grid__icon img").eq(0).width();
    
    var $searchBar = $('#searchBar'),
        $searchText = $('#searchText'),
        $searchInput = $('#searchInput'),
        $searchClear = $('#searchClear'),
        $searchSure = $('#searchSure');
    
    if(goods_width > 20){
        $(".weui-grid__icon img").css({'height': goods_width + 'px'});
    }
    $searchSure.removeClass('on');

    function cancelSearch(){
        $searchBar.removeClass('weui-search-bar_focusing');
        $searchText.show();
    }

    $searchText.on('click', function(){
        $searchBar.addClass('weui-search-bar_focusing');
        $searchInput.focus();
    });

    $searchClear.on('click', function(){
        $searchInput.focus()
        $searchInput.val('');
    });
    
    $searchSure.on('click', function(e){
    	e.stopPropagation();
        var searchkey = $searchInput.val();
        if(!searchkey){
			cancelSearch();
			var href = '/mall/goodslist?key=all';
		}
        else{
        	var href = '/mall/goodslist?key=all&wd=' + searchkey;
        }
    	$searchInput.blur();
    	location.href = encodeURI(encodeURI(href));
    });
    
    $searchInput.on('keypress', function(e){
    	e.stopPropagation();
    	if(e.which == 13){
    		var searchkey = $searchInput.val();
            if(!searchkey){
    			cancelSearch();
    			var href = '/mall/goodslist?key=all';
    		}
            else{
                var href = '/mall/goodslist?key=all&wd=' + searchkey;
            }
        	location.href = encodeURI(encodeURI(href));
        	return false;
		}
    });
    
    $searchInput.on('blur', function () {
        //if(!this.value.length) cancelSearch();
    });
    
    $(".load-more").click(function(){
    	var $this = $(this);
    	if(!$this.hasClass('on')){
    		$this.addClass('on');
    		page++;
    		page_data(page, function(){
    	    	$this.removeClass('on');
    	    });
    	}
    });
    
    function page_data(page, callback){
    	var num = 10;
    	var searchkey = $searchInput.val();
    	if(!$searchSure.hasClass('on')){
			$searchSure.addClass('on');
			isLoading = true;
			
			$.ajax({
	            url: '/mall/recommend_goods',
	            type: 'POST',
	            data: {
				    searchkey: '',
				    num: num,
				    page: page
	            },
	            dataType: 'json',
	            cache: false,
	            beforeSend: function() {
	            	if(page == 1){
	            	    loading();
	            	}
	            },
	            success: function(response){
	            	if(page == 1){
	            	    $("#list").html('');
	            	}
	            	
	                if(response.code == 0){
	                	var html = '';
						for(var i=0; i<response.data.data.length; i++){
							html += '<a href="/mall/detail?key=' + response.data.data[i]['key'] + '" class="weui-grid">';
								html += '<div class="weui-grid__icon">';
								   html += '<img src="/backend/uploads/' + response.data.data[i]['face'] + '" alt="">';
								html += '</div>';
								html += '<p class="weui-grid__label center-title">' + response.data.data[i]['name'] + '</p>';
								if(response.data.data[i]['model'] == 1){
									var exchange = '购物券' + response.data.data[i]['point_price'] + '分';
								}
								else if(response.data.data[i]['model'] == 2){
									var exchange = '消费积分' + response.data.data[i]['point_price'] + '分';
								}
								else{
									var exchange = '消费积分' + response.data.data[i]['point_price'] + '分 + 现金' + response.data.data[i]['cash_price'] + '元';
								}
								html += '<p class="weui-grid__label center-price">' + exchange + '</p>';
							html += '</a>';
						}
						$("#list").append(html);
						if(goods_width > 20){
			    		    $(".weui-grid__icon img").css({'height': goods_width + 'px'});
			    		}
						totalPage = Math.ceil(response.data.count/num);
						
						if(page < totalPage){
							$(".list-bottom-last").hide();
							$(".load-more").show();
						}
						else{
							$(".list-bottom-last").show();
							$(".load-more").hide();
						}
						
						location.hash = '#' + page;
						sessionStorage.center_html = $("#list").html();
						sessionStorage.totalPageCenter = totalPage;
						sessionStorage.searchkeyCenter = searchkey;
	                }
	                else{
	                	var size = $(".weui-grid").size();
	                	if(size > 0){
	                		$(".list-bottom-last").show();
							$(".load-more").hide();
	                	}
	                	else{
	                		layer.msg('没有数据');
	                		$(".list-bottom-last").hide();
							$(".load-more").hide();
	                	}
	                }
	                $searchSure.removeClass('on');
	                callback();
	                if(page == 1){
	                    layer.closeAll('loading');
	                }
	                isLoading = false;
	            },
	            error: function(XMLHttpRequest, textStatus, errorThrown){
	                layer.msg('未知错误');
	                $searchSure.removeClass('on');
	                callback();
	                isLoading = false;
	                if(page == 1){
	                    layer.closeAll('loading');
	                }
	            }
	        });
		}
    }
    
    if(hash && hash.indexOf('#') != -1 && hash.split('').length != 1 && sess_html){
    	page = hash.substring(1);
		totalPage = Number(sessionStorage.totalPageCenter);
		var top = Number(sessionStorage.scrollTopCenter);
		var arr = hash.split('');
		var searchkey = sessionStorage.searchkeyCenter;
		if(searchkey){
			$searchInput.val(searchkey);
		}
		isLoading = false;
		
		$("#list").html('');
		$("#list").html(sess_html);
		if(page < totalPage){
			$(".list-bottom-last").hide();
			$(".load-more").show();
		}
		else{
			$(".list-bottom-last").show();
			$(".load-more").hide();
		}
		
		if(top > 0){
			$(window).scrollTop(top);
		}
	}
    else{
    	location.hash = '#1';
    	sessionStorage.center_html = $("#list").html();
    	sessionStorage.totalPageCenter = totalPage;
    	sessionStorage.searchkeyCenter = $searchInput.val();
    }
    
    function loading(){
    	var index = layer.load(2, {
		    shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
    }
    
    $(window).scroll(function(){
    	sessionStorage.scrollTopCenter = $(window).scrollTop();
    	
	    // 当滚动到最底部以上100像素时， 加载新内容  
	    if ($(document).height() - $(this).scrollTop() - $(this).height()<100) {
	    	if(!isLoading && page < totalPage){
	    		$(".load-more").show();
		    	page++;
		    	page_data(page, function(){
		    		
		    	});
	    	}
	    };  
	});  
});