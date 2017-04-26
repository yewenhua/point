/**
* @descript: gulp
* @author: yewenhua@jiedian.com
* @date: 2015-12-12
**/

//require需要的module
var gulp = require('gulp'),
    minifycss = require('gulp-minify-css'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    minifyHtml = require("gulp-minify-html"),
    del = require('del');

//templates
gulp.task('templatesmin', function() {
	return gulp.src('./backend/templates/**/*.html')
	    .pipe(minifyHtml())
	    .pipe(gulp.dest('./backend/dist/templates'));
});

//tpl
gulp.task('tplmin', function() {
	return gulp.src('./backend/tpl/**/*.html')
	    .pipe(minifyHtml())
	    .pipe(gulp.dest('./backend/dist/tpl'));
});

//压缩合并库css
gulp.task('concatcss', [], function() {

    return gulp.src([
             './backend/css/bootstrap.css',
             './backend/css/animate.css',
             './backend/css/font-awesome.min.css',
             './backend/css/simple-line-icons.css',
             './backend/css/font.css',
             './backend/vendor/modules/angularjs-toaster/toaster.css',
             './backend/css/app.css',
             './backend/js/date-single-picker/single-date.css',
             './backend/js/date-range-picker/daterangepicker-bs3.css',
             './backend/js/date-single-picker/bootstrap-datetimepicker.min.css'
        ])
        
        .pipe(concat('all.css'))
        .pipe(minifycss())    //压缩
        .pipe(rename('all.min.css')) //会将all.js重命名为all.min.js
        .pipe(gulp.dest('./backend/dist/css'));  //输出
});

//压缩合并editor js
gulp.task('concateditorjs', [], function() {

    return gulp.src([
             './ueditor/ueditor.config.js',
             './ueditor/ueditor.all.min.js',
             './ueditor/angular-ueditor.js',
             './ueditor/lang/zh-cn/zh-cn.js',
        ])
        
        .pipe(concat('editor.js'))
        .pipe(uglify())    //压缩
        .pipe(rename('editor.min.js')) //会将editor.js重命名为editor.min.js
        .pipe(gulp.dest('./ueditor'));  //输出
});

//压缩合并库 js
gulp.task('concatlibjs', [], function() {

    return gulp.src([
             './backend/vendor/jquery/jquery.min.js',
             './backend/vendor/angular/angular.js',
             './backend/vendor/Highcharts/highcharts.js',
             './backend/vendor/Highcharts/highcharts-ng.js',
             './backend/vendor/angular/angular-animate/angular-animate.js',
             //'./backend/vendor/angular/angular-aria/angular-aria.js',
             './backend/vendor/angular/angular-cookies/angular-cookies.js',
             //'./backend/vendor/angular/angular-resource/angular-resource.js',
             //'./backend/vendor/angular/angular-sanitize/angular-sanitize.js',
             './backend/vendor/angular/angular-touch/angular-touch.js',
             //'./backend/vendor/angular/angular-material/angular-material.js',
             './backend/vendor/angular/angular-ui-router/angular-ui-router.js',
             './backend/vendor/angular/ngstorage/ngStorage.js',
             './backend/vendor/angular/angular-bootstrap/ui-bootstrap-tpls.js',
             './backend/vendor/angular/oclazyload/ocLazyLoad.js',
             './backend/vendor/angular/angular-translate/angular-translate.js',
             './backend/vendor/angular/angular-translate/loader-static-files.js',
             './backend/vendor/angular/angular-translate/storage-cookie.js',
             './backend/vendor/angular/angular-translate/storage-local.js',
             './backend/vendor/modules/angularjs-toaster/toaster.js',
             './backend/vendor/angular/angular-animate/angular-animate.js',
             './backend/vendor/angular/angular-md5/angular-md5.js',
        ])
        
        .pipe(concat('angular.lib.js'))
        .pipe(uglify())    //压缩
        .pipe(rename('angular.lib.min.js')) //会将all.js重命名为all.min.js
        .pipe(gulp.dest('./backend/dist/js'));  //输出
});

//压缩合并single date js
gulp.task('concatsingledatejs', [], function() {

    return gulp.src([
             './backend/js/date-single-picker/jquery-ui.min.js',
             './backend/js/date-single-picker/moment.min.js',
             './backend/js/date-single-picker/jquery.ui.datepicker-zh-CN.js',
             './backend/js/date-single-picker/single-date.js',
             './backend/js/date-single-picker/bootstrap-datetimepicker.min.js',
             './backend/js/date-single-picker/datetimepicker.js',
        ])
        
        .pipe(concat('single.date.js'))
        .pipe(uglify())    //压缩
        .pipe(rename('single.date.min.js')) //会将all.js重命名为all.min.js
        .pipe(gulp.dest('./backend/dist/js'));  //输出
});

//压缩合并range date js
gulp.task('concatrangedatejs', [], function() {

    return gulp.src([
             './backend/js/date-range-picker/bootstrap.min.js',
             './backend/js/date-range-picker/daterangepicker.js',
             './backend/js/date-range-picker/ng-bs-daterangepicker.js',
        ])
        
        .pipe(concat('range.date.js'))
        .pipe(uglify())    //压缩
        .pipe(rename('range.date.min.js')) //会将all.js重命名为all.min.js
        .pipe(gulp.dest('./backend/dist/js'));  //输出
});

//压缩合并services js
gulp.task('concatservicejs', [], function() {

    return gulp.src([
             './backend/js/services/requestManager.js',
             './backend/js/services/data-load.js',
             './backend/js/services/divpage.js',
             './backend/js/services/commonFunction.js',
             './backend/js/services/interceptor.js',
             './backend/js/services/ui-load.js'
        ])
        
        .pipe(concat('service.js'))
        .pipe(uglify())    //压缩
        .pipe(rename('service.min.js')) //会将all.js重命名为all.min.js
        .pipe(gulp.dest('./backend/dist/js'));  //输出

});

//压缩合并directive js
gulp.task('concatdirectivejs', [], function() {
    return gulp.src([
             //'./backend/js/filters/fromNow.js',
             './backend/js/directives/setnganimate.js',
             './backend/js/directives/ui-butterbar.js',
             './backend/js/directives/ui-focus.js',
             './backend/js/directives/ui-fullscreen.js',
             './backend/js/directives/ui-jq.js',
             './backend/js/directives/ui-module.js',
             './backend/js/directives/ui-nav.js',
             './backend/js/directives/ui-scroll.js',
             './backend/js/directives/ui-shift.js',
             './backend/js/directives/ui-toggleclass.js',
             //'./backend/js/directives/ui-validate.js',
             './backend/js/controllers/bootstrap.js'
        ])
        
        .pipe(concat('directive.js'))
        .pipe(uglify())    //压缩
        .pipe(rename('directive.min.js')) //会将all.js重命名为all.min.js
        .pipe(gulp.dest('./backend/dist/js'));  //输出
});

//copyimg
gulp.task('copyimg',function(){
    return gulp.src('./backend/img/**/*')
        .pipe(gulp.dest('./backend/dist/img'));
})

//copy fonts
gulp.task('copyfonts',function(){
    return gulp.src('./backend/fonts/**/*')
        .pipe(gulp.dest('./backend/dist/fonts'));
})

//copy vendor
gulp.task('copyvendor',function(){
    return gulp.src('./backend/vendor/**/*')
        .pipe(gulp.dest('./backend/dist/vendor'));
})

//压缩ctrl js
gulp.task('minifyctrl', function() {
    return gulp.src([
                      './backend/js/controllers/**/*.js',   //**能用来匹配所有的目录和文件
                      ])

        .pipe(uglify())    //压缩
        .pipe(gulp.dest('./backend/dist/js/controllers'))    //输出到文件夹
});

//压缩合并app js
gulp.task('concatappjs', [], function() {

    return gulp.src([
             './backend/js/app.js',
             './backend/js/config.js',
             './backend/js/config.lazyload.js',
             './backend/js/config.router.js',
             './backend/js/main.js'
        ])
        
        .pipe(concat('app.js'))
        .pipe(uglify())    //压缩
        .pipe(rename('app.min.js')) //会将all.js重命名为all.min.js
        .pipe(gulp.dest('./backend/dist/js'));  //输出
});

//执行压缩前，先删除文件夹里的内容     //cb为任务函数提供的回调，用来通知任务已经完成
gulp.task('clean', function(cb) {
    del(['./backend/dist'], cb());
});

//压缩合并前台微信css
gulp.task('concatwechatcss', [], function() {

    return gulp.src([
             './media/vendor/weui/weui.min.css',
             './media/css/mall.css',
             './media/css/loading.css'
        ])
        
        .pipe(concat('wechat.css'))
        .pipe(minifycss())    //压缩
        .pipe(rename('wechat.min.css')) //会将all.js重命名为all.min.js
        .pipe(gulp.dest('./media/dist/css'));  //输出
});

//压缩合并前台微信 js
gulp.task('concatwechatjs', [], function() {

    return gulp.src([
             './media/vendor/jquery/1.9.1/jquery.min.js',
             './media/vendor/layer/2.4/layer.js'
        ])
        
        .pipe(concat('wechat.js'))
        .pipe(uglify())    //压缩
        .pipe(rename('wechat.min.js'))
        .pipe(gulp.dest('./media/dist/js'));  //输出
});

//copy wechat-skin
gulp.task('copywechatskin',function(){
    return gulp.src('./media/vendor/layer/2.4/skin/**/*')
        .pipe(gulp.dest('./media/dist/js/skin'));
})

//默认命令，在cmd中输入gulp后，执行的就是这个命令，中括号里的为依赖的，先执行的task
gulp.task('default', ['clean'], function() {
    gulp.start('templatesmin', 'tplmin', 'concatcss', 'concateditorjs', 'concatlibjs', 'copyimg', 'copyfonts', 'copyvendor', 'concatsingledatejs', 'concatrangedatejs', 'concatservicejs', 'concatdirectivejs', 'minifyctrl', 'concatappjs', 'concatwechatcss', 'concatwechatjs', 'copywechatskin');
});