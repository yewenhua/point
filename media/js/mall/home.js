$(function(){
    var $searchBar = $('#searchBar'),
		$searchText = $('#searchText'),
		$searchInput = $('#searchInput'),
		$searchClear = $('#searchClear'),
		$searchSure = $('#searchSure');

	$searchSure.removeClass('on');
	
	function cancelSearch(){
	    $searchBar.removeClass('weui-search-bar_focusing');
	    $searchText.show();
	}
	
	$searchText.on('click', function(){
	    $searchBar.addClass('weui-search-bar_focusing');
	    $searchInput.focus();
	});
	
	$searchClear.on('click', function(){
	    $searchInput.focus()
	    $searchInput.val('');
	});
	
	$searchSure.on('click', function(e){
		e.stopPropagation();
	    var searchkey = $searchInput.val();
	    if(!searchkey){
			cancelSearch();
			var href = '/mall/goodslist?key=all';
		}
	    else{
	    	var href = '/mall/goodslist?key=all&wd=' + searchkey;
	    }
		$searchInput.blur();
		location.href = encodeURI(encodeURI(href));
	});
	
	$searchInput.on('keypress', function(e){
		e.stopPropagation();
		if(e.which == 13){
			var searchkey = $searchInput.val();
	        if(!searchkey){
				cancelSearch();
				var href = '/mall/goodslist?key=all';
			}
	        else{
	            var href = '/mall/goodslist?key=all&wd=' + searchkey;
	        }
	    	location.href = encodeURI(encodeURI(href));
	    	return false;
		}
	});
	
	$searchInput.on('blur', function () {
	    //if(!this.value.length) cancelSearch();
	});
	
});