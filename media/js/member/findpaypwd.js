var imageData = {};
function getCode(){	
	$.get("/index.php/member/code", function(data){
		imageData = JSON.parse(data);
        $('.captcha-img').html(imageData.image);
        $('.captcha-img img').height('46');
        $('.captcha-img img').width('108');
    });
}

function checkMobile(mobile){
	var rule = /^1[1|2|3|4|5|6|7|8|9][0-9]\d{8}$/;
	if(rule.test(mobile)){
		return true;
	}
	return false;
}

$(function(){
	forgetNameSpace = {};
	forgetNameSpace.isRegist = '';
	getCode();
	$("#submit").removeClass('on');
	
	$(".captcha-img").click(function(){
		getCode();
    });
	
	$(".goback").click(function(){
		if(type == 'member'){
			location.href = '/member/center';
		}
		else{
			location.href = '/service/center';
		}
    });
	
	$("#submit").click(function(){
		var rand_code = $("#rand_code").val();
		var mobile = $("#mobile").val();
		var mobile_code = $("#mobile_code").val();
		var password = $("#password").val();
		var re_password = $("#re_password").val();
		
		if(!rand_code){
			layer.msg('请输入随机验证码');
			return false;
		}
		if(!mobile){
			layer.msg('请输入手机号码');
			return false;
		}
		if(!mobile_code){
			layer.msg('请输入手机验证码');
			return false;
		}
		if(!password){
			layer.msg('请输入密码');
			return false;
		}
		if(!re_password){
			layer.msg('请再次输入密码');
			return false;
		}
		if(password != re_password){
			layer.msg('两次密码不一致');
			return false;
		}
		
		if(!$("#submit").hasClass('on')){
			$("#submit").addClass('on');
			$.ajax({
	            url: '/member/submit_forget_second',
	            type: 'POST',
	            data: {
				    rand_code: rand_code,
				    mobile: mobile,
				    mobile_code: mobile_code,
				    password: password,
				    type: type
	            },
	            dataType: 'json',
	            cache: false,
	            beforeSend: function() {
	
	            },
	            success: function(response){
	                if(response.code == 0){
	                	layer.msg('修改成功');
	                	$("#rand_code").val('');
	                	$("#mobile").val('');
	                	$("#mobile_code").val('');
	                	$("#password").val('');
	                	$("#re_password").val('');
	                	
	                	setTimeout('gotoCenterUrl()', 1000);
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
	            },
	            error: function(XMLHttpRequest, textStatus, errorThrown){
	                layer.msg('未知错误');
	                $("#submit").removeClass('on');
	            }
	        });
		}
    });
	
	var code_active = false;
	$("#get_mobile_code_div").click(function(){
		var rand_code = $("#rand_code").val();
		if(!rand_code){
        	layer.msg('随机验证码不能为空');
			return false;
		}
        if(rand_code != imageData.word){
        	layer.msg('随机验证码错误');
			return false;
		}
        
		var mobile = $("#mobile").val();
		if(typeof(mobile) == "undefined" || !mobile){
			var message = $("#mobile").attr("placeholder");
			layer.msg(message);
			return false;
		}
		else if(!checkMobile(mobile)){
		    var message = "请输入正确格式的手机号码！";
		    layer.msg(message);
			return false;
		}
		
		if(code_active == false){
			code_active = true;
			var second = 60;
			var int_second = setInterval(function(){
				if(second <= 60 && second > 0){
					second--;
					var html = second + 's后可再请求'
					$("#get_mobile_code").html(html);
					$("#get_mobile_code").addClass('btn-actived');
				}
				else{
					clearInterval(int_second);
					$("#get_mobile_code").html("手机验证码");
					$("#get_mobile_code").removeClass('btn-actived');
					code_active = false;
				}
			}, 1000);
			
			$.ajax({
				url: '/index.php/member/sendForgetSecondSmsCode',
				type: 'POST',
				data: {
				    mobile: mobile,
				    word: rand_code,
				},
				dataType: 'json',
				cache: false,
				beforeSend: function() {
					
				},
				success: function(response){
					if(response.code == 0){
						var message = '发送成功';
						layer.msg(message);
					}
					else{
						layer.msg(response.message);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					var message = '未知错误';
					layer.msg(message);
				}
			});
		}
    });
});

function gotoCenterUrl(){
	if(type == 'member'){
	    var url = '/member/center';
	}
	else{
		var url = '/service/center';
	}
	location.href = url;
}