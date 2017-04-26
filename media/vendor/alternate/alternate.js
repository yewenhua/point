$(function(){
	now = 0;
    isDrag = false;
    isHover = false;
	startX = 0;
    startY = 0;
    x = 0;
    y = 0;
    var isOnPc = !(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent));
    if(!isOnPc){
		var content = document.getElementById("alternate-content-detail");
		content.addEventListener("touchmove", touchMove, false);
		content.addEventListener("touchstart", touchStart, false);
		content.addEventListener("touchend", touchEnd, false);
    }
	
	//自动滚动
	function alternate(){
        if(isDrag == false && isHover == false){
	        var size = $(".alternate-content-detail li").size();
	        if(size == 1){
	        	return false;
	        }
	        var width = $(".alternate-content-detail li").width();
	        var left = parseInt( $('.alternate-content-detail').css('left') );
	        
	        var flag = false;
	        for(var i=0; i<size; i++){
	        	var temp = i * width;
	        	if(-left == temp || left == temp){
	        		flag = true;
	        	}
	        }
	        if(!flag){
	        	//重置
	        	$('.alternate-content-detail').css({"left": "0px"});
	        	$(".alternate-content-detail li").eq(0).css({"left": "0px"});
	        	$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});
	        	$(".count-area li").removeClass('active');
	            $(".count-area li").eq(0).addClass('active');
	        	return false;
	        }

	        if(-left == (size - 1) * width){
	        	//轮播到最后一个时，第一个相对定位到最后一个右边，最后一个轮播之后第一个单元就返回到原来定位
	        	$(".alternate-content-detail li").eq(0).css({"left": size * width + "px"});
	        }
	        
	        //向左轮播
        	$(".alternate-content-detail").animate({"left":"-="+width+"px"},"slow",function(){
        		var left = parseInt( $('.alternate-content-detail').css('left') );
        		var order = (-left/width);
        		if(left == -size * width){
        			//最后一个轮播之后第一个单元就返回到原来定位
        			$(".alternate-content-detail li").eq(0).css({"left":"0px"});
    	        	$(".alternate-content-detail").css({"left":"0px"});
    	        	order = 0;
    	        }

                $(".count-area li").removeClass('active');
                $(".count-area li").eq(order).addClass('active');
            });
        }
    }

    promiss = setInterval(alternate, 5000);
    
    $("#click_left").click(function(e){
    	clearInterval(promiss);
    	isDrag = true;
    	left(function(){
    		promiss = setInterval(alternate, 5000);
    	});
    });
    
    $("#click_right").click(function(e){
    	clearInterval(promiss);
    	isDrag = true;
    	right(function(){
    		promiss = setInterval(alternate, 5000);
    	});
    });
    
    if(isOnPc){
	    $(".count-area li").hover(function(e){
	    	isHover = true;
	    	var width = Number($(".alternate-content-detail li").width());
	    	var order = $(this).attr('data-order');
	    	var left = -order * width;
	    	var size = $(".alternate-content-detail li").size();
	    	
	    	if(order == 0){
	    		//轮播到第一个时，最后一个相对定位到第一个左边，第一个轮播之后最后一个单元就返回到原来定位
	        	$(".alternate-content-detail li").eq(size - 1).css({"left": -size * width + "px"});
	    	}
	    	else if(order == (size - 1)){
	    		//轮播到最后一个时，第一个相对定位到最后一个右边，最后一个轮播之后第一个单元就返回到原来定位
	        	$(".alternate-content-detail li").eq(0).css({"left": size * width + "px"});
	    	}
	
	    	$(".alternate-content-detail").animate({"left": left + "px"},"fast",function(){
	    		var left = parseInt( $('.alternate-content-detail').css('left') );
	    		$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});
	    		$(".alternate-content-detail li").eq(0).css({"left":"0px"});
	    		
	    		var order = (-left/width);
	    		if(left == width){
	    			//第一个轮播之后最后一个单元就返回到原来定位
	    			$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});
	            	$(".alternate-content-detail").css({"left": -(size - 1) * width + "px"});
	            	order = size - 1;
	            }
	    		else if(left == -size * width){
	    			//最后一个轮播之后第一个单元就返回到原来定位
	    			$(".alternate-content-detail li").eq(0).css({"left":"0px"});
	            	$(".alternate-content-detail").css({"left":"0px"});
	            	order = 0;
	            }
	    		
	    		$(".count-area li").removeClass('active');
	            $(".count-area li").eq(order).addClass('active');
	        });
	    }, function(){
	    	isHover = false;
	    });
    }
    
    function left(callback) {
    	var width = Number($(".alternate-content-detail li").width());
    	var size = $(".alternate-content-detail li").size();
    	var left = parseInt( $('.alternate-content-detail').css('left') );
    	if(size == 1){
        	return false;
        }
    	
    	if(left == 0){
        	//轮播到第一个时，最后一个相对定位到第一个左边，第一个轮播之后最后一个单元就返回到原来定位
        	$(".alternate-content-detail li").eq(size - 1).css({"left": -size * width + "px"});
        }
    	
    	$(".alternate-content-detail").animate({"left":"+="+width+"px"},"fast",function(){
    		var left = parseInt( $('.alternate-content-detail').css('left') );
    		var order = (-left/width);
    		
    		if(left == width){
    			//第一个轮播之后最后一个单元就返回到原来定位
    			$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});
            	$(".alternate-content-detail").css({"left": -(size - 1) * width + "px"});
            	order = size - 1;
            }

    		$(".count-area li").removeClass('active');
            $(".count-area li").eq(order).addClass('active');
            isDrag = false;
            callback();
        });
    }

    function right(callback) {
    	var width = Number($(".alternate-content-detail li").width());
    	var left = parseInt( $('.alternate-content-detail').css('left') );
    	var size = $(".alternate-content-detail li").size();
    	if(size == 1){
        	return false;
        }
    	
    	if(-left == (size - 1) * width){
        	//轮播到最后一个时，第一个相对定位到最后一个右边，最后一个轮播之后第一个单元就返回到原来定位
        	$(".alternate-content-detail li").eq(0).css({"left": size * width + "px"});
        }
    	
    	$(".alternate-content-detail").animate({"left":"-="+width+"px"},"fast",function(){
    		var left = parseInt( $('.alternate-content-detail').css('left') );
    		var order = (-left/width);
    		if(left == -size * width){
    			//最后一个轮播之后第一个单元就返回到原来定位
    			$(".alternate-content-detail li").eq(0).css({"left":"0px"});
            	$(".alternate-content-detail").css({"left":"0px"});
            	order = 0;
            }

            $(".count-area li").removeClass('active');
            $(".count-area li").eq(order).addClass('active');
            isDrag = false;
            callback();
        });
    }
    
    function touchMove(event) {
    	//startY不变 touch.pageY随着手指移动在变即当前的坐标（相对屏幕）
        //event.preventDefault();
        var width = Number($(".alternate-content-detail li").width());
        var size = $(".alternate-content-detail li").size();
    	if(size == 1){
        	return false;
        }
    	
        var touchTwo = event.touches[0];
        x = touchTwo.pageX - startX;
        y = touchTwo.pageY - startY;
        
        var order = event.target.attributes["data-order"].value;
        var left = -order * width;
        if (Math.abs(x) > Math.abs(y)) {
        	event.preventDefault();
        	//这里是为手指是横向滚动的,原理是计算X位置的偏移要比Y的偏移大
        	var moveWidth = Math.abs(x);
        	
        	if(x > 0){
        		//向右滑动
        		//alert("向右滑动");
        		moveWidth = moveWidth + left;
        		$(".alternate-content-detail").css({"left":moveWidth+"px"});
        	}
        	else if(x < 0){
        		//向左滑动
        		//alert("向左滑动");
        		moveWidth = -moveWidth + left;
        		$(".alternate-content-detail").css({"left":moveWidth+"px"});
        	}
        }
        else{
        	//这里是为手指是纵向滚动的,原理是计算X位置的偏移要比Y的偏移小
        }
    }

    function touchStart(event) {
        //阻止网页默认动作（即网页滚动）
        //event.preventDefault();
        if (!event.touches.length) return;
        now = Date.now();  //记下触摸开始时间
        var touch = event.touches[0];
        startX = touch.pageX;
        startY = touch.pageY;
        isDrag = true;
        clearInterval(promiss);
        
        var width = Number($(".alternate-content-detail li").width());
        var size = $(".alternate-content-detail li").size();
        if(size == 1){
        	return false;
        }
		var left = parseInt( $('.alternate-content-detail').css('left') );
		var order = $(".count-area").find('li.active').attr('data-order')

    	if(order == 0){
        	//轮播到第一个时，最后一个相对定位到第一个左边，第一个轮播之后最后一个单元就返回到原来定位
        	$(".alternate-content-detail li").eq(size - 1).css({"left": -size * width + "px"});
        }
    	else if(order == (size - 1)){
        	//轮播到最后一个时，第一个相对定位到最后一个右边，最后一个轮播之后第一个单元就返回到原来定位
        	$(".alternate-content-detail li").eq(0).css({"left": size * width + "px"});
        }
    }

    function touchEnd(event) {
    	var isTap = false;
    	var delta = Date.now() - now;
    	if ( delta > 0 && delta <= 750 ) {
    		//触摸时间小于750ms为轻触
    		isTap = true;
    	}
    	var width = Number($(".alternate-content-detail li").width());
    	var size = $(".alternate-content-detail li").size();
    	if(size == 1){
        	return false;
        }
		var left = parseInt( $('.alternate-content-detail').css('left') );
    	var compareWidth = width/4;
    	var tapCompareWidth = 30;
    	var moveWidth = Math.abs(x);
        if (Math.abs(x) > Math.abs(y)) {
        	//这里是为手指是横向滚动的,原理是计算X位置的偏移要比Y的偏移大
        	if(x > 0){
        		//向右滑动
        		//alert("向右滑动"); 轻触只要大于30像素就滚动
        		if((isTap && moveWidth > tapCompareWidth) || moveWidth >= compareWidth){
        			//拖动距离大于一般宽度，则继续向左滑动剩下的距离
        			var animateWidth = width - moveWidth;   	    	
        			$(".alternate-content-detail").animate({"left":"+="+animateWidth+"px"},"fast",function(){
        				var left = parseInt( $('.alternate-content-detail').css('left') );
        	    		var order = (-left/width);
        	    		
        	    		if(left <= width && left > 0){
        	    			//第一个轮播之后最后一个单元就返回到原来定位
        	            	$(".alternate-content-detail").css({"left": -(size - 1) * width + "px"});
        	            	order = size - 1;
        	            }
        	    		
        	    		$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});
    	    			$(".alternate-content-detail li").eq(0).css({"left":"0px"});

        	    		$(".count-area li").removeClass('active');
        	            $(".count-area li").eq(order).addClass('active');
        	            isDrag = false;
        	            promiss = setInterval(alternate, 5000);
    	            });
        		}
        		else{
        			//拖动距离小于一般宽度，回滚到原始状态
        			$(".alternate-content-detail").animate({"left":"-="+moveWidth+"px"},"fast", function(){
        				isDrag = false;
        				promiss = setInterval(alternate, 5000);
        				
        				//第一个轮播之后最后一个单元就返回到原来定位
    	    			$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});
    	    			//最后一个轮播之后第一个单元就返回到原来定位
    	    			$(".alternate-content-detail li").eq(0).css({"left":"0px"});
        			});
        		}
        	}
        	else if(x < 0){
        		//向左滑动
        		//alert("向左滑动");
        		if((isTap && moveWidth > tapCompareWidth) || moveWidth >= compareWidth){
        			//拖动距离大于一般宽度，则继续向左滑动剩下的距离
        			var animateWidth = width - moveWidth;		
        			$(".alternate-content-detail").animate({"left":"-="+animateWidth+"px"},"fast",function(){
        				var left = parseInt( $('.alternate-content-detail').css('left') );
        	    		var order = (-left/width);
        	    		if(left <= -size * width){
        	    			//最后一个轮播之后第一个单元就返回到原来定位
        	            	$(".alternate-content-detail").css({"left":"0px"});
        	            	order = 0;
        	            }
        	    		
        	    		$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});
    	    			$(".alternate-content-detail li").eq(0).css({"left":"0px"});

        	            $(".count-area li").removeClass('active');
        	            $(".count-area li").eq(order).addClass('active');
        	            isDrag = false;
        	            promiss = setInterval(alternate, 5000);
    	            });
        		}
        		else{
        			//拖动距离小于一般宽度，回滚到原始状态
        			$(".alternate-content-detail").animate({"left":"+="+moveWidth+"px"},"fast", function(){
        				isDrag = false;
        				promiss = setInterval(alternate, 5000);
        				
        				//最后一个轮播之后第一个单元就返回到原来定位
    	    			$(".alternate-content-detail li").eq(0).css({"left":"0px"});
    	    			//第一个轮播之后最后一个单元就返回到原来定位
    	    			$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});
        			});
        		}
        	}
        }
        else{
        	//这里是为手指是纵向滚动的,原理是计算X位置的偏移要比Y的偏移小
        	isDrag = false;
        	promiss = setInterval(alternate, 5000);
        }
    }
    
    $(window).resize(function() {
    	clearInterval(promiss);
    	var width = Number($(".alternate-content-detail li").width());
    	var size = $(".alternate-content-detail li").size();
    	
    	$(".alternate-content-detail").animate({"left":"0px"},"fast",function(){	
    		$(".alternate-content-detail li").eq(0).css({"left":"0px"});
    		$(".alternate-content-detail li").eq(size - 1).css({"left":"0px"});

    		$(".count-area li").removeClass('active');
            $(".count-area li").eq(0).addClass('active');
            var promiss = setInterval(alternate, 5000);
        });
	});

});




	