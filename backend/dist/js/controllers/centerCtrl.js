angular.module("app").controller("CenterCtrl",["$scope","$rootScope","CommonFunction","DataLoad","toaster",function(i,e,o,r,u){i.quick={},i.quick.service_mobile="",i.quick.vip_mobile="",i.quick.point="",i.quick.money="",i.submit=!1,i.submit_html="提交",i.quick=function(){if(""==i.quick.service_mobile)return u.pop("error","服务中心报单","服务中心号码不能为空！"),!1;if(""==i.quick.vip_mobile)return u.pop("error","服务中心报单","会员编号不能为空！"),!1;if(""==i.quick.money)return u.pop("error","服务中心报单","报单金额不能为空！"),!1;if(0==i.submit){i.submit=!0,i.submit_html="提交中…";var e=r.serviceDeclaration({service_mobile:i.quick.service_mobile,vip_mobile:i.quick.vip_mobile,money:i.quick.money,point:i.quick.point});return e.then(function(e){e&&0==e.code?(i.quick.service_mobile="",i.quick.vip_mobile="",i.quick.point="",i.quick.money="",u.pop("success","服务中心报单","报单成功！")):u.pop("error","服务中心报单",e.message),i.submit=!1,i.submit_html="提交"},function(){i.submit=!1,i.submit_html="提交",u.pop("error","服务中心报单","报单出错！")})}}}]);