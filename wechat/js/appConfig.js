'use strict';

var app = angular.module('app')

.constant('AppConstants', {
	  debug: true,
})

.config(['$controllerProvider', '$compileProvider', '$filterProvider', '$provide',
    function ($controllerProvider,   $compileProvider,   $filterProvider,   $provide) {
        
        // lazy controller, directive and service
        app.controller = $controllerProvider.register;
        app.directive  = $compileProvider.directive;
        app.filter     = $filterProvider.register;
        app.factory    = $provide.factory;
        app.service    = $provide.service;
        app.constant   = $provide.constant;
        app.value      = $provide.value;
    }
])

.run(
	['$rootScope', '$state', '$stateParams', '$location', 'RequestManager',
		function($rootScope, $state, $stateParams, $location, RequestManager) {
		    //方便获得当前状态的方法，绑到根作用域
			$rootScope.$state = $state;
			$rootScope.$stateParams = $stateParams;
			
			//从一个状态开始过渡到另一个状态时触发这个事件
			$rootScope.$on('$stateChangeStart', function(evt, toState, toParams, fromState, fromParams) {
				RequestManager.cancel();
				// 可以阻止这一状态完成
				//evt.preventDefault();
				
				if (toState.name != 'access.signin') {
					if (!sessionStorage.getItem('userInfo')) {
						$state.go('access.signin');
						evt.preventDefault();
					}
				}
				else{
					if (sessionStorage.getItem('userInfo')) {
						$state.go('app.home');
						evt.preventDefault();
					}
				}
			});
			
			//从一个状态过渡到下一个状态完成时触发这个事件
			$rootScope.$on('$stateChangeSuccess', function(evt, toState, toParams, fromState, fromParams) {
				//console.log("$stateChangeSuccess");
			});
			
			//当过渡期间发生错误时触发这个事件。通常，模板不能被解析或者解析 promise失败时会引发错误
			$rootScope.$on('$stateChangeError', function(evt, toState, toParams, fromState, fromParams) {
				//console.log("$stateChangeError");
			});
			
			//视图开始加载时，DOM被渲染之前，触发这个事件
			$rootScope.$on('$viewContentLoading', function(event, viewConfig) {
				// 在这里可以访问所有视图配置属性
				// 以及一个特殊的“targetView”属性
				// viewConfig.targetView
				//console.log("$viewContentLoading");
			});
		}
	]
)


.config(
	['$stateProvider', '$urlRouterProvider', '$locationProvider', '$httpProvider',
		function($stateProvider, $urlRouterProvider, $locationProvider, $httpProvider) {

			//$locationProvider.html5Mode(true).hashPrefix('!');	
		    $httpProvider.interceptors.push('Interceptor');

			$stateProvider

				.state('app', {
					abstract: true,
					url: '/app',
					templateUrl: 'templates/app.html'
				})
				.state('app.home', {
					url: '/home',
					templateUrl: 'templates/home.html',
					onExit: function(){
						//离开视图时调用这些回调函数
						console.log("onExit");
					}
				})
				.state('user', {
					abstract: true,
					url: '/user',
					template: '<div ui-view></div>'
				})
				.state('user.personal', {
					url: '/personal',
					templateUrl: 'templates/personal.html',
					onEnter: function(){
						//进入视图时调用这些回调函数
						console.log("onEnter");
					}
				})
				.state('access', {
					abstract: true,
                    url: '/access',
                    template: '<div ui-view></div>'
                })
                .state('access.signin', {
                    url: '/signin',
                    templateUrl: 'templates/signin.html',
                    resolve: {
                        deps: ['uiLoad',
                          function( uiLoad ){
                            return uiLoad.load( ['js/controllers/signinCtrl.js'] );
                        }]
                    }
                });
			
			$urlRouterProvider.otherwise('/access/signin');

		}
	]
);