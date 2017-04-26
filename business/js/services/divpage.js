'use strict';

angular.module('divpage', []).factory('divpage', function() {

	function divpage($object, totalPage, diff) {
		//分页函数1
    	switch(totalPage)
    	{
    		case 1:
    			$object.eq(2).addClass("active");
    			$object.eq(3).hide();
    			$object.eq(4).hide();
    			$object.eq(5).hide();
    			$object.eq(6).hide();
    		case 2:
    			switch(diff)
    			{
    				case 0:
    					$object.eq(6).hide();
    					$object.eq(5).hide();
    					$object.eq(4).hide();
    					$object.eq(3).addClass("active");
    					$object.eq(2).show();
    					break;
    				case 1:
    					$object.eq(6).hide();
    					$object.eq(5).hide();
    					$object.eq(4).hide();
    					$object.eq(3).show();
    					$object.eq(2).addClass("active");
    					break;
    			    default:
    			    	break;
    			}
    		    break;
    		case 3:
    			switch(diff)
    			{
    				case 0:
    					$object.eq(6).hide();
    					$object.eq(5).hide();
    					$object.eq(4).addClass("active");
    					$object.eq(3).show();
    					$object.eq(2).show();
    					break;
    				case 1:
    					$object.eq(6).hide();
    					$object.eq(5).hide();
    					$object.eq(4).show();
    					$object.eq(3).addClass("active");
    					$object.eq(2).show();
    					break;
    				case 2:
    					$object.eq(6).hide();
    					$object.eq(5).hide();
    					$object.eq(4).show();
    					$object.eq(3).show();
    					$object.eq(2).addClass("active");
    					break;
    			    default:
    			    	break;
    			}
    		    break;
    		case 4:
    			switch(diff)
    			{
    				case 0:
    					$object.eq(6).hide();
    					$object.eq(5).addClass("active");
    					$object.eq(4).show();
    					$object.eq(3).show();
    					$object.eq(2).show();
    					break;
    				case 1:
    					$object.eq(6).hide();
    					$object.eq(5).show();
    					$object.eq(4).addClass("active");
    					$object.eq(3).show();
    					$object.eq(2).show();
    					break;
    				case 2:
    					$object.eq(6).hide();
    					$object.eq(5).show();
    					$object.eq(4).show();
    					$object.eq(3).addClass("active");
    					$object.eq(2).show();
    					break;
    				case 3:
    					
    					$object.eq(6).hide();
    					$object.eq(5).show();
    					$object.eq(4).show();
    					$object.eq(3).show();
    					$object.eq(2).addClass("active");
    					break;
    			    default:
    			    	break;
    			}
    		    break;
    		default:
    		    break;
    	}
	}

	function getready($selector, totalPage, page) {
		//分页函数2
    	var $object = $selector.children(".divpage");
    	$object.eq(2).html("1").show();
    	$object.eq(3).html("2").show();
    	$object.eq(4).html("3").show();
    	$object.eq(5).html("4").show();
    	$object.eq(6).html("5").show();
    	$object.removeClass("active");
    	var diff = totalPage - page;
    	if(totalPage < 5){	
    		divpage($object, totalPage, diff);
    		return false;
    	}
    	if(diff <= 4){
    		switch(diff)
    		{
    		case 0:
    			$object.eq(6).addClass("active");
    		    break;
    		case 1:
    			$object.eq(5).addClass("active");
    		    break;
    		case 2:
    			$object.eq(4).addClass("active");
    		    break;
    		case 3:
    			$object.eq(3).addClass("active");
    		    break;
    		case 4:
    			$object.eq(2).addClass("active");
    		    break;
    		default:
    			break;
    		}
    		$object.eq(2).html(totalPage - 4);
    		$object.eq(3).html(totalPage - 3);
    		$object.eq(4).html(totalPage - 2);
    		$object.eq(5).html(totalPage - 1);
    		$object.eq(6).html(totalPage);
    	}
    	else{
    		$object.eq(2).html(page).addClass("active");
    		$object.eq(3).html(page + 1);
    		$object.eq(4).html(page + 2);
    		$object.eq(5).html(page + 3);
    		$object.eq(6).html(page + 4);
    	}
	}

	return {
		getready: getready,
	};
});