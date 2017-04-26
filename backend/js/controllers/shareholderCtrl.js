angular.module('app')

.controller('ShareholderCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', function($scope, CommonFunction, DataLoad, toaster) {
	$scope.lastMonthFirstLastDay = function() {
		var year = (new Date()).getFullYear();
		var month = (new Date()).getMonth() + 1;
		var isLeapYear = moment([year]).isLeapYear();
		
	    //大小平月判断
		if(month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12){
			if(month == 1 || month == 3 || month == 5 || month == 7 || month == 8){
				var obj = {
					first: year + '-0' + month + '-01', 
					last: year + '-0' + month + '-31'
				};
			}
			else{
				var obj = {
					first: year + '-' + month + '-01', 
					last: year + '-' + month + '-31'
				};
			}
		}
		else if(month == 4 || month == 6 || month == 9 || month == 11){
			if(month == 4 || month == 6 || month == 9){
				var obj = {
					first: year + '-0' + month + '-01', 
					last: year + '-0' + month + '-30'
				};
			}
			else{
				var obj = {
					first: year + '-' + month + '-01', 
					last: year + '-' + month + '-30'
				};
			}
		}
		else if(month == 2){
			if(isLeapYear){
				var obj = {
					first: year + '-0' + month + '-01', 
					last: year + '-0' + month + '-29'
				};
			}
			else{
				var obj = {
					first: year + '-0' + month + '-01', 
					last: year + '-0' + month + '-28'
				};
			}
		}
		
		return obj;
	}
	$scope.lastMonth = $scope.lastMonthFirstLastDay();
	
	$scope.dateSelectRange = {
	    start: moment(new Date($scope.lastMonth.first)).utc().startOf('day'),
	    end: moment(new Date($scope.lastMonth.last)).utc().startOf('day')
    };
	
	$scope.$watch('dateSelectRange', function(new_val, old_val){
		if(new_val != old_val){
			$scope.getColumnGraphData();
		}
	}, true);
		
	$scope.chartConfig = {
		numberColumn: {
			options: {
				chart: {
					type: 'column'
				},
				colors:[
                    '#27c24c',//第一个颜色
                    '#7266ba',//第二个颜色
                    '#23b7e5',//第三个颜色
                ],
				tooltip: {
					formatter: function () {
		                return '<b>' + this.x + '</b><br/>' +
		                    //'总现金量：' + this.point.stackTotal + '元<br/>' +
		                    this.series.name + '：' + this.y + '元<br/>' + 
		                    '占百分比：' + (100 * this.y/this.point.stackTotal).toFixed(1) + '%';
		            }
				},
				plotOptions: {
					column: {
		                stacking: 'normal',
		                dataLabels: {
		                    enabled: true,
		                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
		                    style: {
		                        textShadow: '0 0 3px black'
		                    }
		                }
		            }
				},
		    },
			title: {
				text: '现金对比图'
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories: []
			},
			yAxis: {
				min: 0,
				title: {
					text: '下级总现金对比图'
				},
				stackLabels: {
	                enabled: true,
	                style: {
	                    fontWeight: 'bold',
	                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
	                }
	            },
	            labels: {
	                formatter: function () {
	                    return this.value / 10000 + '万元';
	                }
	            }
			},
			legend: {
	            align: 'right',
	            x: -30,
	            verticalAlign: 'top',
	            y: 25,
	            floating: true,
	            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
	            borderColor: '#CCC',
	            borderWidth: 1,
	            shadow: false
	        },
	        series: [{
	            name: '在线报单',
	            data: []
	        }, {
	            name: '后台升级',
	            data: []
	        }, {
	            name: '快速报单',
	            data: []
	        }]
		}
	};
	
	$scope.columnloading = true;
	$scope.haveColumnGraphData = false;
	$scope.getColumnGraphData = function() {
  	      $scope.haveColumnGraphData = false;
  	      $scope.columnloading = true;
  	      $scope.chartConfig.numberColumn.series[0].data = [];
  	      $scope.chartConfig.numberColumn.series[1].data = [];
  	      $scope.chartConfig.numberColumn.series[2].data = [];
  	      
		  var promise = DataLoad.getShareholderGraphData({
			  start: $scope.dateSelectRange.start.format('YYYY-MM-DD'),
			  end: $scope.dateSelectRange.end.format('YYYY-MM-DD'),
		  });
		  return promise.then(function (returnData) {
				if (returnData && returnData.code == 0) {
					$scope.originalColumnData = returnData.data;
					var graghData = [];
					var category = [];
					
					var graghData = {
					    online: [],
					    backend: [],
					    quick: []
				    };
					
					angular.forEach($scope.originalColumnData, function(value, key) {
						category.push(value.name);
						graghData.online[key] = value.online_declaration;
						graghData.backend[key] = value.backend_upgrade;
						graghData.quick[key] = value.quick_declaration;
					});
					
					$scope.chartConfig.numberColumn.xAxis.categories = category;
					$scope.chartConfig.numberColumn.series[0].data = graghData.online;
			  	    $scope.chartConfig.numberColumn.series[1].data = graghData.backend;
			  	    $scope.chartConfig.numberColumn.series[2].data = graghData.quick;

					$scope.haveColumnGraphData = true;
				}
				else{
					$scope.haveColumnGraphData = false;
				}
				$scope.columnloading = false;
		  }, function () {
			  $scope.columnloading = false;
			  $scope.haveColumnGraphData = false;
		      toaster.pop('error', '股东数据统计', '获取数据出错！');
		  });
    }
	$scope.getColumnGraphData();
}]);
