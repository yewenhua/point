'use strict';

/* Controllers */

angular.module('app').controller('WelcomeCtrl', ['$scope', 'DataLoad', '$window', function($scope, DataLoad, $window) {
	$scope.haveData = true;
	$scope.loading = true;
	$scope.orderData = [];
	$scope.statusNum = null;
	$scope.allOrderNum = 0;
	$scope.monthData = null;
	$scope.cashData = null;
	
    $scope.haveOrderGraphData = false;
    $scope.haveCashGraphData = false;
    $scope.allCash = 0;
    
    $scope.chartConfig = {
    	cashArea: {
			options: {
				chart: {
    	            type: 'area',
    	            height: 310
				},
				/*
				colors:[
                    '#27c24c',//第一个颜色
                    '#7266ba',//第二个颜色
                    '#23b7e5',//第三个颜色
                ],
                */
				tooltip: {
					pointFormat: '总现金流量： <b>{point.y:,.0f}元</b>'
				},
				plotOptions: {
					area: {
		                marker: {
		                    enabled: false,
		                    symbol: 'circle',
		                    radius: 2,
		                    states: {
		                        hover: {
		                            enabled: true
		                        }
		                    }
		                }
		            }
				},
			},
			title: {
				text: '近一个月内订单现金流量走势图'
			},
			subtitle: {
				text: ''
			},
			xAxis: {
	            categories: []
	        },
	        yAxis: {
	            title: {
	                text: ''
	            },
	            labels: {
	                formatter: function () {
	                    return this.value / 10000 + '万元';
	                }
	            }
	        },
			series: [{
	            type: 'area',
	            name: '订单现金额',
	            data: []
	        }]
		},
		orderArea: {
			options: {
				chart: {
    	            type: 'area',
    	            height: 400
				},
				/*
				colors:[
                    '#27c24c',//第一个颜色
                    '#7266ba',//第二个颜色
                    '#23b7e5',//第三个颜色
                ],
                */
				tooltip: {
					pointFormat: '订单量： <b>{point.y:,.0f}单</b>'
				},
				plotOptions: {
					area: {
		                marker: {
		                    enabled: false,
		                    symbol: 'circle',
		                    radius: 2,
		                    states: {
		                        hover: {
		                            enabled: true
		                        }
		                    }
		                }
		            }
				},
			},
			title: {
				text: '近一个月内订单量走势图'
			},
			subtitle: {
				text: ''
			},
			xAxis: {
	            categories: []
	        },
	        yAxis: {
	            title: {
	                text: ''
	            },
	            labels: {
	                formatter: function () {
	                    return this.value + '单';
	                }
	            }
	        },
			series: [{
	            type: 'area',
	            name: '订单',
	            data: []
	        }]
		}
    };
    
    $scope.allOrderStatusData = function() {
    	  $scope.loading = true;
    	  $scope.haveOrderGraphData = false;
		  var promise = DataLoad.allOrderStatusData({

		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.orderData = returnData.data;
					$scope.statusNum = returnData.statusNum;
					$scope.allOrderNum = returnData.allOrderNum;
					$scope.monthData = returnData.monthData;
					var res = [];
					var xAxis = [];
					angular.forEach($scope.monthData, function(item, key) {
						res.push(item);
						xAxis.push(key);
				    });
					$scope.chartConfig.orderArea.series[0].data = res;
					$scope.chartConfig.orderArea.xAxis.categories = xAxis;
				} 
				else {
					$scope.orderData = [];
					$scope.statusNum = null;
					$scope.monthData = null;
				}
				$scope.loading = false;
				$scope.haveOrderGraphData = true; //不管成功与否都显示表格
		  }, function () {
			  $scope.orderData = [];
			  $scope.statusNum = null;
			  $scope.monthData = null;
			  $scope.loading = false;
			  $scope.haveOrderGraphData = true;
		      toaster.pop('error', '首页', '获取订单数据出错！');
		  });
    }
    
    $scope.cashLoading = true;
    $scope.welcomeCashData = function() {
    	  $scope.cashLoading = true;
    	  $scope.haveCashGraphData = false;
    	  $scope.chartConfig.cashArea.series[0].data = [];
    	  
		  var promise = DataLoad.welcomeCashData({

		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.cashData = returnData.data;
					
					var resOrder = [];
					var xAxis = [];
					angular.forEach($scope.cashData.monthOrderData, function(item, key) {
						resOrder.push(item);
						xAxis.push(key);
				    });
					$scope.chartConfig.cashArea.series[0].data = resOrder;
					$scope.chartConfig.cashArea.xAxis.categories = xAxis;
				} 
				$scope.haveCashGraphData = true;//不管成功与否都显示表格
				$scope.cashLoading = false;
		  }, function () {
		      toaster.pop('error', '首页', '获取现金流量数据出错！');
		      $scope.haveCashGraphData = true;
		      $scope.cashLoading = false;
		  });
    }
    
    $scope.allCashData = function() {
		  var promise = DataLoad.allCashData({

		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.allCash = returnData.data;
				} 
		  }, function () {
		      toaster.pop('error', '首页', '获取总现金出错！');
		  });
    }
     
    $scope.allOrderStatusData();
    $scope.welcomeCashData();
    $scope.allCashData();
    
    $scope.goLink = function(param) {
    	var start = '2016-12-01';
    	var end = moment().utc().endOf('day').format('YYYY-MM-DD');
    	if(param == 'send'){
    	    $window.location.href = "/admin#/app/orderlist?status=1&start=" + start + "&end=" + end;
    	}
    	else if(param == 'sure'){
    	    $window.location.href = "/admin#/app/orderlist?status=2&start=" + start + "&end=" + end;
    	}
    	else if(param == 'complete'){
    	    $window.location.href = "/admin#/app/orderlist?status=3&start=" + start + "&end=" + end;
    	}
    	else if(param == 'refund'){
    	    $window.location.href = "/admin#/app/orderlist?status=7&start=" + start + "&end=" + end;
    	}
    }
    
}]);