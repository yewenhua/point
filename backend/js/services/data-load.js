'use strict';

angular.module('DataLoad', []).factory('DataLoad', ['$http', '$q', 'RequestManager',
	function ($http, $q, RequestManager) {
		function httpData(method, url, data, canBeKilled) {
			var deferred = $q.defer();
			var request = $http({
				method: method,
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8'
				},
				data: $.param(data),
				url: url,
				timeout: deferred.promise
			}).success(function (data, status, headers, config) {
				deferred.resolve(data);
			}).error(function (data, status, headers, config) {
				deferred.reject(data);
			});
			var promise = request.then(function (response) {
				if (response) {
					return response.data;
				} else {
					return null;
				}
			});
			promise.canceller = deferred;
			/*promise.finally(function() {
			 RequestManager.remove(url);
			 });*/
			if (canBeKilled == true) {
				RequestManager.add({
					promise: promise,
					url: url
				});
			}
			return promise;
		}

		function getData(url, canBeKilled) {
			return httpData('GET', url, null, canBeKilled);
		}

		function postData(url, data, canBeKilled) {
			return httpData('POST', url, data, canBeKilled);
		}

		return {

			// 登录登出
			signin: function (data) {
				return postData('/index.php/admin/signin', data, true);
			},
			logout: function (data) {
				return postData('/index.php/admin/logout', data, true);
			},
			chgpwd: function (data) {
				return postData('/index.php/admin/chgpwd', data, true);
			},
			chgPersonal: function (data) {
				return postData('/index.php/admin/chgPersonal', data, true);
			},
			isExist: function (data) {
				return postData('/index.php/admin/isExist', data, true);
			},
			
			//system
			getSystemData: function (data) {
				return postData('/index.php/system/getSystemData', data, false);
			},
			editSystem: function (data) {
				return postData('/index.php/system/editSystem', data, true);
			},
			
			//privilege
			editPrivilege: function (data) {
				return postData('/index.php/privilege/editPrivilege', data, true);
			},
			getPrivilegePageData: function (data) {
				return postData('/index.php/privilege/selectPageData', data, true);
			},
			getAllPrivilegeData: function (data) {
				return postData('/index.php/privilege/selectAllData', data, true);
			},
			deletePrivilege: function (data) {
				return postData('/index.php/privilege/deletePrivilege', data, true);
			},
			getRolePageData: function (data) {
				return postData('/index.php/privilege/selectRolePageData', data, true);
			},
			editRole: function (data) {
				return postData('/index.php/privilege/editRole', data, true);
			},
			deleteRole: function (data) {
				return postData('/index.php/privilege/deleteRole', data, true);
			},
			getAllRoleData: function (data) {
				return postData('/index.php/privilege/getAllRoleData', data, true);
			},
			getAdminPageData: function (data) {
				return postData('/index.php/admin/selectAdminPageData', data, true);
			},
			editAdmin: function (data) {
				return postData('/index.php/admin/editAdmin', data, true);
			},
			deleteAdmin: function (data) {
				return postData('/index.php/admin/deleteAdmin', data, true);
			},
			getRoleByKey: function (data) {
				return postData('/index.php/privilege/getRoleByKey', data, true);
			},
			
			//log
			logPageData: function (data) {
				return postData('/index.php/log/logPageData', data, true);
			},
			
			
			//部门列表
			departmentAllData: function (data) {
				return postData('/index.php/department/selectAllData', data, true);
			},
			departmentPageData: function (data) {
				return postData('/index.php/department/selectPageData', data, true);
			},
			editDepartment: function (data) {
				return postData('/index.php/department/editDepartment', data, true);
			},
			deleteDepartmentById: function (data) {
				return postData('/index.php/department/deleteRow', data, true);
			},
			deleteDepartmentBatch: function (data) {
				return postData('/index.php/department/deleteBatch', data, true);
			},
			lockDepartmentBatch: function (data) {
				return postData('/index.php/department/lockBatch', data, true);
			},
			changeSortDep: function (data) {
				return postData('/index.php/department/changeSortEachother', data, true);
			},
			
			//getNewMessage
			getNewMessage: function (data) {
				return postData('/index.php/admin/getNewMessage', data, false);
			},
			
			//welcome
			allOrderStatusData: function (data) {
				return postData('/index.php/admin/allOrderStatusData', data, true);
			},
			welcomeCashData: function (data) {
				return postData('/index.php/admin/welcomeCashData', data, true);
			},
			allCashData: function (data) {
				return postData('/index.php/admin/allCashData', data, true);
			},
			
			//tree
			treeAllData: function (data) {
				return postData('/index.php/tree/select_array_tree', data, true);
			},
			editTree: function (data) {
				return postData('/index.php/tree/edit_tree', data, true);
			},
			deleteTree: function (data) {
				return postData('/index.php/tree/delete_tree', data, true);
			},
			treeCategory: function (data) {
				return postData('/index.php/tree/select_tree_category', data, true);
			},
			getSearchPageData: function (data) {
				return postData('/index.php/upload/searchPageData', data, true);
			},
			
			//download
			getMyDownloadPageData: function (data) {
				return postData('/index.php/download/selectPageData', data, true);
			},

			//goods
			deleteImg: function (data) {
				return postData('/index.php/goods/deleteImg', data, true);
			},
			saveGoods: function (data) {
				return postData('/index.php/goods/save', data, true);
			},
			getGoodsPageData: function (data) {
				return postData('/index.php/goods/selectPageData', data, true);
			},
			deleteGoods: function (data) {
				return postData('/index.php/goods/deleteGoods', data, true);
			},
			switch_status: function (data) {
				return postData('/index.php/goods/switch_status', data, true);
			},
			recommend_status: function (data) {
				return postData('/index.php/goods/recommend_status', data, true);
			},
			deleteGoodsBatch: function (data) {
				return postData('/index.php/goods/deleteBatch', data, true);
			},
			changeSortGoods: function (data) {
				return postData('/index.php/goods/changeSortEachother', data, true);
			},
			changeSortTop: function (data) {
				return postData('/index.php/goods/changeSortTop', data, true);
			},
			
			//setting
			getScheduleData: function (data) {
				return postData('/index.php/setting/getScheduleData', data, false);
			},
			editSchedule: function (data) {
				return postData('/index.php/setting/editSchedule', data, true);
			},
			
			//member
			getMemberPageData: function (data) {
				return postData('/index.php/member/selectPageData', data, true);
			},
			deleteUserBatch: function (data) {
				return postData('/index.php/member/deleteBatch', data, true);
			},
			getStudentPageData: function (data) {
				return postData('/index.php/member/student_data_by_page_of_admin', data, true);
			},
			changeRecommend: function (data) {
				return postData('/index.php/member/changeRecommend', data, true);
			},
			upgradeLevel: function (data) {
				return postData('/index.php/member/upgradeLevel', data, true);
			},
			quickDeclaration: function (data) {
				return postData('/index.php/admin/quickDeclaration', data, true);
			},
			batchMemberImport: function (data) {
				return postData('/index.php/admin/batchMemberImport', data, true);
			},
			manager_status: function (data) {
				return postData('/index.php/admin/manager_status', data, true);
			},
			serviceDeclaration: function (data) {
				return postData('/index.php/admin/serviceDeclaration', data, true);
			},
			company_status: function (data) {
				return postData('/index.php/admin/company_status', data, true);
			},
			quickBusiness: function (data) {
				return postData('/index.php/admin/quickBusiness', data, true);
			},
			message_status: function (data) {
				return postData('/index.php/admin/message_status', data, true);
			},
			
			//order
			getOrderPageData: function (data) {
				return postData('/index.php/orders/selectPageData', data, true);
			},
			deleteOrderBatch: function (data) {
				return postData('/index.php/orders/deleteBatch', data, true);
			},
			changeOrderStatus: function (data) {
				return postData('/index.php/orders/changeStatus', data, true);
			},
			submitLogisticsInfo: function (data) {
				return postData('/index.php/orders/submitLogisticsInfo', data, true);
			},
			modifyLogisticsInfo: function (data) {
				return postData('/index.php/orders/modifyLogisticsInfo', data, true);
			},
			submitRefundOrder: function (data) {
				return postData('/index.php/orders/submitRefundOrder', data, true);
			},
			checkRefundOrder: function (data) {
				return postData('/index.php/orders/checkRefundOrder', data, true);
			},
			
			//declaration
			getDeclarationPageData: function (data) {
				return postData('/index.php/service/selectPageData', data, true);
			},
			deleteDeclarationBatch: function (data) {
				return postData('/index.php/service/deleteBatch', data, true);
			},
			
			//banner
			bannerAllData: function (data) {
				return postData('/index.php/admin/allBannerData', data, true);
			},
			editBanner: function (data) {
				return postData('/index.php/admin/editBanner', data, true);
			},
			deleteBanner: function (data) {
				return postData('/index.php/admin/deleteBanner', data, true);
			},
			
			//cash
			getCashPageData: function (data) {
				return postData('/index.php/admin/getCashPageData', data, true);
			},
			deleteCashBatch: function (data) {
				return postData('/index.php/admin/deleteCashBatch', data, true);
			},
			
			//commision
			getCommisionPageData: function (data) {
				return postData('/index.php/admin/getCommisionPageData', data, true);
			},
			deleteCommisionBatch: function (data) {
				return postData('/index.php/admin/deleteCommisionBatch', data, true);
			},
			
			//exchange
			getExchangePageData: function (data) {
				return postData('/index.php/admin/getExchangePageData', data, true);
			},
			deleteExchangeBatch: function (data) {
				return postData('/index.php/admin/deleteExchangeBatch', data, true);
			},
			
			//consume
			getConsumePageData: function (data) {
				return postData('/index.php/admin/getConsumePageData', data, true);
			},
			deleteConsumeBatch: function (data) {
				return postData('/index.php/admin/deleteConsumeBatch', data, true);
			},
			
			//useable
			getUseablePageData: function (data) {
				return postData('/index.php/admin/getUseablePageData', data, true);
			},
			deleteUseableBatch: function (data) {
				return postData('/index.php/admin/deleteUseableBatch', data, true);
			},
			
			//share
			getSharePageData: function (data) {
				return postData('/index.php/admin/getSharePageData', data, true);
			},
			deleteShareBatch: function (data) {
				return postData('/index.php/admin/deleteShareBatch', data, true);
			},
			
			//wait
			getWaitPageData: function (data) {
				return postData('/index.php/admin/getWaitPageData', data, true);
			},
			deleteWaitBatch: function (data) {
				return postData('/index.php/admin/deleteWaitBatch', data, true);
			},
			clearWaitPoint: function (data) {
				return postData('/index.php/admin/clearWaitPoint', data, true);
			},
			
			//upgrade
			getUpgradePageData: function (data) {
				return postData('/index.php/admin/getUpgradePageData', data, true);
			},
			deleteUpgradeBatch: function (data) {
				return postData('/index.php/admin/deleteUpgradeBatch', data, true);
			},
			
			//gragh
			getShareholderGraphData: function (data) {
				return postData('/index.php/admin/getShareholderGraphData', data, true);
			},
			
			//takecash
			getTakecashPageData: function (data) {
				return postData('/index.php/admin/getTakecashPageData', data, true);
			},
			deleteTakecashBatch: function (data) {
				return postData('/index.php/admin/deleteTakecashBatch', data, true);
			},
			checkTakecash: function (data) {
				return postData('/index.php/admin/checkTakecash', data, true);
			},
			
			//Wallet
			getWalletPageData: function (data) {
				return postData('/index.php/admin/getWalletPageData', data, true);
			},
			deleteWalletBatch: function (data) {
				return postData('/index.php/admin/deleteWalletBatch', data, true);
			},
			
			//Message
			getMessagePageData: function (data) {
				return postData('/index.php/admin/getMessagePageData', data, true);
			},
			deleteMessageBatch: function (data) {
				return postData('/index.php/admin/deleteMessageBatch', data, true);
			},
			
			//Logistic
			getLogisticPageData: function (data) {
				return postData('/index.php/admin/getLogisticPageData', data, true);
			},
			deleteLogisticBatch: function (data) {
				return postData('/index.php/admin/deleteLogisticBatch', data, true);
			},
			updateLogistic: function (data) {
				return postData('/index.php/admin/updateLogistic', data, true);
			},
			getLogisticList: function (data) {
				return postData('/index.php/admin/getLogisticList', data, true);
			},
			
			//mall
			selectCompanyData: function (data) {
				return postData('/index.php/mall/selectCompanyData', data, true);
			},
			
			//config
			getConfigDeclaration: function (data) {
				return postData('/index.php/admin/getConfigDeclaration', data, true);
			},
			configDeclaration: function (data) {
				return postData('/index.php/admin/configDeclaration', data, true);
			},
			posterData: function (data) {
				return postData('/index.php/admin/posterData', data, true);
			},
			editPoster: function (data) {
				return postData('/index.php/admin/editPoster', data, true);
			},
			
			//statistic
			getMerchantStatisticPageData: function (data) {
				return postData('/index.php/admin/getMerchantStatisticPageData', data, true);
			},
			
			//compensation
			insertCompensation: function (data) {
				return postData('/index.php/admin/insertCompensation', data, true);
			},
			getRepeatPageData: function (data) {
				return postData('/index.php/admin/getRepeatPageData', data, true);
			},
			deleteRepeatBatch: function (data) {
				return postData('/index.php/admin/deleteRepeatBatch', data, true);
			},
			
			//sms
			smsTemplate: function (data) {
				return postData('/index.php/admin/smsTemplate', data, true);
			},
			updateTemplate: function (data) {
				return postData('/index.php/admin/updateTemplate', data, true);
			},
			switchTemplateStatus: function (data) {
				return postData('/index.php/admin/switchTemplateStatus', data, true);
			},
			
			//commision setting
			getCommisionSetting: function (data) {
				return postData('/index.php/setting/getCommisionSetting', data, false);
			},
			commisionSetting: function (data) {
				return postData('/index.php/setting/commisionSetting', data, true);
			},
			
			//article
			getArticlePageData: function (data) {
				return postData('/index.php/admin/select_page_article', data, true);
			},
			editArticle: function (data) {
				return postData('/index.php/admin/edit_article', data, true);
			},
			deleteArticleBatch: function (data) {
				return postData('/index.php/admin/deleteArticleBatch', data, true);
			},
			changeArticleSortTop: function (data) {
				return postData('/index.php/article/changeSortTop', data, true);
			},
			changeSortArticle: function (data) {
				return postData('/index.php/article/changeSortEachother', data, true);
			},
			
			//order
			getMerchantOrderPageData: function (data) {
				return postData('/index.php/admin/getMerchantOrderPageData', data, true);
			},
			
			//sale
			getSaleRankData: function (data) {
				return postData('/index.php/admin/getSaleRankData', data, true);
			},
			
			//rank
			getMerchantGoodsPageData: function (data) {
				return postData('/index.php/admin/getMerchantGoodsPageData', data, true);
			},
			getGoodsStatisticPageData: function (data) {
				return postData('/index.php/admin/getGoodsStatisticPageData', data, true);
			},
			getConsumeStatisticPageData: function (data) {
				return postData('/index.php/admin/getConsumeStatisticPageData', data, true);
			},
			getDeclarationStatisticPageData: function (data) {
				return postData('/index.php/admin/getDeclarationStatisticPageData', data, true);
			},
			getMemberStatisticPageData: function (data) {
				return postData('/index.php/admin/getMemberStatisticPageData', data, true);
			},
			
			//sharemanager
			getSharemanagerPageData: function (data) {
				return postData('/index.php/admin/getSharemanagerPageData', data, true);
			},
			deleteSharemanagerBatch: function (data) {
				return postData('/index.php/admin/deleteSharemanagerBatch', data, true);
			},
			dealSharemanager: function (data) {
				return postData('/index.php/admin/dealSharemanager', data, true);
			},
		};
	}
]);