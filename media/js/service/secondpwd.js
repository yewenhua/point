$(function(){
	$("#submit").removeClass('on');
	$(".goback").click(function(){
		location.href = '/service/center';
    });
	
	$("#submit").click(function(){
		var original_password = $("#original_password").val();
		var new_password = $("#new_password").val();
		var renew_password = $("#renew_password").val();
		
		if(have_second_pwd == 'yes' && !original_password){
			layer.msg('请输入原始二级密码');
			return false;
		}
		if(!new_password){
			layer.msg('请输入新二级密码');
			return false;
		}
		if(!renew_password){
			layer.msg('请再次输入新二级密码');
			return false;
		}
		if(new_password != renew_password){
			layer.msg('两次新密码不一致');
			return false;
		}
		
		if(!$("#submit").hasClass('on')){
			$("#submit").addClass('on');
			$.ajax({
	            url: '/service/submit_second_chgpwd',
	            type: 'POST',
	            data: {
				    original_password: original_password,
				    new_password: new_password
	            },
	            dataType: 'json',
	            cache: false,
	            beforeSend: function() {
	
	            },
	            success: function(response){
	                if(response.code == 0){
	                	layer.msg('修改成功');
	                	$("#original_password").val('');
	                	$("#new_password").val('');
	                	$("#renew_password").val('');
	                	setTimeout('gotoUrl()', 1000);
	                }
	                else if(response.code == 9999 || response.code == 8888){
						layer.msg(response.message);
						setTimeout(function(){
							location.href = '/service/login';
						}, 1500);
					}
	                else{
	                    layer.msg('原始二级密码错误');
	                }
	                $("#submit").removeClass('on');
	            },
	            error: function(XMLHttpRequest, textStatus, errorThrown){
	                layer.msg('未知错误');
	                $("#submit").removeClass('on');
	            }
	        });
		}
    });
});

function gotoUrl(){
	location.href = '/member/center';
}