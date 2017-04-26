angular.module("app").controller("UpgradeCtrl",["$scope","CommonFunction","DataLoad","toaster","divpage","$modal","$stateParams",function(a,t,e,l,n,i,d){a.haveData=!0,a.loading=!0,a.query="",a.page=1,a.totalPage=1,a.perPage=10,a.dataList=[],a.init=!0,a.selectAll=!1,a.all_delete_disabled=!0,a.all_delete_html="删除",a.submit=!1,a.dateSelectSingle={start:moment(new Date).utc().startOf("day"),end:""},a.$watch("dateSelectSingle.start",function(t,e){t!=e&&(a.init=!1,a.page=1,a.getAllData())},!0),a.$watch("status",function(t,e){t!=e&&(a.page=1,a.getAllData())}),a.getAllData=function(){a.loading=!0;var t=e.getUpgradePageData({searchkey:a.query,time:a.init?"":a.dateSelectSingle.end.format("YYYY-MM-DD"),offset:(a.page-1)*a.perPage,num:a.perPage});return t.then(function(t){if(t&&0==t.code)if(0!=t.data.data.length){a.haveData=!0,a.dataList=t.data.data,a.totalPage=Math.ceil(t.data.count/a.perPage);var e=$("#divpage-page");n.getready(e,a.totalPage,a.page)}else a.dataList=[],l.pop("success","升级记录","没有数据！"),a.haveData=!1;else a.dataList=[],a.haveData=!1,l.pop("error","升级记录",t.message);a.loading=!1},function(){a.dataList=[],a.haveData=!1,a.loading=!1,l.pop("error","升级记录","获取数据出错！")})},a.getAllData(),a.isFirst=function(a){return 1==a},a.isDisable=function(t){return 1==t&&1==a.totalPage?!0:!(t<a.totalPage)},a.getInfoPage=function(t){t<=a.totalPage&&t>0&&(a.page=t,a.getAllData())},a.divpageByPage=function(t){var e=$(t.currentTarget).html(),l=Number(e);angular.isNumber(l)&&l>0&&a.getInfoPage(l)},a.searchData=function(){a.page=1,a.getAllData()},a.$watch("dataList",function(t,e){t!=e&&a.dataList.length>0&&(a.num=0,a.all=0,angular.forEach(a.dataList,function(t,e){t.selected&&a.num++,a.all++}),a.num>0?a.all_delete_disabled=!1:a.all_delete_disabled=!0,a.num==a.all?a.selectAll=!0:a.selectAll=!1)},!0),a.$watch("selectAll",function(t,e){t!=e&&a.dataList.length>0&&(t?angular.forEach(a.dataList,function(a,t){a.selected=!0}):(a.count=0,a.all=0,angular.forEach(a.dataList,function(t,e){t.selected&&a.count++,a.all++}),a.count==a.all&&angular.forEach(a.dataList,function(a,t){a.selected=!1})))}),a.deleteBatch=function(){if(1==confirm("确定要删除吗?")&&0==a.submit){a.submit=!0,a.idlist=[],angular.forEach(a.dataList,function(t,e){1==t.selected&&a.idlist.push(t.id)});var t=e.deleteUpgradeBatch({idlist:JSON.stringify(a.idlist)});return t.then(function(t){a.all_disagree_disabled=!1,a.all_delete_html="删除",t&&0==t.code?(l.pop("success","升级记录",t.message),location.reload()):(a.submit=!1,l.pop("error","升级记录",t.message))},function(){a.submit=!1,a.all_disagree_disabled=!1,a.all_delete_html="删除",l.pop("error","升级记录","操作出错！")})}}}]);