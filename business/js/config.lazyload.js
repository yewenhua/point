// lazyload config

angular.module('app')
    /**
   * jQuery plugin config use ui-jq directive , config the js and css files that required
   * key: function name of the jQuery plugin
   * value: array of the css js file located
   */
  .constant('JQ_CONFIG', {
      //easyPieChart:   ['/business/vendor/jquery/charts/easypiechart/jquery.easy-pie-chart.js'],
      //sparkline:      ['/business/vendor/jquery/charts/sparkline/jquery.sparkline.min.js'],
      //plot:           ['/business/vendor/jquery/charts/flot/jquery.flot.min.js', 
      //                    '/business/vendor/jquery/charts/flot/jquery.flot.resize.js',
      //                    '/business/vendor/jquery/charts/flot/jquery.flot.tooltip.min.js',
      //                    '/business/vendor/jquery/charts/flot/jquery.flot.spline.js',
      //                    '/business/vendor/jquery/charts/flot/jquery.flot.orderBars.js',
      //                    '/business/vendor/jquery/charts/flot/jquery.flot.pie.min.js'],
      slimScroll:     ['/business/vendor/jquery/slimscroll/jquery.slimscroll.min.js'],
      sortable:       ['/business/vendor/jquery/sortable/jquery.sortable.js'],
      //nestable:       ['/business/vendor/jquery/nestable/jquery.nestable.js',
      //                    '/business/vendor/jquery/nestable/nestable.css'],
      filestyle:      ['/business/vendor/jquery/file/bootstrap-filestyle.min.js'],
      //slider:         ['/business/vendor/jquery/slider/bootstrap-slider.js',
      //                  '/business/vendor/jquery/slider/slider.css'],
      chosen:         ['/business/vendor/jquery/chosen/chosen.jquery.min.js',
                          '/business/vendor/jquery/chosen/chosen.css'],
      //TouchSpin:      ['/business/vendor/jquery/spinner/jquery.bootstrap-touchspin.min.js',
      //                    '/business/vendor/jquery/spinner/jquery.bootstrap-touchspin.css'],
      //wysiwyg:        ['/business/vendor/jquery/wysiwyg/bootstrap-wysiwyg.js',
      //                    '/business/vendor/jquery/wysiwyg/jquery.hotkeys.js'],
      //dataTable:      ['/business/vendor/jquery/datatables/jquery.dataTables.min.js',
      //                    '/business/vendor/jquery/datatables/dataTables.bootstrap.js',
      //                    '/business/vendor/jquery/datatables/dataTables.bootstrap.css'],
      //vectorMap:      ['/business/vendor/jquery/jvectormap/jquery-jvectormap.min.js', 
      //                    '/business/vendor/jquery/jvectormap/jquery-jvectormap-world-mill-en.js',
      //                    '/business/vendor/jquery/jvectormap/jquery-jvectormap-us-aea-en.js',
      //                    '/business/vendor/jquery/jvectormap/jquery-jvectormap.css'],
      footable:       ['/business/vendor/jquery/footable/footable.all.min.js',
                          '/business/vendor/jquery/footable/footable.core.css']
      }
  )
  // oclazyload config
  .config(['$ocLazyLoadProvider', function($ocLazyLoadProvider) {
      // We configure ocLazyLoad to use the lib script.js as the async loader
      $ocLazyLoadProvider.config({
          debug:  false,
          events: true,
          modules: [
              /*
              {
                  name: 'ngGrid',
                  files: [
                      '/business/vendor/modules/ng-grid/ng-grid.min.js',
                      '/business/vendor/modules/ng-grid/ng-grid.min.css',
                      '/business/vendor/modules/ng-grid/theme.css'
                  ]
              },
              {
                  name: 'ui.select',
                  files: [
                      '/business/vendor/modules/angular-ui-select/select.min.js',
                      '/business/vendor/modules/angular-ui-select/select.min.css'
                  ]
              },
              */
              {
                  name:'angularFileUpload',
                  files: [
                    '/business/vendor/modules/angular-file-upload/angular-file-upload.min.js'
                  ]
              },
              /*
              {
                  name:'ui.calendar',
                  files: ['/business/vendor/modules/angular-ui-calendar/calendar.js']
              },
              {
                  name: 'ngImgCrop',
                  files: [
                      '/business/vendor/modules/ngImgCrop/ng-img-crop.js',
                      '/business/vendor/modules/ngImgCrop/ng-img-crop.css'
                  ]
              },
              */
              {
                  name: 'angularBootstrapNavTree',
                  files: [
                      '/business/vendor/modules/angular-bootstrap-nav-tree/abn_tree_directive.js',
                      '/business/vendor/modules/angular-bootstrap-nav-tree/abn_tree.css'
                  ]
              },
              /*
              {
                  name: 'toaster',
                  files: [
                      '/business/vendor/modules/angularjs-toaster/toaster.js',
                      '/business/vendor/modules/angularjs-toaster/toaster.css'
                  ]
              },
              {
                  name: 'textAngular',
                  files: [
                      '/business/vendor/modules/textAngular/textAngular-sanitize.min.js',
                      '/business/vendor/modules/textAngular/textAngular.min.js'
                  ]
              },
              {
                  name: 'vr.directives.slider',
                  files: [
                      '/business/vendor/modules/angular-slider/angular-slider.min.js',
                      '/business/vendor/modules/angular-slider/angular-slider.css'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular',
                  files: [
                      '/business/vendor/modules/videogular/videogular.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.controls',
                  files: [
                      '/business/vendor/modules/videogular/plugins/controls.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.buffering',
                  files: [
                      '/business/vendor/modules/videogular/plugins/buffering.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.overlayplay',
                  files: [
                      '/business/vendor/modules/videogular/plugins/overlay-play.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.poster',
                  files: [
                      '/business/vendor/modules/videogular/plugins/poster.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.imaads',
                  files: [
                      '/business/vendor/modules/videogular/plugins/ima-ads.min.js'
                  ]
              }
              */
          ]
      });
  }])
;