
$(function(){
	$("#submit").removeClass('on');
	$(".goback").click(function(){
		location.href = '/member/center';
    });
	
	var is_user_exist_by_mobile = false;
	var init = true;
	$("#user_mobile").keyup(function(){
		init = true;
		is_user_exist_by_mobile = false;
		var mobile = $("#user_mobile").val();
		
		if(mobile.length == 11){
			if(!checkMobile(mobile)){
				layer.msg('转让用户手机号码格式错误');
				return false;
			}
			else{
				$.ajax({
		            url: '/member/is_user_same_line_by_mobile',
		            type: 'POST',
		            data: {
					    mobile: mobile
		            },
		            dataType: 'json',
		            cache: false,
		            beforeSend: function() {
		
		            },
		            success: function(response){
		                if(response.code == 0){
		                	layer.msg('转让用户可用');
		                	is_user_exist_by_mobile = true;
		                }
		                else if(response.code == 9999){
							location.href = '/member/login';
							is_user_exist_by_mobile = false;
						}
		                else{
		                	layer.msg(response.message);
		                	is_user_exist_by_mobile = false;
		                }
		                init = false;
		            },
		            error: function(XMLHttpRequest, textStatus, errorThrown){
		                layer.msg('验证转让用户出错');
		                init = false;
		                is_user_exist_by_mobile = false;
		            }
		        });
			}
		}
	});
	
	$("#submit").click(function(){
		var point = Number($("#transfer-point").val());
		var mobile = $("#user_mobile").val();
		
		if(!checkMobile(mobile)){
			layer.msg('转让用户手机号码格式错误');
			return false;
		}
		else{
			if(!checkPoint(point)){
				layer.msg('积分必须是数字');
				return false;
			}
			else{
				if(!init){
					if(is_user_exist_by_mobile){
						layer.prompt({
							title: '请输入二级密码', 
							formType: 1
						}, function(text){
							if(!$("#submit").hasClass('on')){
								layer.closeAll('dialog');
								$("#submit").addClass('on');
								$("#submit").html('提交中…');
								var now_consume = Number($("#now-consume").html());
								var new_consume_point = now_consume - point;
								
								$.ajax({
						            url: '/point/do_transfer_consume',
						            type: 'POST',
						            data: {
									    point: point,
									    mobile: mobile,
									    check_key: text
						            },
						            dataType: 'json',
						            cache: false,
						            beforeSend: function() {
						
						            },
						            success: function(response){
						                if(response.code == 0){
						                	layer.msg('提交成功');
						                	$("#transfer-point").val('');
						                	$("#user_mobile").val('');
						                	$("#now-consume").html(new_consume_point);
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
						                $("#submit").removeClass('on');
						                $("#submit").html('提交');
						            },
						            error: function(XMLHttpRequest, textStatus, errorThrown){
						                layer.msg('未知错误');
						                $("#submit").removeClass('on');
						                $("#submit").html('提交');
						            }
						        });
							}
						});
					}
					else{
						layer.msg('转让用户必须是上下级非普通会员');
					}
				}
				else{
					layer.msg('正在验证转让用户，请稍后……');
				}
			}
		}
	});
});

function checkPoint(point){
	var rule = /^([\d]{1,9})$/;
	if(rule.test(point)){
		return true;
	}
	return false;
}

function checkMobile(mobile){
	var rule = /^1[1|2|3|4|5|6|7|8|9][0-9]\d{4,8}$/;
	if(rule.test(mobile)){
		return true;
	}
	return false;
}