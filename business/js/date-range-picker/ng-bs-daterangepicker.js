/**
 * @license ng-bs-daterangepicker v0.0.1
 * (c) 2013 Luis Farzati http://github.com/luisfarzati/ng-bs-daterangepicker
 * License: MIT
 */
(function(angular) {
	'use strict';

	angular.module('app').directive('daterange',['$compile', '$parse', function($compile, $parse) {
		return {
			restrict: 'A',
			require: '?ngModel',
			link: function($scope, $element, $attributes, ngModel) {

				if ($attributes.daterange !== 'daterange' || ngModel === null) {
					return;
				}

				var options = {};
				options.format = $attributes.format || 'YYYY-MM-DD';
				options.separator = $attributes.separator || ' ~ ';
				options.minDate = $attributes.minDate && moment($attributes.minDate);
				options.maxDate = $attributes.maxDate && moment($attributes.maxDate);
				options.dateLimit = $attributes.limit && moment.duration.apply(this, $attributes.limit.split(' ').map(function(elem, index) {
					return index === 0 && parseInt(elem, 10) || elem;
				}));
				options.ranges = $attributes.ranges && $parse($attributes.ranges)($scope);

				function format(date) {
					return date.format(options.format);
				}

				function formatted(dates) {
					return [format(dates.start), format(dates.end)].join(options.separator);
				}

				ngModel.$formatters.unshift(function(modelValue) {
					if (!modelValue) return '';
					return modelValue;
				});

				ngModel.$parsers.unshift(function(viewValue) {
					return viewValue;
				});

				ngModel.$render = function() {
					if (!ngModel.$viewValue || !ngModel.$viewValue.start) return;
					
					$element.val(formatted(ngModel.$viewValue));
				};

				$scope.$watch($attributes.ngModel, function(modelValue) {
					if (!modelValue || (!modelValue.start)) {
						ngModel.$setViewValue({
							start: moment().utc().startOf('day'),
							end: moment().utc().startOf('day')
						});
						return;
					}
					$element.data('daterangepicker').startDate = modelValue.start;
					$element.data('daterangepicker').endDate = modelValue.end;
					$element.data('daterangepicker').updateView();
					$element.data('daterangepicker').updateCalendars();
					$element.data('daterangepicker').updateInputText();
				});

				$element.daterangepicker(options, function(start, end) {
					$scope.$apply(function() {
						ngModel.$setViewValue({
							start: start,
							end: end
						});
						ngModel.$render();
					});
				});

			}
		};
	}]);

})(angular);