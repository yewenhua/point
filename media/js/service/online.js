$(function(){
	$("#submit").removeClass('on');
	$(".goback").click(function(){
		location.href = '/service/center';
    });
	
	var is_user_exist_by_mobile = false;
	var init = true;

	function is_child_user_exist_by_mobile(){
		init = true;
		is_user_exist_by_mobile = false;
		var mobile = $("#mobile").val();
		
		if(mobile.length == 11){
			if(!checkMobile(mobile)){
				layer.msg('用户编号手机号码格式错误');
				return false;
			}
			else{
				$.ajax({
		            url: '/member/is_child_user_exist_by_mobile',
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
		                	layer.msg('用户编号可用');
		                	is_user_exist_by_mobile = true;
		                }
		                else if(response.code == 9999 || response.code == 8888){
		                	is_user_exist_by_mobile = false;
							layer.msg(response.message);
							setTimeout(function(){
								location.href = '/service/login';
							}, 1500);
						}
		                else{
		                	layer.msg(response.message);
		                	is_user_exist_by_mobile = false;
		                }
		                init = false;
		            },
		            error: function(XMLHttpRequest, textStatus, errorThrown){
		                layer.msg('验证用户编号出错');
		                init = false;
		                is_user_exist_by_mobile = false;
		            }
		        });
			}
		}
	}
	
	$("#mobile").change(function(){
		is_child_user_exist_by_mobile();
	});
	
	$("#mobile").keyup(function(){
		is_child_user_exist_by_mobile();
	});	
	
	$("#submit").click(function(){
		var pay_type = $('input:radio[name="pay_type"]:checked').val();
		var point = Number($("#point").val());
		var total = Number($("#total").val());
		var mobile = $("#mobile").val();
		var pay_money = total - point;
		var least = Number(schedule.least_money);
		var most = Number(schedule.most_money);
		var rate = Number(schedule.use_consume_most_rate);
		
		if(!total){
			layer.msg('报单金额不能为空');
			return false;
		}
		if(total < least || total > most){
			var msg = '报单金额必须在' + least + '和' + most + '之间';
			layer.msg(msg);
			return false;
		}
		if(!mobile){
			layer.msg('用户编号不能为空');
			return false;
		}
		
		if(pay_type == 'wallet' && commision < pay_money){
			layer.msg('钱包余额不足');
			return false;
		}
		
		if(!checkMobile(mobile)){
			layer.msg('用户编号手机号码格式错误');
			return false;
		}
		else{
			if(point && !checkNumber(point)){
				layer.msg('积分必须是数字');
				return false;
			}
			else{
				var most_point = total * rate * 0.01;
				if((point > 0 && most_point >= point) || !point){
					if(!init){
						if(is_user_exist_by_mobile){
							layer.prompt({
								title: '请输入二级密码', 
								formType: 1
							}, function(text){
								if(!$("#submit").hasClass('on')){
									layer.closeAll('dialog');
									$("#submit").addClass('on');
									$("#check_key").val(text);
									
									if(pay_type == 'wxpay'){
										$("#submit_form").attr('action', '/service/do_online');
									}
									else{
										$("#submit_form").attr('action', '/service/do_online_wallet');
									}
									layer.msg('支付跳转中…');
									$("#submit_form").submit();
								}
							});
						}
						else{
							layer.msg('用户编号必须存在且是当前用户的下级');
						}
					}
					else{
						layer.msg('正在验证用户编号，请稍后……');
					}
				}
				else{
					layer.msg('消费积分比例不能大于'+ rate +'%');
				}
			}
		}
	});
});

function checkMobile(mobile){
	var rule = /^1[1|2|3|4|5|6|7|8|9][0-9]\d{4,8}$/;
	if(rule.test(mobile)){
		return true;
	}
	return false;
}

function checkNumber(num){
	var rule = /^([\d]{1,9})$/;
	if(rule.test(num)){
		return true;
	}
	return false;
}