$(function(){
	$("#city_select_content").citySelect({nodata:"none",required:false});
	
	$(".goback").click(function(){
		location.href = '/service/center';
    });
	
	$("#submit").click(function(){
		var card_no = $("#card_no").val();
		var bank_address = $("#bank_address").val();
		var bank_name = $("#bank_name").val();
		var username = $("#username").val();
		var prov = $(".prov").val();
		var city = $(".city").val();
		var dist = $(".dist").val();
		var type = $("#type").val();

		if(typeof(bank_name) == "undefined" || !bank_name){
			var msg = $("#bank_name").attr("placeholder");
			layer.msg(msg);
			return false;
		}
		else if(typeof(card_no) == "undefined" || !card_no){
			var msg = $("#card_no").attr("placeholder");
			layer.msg(msg);
			return false;
		}
		else if(prov === '' || prov === null || city === '' || city === null){
			var msg = "请输选择开户地点！";
			layer.msg(msg);
			return false;
		}
		else if(dist === '' && dist !== null){
			var msg = "请选择开户区域！";
			layer.msg(msg);
			return false;
		}
		else if(typeof(bank_address) == "undefined" || !bank_address){
			var msg = "请输入您的开户网点！";
			layer.msg(msg);
			return false;
		}
		
		if(dist === null){
			bank_address = prov + city + bank_address;
		}
		else{
			bank_address = prov + city + dist + bank_address;
		}
		
		$("#submit").html('提交中…');
		$.ajax({
			url: '/index.php/member/insertBankInfo',
			type: 'POST',
			data: {
			    bank_name: bank_name,
			    card_no: card_no,
			    username: username,
			    type: type,
			    bank_address: bank_address,
			},
			dataType: 'json',
			cache: false,
			beforeSend: function() {
				
			},
			success: function(response){
				if(response.code == 0){
					$("#card_no").val('');
					$("#bank_address").val('');
					$("#username").val('');
					$("#bank_name").val('');
					var msg = "添加成功";
					layer.msg(msg);
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
					var msg = "添加失败";
					layer.msg(msg);
				}
				$("#submit").html('提交');
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				var msg = "添加出错";
				layer.msg(msg);
				$("#submit").html('提交');
			}
		});
    });
});

function gotoUrl(){
	location.href = '/service/banklist';
}