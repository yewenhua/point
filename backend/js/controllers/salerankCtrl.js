angular.module('app')

.controller('SalerankCtrl', ['$scope', 'CommonFunction', 'DataLoad', 'toaster', '$rootScope', function($scope, CommonFunction, DataLoad, toaster, $rootScope) {
	$scope.dataList = [];
	$scope.total = [];
	$scope.loading = true;
	$scope.haveData = true;
	$scope.selectedYear = '';
	$scope.selectedMonth = '';
	
	$scope.yearData = [];
	var beginYear = 2016;
	var thisYear = (new Date()).getFullYear();
	if(beginYear == thisYear){
		var temp = {};
		temp.id = beginYear;
		temp.name = beginYear;
		$scope.yearData.push(temp);
	}
	else{
		for(var i=beginYear; i<=thisYear; i++){
			var temp = {};
			temp.id = i;
			temp.name = i;
			$scope.yearData.push(temp);
		}
	}
	$scope.selectedYear = thisYear;
	
	$scope.monthSelectData = [];
	for(i=0; i<=12; i++){
		if(i == 0){
			$scope.monthSelectData.push({id:99, name:'全部月'});
		}
		else{
		    $scope.monthSelectData.push({id:i, name:i+'月'});
		}
	}
	$scope.selectedMonth = $scope.monthSelectData[0].id;
	
	$scope.changeMonth = function() {
		$scope.getAllData();
	}
	
	$scope.changeYear = function() {
		 $scope.selectedMonth = $scope.monthSelectData[0].id;
		 $scope.getAllData();
	}
	
	$scope.getAllData = function() {
		 $scope.loading = true;
		 var promise = DataLoad.getSaleRankData({
			  year: $scope.selectedYear,
			  month: $scope.selectedMonth
		 });
		 return promise.then(function (returnData) {
			  if (returnData && returnData.code == 0) {
				  $scope.haveData = true;
				  $scope.dataList = returnData.data;
				  $scope.total = returnData.total;
			  } 
			  else {
				  $scope.dataList = [];
				  $scope.total = [];
				  $scope.haveData = false;
				  toaster.pop('error', '销售统计', returnData.message);
			  }
			  $scope.loading = false;
		 }, function () {
			  $scope.dataList = [];
			  $scope.total = [];
			  $scope.haveData = false;
			  $scope.loading = false;
			  toaster.pop('error', '销售统计', '获取数据出错！');
		 });
    }
	$scope.getAllData();
}]);