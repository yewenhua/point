// config

var app = angular.module('app')
.config(
     ['$controllerProvider', '$compileProvider', '$filterProvider', '$provide',
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
.config(['$translateProvider', function($translateProvider){
     $translateProvider.useStaticFilesLoader({
        prefix: '/backend/l10n/',
        suffix: '.js'
     });
     // Tell the module what language to use by default
     $translateProvider.preferredLanguage('en');
     // Tell the module to store the language in the local storage
     $translateProvider.useLocalStorage();
}])
.config(['$httpProvider', function($httpProvider) {
	 $httpProvider.interceptors.push('interceptor');
}]);