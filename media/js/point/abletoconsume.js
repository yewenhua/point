$(function(){
	$("#submit").removeClass('on');
	$(".goback").click(function(){
		history.back(-1);
    });
	
	$("#submit").click(function(){
		var point = Number($("#transfer-point").val());
		if(!point){
			layer.msg('请输入待转积分');
			return false;
		}
		
		if(!checkPoint(point)){
			layer.msg('积分必须是数字');
			return false;
		}
		else{
			layer.prompt({
				title: '请输入二级密码', 
				formType: 1
			}, function(text){
				if(!$("#submit").hasClass('on')){
					layer.closeAll('dialog');
					$("#submit").addClass('on');
					$("#submit").html('提交中…');
					var now_useable = Number($("#now-useable").html());
					var now_consume = Number($("#now-consume").html());
					var new_useable_point = now_useable - point;
					var new_consume_point = now_consume + point;
					
					$.ajax({
			            url: '/point/do_able_consume',
			            type: 'POST',
			            data: {
						    point: point,
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
			                	$("#now-useable").html(new_useable_point);
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
	});
});

function checkPoint(point){
	var rule = /^([\d]{1,9})$/;
	if(rule.test(point)){
		return true;
	}
	return false;
}