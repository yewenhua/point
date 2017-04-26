$(function(){
	$(".goback").click(function(){
		if(type == 'member'){
			location.href = '/member/commision';
		}
		else{
			location.href = '/service/commision';
		}
    });
	
	$("#submit_money").keyup(function(){
		var submit_money = $(this).val();
		if(checkMonty(submit_money)){
			var tax_money = 3500;
			var rate = 0.08;
			if(submit_money > tax_money){
				var get_money = tax_money + (submit_money - tax_money) * (1 - rate);
				$("#get_money").html(get_money);
			}
			else{
				$("#get_money").html(submit_money);
			}
		}
		else{
			$("#get_money").html('');
		}
	});
	
	$("#submit_money").blur(function(){
		var submit_money = $(this).val();
		if(!hundredMoney(submit_money)){
			layer.msg('金额必须是100的倍数');
		}
	});
	
	$("#submit").click(function(){
		var submit_money = $("#submit_money").val();
		if(!checkMonty(submit_money)){
			layer.msg('金额必须是数字');
			return false;
		}
		else{
			var left_money = Number($("#left_money").html());
			if(submit_money > left_money){
				layer.msg('提现金额不能大于余额');
				return false;
			}
			if(submit_money < 100){
				layer.msg('提现金额必须大于100');
				return false;
			}
			if(!hundredMoney(submit_money)){
				layer.msg('金额必须是100的倍数');
				return false;
			}
			
			var bank_id = $("#bank").val();
			layer.prompt({
				title: '请输入二级密码', 
				formType: 1
			}, function(text){
				if(!$("#submit").hasClass('on')){
					layer.closeAll('dialog');
					$("#submit").addClass('on');
					$("#submit").html('提交中…');
					
					$.ajax({
			            url: '/member/do_get_cash',
			            type: 'POST',
			            data: {
						    type: type,
						    money: submit_money,
						    bank_id: bank_id,
						    check_key: text
			            },
			            dataType: 'json',
			            cache: false,
			            beforeSend: function() {
			
			            },
			            success: function(response){
			                if(response.code == 0){
			                	layer.msg('提交成功');
			                	$("#submit_money").val('');
			                	$("#get_money").html('');
			                	setTimeout('gotoUrl()', 1000);
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
	});
});

function checkMonty(money){
	var rule = /^([\d]{1,9})$/;
	if(rule.test(money)){
		return true;
	}
	return false;
}

function hundredMoney(money){
	var rule = /^([\d]{1,9})$/;
	if(rule.test(money) && (money % 100 == 0)){
		return true;
	}
	return false;
}

function gotoUrl(){
	var url = '/member/cashlog';
	location.href = url;
}