$(function(){
	$("#submit").removeClass('on');
	$(".goback").click(function(){
		history.back(-1);
    });
	
	$("#memo").keyup(function(){
		var value = $(this).val();
		var size = value.length;
		$("#rest").html(size);
	});
	
	$("#logistic").change(function(){
		var value = $(this).val();
		if(value == 'logistic'){
			$("#logistic-cell").show();
			$("#send_type").val('logistic');
		}
		else{
			$("#logistic-cell").hide();
			$("#send_type").val('self');
		}
	});
	
	$("#submit").click(function(){
		var send_type = $("#logistic").val();
		var pay_type = '';
		var address = $("#address").val();
		if(!address){
			layer.msg('收货地址不能为空');
			return false;
		}

		var total_money = pay_money;
		if(send_type == 'logistic'){
			total_money = pay_money + logistic_fee;
		}
		
		pay_type = $('input:radio[name="pay_type"]:checked').val();
		var num = $("#buy_num").val();
		var buy_goods_key = $("#buy_goods_key").val();
		var order_id = $("#order_id").val();

		if(num && buy_goods_key && order_id){
			if(!$("#submit").hasClass('on')){
				$("#submit").addClass('on');
				layer.msg('支付跳转中…');
				$("#pay_type").val(pay_type);
				$("#comment").val($("#memo").val());
			    $("#submit_form").submit();
			}
		}
		else{
			layer.msg('参数错误');
		}
    });
});