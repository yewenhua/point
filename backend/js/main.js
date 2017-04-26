'use strict';
angular.module('app')
.controller('AppCtrl', ['$scope', '$rootScope', '$translate', '$localStorage', '$window', '$state', 'DataLoad', '$interval', '$timeout',
    function($scope,  $rootScope,  $translate,   $localStorage,   $window , $state , DataLoad, $interval, $timeout) {
      // add 'ie' classes to html
      var isIE = !!navigator.userAgent.match(/MSIE/i);
      isIE && angular.element($window.document.body).addClass('ie');
      isSmartDevice( $window ) && angular.element($window.document.body).addClass('smart');

      // config
      $scope.app = {
          name: '微商之家',
          version: '2.0',
          // for chart colors
          color: {
	          primary: '#7266ba',
	          info:    '#23b7e5',
	          success: '#27c24c',
	          warning: '#fad733',
	          danger:  '#f05050',
	          light:   '#e8eff0',
	          dark:    '#3a3f51',
	          black:   '#1c2b36'
          },
          settings: {
	          themeID: 10,
	          navbarHeaderColor: 'bg-info dker',
	          navbarCollapseColor: 'bg-info dk',
	          asideColor: 'bg-black',
	          headerFixed: true,
	          asideFixed: false,
	          asideFolded: false,
	          asideDock: false,
	          container: false
          }
      }

      // save settings to local storage
      if ( angular.isDefined($localStorage.settings) ) {
          $scope.app.settings = $localStorage.settings;
      } else {
          $localStorage.settings = $scope.app.settings;
      }
      $scope.$watch('app.settings', function(){
          if( $scope.app.settings.asideDock  &&  $scope.app.settings.asideFixed ){
            $scope.app.settings.headerFixed = true;
          }
          $localStorage.settings = $scope.app.settings;
      }, true);

      function isSmartDevice( $window )
      {
          var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
          return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
      }
      
      $rootScope.userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
      $scope.logout = function () {
    	  $rootScope.init = true;
    	  sessionStorage.removeItem('userInfo');
		  $state.go('access.signin');
		  
		  var promise = DataLoad.logout({});
		  return promise.then(function (returnData) {
			  
		  }, function () {
			  
		  });
      }
      
      $rootScope.init = true;
      $rootScope.systemInfo = {};
      $rootScope.getSystemData = function() {
		  var promise = DataLoad.getSystemData({

		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$rootScope.systemInfo = returnData.data;
					$rootScope.systemInfo.signin_title = $rootScope.systemInfo.title + '管理后台';
					$scope.app.name = angular.copy($rootScope.systemInfo.title);
				} 
				else {
					$rootScope.systemInfo = {};
					$rootScope.systemInfo.title = '微商管理系统';
					$rootScope.systemInfo.signin_title = '微商后台管理系统';
					$rootScope.systemInfo.author = '版权所有';
				}
		  }, function () {
			    $rootScope.systemInfo = {};
				$rootScope.systemInfo.title = '微商管理系统';
				$rootScope.systemInfo.signin_title = '微商后台管理系统';
				$rootScope.systemInfo.author = '版权所有';
		  });
      }
      $rootScope.getSystemData();
      
    /**
      *  console.log($state.current.name);
      *  uses a reference to the object. By the time we see it in the console the object is already changed.
      *  A $watch is needed here, because it's all asynchronous 
      */
      $scope.currState = $state;
      $scope.$watch('currState.current.name', function(newValue, oldValue) {
    	  if(newValue){
    		  if ($state.current.name != 'access.signin') {
    			  var userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
    	    	  if(!userInfo){
    	    		  $rootScope.init = true;
    	    		  $scope.logout();
    	    		  return false;
    	    	  }
    	    	  else{
    	    		  if($rootScope.init){
    	    			  $rootScope.init = false;
    	    		      $scope.notify(userInfo);
    	    		  }
    	    	  }
    		  }
    	  }
      }); 
      
      $scope.notify = function (userInfo) {
    	  $interval(function(){
    		  $scope.getNewMessage();
    	  }, 5000);
      }
      
      $scope.isPrivilege = function (state) {
    	  if ($state.current.name != 'access.signin') {
	    	  var active = false;
	    	  var userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
	    	  if(userInfo){
		    	  angular.forEach(userInfo.privilege_list, function(value, key){
					  if(value.page_state == state){
						  active = true;
					  }
				  });
		    	  if(active == false){
		    		  return false;
		    	  }
		    	  else{
		    		  return true;
		    	  }
	    	  }
	    	  else{
	    		  return false;
	    	  }
    	  }
      }
      
      $scope.isOperate = function (state) {
    	  if ($state.current.name != 'access.signin') {
	    	  var active = false;
	    	  var userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
	    	  if(userInfo){
		    	  angular.forEach(userInfo.privilege_list, function(value, key){
					  if(value.page_state == state && value.writeSelected == 1){
						  active = true;
					  }
				  });
		    	  if(active == false){
		    		  return false;
		    	  }
		    	  else{
		    		  return true;
		    	  }
	    	  }
	    	  else{
	    		  return false;
	    	  }
    	  }
      }

	  $rootScope.diffNum_no_review = 0;
	  $rootScope.alert_no_review_title = '';
	  $rootScope.goto_state_no_review = '';
	  
	  $rootScope.diffNum_no_refund = 0;
	  $rootScope.alert_no_refund_title = '';
	  $rootScope.goto_state_no_refund = '';
	  
	  $rootScope.getNewMessage = function() {
		  var promise = DataLoad.getNewMessage({
			  
		  });
		  return promise.then(function (returnData) {
			  if (returnData && returnData.code == 0) {
				  if(returnData.waitSendCount > 0 && returnData.waitSendCount != $rootScope.diffNum_no_review){
					  $rootScope.alert_no_review_title = '待发货';
					  $rootScope.diffNum_no_review = angular.copy(returnData.waitSendCount);
					  $rootScope.goto_state_no_review = 'app.orderlist';
					  $rootScope.messageType = 'review';
				      $scope.playSoundHtml5($rootScope.messageType);
				  }
				  if(returnData.waitRefundCount > 0 && returnData.waitRefundCount != $rootScope.diffNum_no_refund){
					  $rootScope.alert_no_refund_title = '待退款';
					  $rootScope.diffNum_no_refund = angular.copy(returnData.waitRefundCount);
					  $rootScope.goto_state_no_refund = 'app.orderlist';
					  $rootScope.messageType = 'refund';
				      $scope.playSoundHtml5($rootScope.messageType);
				  }
			  } 
			  else{
				  $("#closeReviewMessage").fadeOut(); 
				  $("#closeRefundMessage").fadeOut(); 
			  }
		  }, function () {
			  $("#closeReviewMessage").fadeOut(); 
			  $("#closeRefundMessage").fadeOut(); 
		  });
      }
      
      $scope.playSoundHtml5 = function($type) {
    	  var playSoundHtml5 = document.getElementById('playSoundHtml5');
		  playSoundHtml5.play();
		  if( $type == "review" ){
              $("#closeReviewMessage").fadeIn(); 
          }
		  else if( $type == "refund" ){
              $("#closeRefundMessage").fadeIn(); 
          }
      }
      
      $scope.closeReviewMessage = function($event) {
    	  $event.stopPropagation();
    	  var start = '2016-12-01';
      	  var end = moment().utc().endOf('day').format('YYYY-MM-DD');
    	  $("#closeReviewMessage").fadeOut();
    	  $window.location.href = "/admin#/app/orderlist?status=1&start=" + start + "&end=" + end;
      }
      
      $scope.closeReviewMessageOnly = function($event) {
    	  $event.stopPropagation();
    	  $("#closeReviewMessage").fadeOut();
      }
      
      $scope.closeRefundMessage = function($event) {
    	  $event.stopPropagation();
    	  var start = '2016-12-01';
      	  var end = moment().utc().endOf('day').format('YYYY-MM-DD');
    	  $("#closeRefundMessage").fadeOut();
    	  $window.location.href = "/admin#/app/orderlist?status=7&start=" + start + "&end=" + end;
      }
      
      $scope.closeRefundMessageOnly = function($event) {
    	  $event.stopPropagation();
    	  $("#closeRefundMessage").fadeOut();
      }
 }])
 
.directive('ngEnter', function() {
	return function(scope, element, attrs) {
		element.bind("keydown keypress", function(event) {
			if (event.which === 13) {
				scope.$apply(function() {
					//$eval是一个作用域scope中的方法，它将会在当前作用域中执行一个表达式并返回结果
					scope.$eval(attrs.ngEnter);
				});

				event.preventDefault();
			}
		});
	};
});

angular.module('app').filter('sizeFilter', function(){
	return function(size){
		var returnSize = '';
		if(size > 1024){
			var temp = size/1024;
			returnSize = temp.toFixed(2) + ' G';
		}
		else{
			returnSize = size + ' M';
		}
		
		return returnSize;
	}
});