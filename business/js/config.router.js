'use strict';
angular.module('app')
  .run(
    ['$rootScope', '$state', '$stateParams', 'RequestManager', 'toaster',
      function ($rootScope,   $state,   $stateParams, RequestManager, toaster) {
          $rootScope.$state = $state;
          $rootScope.$stateParams = $stateParams;     
          
          //从一个状态开始过渡到另一个状态时触发这个事件
		  $rootScope.$on('$stateChangeStart', function(evt, toState, toParams, fromState, fromParams) {
			  RequestManager.cancel();
			  // 可以阻止这一状态完成
			  //evt.preventDefault();
			  		  
			  //权限管理
			  function userAuth() {
				  $rootScope.userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
			  }
			  
			  if (toState.name != 'access.signin') {
				  if (!sessionStorage.getItem('userInfo')) {
					  $state.go('access.signin');
					  evt.preventDefault();
				  }
				  else{
					  userAuth();
				  }
			  }
			  else{
				  if (sessionStorage.getItem('userInfo')) {
					  userAuth();
					  $state.go('app.welcome');
					  evt.preventDefault();
				  }
			  }
		  });
       }
    ]
  )
  .config(
    ['$stateProvider', '$urlRouterProvider',
       function ($stateProvider,   $urlRouterProvider) {
          
          $urlRouterProvider.otherwise('/app/welcome');
          $stateProvider
              .state('app', {
                  abstract: true,
                  url: '/app',
                  templateUrl: path + 'tpl/app.html'
              })
			  .state('app.orderlist', {
				  url: '/orderlist?status',
				  templateUrl: path + 'templates/orderlist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/orderlistCtrl.js');
                      }]
	              }
			  })
              .state('app.chgpwd', {
                  url: '/chgpwd',
                  templateUrl: path + 'templates/chgpwd.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/chgpwdCtrl.js');
                      }]
                  }
              })
              .state('app.welcome', {
                  url: '/welcome',
                  templateUrl: path + 'templates/welcome.html',
                  resolve: {
	                  deps: ['uiLoad',
	                     function( uiLoad){
	                       return uiLoad.load(path + 'js/controllers/welcomeCtrl.js');
	                  }]
                  }
              })
              .state('access', {
                  url: '/access',
                  template: '<div ui-view class="fade-in-right-big smooth"></div>'
              })
              .state('access.signin', {
                  url: '/signin',
                  templateUrl: path + 'tpl/page_signin.html',
                  resolve: {
                      deps: ['uiLoad',
                        function( uiLoad ){
                          return uiLoad.load( [path + 'js/controllers/signinCtrl.js'] );
                      }]
                  }
              })
          }
      ]
  );