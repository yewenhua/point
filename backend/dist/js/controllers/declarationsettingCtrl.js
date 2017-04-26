angular.module("app").controller("DeclarationsettingCtrl",["$scope","CommonFunction","DataLoad","toaster","divpage","$modal",function(e,r,o,i,t,n){e.config={},e.config.least_money="",e.config.most_money="",e.config.use_consume_most_rate="",e.config.wait_rate="",e.config.first_level_rate="",e.config.second_level_rate="",e.config.third_level_rate="",e.config.give_exchange_rate="",e.config.give_consume_rate="",e.config.give_share_rate="",e.config.wait_period="",e.config.first_level_must=0,e.config.second_level_must=2,e.config.third_level_must=2,e.config.charge_repeat_rate="",e.config.charge_repeat_period="",e.config.merchant_repeat_rate="",e.config.merchant_repeat_period="",e.submit=!1,e.loading=!0,e.submit_html="提交",e.getConfigDeclaration=function(){var r=o.getConfigDeclaration({});return r.then(function(r){r&&0==r.code&&(e.config=r.data),e.loading=!1},function(){e.loading=!1,i.pop("error","报单设置","获取报单设置出错")})},e.getConfigDeclaration(),e.submitConfig=function(){if(""==e.config.least_money)return i.pop("error","报单设置","报单最低金额不能为空！"),!1;if(""==e.config.most_money)return i.pop("error","报单设置","报单最高金额不能为空！"),!1;if(""==e.config.use_consume_most_rate)return i.pop("error","报单设置","报单兑冲消费积分最高比例不能为空！"),!1;if(""==e.config.wait_rate)return i.pop("error","报单设置","待用积分倍率不能为空！"),!1;if(""==e.config.first_level_rate)return i.pop("error","报单设置","第一层分享比例不能为空！"),!1;if(""==e.config.second_level_rate)return i.pop("error","报单设置","第二层分享比例不能为空！"),!1;if(""==e.config.third_level_rate)return i.pop("error","报单设置","第三层分享比例不能为空！"),!1;if(""==e.config.give_exchange_rate)return i.pop("error","报单设置","赠送兑换积分比例不能为空！"),!1;if(""==e.config.give_consume_rate)return i.pop("error","报单设置","赠送消费积分比例不能为空！"),!1;if(""==e.config.give_share_rate)return i.pop("error","报单设置","赠送分享积分比例不能为空！"),!1;if(""==e.config.wait_period)return i.pop("error","报单设置","待用积分周期不能为空！"),!1;if(""==e.config.charge_repeat_rate)return i.pop("error","报单设置","充值复投倍率不能为空！"),!1;if(""==e.config.charge_repeat_period)return i.pop("error","报单设置","充值复投周期不能为空！"),!1;if(""==e.config.merchant_repeat_rate)return i.pop("error","报单设置","商家复投倍率不能为空！"),!1;if(""==e.config.merchant_repeat_period)return i.pop("error","报单设置","商家复投周期不能为空！"),!1;if(0==e.submit){e.submit=!0,e.submit_html="提交中…";var r=o.configDeclaration({config:e.config});return r.then(function(r){r&&0==r.code?i.pop("success","报单设置","设置成功"):i.pop("error","报单设置",r.message),e.submit=!1,e.submit_html="提交"},function(){e.submit=!1,e.submit_html="提交",i.pop("error","报单设置","设置出错")})}}}]);