$(function(){
	var previous_url = document.referrer;
	sessionStorage.previous_url = previous_url;

	var hash = location.hash;
	var sess_html = sessionStorage.goods_html;
	var page = 1;
	var totalPage = 1;
	var isLoading = true;
	var $searchBar = $('#searchBar'),
	    $searchText = $('#searchText'),
	    $searchInput = $('#searchInput'),
	    $searchClear = $('#searchClear'),
	    $searchSure = $('#searchSure');
	
	if(wd){
		$searchInput.val(wd);
		$searchBar.addClass('weui-search-bar_focusing');
	    $searchInput.focus();
	}
	
	function cancelSearch(){
	    $searchBar.removeClass('weui-search-bar_focusing');
	    $searchText.show();
	}
	
	$searchText.on('click', function(){
	    $searchBar.addClass('weui-search-bar_focusing');
	    $searchInput.focus();
	});
	
	$searchClear.on('click', function(){
	    $searchInput.focus();
	    $searchInput.val('');
	});
	$searchSure.on('click', function(e){
		e.stopPropagation();
		var searchkey = $("#searchInput").val();
		if(!searchkey){
			cancelSearch();
		}
		$(".load-more").hide();
		page = 1;
	    $searchInput.blur();
	    page_info(page);
	    return false;
	});
	
	$searchInput.on('keypress', function(e){
		e.stopPropagation();
    	if(e.which == 13){
    		var searchkey = $("#searchInput").val();
    		if(!searchkey){
    			cancelSearch();
    		}
    		$(".load-more").hide();
    		page = 1;
    		page_info(page);
    		return false;
		}
    });

	$(".list-header-sort li").click(function(){
		$(".list-header-sort li").removeClass('active');
		$(this).addClass('active');
		$(".load-more").hide();
		page = 1;
		page_info(page);
	});
	
	function page_info(page){
		$("#table-list").show();
		var obj = {}; 
		obj.num = 10;
		
		var searchParam = {};
		searchParam.searchkey = $("#searchInput").val();
		searchParam.sort = $(".list-header-sort li.active").data('sort');
		searchParam.path = $("#path").val();
		isLoading = true;
		
		$.ajax({
			url: '/index.php/mall/list_page_info',
			type: 'POST',
			data: {
			    page: page,
			    num: obj.num,
			    searchkey: searchParam.searchkey,
			    sort: searchParam.sort,
			    path: searchParam.path
			},
			dataType: 'json',
			cache: false,
			beforeSend: function() {
				if(page == 1){
				    loading();
				    $("#list").html('');
				}
			},
			success: function(response){
				if(response.code == 0 && response.data.data.length > 0){
					var html = '';
					for(var i=0; i<response.data.data.length; i++){
						var sort_num = i+1 + (page - 1) * obj.num;
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
					var eleObj = $("#list").children('a').eq(0);
					var goods_width = eleObj.width();
					var imgObj = $("#list").find('img')
					if(goods_width > 0){
					    imgObj.css({'height': goods_width + 'px'});
					}
					
					obj.total = Math.ceil(response.data.count/obj.num);
					totalPage = obj.total;
					if(page < totalPage){
						$(".list-bottom-last").hide();
						$(".load-more").show();
					}
					else{
						$(".list-bottom-last").show();
						$(".load-more").hide();
					}
					location.hash = '#' + page;
					sessionStorage.goods_html = $("#list").html();
					sessionStorage.totalPage = totalPage;
					sessionStorage.searchkey = searchParam.searchkey;
					sessionStorage.page = page;
				}
				else{
					html = '<div class="nodata">没有您要找的商品，换个类别或词汇试试</div>';
					$("#list").append(html);
					$(".list-bottom-last").hide();
					$(".load-more").hide();
				}
				if(page == 1){
				    layer.closeAll('loading');
				}
				isLoading = false;
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				layer.msg('获取商品信息失败');
				if(page == 1){
				    layer.closeAll('loading');
				}
				isLoading = false;
				$(".list-bottom-last").hide();
				$(".load-more").hide();
			}
		});
	}
	
	if(hash && hash.indexOf('#') != -1 && hash.split('').length != 1 && sess_html){
		var arr = hash.split('');
		var searchkey = sessionStorage.searchkey;
		var top = Number(sessionStorage.scrollTop);
		if(searchkey){
			$searchInput.val(searchkey);
		}
		isLoading = false;
		page = hash.substring(1);
		totalPage = Number(sessionStorage.totalPage);
		
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
		$(".load-more").hide();
		page = 1;
		page_info(page);
	}
	
	function loading(){
    	var index = layer.load(2, {
		    shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
    }
	
	$(window).scroll(function(){  
		sessionStorage.scrollTop = $(window).scrollTop();
		
	    // 当滚动到最底部以上100像素时， 加载新内容  
	    if ($(document).height() - $(this).scrollTop() - $(this).height()<100) {
	    	if(!isLoading && page < totalPage){
	    		$(".load-more").show();
		    	page++;
		    	page_info(page);
	    	}
	    };  
	});  
	
	$(window).on('hashchange', function (e) {
		var hash = location.hash;
		var hash_page = Number(sessionStorage.page);
		var next_page = Number(hash.substring(1));
		
		if(next_page < hash_page){
			previous_url = sessionStorage.previous_url;
			if(previous_url){
				location.href = previous_url;
			}
		}
	}); 
});