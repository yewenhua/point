$(function(){
    var $searchBar = $('#searchBar'),
        $searchText = $('#searchText'),
        $searchInput = $('#searchInput'),
        $searchClear = $('#searchClear'),
        $searchSure = $('#searchSure');
    var page = 1;

    function cancelSearch(){
        $searchBar.removeClass('weui-search-bar_focusing');
        $searchText.show();
    }

    $searchText.on('click', function(){
        $searchBar.addClass('weui-search-bar_focusing');
        $searchInput.focus();
    });
    
    $searchInput.on('blur', function () {
        if(!this.value.length) cancelSearch();
    });
    
    $searchClear.on('click', function(){
        $searchInput.focus();
        $searchInput.val('');
    });
    
    $searchSure.on('click', function(){
        cancelSearch();
        $searchInput.blur();
        list_data(1, function(){
        	
        });
    });
    
    $searchInput.on('keypress', function(e){
    	if(e.which == 13){
    		cancelSearch();
    		list_data(1, function(){
    			
    		});
		}
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
    
    $(".goback").click(function(){
    	location.href = '/mall/center';
    });
    
    function list_data(page, callback){
    	var searchkey = $searchInput.val();
    	var num = 10;
    	
    	$.ajax({
			url: '/index.php/orders/order_data_by_page',
			type: 'POST',
			data: {
			    page: page,
			    num: num,
			    searchkey: searchkey
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
								html += '<div class="weui-media-box weui-media-box_text point-item-top record-item-top order-item-top">';
									html += '<div class="left">编号：' + response.data.data[i]['order_id'] + '</div>';
									html += '<div class="right">' + response.data.data[i]['created_at'].substring(0, 16) + '</div>';
									html += '<div class="clear"></div>';
								html += '</div>';
								
								html += '<a href="/orders/detail?key=' + response.data.data[i]['key'] + '" class="weui-media-box weui-media-box_appmsg review-item">';
									html += '<div class="weui-media-box__hd">';
									   html += '<img class="weui-media-box__thumb" src="/backend/uploads/' + response.data.data[i]['goods_face'] + '" alt="">';
									html += '</div>';
									html += '<div class="weui-media-box__bd">';
										html += '<h4 class="weui-media-box__title">' + response.data.data[i]['goods_name'] + '</h4>';
										html += '<div class="relative">';
										    var buy_op = '数量：' + response.data.data[i]['num'];
										    if(response.data.data[i]['size_op']){
										    	buy_op = buy_op + '&nbsp;&nbsp;' + response.data.data[i]['size_op'];
										    }
										    html += '<p class="weui-media-box__desc order-list-num">' + buy_op + '</p>';
										    if(response.data.data[i]['status'] == 0){
										    	var statusName = '未支付';
										    }
										    else if(response.data.data[i]['status'] == 1){
										    	var statusName = '待发货';
										    }
										    else if(response.data.data[i]['status'] == 2){
										    	var statusName = '已发货';
										    }
										    else if(response.data.data[i]['status'] == 3){
										    	var statusName = '已完成';
										    }
										    else if(response.data.data[i]['status'] == 4){
										    	var statusName = '已关闭';
										    }
										    else if(response.data.data[i]['status'] == 5){
										    	var statusName = '退款中';
										    }
										    else if(response.data.data[i]['status'] == 6){
										    	var statusName = '已退款';
										    }
										    else if(response.data.data[i]['status'] == 7){
										    	var statusName = '待退款';
										    }
										    else{
										    	var statusName = '';
										    }
										    
										    if(response.data.data[i]['status'] == 0){
										        html += '<div class="a_link_gotopay" data-link="/orders/pay?key=' + response.data.data[i]['key'] + '" class="gotopay">去支付</div>';
										    }
										    else{
										    	html += '<div class="order-status">'+ statusName +'</div>';
										    }
										html += '</div>';
										var price = '';
										if(response.data.data[i]['share_id'] == 0){
											if(response.data.data[i]['goods_model'] == 1){
												price = '购物券' + response.data.data[i]['point_price'] + '分';
											}
											else if(response.data.data[i]['goods_model'] == 2){
												price = '消费积分' + response.data.data[i]['point_price'] + '分';
											}
											else if(response.data.data[i]['goods_model'] == 3){
												price = '消费积分' + response.data.data[i]['point_price'] + '分 + 现金 &yen;' + response.data.data[i]['cash_price'] + '元';
											}
										}
										else{
											price = '分享价' + response.data.data[i]['cash_price'] + '元';
										}
										html += '<div class="review-price">' + price + '</div>';
									html += '</div>';
								html += '</a>';
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
					if(page == 1){
						$("#list").html('');
					}
					html = '<div class="nodata">没有数据</div>';
					$("#list").append(html);
					$(".list-bottom-last").hide();
					$(".load-more").hide();
				}
				layer.closeAll('loading');
				callback();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				layer.msg('获取订单信息失败');
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
    
    $(document).on('click', ".gotopay", function(event){
    	event.stopPropagation();
    	var link = $(this).data('link');
    	location.href = link;
    });
});