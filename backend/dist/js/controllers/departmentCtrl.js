angular.module("app").controller("DepartmentCtrl",["$rootScope","$scope","CommonFunction","DataLoad","toaster","divpage","$modal","$log","$interval","$timeout","$document",function(e,t,a,i,r,o,l,n,s,c,d){t.itemData={name:"",is_lock:"",isNew:!0},t.editing=!1,t.creating=!1,t.haveData=!0,t.loading=!0,t.query="",t.page=1,t.totalPage=1,t.perPage=10,t.dataList=[],t.originalData="",t.submit=!1,t.selectAll=!1,t.all_disagree_disabled=!0,t.all_disagree_html="批量删除",t.moveable=!1,t.isFirst=function(e){return 1==e},t.isDisable=function(e){return 1==e&&1==t.totalPage?!0:!(e<t.totalPage)},t.getInfoPage=function(e){e<=t.totalPage&&e>0&&(t.page=e,t.getAllData())},t.divpageByPage=function(e){var a=$(e.currentTarget).html(),i=Number(a);angular.isNumber(i)&&i>0&&t.getInfoPage(i)},t.searchData=function(){t.page=1,t.getAllData()},t.getAllData=function(){t.loading=!0;var e=i.departmentPageData({searchkey:t.query,offset:(t.page-1)*t.perPage,num:t.perPage});return e.then(function(e){if(e&&0==e.code)if(0!=e.data.data.length){t.haveData=!0,t.originalData=e.data,t.dataList=e.data.data,angular.forEach(t.dataList,function(e,t){e.agreehtml="修改",e.disagreehtml="删除",e.disabled=!1,e.selected=!1}),t.totalPage=Math.ceil(e.data.count/t.perPage);var a=$("#divpage-page");o.getready(a,t.totalPage,t.page)}else t.dataList=[],t.originalData="",r.pop("error"," 结算报表","没有数据！"),t.haveData=!1;else t.dataList=[],t.originalData="",t.haveData=!1,r.pop("error"," 结算报表",e.message);t.loading=!1},function(){t.dataList=[],t.originalData="",t.haveData=!1,t.loading=!1,r.pop("error"," 结算报表"," 获取产品出错！")})},t.getAllData(),t.createNew=function(){t.itemData={name:"",is_lock:"",isNew:!0},t.creating=!0},t.cancel=function(){t.editing=!1,t.creating=!1},t.updateItem=function(e){t.editing=!0,t.itemData=angular.copy(e),t.itemData.is_lock=1==t.itemData.is_lock,t.itemData.isNew=!1},t.active=!1,t.submitEditItem=function(){if(""==t.itemData.name)return r.pop("error","部门管理","部门名称不能为空！"),!1;if(""==t.itemData.sort_id)return r.pop("error","部门管理","排序号不能为空！"),!1;if(0==t.active){t.active=!0;var e=i.editDepartment({id:t.itemData.isNew?"":t.itemData.id,name:t.itemData.name,is_lock:t.itemData.is_lock?1:0});return e.then(function(e){e&&0==e.code?(r.pop("success","部门管理","修改成功！"),1==t.itemData.isNew&&(t.itemData.id=e.data,t.itemData.isNew=!1),t.itemData={name:"",sort_id:"",is_lock:!1,isNew:!1},t.editing=!1,t.creating=!1,t.haveData=!0,t.loading=!0,t.getAllData()):r.pop("error","部门管理","部门管理出错！"),t.active=!1},function(){t.active=!1,r.pop("error","部门管理","未知错误，请刷新重试！")})}},t.deleteItem=function(e){1==confirm("确定要删除吗?")&&0==t.submit&&(t.submit=!0,e.disagreehtml="提交中…",e.disabled=!0,t.deleteItemById(e))},t.deleteItemById=function(e){var a=i.deleteDepartmentById({id:e.id});return a.then(function(a){t.submit=!1,e.agreehtml="修改",e.disagreehtml="删除",e.disabled=!1,a&&0==a.code?(r.pop("success","部门管理",a.message),location.reload()):r.pop("error","部门管理",a.message)},function(){t.submit=!1,e.agreehtml="修改",e.disagreehtml="删除",e.disabled=!1,r.pop("error","部门管理"," 操作出错！")})},t.deleteBatch=function(){1==confirm("确定要删除吗?")&&0==t.submit&&(t.submit=!0,t.all_disagree_disabled=!0,t.all_disagree_html="删除中…",t.deleteDepartmentBatch())},t.idlist=[],t.deleteDepartmentBatch=function(){t.idlist=[],angular.forEach(t.dataList,function(e,a){1==e.selected&&t.idlist.push(e.id)});var e=i.deleteDepartmentBatch({idlist:JSON.stringify(t.idlist)});return e.then(function(e){t.all_disagree_disabled=!1,t.all_disagree_html="删除",e&&0==e.code?(r.pop("success","部门管理",e.message),location.reload()):(t.submit=!1,r.pop("error","部门管理",e.message))},function(){t.submit=!1,t.all_disagree_disabled=!1,t.all_disagree_html="删除",r.pop("error","部门管理"," 操作出错！")})},t.batchLock=function(){1==confirm("确定要批量锁定吗?")&&0==t.submit&&(t.submit=!0,t.all_disagree_disabled=!0,t.lockDepartmentBatch())},t.lockDepartmentBatch=function(){t.idlist=[],angular.forEach(t.dataList,function(e,a){1==e.selected&&t.idlist.push(e.id)});var e=i.lockDepartmentBatch({idlist:JSON.stringify(t.idlist)});return e.then(function(e){t.all_disagree_disabled=!1,e&&0==e.code?(r.pop("success","部门管理",e.message),location.reload()):(t.submit=!1,r.pop("error","部门管理",e.message))},function(){t.submit=!1,t.all_disagree_disabled=!1,r.pop("error","部门管理","操作出错！")})},t.up=function(){if(t.selectSortItem="",t.selectSortIndex="",angular.forEach(t.dataList,function(e,a){e.selected&&(t.selectSortItem=angular.copy(e),t.selectSortIndex=angular.copy(a))}),""!=t.selectSortItem&&""!==t.selectSortIndex)if(t.offset=(t.page-1)*t.perPage+t.selectSortIndex-1,t.offset<0)r.pop("error","部门管理","没有上一条了");else{var e="up";t.changeSortEachother(t.selectSortItem.id,t.selectSortItem.sort_id,t.offset,e)}else r.pop("error","部门管理","操作出错！")},t.down=function(){if(t.selectSortItem="",t.selectSortIndex="",angular.forEach(t.dataList,function(e,a){e.selected&&(t.selectSortItem=angular.copy(e),t.selectSortIndex=angular.copy(a))}),""!=t.selectSortItem&&""!==t.selectSortIndex){var e="down";t.offset=(t.page-1)*t.perPage+t.selectSortIndex+1,t.changeSortEachother(t.selectSortItem.id,t.selectSortItem.sort_id,t.offset,e)}else r.pop("error","部门管理","操作出错！")},t.changeSortEachother=function(e,a,o,l){var n=i.changeSortDep({id:e,sort_id:a,offset:o,type:l});return n.then(function(e){e&&0==e.code?t.getAllData():r.pop("error","部门管理",e.message)},function(){r.pop("error","部门管理","操作出错！")})},t.$watch("dataList",function(e,a){e!=a&&(t.num=0,t.all=0,angular.forEach(t.dataList,function(e,a){e.selected&&t.num++,t.all++}),t.num>0?(t.all_disagree_disabled=!1,1==t.num?t.moveable=!0:t.moveable=!1):(t.moveable=!1,t.all_disagree_disabled=!0),t.num==t.all?t.selectAll=!0:t.selectAll=!1)},!0),t.$watch("selectAll",function(e,a){e!=a&&(e?angular.forEach(t.dataList,function(e,t){e.selected=!0}):(t.count=0,t.all=0,angular.forEach(t.dataList,function(e,a){e.selected&&t.count++,t.all++}),t.count==t.all&&angular.forEach(t.dataList,function(e,t){e.selected=!1})))})}]);