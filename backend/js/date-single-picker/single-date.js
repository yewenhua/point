app.directive('singledatepicker', ['$cookieStore', 
		function($cookieStore) {
			return {
				restrict: 'EA',
				require: '?ngModel',
				scope: {
					select: '&',
					type: "="
				},
				link: function(scope, element, attr, ngModel) {
					
					if (attr.singledatepicker == null || ngModel == null) {
						return;
					}
					
					var date = new Date();
					var time = date.getTime();
					date.setTime(time - 0);
					var month = date.getMonth() + 1;
					if (month < 10) {
						month = '0' + month;
					}
					scope.type = scope.type || null;
					var now = null;
					if (scope.type != 1) { //是否限制最大值
						now = date.getFullYear() + '-' + month + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
					}
					var optionObj = {
						monthNames: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
						monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
						dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
						dateFormat: "yy-mm-dd",
						maxDate: now
					};

					var updateModel = function(dateStr) {
						scope.$apply(function() {
							ngModel.$setViewValue(dateStr);
						});
					};
					
					//$formatters一个包含即将执行函数的数组   是一个由函数组成的列表，串行执行，作用是把变量值变成显示的值。
					//从变量( ModelValue )到显示值( ViewValue )的过程， $formatters 属性，把一个列表变成一个字符串。 
					//无论什么时候model的值发生了变化，它都会作为一个管道。其中的每一个函数都被依次调用，并将结果传递给下一个函数。该函数用于将模型传递给视图的值进行格式化
		            ngModel.$formatters.push(function(value){
		            	if(typeof(value) == 'string' && value.length == 10 && value.indexOf('-', 0) != -1){
		            		dateString = value;
		            	}
		            	else{
		            		if(angular.isUndefined(value) || angular.isUndefined(value.end)){
			            		value = {};
			            		value.end = moment().utc().endOf('day').second(-0);
			            	}   
			            	if(value.end != ''){
			            		var dateString = value.end.format('YYYY-MM-DD');
			            	}
			            	else{
			            		var dateString = '';
			            	}
		            	}
		            	
						return dateString;
					});
					
					//$parsers 将要执行的函数的数组  与上面的方向相反，把显示的值变成变量值。  
					//从显示值到变量的过程， $parsers 属性，把一个字符串变成一个列表。
					//无论什么时候控制器从DOM中读取了一个值，它都将作为一个管道。其中的函数依次被调用，并将结果传递给下一个。最后出来的值将会被传递到model中
		            ngModel.$parsers.push(function(value){
		            	var startDate = moment(new Date(value)).utc().startOf('day');
		            	var endDate = moment(new Date(value)).utc().endOf('day');
						return {start:startDate, end:endDate};
					});
		            
		            
					optionObj.onSelect = function(dateStr, picker) {
						updateModel(dateStr);
					};
					$(element).datepicker(optionObj);
				}
			};
		}
	]);