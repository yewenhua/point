angular.module("app").controller("BannerCtrl",["$scope","CommonFunction","DataLoad","toaster","FileUploader",function(e,n,r,a,t){e.bannerList=[],e.editing=!1,e.banner={},e.banner.name="",e.banner.url="",e.banner.order_id="",e.banner.link="",e.banner.isNew=!1,e.haveData=!1,e.loading=!0;var i=e.uploader=new t({url:"/backend/upload_soft.php"});i.filters.push({name:"customFilter",fn:function(e,n){return this.queue.length<10}}),i.onAfterAddingFile=function(n){e.uploader.queue.length>1&&e.uploader.queue.splice(0,1)},i.onCompleteItem=function(n,r,t,i){0==r.code?($(".bootstrap-filestyle").children("input").val(""),n.file.url="/backend/uploads/"+r.file,e.editBanner()):(e.submitActive=!1,a.pop("error","广告管理",r.answer))},e.add=function(){e.editing=!0,e.banner={},e.banner.name="",e.banner.url="",e.banner.order_id="",e.banner.link="",e.banner.isNew=!0,i.clearQueue()},e.update=function(n){e.editing=!0,i.clearQueue(),e.banner=angular.copy(n),e.banner.isNew=!1},e.deleteBanner=function(n){if(1==confirm("确定要删除吗?")){var t=r.deleteBanner({id:n.id});return t.then(function(n){n&&0==n.code?(a.pop("success","广告管理",n.message),e.getAllData()):a.pop("error","广告管理",n.message)},function(){a.pop("error","广告管理"," 删除图片出错！")})}},e.cancle=function(){e.editing=!1},e.submitActive=!1,e.submitUpload=function(){if(0==e.submitActive){if(""==e.banner.name)return a.pop("error","广告管理","轮播名称不能为空！"),!1;if(""==e.banner.link)return a.pop("error","广告管理","链接不能为空！"),!1;e.submitActive=!0,e.uploader.queue.length<=0&&!e.banner.isNew?e.editBanner():i.uploadAll()}},e.editBanner=function(){if(e.banner.isNew){if(e.uploader.queue.length<=0)return e.submitActive=!1,a.pop("error","广告管理","图片不能为空！"),!1;angular.forEach(e.uploader.queue,function(n){e.banner.url=n.file.url})}else e.uploader.queue.length<=0?e.banner.url="":angular.forEach(e.uploader.queue,function(n){e.banner.url=n.file.url});var n=r.editBanner({id:e.banner.isNew?"":e.banner.id,name:e.banner.name,url:e.banner.url,order_id:e.banner.order_id,link:e.banner.link});return n.then(function(n){e.submitActive=!1,n&&0==n.code?(a.pop("success","广告管理","保存成功！"),e.editing=!1,e.getAllData()):a.pop("error","广告管理",n.message)},function(){e.submitActive=!1,a.pop("error","广告管理"," 保存广告出错！")})},e.getAllData=function(){e.loading=!0;var n=r.bannerAllData({});return n.then(function(n){n&&0==n.code?0!=n.data.length?(e.haveData=!0,e.bannerList=n.data,angular.forEach(e.bannerList,function(e){e.selected=!1})):(e.haveData=!1,e.bannerList=[]):(e.haveData=!1,a.pop("error","广告管理","没有数据")),e.loading=!1},function(){e.haveData=!1,e.loading=!1,a.pop("error","广告管理","获取出错！")})},e.getAllData()}]);