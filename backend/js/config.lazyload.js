// lazyload config

angular.module('app')
    /**
   * jQuery plugin config use ui-jq directive , config the js and css files that required
   * key: function name of the jQuery plugin
   * value: array of the css js file located
   */
  .constant('JQ_CONFIG', {
      //easyPieChart:   ['/backend/vendor/jquery/charts/easypiechart/jquery.easy-pie-chart.js'],
      //sparkline:      ['/backend/vendor/jquery/charts/sparkline/jquery.sparkline.min.js'],
      //plot:           ['/backend/vendor/jquery/charts/flot/jquery.flot.min.js', 
      //                    '/backend/vendor/jquery/charts/flot/jquery.flot.resize.js',
      //                    '/backend/vendor/jquery/charts/flot/jquery.flot.tooltip.min.js',
      //                    '/backend/vendor/jquery/charts/flot/jquery.flot.spline.js',
      //                    '/backend/vendor/jquery/charts/flot/jquery.flot.orderBars.js',
      //                    '/backend/vendor/jquery/charts/flot/jquery.flot.pie.min.js'],
      slimScroll:     ['/backend/vendor/jquery/slimscroll/jquery.slimscroll.min.js'],
      sortable:       ['/backend/vendor/jquery/sortable/jquery.sortable.js'],
      //nestable:       ['/backend/vendor/jquery/nestable/jquery.nestable.js',
      //                    '/backend/vendor/jquery/nestable/nestable.css'],
      filestyle:      ['/backend/vendor/jquery/file/bootstrap-filestyle.min.js'],
      //slider:         ['/backend/vendor/jquery/slider/bootstrap-slider.js',
      //                  '/backend/vendor/jquery/slider/slider.css'],
      chosen:         ['/backend/vendor/jquery/chosen/chosen.jquery.min.js',
                          '/backend/vendor/jquery/chosen/chosen.css'],
      //TouchSpin:      ['/backend/vendor/jquery/spinner/jquery.bootstrap-touchspin.min.js',
      //                    '/backend/vendor/jquery/spinner/jquery.bootstrap-touchspin.css'],
      //wysiwyg:        ['/backend/vendor/jquery/wysiwyg/bootstrap-wysiwyg.js',
      //                    '/backend/vendor/jquery/wysiwyg/jquery.hotkeys.js'],
      //dataTable:      ['/backend/vendor/jquery/datatables/jquery.dataTables.min.js',
      //                    '/backend/vendor/jquery/datatables/dataTables.bootstrap.js',
      //                    '/backend/vendor/jquery/datatables/dataTables.bootstrap.css'],
      //vectorMap:      ['/backend/vendor/jquery/jvectormap/jquery-jvectormap.min.js', 
      //                    '/backend/vendor/jquery/jvectormap/jquery-jvectormap-world-mill-en.js',
      //                    '/backend/vendor/jquery/jvectormap/jquery-jvectormap-us-aea-en.js',
      //                    '/backend/vendor/jquery/jvectormap/jquery-jvectormap.css'],
      footable:       ['/backend/vendor/jquery/footable/footable.all.min.js',
                          '/backend/vendor/jquery/footable/footable.core.css']
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
                      '/backend/vendor/modules/ng-grid/ng-grid.min.js',
                      '/backend/vendor/modules/ng-grid/ng-grid.min.css',
                      '/backend/vendor/modules/ng-grid/theme.css'
                  ]
              },
              {
                  name: 'ui.select',
                  files: [
                      '/backend/vendor/modules/angular-ui-select/select.min.js',
                      '/backend/vendor/modules/angular-ui-select/select.min.css'
                  ]
              },
              */
              {
                  name:'angularFileUpload',
                  files: [
                    '/backend/vendor/modules/angular-file-upload/angular-file-upload.min.js'
                  ]
              },
              /*
              {
                  name:'ui.calendar',
                  files: ['/backend/vendor/modules/angular-ui-calendar/calendar.js']
              },
              {
                  name: 'ngImgCrop',
                  files: [
                      '/backend/vendor/modules/ngImgCrop/ng-img-crop.js',
                      '/backend/vendor/modules/ngImgCrop/ng-img-crop.css'
                  ]
              },
              */
              {
                  name: 'angularBootstrapNavTree',
                  files: [
                      '/backend/vendor/modules/angular-bootstrap-nav-tree/abn_tree_directive.js',
                      '/backend/vendor/modules/angular-bootstrap-nav-tree/abn_tree.css'
                  ]
              },
              /*
              {
                  name: 'toaster',
                  files: [
                      '/backend/vendor/modules/angularjs-toaster/toaster.js',
                      '/backend/vendor/modules/angularjs-toaster/toaster.css'
                  ]
              },
              {
                  name: 'textAngular',
                  files: [
                      '/backend/vendor/modules/textAngular/textAngular-sanitize.min.js',
                      '/backend/vendor/modules/textAngular/textAngular.min.js'
                  ]
              },
              {
                  name: 'vr.directives.slider',
                  files: [
                      '/backend/vendor/modules/angular-slider/angular-slider.min.js',
                      '/backend/vendor/modules/angular-slider/angular-slider.css'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular',
                  files: [
                      '/backend/vendor/modules/videogular/videogular.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.controls',
                  files: [
                      '/backend/vendor/modules/videogular/plugins/controls.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.buffering',
                  files: [
                      '/backend/vendor/modules/videogular/plugins/buffering.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.overlayplay',
                  files: [
                      '/backend/vendor/modules/videogular/plugins/overlay-play.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.poster',
                  files: [
                      '/backend/vendor/modules/videogular/plugins/poster.min.js'
                  ]
              },
              {
                  name: 'com.2fdevs.videogular.plugins.imaads',
                  files: [
                      '/backend/vendor/modules/videogular/plugins/ima-ads.min.js'
                  ]
              }
              */
          ]
      });
  }])
;