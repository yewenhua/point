
(function($){
	$.fn.alertBox = {};
	
	$.alertMessage = function(options){
		var settings = $.extend({}, $.fn.alertBox.defaults, options);
		var html = '<div id="alert_area_content">'
			 + '<div class="alert_area animate0 fadeInUpTwo"></div>'
		     + '<div class="alert_content animate0 fadeInUpText">' + settings.content + '</div>'
		     + '</div>';
		$("body").append(html);
		setTimeout('$("#alert_area_content").remove();',1200);
	}
	
	$.alertInfo = function(options){
		var close_btn_html = '';
		var settings = $.extend({}, $.fn.alertBox.defaults, options);
		if(settings.type == 2){
			close_btn_html = '<a href="'+ settings.url +'" class="w-full">' + 
	   	       '<button class="btn m-b-xs w-full btn-info" ng-class="" ng-bind-html="">'+ settings.btn_name +'</button>' +
	   	    '</a>'; 
		}
		else{
			close_btn_html = '<button id="popup-close-button" class="btn m-b-xs w-full btn-info" ng-class="" ng-bind-html="">关闭</button>'; 
		}
		
		var html = '<div class="popup-container popup-showing active">' +
		    '<div class="popup-bg"></div>' +
		    '<div class="popup">' +
			    '<div class="popup-head">' +
			      '<h3 class="popup-title" title="title">' + settings.title + '</h3>' +
			      //'<h5 class="popup-sub-title" subTitle="subTitle" ng-if="subTitle">' + settings.subTitle + '</h5>' +
			    '</div>' +
			    '<div class="popup-body">' +
			      '<h3 class="popup-title" ng-bind-html="title">' + settings.content + '</h3>' +
			    '</div>' +
			    '<div class="popup-buttons">' + close_btn_html + '</div>' +
			'</div>' +
		'</div>';
		
		$("body").append(html);
		
		$(document).on('click', '#popup-close-button', function(){
			$this = $(this).parents(".popup-container");
			$this.removeClass('active').addClass('popup-hidden').fadeOut(100, function(){
				$this.remove();
			});
		});
	}
	
	$.alertConfirm = function(options){
		var close_btn_html = '';
		var settings = $.extend({}, $.fn.alertBox.defaults, options);
		close_btn_html = '<button id="confirm-close-button" class="btn m-b-xs w-full btn-info" ng-class="" ng-bind-html="">确定</button>'; 
		
		var html = '<div class="popup-container popup-showing active max-zindex">' +
		    '<div class="popup-bg"></div>' +
		    '<div class="popup">' +
			    '<div class="popup-head">' +
			      '<h3 class="popup-title" title="title">' + settings.title + '</h3>' +
			      '<h5 class="popup-sub-title text-danger" subTitle="subTitle" id="subTitle">' + settings.subTitle + '</h5>' +
			    '</div>' +
			    '<div class="popup-body">' +
			      '<input type="text" class="form-control" placeholder="'+ settings.content +'" id="confirm-value"/>' +
			    '</div>' +
			    '<div class="popup-buttons">' + close_btn_html + '</div>' +
			'</div>' +
		'</div>';
		
		$("body").append(html);
		document.getElementById("subTitle").style.display="none";
		
		$(document).on('click', '#confirm-close-button', function(){
			$this = $(this).parents(".popup-container");
			var pwd = document.getElementById("confirm-value").value;
			if(pwd == settings.password){
				document.getElementById("subTitle").style.display="none";
				$this.removeClass('active').addClass('popup-hidden').fadeOut(100, function(){
					$this.remove();
				});
			}
			else{
				document.getElementById("subTitle").style.display="block";
			}
		});
	}
	
	$.fn.alertBox.defaults = {
		"title":"标题！",
		"subTitle":"副标题！",
		"type": 1, //1为普通关闭  2为跳转
		'url': '',
		'btn_name': '',
		'password': '',
		"content":"内容不能为空！"	
	};
	
})(jQuery);