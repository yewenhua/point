
angular.module('app')

// state
.run(['$rootScope', '$state', '$stateParams', 'RequestManager', '$interval', 'pwd',
	  function ($rootScope, $state, $stateParams, RequestManager, $interval, pwd) {
	        
			$rootScope.$state = $state;
			$rootScope.$stateParams = $stateParams;
	
			$rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
	
				RequestManager.cancel();
				
				if (toState.name != 'access.signin' && toState.name != 'access.signup' && toState.name != 'access.forget' && toState.name != 'access.welcome' && toState.name != 'access.signupByShare') {
					if (!localStorage.getItem('userInfo')) {
						$state.go('access.signin');
						event.preventDefault();
					}
					else{
						var localUserInfo = JSON.parse(localStorage.getItem('userInfo'));
						var currentUser = angular.copy(localUserInfo);
						currentUser.id = pwd.removeNumPwd(currentUser.id);
						currentUser.mobile = pwd.removePhonePwd(currentUser.mobile);
						$rootScope.userInfo = currentUser;
					}
				}
				else{
					if (localStorage.getItem('userInfo')) {
						var localUserInfo = JSON.parse(localStorage.getItem('userInfo'));
						var currentUser = angular.copy(localUserInfo);
						currentUser.id = pwd.removeNumPwd(currentUser.id);
						currentUser.mobile = pwd.removePhonePwd(currentUser.mobile);
						$rootScope.userInfo = currentUser;
						$state.go('tab.dash');
						event.preventDefault();
					}
				}
			});
	  }
])

.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', '$httpProvider', function($stateProvider, $urlRouterProvider, $locationProvider, $httpProvider) {
	$httpProvider.interceptors.push('Interceptor');
		
	$stateProvider
	  .state('tab', {
		    url: '/tab',
		    abstract: true,
		    templateUrl: path + 'templates/tabs.html',
		    resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/main.js'] );
                }]
            }
	  })
	  .state('tab.dash', {
		    url: '/dash',
		    views: {
			      'tab-dash': {
				        templateUrl: path + 'templates/tab-dash.html',
				        resolve: {
	                        deps: ['uiLoad',
	                          function( uiLoad ){
	                            return uiLoad.load( [path + 'css/alternate.css', path + 'js/controllers/dashCtrl.js', path + 'js/controllers/alternateCtrl.js'] );
	                        }]
	                    }
			      }
		    }
	  })
	  .state('tab.account', {
		    url: '/account',
		    views: {
			      'tab-account': {
				        templateUrl: path + 'templates/tab-account.html',
				        resolve: {
	                        deps: ['uiLoad',
	                          function( uiLoad ){
	                            return uiLoad.load( [path + 'js/controllers/accountCtrl.js'] );
	                        }]
	                    }
			      }
		    }
	  })
	  .state('tab.profile', {
		    url: '/profile',
		    views: {
			      'tab-profile': {
				        templateUrl: path + 'templates/tab-profile.html',
				        resolve: {
	                        deps: ['uiLoad',
	                          function( uiLoad ){
	                            return uiLoad.load( [path + 'js/controllers/profileCtrl.js'] );
	                        }]
	                    }
			      }
		    }
	  })
	  
	  .state('list', {
		    url: '/list',
		    abstract: true,
		    template: '<ion-nav-view></ion-nav-view>'
	  })
	  .state('list.apply', {
	        url: '/apply',
	        templateUrl: path + 'templates/list/apply.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/list/applyCtrl.js'] );
                }]
            }
	  })
	  .state('list.catch', {
	        url: '/catch',
	        templateUrl: path + 'templates/list/catch.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/list/catchCtrl.js'] );
                }]
            }
	  })
	  .state('list.cashwater', {
	        url: '/cashwater',
	        templateUrl: path + 'templates/list/cashwater.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/list/cashwaterCtrl.js'] );
                }]
            }
	  })
	  .state('list.banklist', {
	        url: '/banklist',
	        templateUrl: path + 'templates/list/banklist.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/list/banklistCtrl.js'] );
                }]
            }
	  })
	  .state('list.message', {
	        url: '/message',
	        templateUrl: path + 'templates/list/message.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/list/messageCtrl.js'] );
                }]
            }
	  })
	  .state('list.messagedetail', {
	        url: '/messagedetail/:id',
	        templateUrl: path + 'templates/list/messagedetail.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/list/messagedetailCtrl.js'] );
                }]
            }
	  })
	  
	  .state('access', {
		    url: '/access',
		    abstract: true,
		    template: '<ion-nav-view></ion-nav-view>'
	  })
	  /*
	  .state('access.welcome', {
	        url: '/welcome',
	        templateUrl: path + 'templates/access/welcome.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/access/welcomeCtrl.js'] );
                }]
            }
	  })
	  */
	  .state('access.signin', {
	        url: '/signin',
	        templateUrl: path + 'templates/access/signin.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/access/signinCtrl.js'] );
                }]
            }
	  })
	  .state('access.signup', {
	        url: '/signup',
	        templateUrl: path + 'templates/access/signup.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/access/signupCtrl.js', path + 'js/controllers/modal/signupProtocalCtrl.js'] );
                }]
            }
	  })
	  .state('access.signupByShare', {
	        url: '/signupByShare/:server_id/:code',
	        templateUrl: path + 'templates/access/signupByShare.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/access/signupByShareCtrl.js', path + 'js/controllers/modal/signupProtocalCtrl.js'] );
                }]
            }
	  })
	  .state('access.forget', {
	        url: '/forget',
	        templateUrl: path + 'templates/access/forget.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/access/forgetCtrl.js'] );
                }]
            }
	  })
	  .state('access.account', {
		    url: '/account',
		    templateUrl: path + 'templates/tab-account.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/accountCtrl.js'] );
                }]
            }
	  })
	  /*
	  .state('access.addlock', {
		    url: '/addlock',
		    templateUrl: path + 'templates/access/addlock.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/access/addlockCtrl.js'] );
                }]
            }
	  })
	  .state('access.updatelock', {
		    url: '/updatelock',
		    templateUrl: path + 'templates/access/updatelock.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/access/updatelockCtrl.js'] );
                }]
            }
	  })
	  .state('access.gestureEnterance', {
			url: '/gestureEnterance',
			templateUrl: path + 'templates/access/gestureEnterance.html',
			resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/access/gestureEnteranceCtrl.js'] );
                }]
            }
	  })
	  */
	  .state('product', {
		    url: '/product',
		    abstract: true,
		    template: '<ion-nav-view></ion-nav-view>'
	  })
	  .state('product.detail', {
		    url: '/detail/:proid',
		    templateUrl: path + 'templates/product/detail.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/product/detailCtrl.js', path + 'js/controllers/modal/financeProtocalCtrl.js'] );
                }]
            }
	  })
	  .state('user', {
		    url: '/user',
		    abstract: true,
		    template: '<ion-nav-view></ion-nav-view>'
	  })
	  .state('user.info', {
	        url: '/info',
	        templateUrl: path + 'templates/user/info.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/infoCtrl.js'] );
                }]
            }
	  })
	  .state('user.update', {
	        url: '/update/:type',
	        templateUrl: path + 'templates/user/update.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/updateCtrl.js'] );
                }]
            }
	  })
	  /*
	  .state('user.recharge', {
	        url: '/recharge',
	        templateUrl: path + 'templates/user/recharge.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/rechargeCtrl.js'] );
                }]
            }
	  })
	  */
	  .state('user.takecash', {
	        url: '/takecash',
	        templateUrl: path + 'templates/user/takecash.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/takecashCtrl.js'] );
                }]
            }
	  })
	  .state('user.setting', {
	        url: '/setting',
	        templateUrl: path + 'templates/user/setting.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/settingCtrl.js'] );
                }]
            }
	  })
	  .state('user.about', {
	        url: '/about',
	        templateUrl: path + 'templates/user/about.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/aboutCtrl.js'] );
                }]
            }
	  })
	  .state('user.service', {
	        url: '/service',
	        templateUrl: path + 'templates/user/service.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/serviceCtrl.js'] );
                }]
            }
	  })
	  .state('user.bank', {
	        url: '/bank',
	        templateUrl: path + 'templates/user/bank.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/bankCtrl.js', path + 'js/other/jquery.cityselect.js'] );
                }]
            }
	  })
	  .state('user.upload', {
	        url: '/upload',
	        templateUrl: path + 'templates/user/upload.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/uploadCtrl.js'] );
                }]
            }
	  })
	  .state('user.jujian', {
	        url: '/jujian',
	        templateUrl: path + 'templates/user/jujian.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/jujianCtrl.js'] );
                }]
            }
	  })
	  .state('user.julist', {
	        url: '/julist',
	        templateUrl: path + 'templates/user/julist.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/user/julistCtrl.js'] );
                }]
            }
	  })
	  .state('jujian', {
		    url: '/jujian',
		    abstract: true,
		    template: '<ion-nav-view></ion-nav-view>'
	  })
	  .state('jujian.promotion', {
	        url: '/promotion',
	        templateUrl: path + 'templates/jujian/promotion.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/jujian/promotionCtrl.js'] );
                }]
            }
	  })
	  .state('jujian.popularity', {
	        url: '/popularity',
	        templateUrl: path + 'templates/jujian/popularity.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/jujian/popularityCtrl.js'] );
                }]
            }
	  })
	  .state('jujian.commision', {
	        url: '/commision',
	        templateUrl: path + 'templates/jujian/commision.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/jujian/commisionCtrl.js'] );
                }]
            }
	  })
	  .state('jujian.poplist', {
	        url: '/poplist/:level',
	        templateUrl: path + 'templates/jujian/poplist.html',
	        resolve: {
                deps: ['uiLoad',
                  function( uiLoad ){
                    return uiLoad.load( [path + 'js/controllers/jujian/poplistCtrl.js'] );
                }]
            }
	  });
	
	  $urlRouterProvider.otherwise('/access/signin');

}]);
