'use strict';

angular.module('DataLoad', []).factory('DataLoad', ['$http', '$q', 'RequestManager', 'AppConstants', '$rootScope', 'md5',
	function ($http, $q, RequestManager, AppConstants, $rootScope, md5) {

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

			//用户操作 注册登录忘记密码等
			login: function (data) {
				return postData('/index.php/user/signin', data, true);
			},
			logout: function (data) {
				return postData('/index.php/mobile/logout', data, true);
			},
			getProtocalData: function (data) {
				return postData('/index.php/mobile/getProtocalData', data, true);
			},
			sendSMS: function (data) {
				return postData('/index.php/user/sendSMS', data, true);
			},
			sendVoice: function (data) {
				return postData('/index.php/user/sendVoice', data, true);
			},
			isRegist: function (data) {
				return postData('/index.php/user/isRegist', data, true);
			},
			isServerExist: function (data) {
				return postData('/index.php/user/isServerExist', data, true);
			},
			submitRegistInfo: function (data) {
				return postData('/index.php/mobile/submitRegistInfo', data, true);
			},
			forgetPwd: function (data) {
				return postData('/index.php/mobile/forgetPwd', data, true);
			},
			getUserInfo: function (data) {
				return postData('/index.php/mobile/getUserInfo', data, true);
			},
			sendSMSOfAccount: function (data) {
				return postData('/index.php/user/sendSMSOfAccount', data, true);
			},
			updateSingleProfile: function (data) {
				return postData('/index.php/mobile/updateSingleProfile', data, true);
			},
			
			
			//产品
			getAllProduct: function (data) {
				return postData('/index.php/mobile/getAllProduct', data, true);
			},
			getProductById: function (data) {
				return postData('/index.php/mobile/getProductById', data, true);
			},
			changeProtocalStatus: function (data) {
				return postData('/index.php/mobile/changeProtocalStatus', data, true);
			},
			submitApply: function (data) {
				return postData('/index.php/mobile/submitApply', data, true);
			},
			submitClearApply: function (data) {
				return postData('/index.php/mobile/submitClearApply', data, true);
			},
			
			
			//列表
			getCashWater: function (data) {
				return postData('/index.php/mobile/getCashWater', data, true);
			},
			getApplyList: function (data) {
				return postData('/index.php/mobile/getApplyList', data, true);
			},
			getTakeCashList: function (data) {
				return postData('/index.php/mobile/getTakeCashList', data, true);
			},
			getMessageList: function (data) {
				return postData('/index.php/mobile/getMessageList', data, true);
			},
			getMessageById: function (data) {
				return postData('/index.php/mobile/getMessageById', data, true);
			},
			getUnreadMsg: function (data) {
				return postData('/index.php/mobile/getUnreadMsg', data, true);
			},
			getBankList: function (data) {
				return postData('/index.php/mobile/getBankList', data, true);
			},
			
			
			//提现及银行卡
			submitTakecash: function (data) {
				return postData('/index.php/mobile/submitTakecash', data, true);
			},
			insertBankInfo: function (data) {
				return postData('/index.php/mobile/insertBankInfo', data, true);
			},
			
			//居间商
			getJujianInfo: function (data) {
				return postData('/index.php/mobile/getJujianInfo', data, true);
			},
			submitJujianApply: function (data) {
				return postData('/index.php/user/submitJujianApply', data, true);
			},
			getPromotionImg: function (data) {
				return postData('/index.php/mobile/getPromotionImg', data, true);
			},
			getLevelChildrenData: function (data) {
				return postData('/index.php/mobile/getLevelChildrenData', data, true);
			},
			getLevelDetailData: function (data) {
				return postData('/index.php/mobile/getLevelDetailData', data, true);
			},
			getCommisionDataByPage: function (data) {
				return postData('/index.php/mobile/getCommisionDataByPage', data, true);
			},
			
			
			
			getBanner: function (data) {
				return postData('/index.php/mobile/getBanner', data, true);
			},
			getBankInfoByBank: function (data) {
				return postData('/index.php/mobile/getBankInfoByBank', data, true);
			},
			getPayCode: function (data) {
				return postData('/index.php/quickPay/codeByApp', data, true);
			},
			submitPay: function (data) {
				return postData('/index.php/quickPay/payByApp', data, true);
			},
			queryCardInfo: function (data) {
				return postData('/index.php/quickPay/queryCardInfoByApp', data, true);
			},
			unbind: function (data) {
				return postData('/index.php/quickPay/unbindByApp', data, true);
			},

		};
	}
]);