
$(function(){
	if(is_exchange_pwd_exist == 'no'){
		layer.confirm('未设置二级密码，现在去设置？', {
		    btn: ['确定','取消'] //按钮
		}, function(){
		    layer.msg('页面跳转中…')
		    location.href = '/member/secondpwd';
		}, function(){
		    
		});
	}
	
	$("#poster").click(function(){
		var $this = $(this);
		if(!$this.hasClass('on')){
			$this.addClass('on');
			layer.msg('海报生成中…');
			$.ajax({
	            url: '/wechat/sendPosterNotWechat',
	            type: 'POST',
	            data: {
	                
	            },
	            dataType: 'json',
	            cache: false,
	            beforeSend: function() {
	
	            },
	            success: function(response){
	            	$this.removeClass('on');
	                if(response.code == 0){
	                	layer.msg('已发送至微信');
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
	                $this.removeClass('on');
	            }
	        });
		}
    });
});