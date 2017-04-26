$(function(){
	$(".goback").click(function(){
		if(type == 'member'){
			location.href = '/member/center';
		}
		else{
			location.href = '/service/center';
		}
    });
	
	$("#getcash").click(function(){
		if(bank_num > 0){
			if(type == 'member'){
			    location.href = '/member/getcash/member';
			}
			else{
				location.href = '/member/getcash/service';
			}
		}
		else{
			layer.closeAll('dialog');
			
			layer.confirm('您还未绑定过银行卡', {
			    btn: ['绑定','取消'] //按钮
			}, function(){
				if(type == 'member'){
				    location.href = '/member/bank';
				}
				else{
					location.href = '/service/bank';
				}
			}, function(){
			    
			});
		}
	});
});