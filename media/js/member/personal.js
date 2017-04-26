$(function(){
	$("#submit").removeClass('on');
	$(".goback").click(function(){
		location.href = '/member/center';
    });
	
	$("#submit").click(function(){
		var name = $("#name").val();
		var card_no = $("#card_no").val();
		var sex = $('input:radio[name="sex"]:checked').val();
		
		if(!name){
			layer.msg('请输入用户名');
			return false;
		}
		
		if(!$("#submit").hasClass('on')){
			$("#submit").addClass('on');
			$.ajax({
	            url: '/member/submit_personal',
	            type: 'POST',
	            data: {
				    name: name,
				    card_no: card_no,
				    sex: sex
	            },
	            dataType: 'json',
	            cache: false,
	            beforeSend: function() {
	
	            },
	            success: function(response){
	                if(response.code == 0){
	                	layer.msg('修改成功');
	                	setTimeout('gotoUrl()', 1000);
	                }
	                else if(response.code == 9999 || response.code == 8888){
						layer.msg(response.message);
						setTimeout(function(){
							location.href = '/member/login';
						}, 1500);
					}
	                else{
	                    layer.msg('修改失败');
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