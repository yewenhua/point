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
				  var auth = false;
				  angular.forEach($rootScope.userInfo.privilege_list, function(value, key){
					  if(value.page_state == toState.name){
						  auth = true;
					  }
				  });
				  
				  if(auth == false){
					  evt.preventDefault();
				      toaster.pop('error', '微商管理系统', '目标页面无权限访问！');
				  }
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
				  url: '/orderlist?comid&status&start&end&type',
				  templateUrl: path + 'templates/orderlist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/orderlistCtrl.js');
                      }]
	              }
			  })
			  .state('app.userlist', {
				  url: '/userlist',
				  templateUrl: path + 'templates/userlist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/userlistCtrl.js');
                      }]
	              }
			  })
			  .state('app.declaration', {
				  url: '/declaration?userid&type',
				  templateUrl: path + 'templates/declaration.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/declarationCtrl.js');
                      }]
	              }
			  })
			  .state('app.cashlist', {
				  url: '/cashlist?userid',
				  templateUrl: path + 'templates/cashlist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/cashlistCtrl.js');
                      }]
	              }
			  })
			  .state('app.commisionlist', {
				  url: '/commisionlist?userid',
				  templateUrl: path + 'templates/commisionlist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/commisionlistCtrl.js');
                      }]
	              }
			  })
			  .state('app.exchangelist', {
				  url: '/exchangelist?userid',
				  templateUrl: path + 'templates/exchangelist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/exchangelistCtrl.js');
                      }]
	              }
			  })
			  .state('app.consumelist', {
				  url: '/consumelist?userid',
				  templateUrl: path + 'templates/consumelist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/consumelistCtrl.js');
                      }]
	              }
			  })
			  .state('app.useablelist', {
				  url: '/useablelist?userid',
				  templateUrl: path + 'templates/useablelist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/useablelistCtrl.js');
                      }]
	              }
			  })
			  .state('app.sharelist', {
				  url: '/sharelist?userid',
				  templateUrl: path + 'templates/sharelist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/sharelistCtrl.js');
                      }]
	              }
			  })
			  .state('app.waitlist', {
				  url: '/waitlist?userid',
				  templateUrl: path + 'templates/waitlist.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/waitlistCtrl.js');
                      }]
	              }
			  })
			  .state('app.upgrade', {
				  url: '/upgrade',
				  templateUrl: path + 'templates/upgrade.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/upgradeCtrl.js');
                      }]
	              }
			  })
			  .state('app.commisionsetting', {
				  url: '/commisionsetting',
				  templateUrl: path + 'templates/commisionsetting.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/commisionsettingCtrl.js');
                      }]
	              }
			  })
			  .state('app.declarationsetting', {
				  url: '/declarationsetting',
				  templateUrl: path + 'templates/declarationsetting.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/declarationsettingCtrl.js');
                      }]
	              }
			  })
			  .state('app.ercode', {
				  url: '/ercode',
				  templateUrl: path + 'templates/ercode.html',
	              resolve: {
				      deps: ['$ocLazyLoad',
	                     function( $ocLazyLoad ){
	                         return $ocLazyLoad.load(['angularFileUpload']).then(
	                             function(){
	                                 return $ocLazyLoad.load([
	                                                          path + 'vendor/poster/designer.js', 
	                                                          path + 'vendor/poster/spectrum.css', 
	                                                          path + 'vendor/poster/spectrum.js', 
	                                                          path + 'js/controllers/ercodeCtrl.js'
	                                 ]);
	                             }
	                         );
	                     }
				      ]
	              }
			  })
			  .state('app.servicesetting', {
				  url: '/servicesetting',
				  templateUrl: path + 'templates/servicesetting.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/servicesettingCtrl.js');
                      }]
	              }
			  })
			  .state('app.wechatsetting', {
				  url: '/wechatsetting',
				  templateUrl: path + 'templates/wechatsetting.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/wechatsettingCtrl.js');
                      }]
	              }
			  })
			  .state('app.smssetting', {
				  url: '/smssetting',
				  templateUrl: path + 'templates/smssetting.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/smssettingCtrl.js');
                      }]
	              }
			  })
			  .state('app.personal', {
				  url: '/personal',
				  templateUrl: path + 'templates/personal.html',
	              resolve: {
				      deps: ['uiLoad',
                        function( uiLoad){
                          return uiLoad.load(path + 'js/controllers/personalCtrl.js');
                      }]
	              }
			  })
              
              .state('app.system', {
                  url: '/system',
                  templateUrl: path + 'templates/system.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/systemCtrl.js');
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
              .state('app.adminsetting', {
                  url: '/adminsetting',
                  templateUrl: path + 'templates/adminsetting.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/adminsettingCtrl.js');
                      }]
                  }
              })
              .state('app.privilege', {
                  url: '/privilege',
                  templateUrl: path + 'templates/privilege.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/privilegeCtrl.js');
                      }]
                  }
              })
              .state('app.role', {
                  url: '/role',
                  templateUrl: path + 'templates/role.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/roleCtrl.js');
                      }]
                  }
              })
              .state('app.log', {
                  url: '/log',
                  templateUrl: path + 'templates/log.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/logCtrl.js');
                      }]
                  }
              })
              .state('app.category', {
                  url: '/category',
                  templateUrl: path + 'templates/category.html',
                  resolve: {
            	      deps: ['$ocLazyLoad',
                         function( $ocLazyLoad ){
                           return $ocLazyLoad.load(['angularBootstrapNavTree', 'angularFileUpload']).then(
                               function(){
                                  return $ocLazyLoad.load([path + 'js/controllers/categoryCtrl.js']);
                               }
                           );
                         }
                      ]
                  }
              })
              .state('app.banner', {
                  url: '/banner',
                  templateUrl: path + 'templates/banner.html',
                  resolve: {
            	      deps: ['$ocLazyLoad',
	                      function( $ocLazyLoad ){
	                          return $ocLazyLoad.load(['angularFileUpload']).then(
	                              function(){
	                                  return $ocLazyLoad.load([path + 'js/controllers/bannerCtrl.js']);
	                              }
	                          );
	                      }
				      ]
                  }
              })
              .state('app.goodslist', {
                  url: '/goodslist?key&start&end&type',
                  templateUrl: path + 'templates/goodslist.html',
                  resolve: {
            	      deps: ['$ocLazyLoad',
                         function( $ocLazyLoad ){
                           return $ocLazyLoad.load(['angularBootstrapNavTree', 'angularFileUpload']).then(
                               function(){
                                  return $ocLazyLoad.load([path + 'js/controllers/goodslistCtrl.js']);
                               }
                           );
                         }
                      ]
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
              .state('app.userimport', {
                  url: '/userimport',
                  templateUrl: path + 'templates/userimport.html',
                  resolve: {
	                  deps: ['$ocLazyLoad',
	                     function( $ocLazyLoad ){
	                         return $ocLazyLoad.load(['angularFileUpload']).then(
	                             function(){
	                                 return $ocLazyLoad.load([path + 'js/controllers/userimportCtrl.js']);
	                             }
	                         );
	                     }
				      ]
                  }
              })
              .state('app.quick', {
                  url: '/quick',
                  templateUrl: path + 'templates/quick.html',
                  resolve: {
	                  deps: ['uiLoad',
	                     function( uiLoad){
	                       return uiLoad.load(path + 'js/controllers/quickCtrl.js');
	                  }]
                  }
              })
              .state('app.center', {
                  url: '/center',
                  templateUrl: path + 'templates/center.html',
                  resolve: {
	                  deps: ['uiLoad',
	                     function( uiLoad){
	                       return uiLoad.load(path + 'js/controllers/centerCtrl.js');
	                  }]
                  }
              })
              .state('app.department', {
                  url: '/department',
                  templateUrl: path + 'templates/department.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/departmentCtrl.js');
                      }]
                  }
              })
              .state('app.shareholder', {
                  url: '/shareholder',
                  templateUrl: path + 'templates/shareholder.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/shareholderCtrl.js');
                      }]
                  }
              })
              .state('app.merchant', {
                  url: '/merchant',
                  templateUrl: path + 'templates/merchant.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/merchantCtrl.js');
                      }]
                  }
              })
              .state('app.takecash', {
                  url: '/takecash',
                  templateUrl: path + 'templates/takecash.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/takecashCtrl.js');
                      }]
                  }
              })
              .state('app.wallet', {
                  url: '/wallet',
                  templateUrl: path + 'templates/wallet.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/walletCtrl.js');
                      }]
                  }
              })
              .state('app.message', {
                  url: '/message',
                  templateUrl: path + 'templates/message.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/messageCtrl.js');
                      }]
                  }
              })
              .state('app.logistic', {
                  url: '/logistic',
                  templateUrl: path + 'templates/logistic.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/logisticCtrl.js');
                      }]
                  }
              })
              .state('app.business', {
                  url: '/business',
                  templateUrl: path + 'templates/business.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/businessCtrl.js');
                      }]
                  }
              })
              .state('app.repeatcompensation', {
				  url: '/repeatcompensation',
				  templateUrl: path + 'templates/repeatcompensation.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/repeatcompensationCtrl.js');
                      }]
	              }
			  })
			  .state('app.repeatmanager', {
				  url: '/repeatmanager',
				  templateUrl: path + 'templates/repeatmanager.html',
	              resolve: {
				      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/repeatmanagerCtrl.js');
                      }]
	              }
			  })
			  .state('app.smstemplate', {
                  url: '/smstemplate',
                  templateUrl: path + 'templates/smstemplate.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/smstemplateCtrl.js');
                      }]
                  }
              })
              .state('app.article', {
                  url: '/article',
                  templateUrl: path + 'templates/article.html',
                  resolve: {
	                  deps: ['$ocLazyLoad',
	                     function( $ocLazyLoad ){
	                         return $ocLazyLoad.load(['angularFileUpload']).then(
	                             function(){
	                                 return $ocLazyLoad.load([path + 'js/controllers/articleCtrl.js']);
	                             }
	                         );
	                     }
				      ]
                  }
              })
              .state('app.rankorder', {
                  url: '/rankorder',
                  templateUrl: path + 'templates/rankorder.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/rankorderCtrl.js');
                      }]
                  }
              })
              .state('app.salerank', {
                  url: '/salerank',
                  templateUrl: path + 'templates/salerank.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/salerankCtrl.js');
                      }]
                  }
              })
              .state('app.merchantgoods', {
                  url: '/merchantgoods',
                  templateUrl: path + 'templates/merchantgoods.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/merchantgoodsCtrl.js');
                      }]
                  }
              })
              .state('app.salegoods', {
                  url: '/salegoods',
                  templateUrl: path + 'templates/salegoods.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/salegoodsCtrl.js');
                      }]
                  }
              })
              .state('app.consumerank', {
                  url: '/consumerank',
                  templateUrl: path + 'templates/consumerank.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/consumerankCtrl.js');
                      }]
                  }
              })
              .state('app.declarationrank', {
                  url: '/declarationrank',
                  templateUrl: path + 'templates/declarationrank.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/declarationrankCtrl.js');
                      }]
                  }
              })
              .state('app.memberrank', {
                  url: '/memberrank',
                  templateUrl: path + 'templates/memberrank.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/memberrankCtrl.js');
                      }]
                  }
              })
              .state('app.sharemanager', {
                  url: '/sharemanager',
                  templateUrl: path + 'templates/sharemanager.html',
                  resolve: {
            	      deps: ['uiLoad',
                         function( uiLoad){
                           return uiLoad.load(path + 'js/controllers/sharemanagerCtrl.js');
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