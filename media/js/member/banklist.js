$(function(){
	for(var i=0; i<size; i++){
		var selector = '.bank-item-' + i;
		var el = document.querySelector(selector);
		var startPosition, endPosition, deltaX, deltaY, moveLength, now;
	
		el.addEventListener('touchstart', function (e) {
			$(".bank").css({"left":"0px"});
			$('.weui_panel_delete').css('z-index', '-1');
			if (!e.touches.length) return;
	        now = Date.now();  //记下触摸开始时间
		    var touch = e.touches[0];
		    startPosition = {
		        x: touch.pageX,
		        y: touch.pageY
		    }
		});
		
		el.addEventListener('touchmove', function (e) {
			var $this = $(this);
		    var touch = e.touches[0];
		    endPosition = {
		        x: touch.pageX,
		        y: touch.pageY
		    }
		
		    deltaX = endPosition.x - startPosition.x;
		    deltaY = endPosition.y - startPosition.y;
		    moveLength = Math.sqrt(Math.pow(Math.abs(deltaX), 2) + Math.pow(Math.abs(deltaY), 2));
	
		    var moveLengthX = endPosition.x - startPosition.x;
			var moveLengthY = endPosition.y - startPosition.y;
		    if (Math.abs(moveLengthX) > Math.abs(moveLengthY)) {
				if(moveLengthX < 0){
					$this.css({"left":moveLengthX+"px"});
					var deleteBtnRight = -100-moveLengthX;
					if(deleteBtnRight <= 0){
					    $this.next('.weui_panel_delete').css({"right":deleteBtnRight+"px"});
					}
					else{
						$this.next('.weui_panel_delete').css({"right":"0px"});
					}
					$this.next('.weui_panel_delete').css('z-index', '1');
				}
			}
		});
	
		el.addEventListener('touchend', function (e) {
			var isTap = false;
	    	var delta = Date.now() - now;
	    	if ( delta > 0 && delta <= 350 ) {
	    		//触摸时间小于350ms为轻触
	    		isTap = true;
	    	}
			var compareWidth = 30;
			var moveLengthX = endPosition.x - startPosition.x;
			var moveLengthY = endPosition.y - startPosition.y;
			var $this = $(this);

			if (Math.abs(moveLengthX) > Math.abs(moveLengthY) && !isTap) {
				if(moveLengthX < 0){
					if(Math.abs(moveLengthX) >= compareWidth){
						$this.next('.weui_panel_delete').css({"right":"0px"});
						var animateWidth = 100 - Math.abs(moveLengthX);   	    	
						$this.animate({"left":"-="+animateWidth+"px"}, "fast",function(){
							$this.next('.weui_panel_delete').css('z-index', '1');
							startPosition = {
						        x: 0,
						        y: 0
						    };
							endPosition = {
						        x: 0,
						        y: 0
						    };
		    			});
					}
					else{
						$this.next('.weui_panel_delete').css({"right":"-100px"});
						var animateWidth = Math.abs(moveLengthX);   	    	
						$this.animate({"left":"+="+animateWidth+"px"}, "fast",function(){
							$this.next('.weui_panel_delete').css('z-index', '-1');
							startPosition = {
						        x: 0,
						        y: 0
						    };
							endPosition = {
						        x: 0,
						        y: 0
						    };
		    			});
					}
				}
			}
			else{
				$(".bank").css({"left":"0px"});
				$('.weui_panel_delete').css('z-index', '-1');
			}
		});
	}

	$(document).on('click', ".weui_panel_delete", function(){
		$this = $(this);
		var id = $this.data('bankid');
		layer.confirm('确定要删除吗？', {
		    btn: ['确定','取消'] //按钮
		}, function(){
		    $.ajax({
				url: '/index.php/member/deleteBankInfo',
				type: 'POST',
				data: {
				   id: id,
				   type: type
				},
				dataType: 'json',
				cache: false,
				beforeSend: function() {
					
				},
				success: function(response){
					if(response.code == 0){
						var message = "删除成功";
						layer.msg(message);
						$this.parent('.banklist-outer').remove();
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
						var message = "删除失败";
						layer.msg(message);
					}
					
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					var message = "删除出错";
					layer.msg(message);
				}
			});
		}, function(){
		    
		});
	});

});