$(function(){
	$(".goback").click(function(){
		location.href = '/orders/lists';
    });
	
	var $iosActionsheet = $('#iosActionsheet');
    var $iosMask = $('#iosMask');

    function hideActionSheet() {
        $iosActionsheet.removeClass('weui-actionsheet_toggle');
        $iosMask.fadeOut(200);
    }

    $iosMask.on('click', hideActionSheet);
    $('#iosActionsheetCancel').on('click', hideActionSheet);
    $("#showIOSActionSheet").on("click", function(){
    	$("#refund-item").show();
    	$("#sure-item").hide();
        $iosActionsheet.addClass('weui-actionsheet_toggle');
        $iosMask.fadeIn(200);
    });
    
    $(".sure_refund_btn").on("click", function(){
    	layer.msg('提交中…');
    	$.ajax({
            url: '/orders/refund',
            type: 'POST',
            data: {
    		    key: key
            },
            dataType: 'json',
            cache: false,
            beforeSend: function() {

            },
            success: function(response){
                if(response.code == 0){
                	layer.msg('申请成功');
                	$("#showIOSActionSheet").remove();
                	setTimeout(function(){
                		location.reload();
                	}, 1000);
                }
                else if(response.code == 9999 || response.code == 8888){
					layer.msg(response.message);
					setTimeout(function(){
						location.href = '/member/login';
					}, 1500);
				}
                else{
                    layer.msg(response.message);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                layer.msg('未知错误');
            }
        });
    	
    	hideActionSheet();
    });
    
    $("#sureOrder").on("click", function(){
    	$("#refund-item").hide();
    	$("#sure-item").show();
        $iosActionsheet.addClass('weui-actionsheet_toggle');
        $iosMask.fadeIn(200);
    });
    
    $(".sure_order_btn").on("click", function(){
    	layer.msg('提交中…');
    	$.ajax({
            url: '/orders/sureorder',
            type: 'POST',
            data: {
    		    key: key
            },
            dataType: 'json',
            cache: false,
            beforeSend: function() {

            },
            success: function(response){
                if(response.code == 0){
                	layer.msg('操作成功');
                	$("#sureOrder").remove();
                	setTimeout(function(){
                		location.reload();
                	}, 1000);
                }
                else if(response.code == 9999 || response.code == 8888){
					layer.msg(response.message);
					setTimeout(function(){
						location.href = '/member/login';
					}, 1500);
				}
                else{
                    layer.msg(response.message);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                layer.msg('未知错误');
            }
        });
    	
    	hideActionSheet();
    });
});