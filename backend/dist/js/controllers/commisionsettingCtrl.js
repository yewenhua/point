angular.module("app").controller("CommisionsettingCtrl",["$scope","$rootScope","CommonFunction","DataLoad","toaster",function(e,d,i,s,n){e.schedule={},e.schedule.junior_first="",e.schedule.junior_second="",e.schedule.junior_third="",e.schedule.middle_first="",e.schedule.middle_second="",e.schedule.middle_third="",e.schedule.advanced_first="",e.schedule.advanced_second="",e.schedule.advanced_third="",e.schedule.isNew=!0,e.loading=!0,e.submiting=!1,e.submit_html="提交",e.commisionSetting=function(){if(""==e.schedule.junior_first)return n.pop("error","佣金设置","初级第一层分配比例不能为空！"),!1;if(""==e.schedule.middle_first)return n.pop("error","佣金设置","中级第一层分配比例不能为空！"),!1;if(""==e.schedule.advanced_first)return n.pop("error","佣金设置","高级第一层分配比例不能为空！"),!1;e.submiting=!0,e.submit_html="提交中…";var d=s.commisionSetting({id:e.schedule.isNew?"":e.schedule.id,junior_first:e.schedule.junior_first,junior_second:e.schedule.junior_second,junior_third:e.schedule.junior_third,middle_first:e.schedule.middle_first,middle_second:e.schedule.middle_second,middle_third:e.schedule.middle_third,advanced_first:e.schedule.advanced_first,advanced_second:e.schedule.advanced_second,advanced_third:e.schedule.advanced_third});return d.then(function(d){d&&0==d.code?(1==e.schedule.isNew&&(e.schedule.id=d.data,e.schedule.isNew=!1),n.pop("success","佣金设置","保存成功！")):n.pop("error","佣金设置",d.message),e.submiting=!1,e.submit_html="提交"},function(){e.submiting=!1,e.submit_html="提交",n.pop("error","佣金设置","保存佣金设置出错！")})},e.getCommisionSetting=function(){e.loading=!0;var d=s.getCommisionSetting({});return d.then(function(d){d&&0==d.code?(e.schedule=d.data,e.schedule.isNew=!1):e.schedule.isNew=!0,e.loading=!1},function(){e.schedule.isNew=!0,e.loading=!1,n.pop("error","佣金设置","获取佣金设置出错！")})},e.getCommisionSetting()}]);