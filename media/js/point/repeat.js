$(function(){
	$("#submit").removeClass('on');
	$(".goback").click(function(){
		history.back(-1);
    });
	
	function drawunderpie(){
		var height = $("#wait-point-pie").outerHeight();
		var x = height;
		var y = height;
		var r = height - 15;
		var rate = info.already_shot_point/info.limit_point;
		var c=document.getElementById("wait-point-pie");
		var cxt=c.getContext("2d");
		
		//背景
		cxt.strokeStyle="#e8eff0";
		cxt.lineWidth=4;
		cxt.beginPath();
		cxt.arc(x, y, r, Math.PI*1, 0, false);
		cxt.stroke();
		
		//使用额
		cxt.strokeStyle="#ab2b2a";
		cxt.lineWidth=4;
		cxt.beginPath();
		cxt.arc(x, y, r, Math.PI*1, Math.PI*(1 + rate), false);
		cxt.stroke();
	}
	drawunderpie();
	
	$("#shoot-point").keyup(function(){
		var point = Number($(this).val());
		if(!checkPoint(point)){
			layer.msg('复投积分必须是数字');
			$("#future-point").html('--');
			return false;
		}
		else{
			var future_point = multiple * point;
			$("#future-point").html(future_point);
		}
	});
	
	$("#submit").click(function(){
		var now_point = Number($("#now-point").html());
		var shoot_point = Number($("#shoot-point").val());
		var rest_point = info.limit_point - info.already_shot_point;
		
		if(!checkPoint(shoot_point)){
			layer.msg('复投积分必须是数字');
			return false;
		}
		if(!shoot_point){
			layer.msg('请输入复投积分');
			return false;
		}
		if(shoot_point > now_point){
			layer.msg('复投积分不能大于可用积分');
			return false;
		}
		if(shoot_point > rest_point){
			layer.msg('复投积分大于可使用额度');
			return false;
		}
		
		var new_useable_point = now_point - shoot_point;
		
		layer.prompt({
			title: '请输入二级密码', 
			formType: 1
		}, function(text){
			if(!$("#submit").hasClass('on')){
				layer.closeAll('dialog');
				$("#submit").addClass('on');
				$("#submit").html('提交中…');
				
				$.ajax({
		            url: '/point/do_repeat',
		            type: 'POST',
		            data: {
					    shoot_point: shoot_point,
					    check_key: text,
					    key: info.key
		            },
		            dataType: 'json',
		            cache: false,
		            beforeSend: function() {
		
		            },
		            success: function(response){
		                if(response.code == 0){
		                	layer.msg('提交成功');
		                	info.already_shot_point = Number(info.already_shot_point) + shoot_point;
		                	$("#already_shot_point").html(info.already_shot_point);
		                	$("#shoot-point").val('');
		                	$("#now-point").html(new_useable_point);
		                	$("#future-point").html('--');
		                	setTimeout(function(){
								location.href = '/point/repeatlist';
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
    });
});

function checkPoint(point){
	var rule = /^([\d]{1,9})$/;
	if(rule.test(point)){
		return true;
	}
	return false;
}