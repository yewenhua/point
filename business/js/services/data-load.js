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
				return postData('/index.php/merchant/signin', data, true);
			},
			logout: function (data) {
				return postData('/index.php/merchant/logout', data, true);
			},
			chgpwd: function (data) {
				return postData('/index.php/merchant/chgpwd', data, true);
			},
			
			//system
			getSystemData: function (data) {
				return postData('/index.php/system/getSystemData', data, false);
			},
			editSystem: function (data) {
				return postData('/index.php/system/editSystem', data, true);
			},

			//getNewMessage
			getNewMessage: function (data) {
				return postData('/index.php/merchant/getNewMessage', data, false);
			},
			
			//welcome
			allOrderStatusData: function (data) {
				return postData('/index.php/merchant/allOrderStatusData', data, true);
			},
			welcomeCashData: function (data) {
				return postData('/index.php/merchant/welcomeCashData', data, true);
			},
			allCashData: function (data) {
				return postData('/index.php/merchant/allCashData', data, true);
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
		
			
			//order
			getOrderPageData: function (data) {
				return postData('/index.php/merchant/selectPageData', data, true);
			},
			changeOrderStatus: function (data) {
				return postData('/index.php/merchant/changeStatus', data, true);
			},
			submitLogisticsInfo: function (data) {
				return postData('/index.php/merchant/submitLogisticsInfo', data, true);
			},
			modifyLogisticsInfo: function (data) {
				return postData('/index.php/orders/modifyLogisticsInfo', data, true);
			},
		};
	}
]);