app.directive('datetimepicker', ['$cookieStore', 
		function($cookieStore) {
			return {
				restrict: 'EA',
				require: '?ngModel',
				scope: {
					select: '&',
					type: "="
				},
				link: function(scope, element, attr, ngModel) {
					if (attr.datetimepicker == null || ngModel == null) {
						return;
					}

					var optionObj = {
						format: 'yyyy-mm-dd hh:ii'
					};
					
					//$formatters一个包含即将执行函数的数组   是一个由函数组成的列表，串行执行，作用是把变量值变成显示的值。
					//从变量( ModelValue )到显示值( ViewValue )的过程， $formatters 属性，把一个列表变成一个字符串。 
					//无论什么时候model的值发生了变化，它都会作为一个管道。其中的每一个函数都被依次调用，并将结果传递给下一个函数。该函数用于将模型传递给视图的值进行格式化
		            ngModel.$formatters.push(function(value){
						return value;
					});
					
					//$parsers 将要执行的函数的数组  与上面的方向相反，把显示的值变成变量值。  
					//从显示值到变量的过程， $parsers 属性，把一个字符串变成一个列表。
					//无论什么时候控制器从DOM中读取了一个值，它都将作为一个管道。其中的函数依次被调用，并将结果传递给下一个。最后出来的值将会被传递到model中
		            ngModel.$parsers.push(function(value){
						return value;
					});

					$(element).datetimepicker(optionObj).on('changeDate', function(ev){
		            	scope.$apply(function() {
							ngModel.$setViewValue(ev.target.value);
						});
					});
				}
			};
		}
	]);