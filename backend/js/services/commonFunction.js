'use strict';

angular.module('CommonFunction', []).factory('CommonFunction', ['$http', '$q', function ($http, $q) {

	return {

		/*
		 * 参数date 为时间对象
		 * 参数offset 为偏移量
		 * 返回制定日期之前offset天的日期    
		 * 返回类型"2015-08-26"
		 */
		dateFormatByOffset: function (date, offset) {
			date.setTime(date.getTime() - offset * 86400000);
			var tmpCountYear = date.getFullYear();
			var tmpCountMonth = Number(date.getMonth());
			var tmpCountDay = Number(date.getDate());

			tmpCountMonth = tmpCountMonth + 1;
			if (tmpCountMonth < 10) {
				tmpCountMonth = "0" + tmpCountMonth;
			}

			if (tmpCountDay < 10) {
				tmpCountDay = "0" + tmpCountDay;
			}
			var tmpCountTime = tmpCountYear + "-" + tmpCountMonth + "-" + tmpCountDay;
			return tmpCountTime;
		},
		
		/*
		 * 参数date 为时间对象
		 * 参数offset 为偏移量
		 * 返回制定日期之前offset天的日期    
		 * 返回类型"2015-08-26 12:12:00"
		 */
		dateFormatToSecond: function (date, offset) {
			date.setTime(date.getTime() - offset * 86400000);
			var tmpCountYear = date.getFullYear();
			var tmpCountMonth = Number(date.getMonth());
			var tmpCountDay = Number(date.getDate());
			var tmpCountHour = Number(date.getHours());
			var tmpCountMinute = Number(date.getMinutes());
			var tmpCountSecond = Number(date.getSeconds());

			tmpCountMonth = tmpCountMonth + 1;
			if (tmpCountMonth < 10) {
				tmpCountMonth = "0" + tmpCountMonth;
			}

			if (tmpCountDay < 10) {
				tmpCountDay = "0" + tmpCountDay;
			}
			
			if (tmpCountHour < 10) {
				tmpCountHour = "0" + tmpCountHour;
			}
			
			if (tmpCountMinute < 10) {
				tmpCountMinute = "0" + tmpCountMinute;
			}
			
			if (tmpCountSecond < 10) {
				tmpCountSecond = "0" + tmpCountSecond;
			}
			var tmpCountTime = tmpCountYear + "-" + tmpCountMonth + "-" + tmpCountDay + " " + tmpCountHour + ":" + tmpCountMinute + ":" + tmpCountSecond;
			return tmpCountTime;
		},

		/*
		 * 判断对象是否为空
		 */
		isEmpty: function (data) {
			if (angular.isArray(data)) { //数组
				if (data.length == 0) {
					return true;
				} else {
					return false;
				}
			} else if (angular.isObject(data)) { //对象
				var length = 0;
				angular.forEach(data, function (value, key) {
					length = length + 1;
				});
				if (length == 0) {
					return true;
				} else {
					return false;
				}
			} else if (angular.isString(data)) { //字符串
				if (angular.isUndefined(data)) {
					return true;
				} else {
					return false;
				}
			} else if (angular.isNumber(data)) { //数字
				return true;
			}
		},

		/*
		 * 数组合并
		 */
		array_merge: function (array1, array2) {
			if (arguments.length < 2) {
				return arguments[0];
			}
			var isArray = 0
			var mergeSource = angular.copy(arguments);
			if (angular.isArray(arguments[0])) {
				var ret = [];
			} else
				var ret = {};
			angular.forEach(mergeSource, function (v, k) {
				angular.forEach(v, function (value, key) {
					ret[key] = value;
				});
			});
			mergeSource = undefined;
			return ret;
		},

		/*
		 * 是否存在数组元素中
		 */
		isInArray: function (arr, value) {
			for (var i = 0; i < arr.length; i++) {
				if (arr[i] == value) {
					return true;
				}
			}
			return false;
		},

		/*
		 * json_encode
		 */
		json_encode: function (mixed_val) {
			var retVal, json = this.window.JSON;
			try {
				if (typeof json === 'object' && typeof json.stringify === 'function') {
					retVal = json.stringify(mixed_val); // Errors will not be caught here if our own equivalent to resource
					if (retVal === undefined) {
						throw new SyntaxError('json_encode');
					}
					return retVal;
				}

				var value = mixed_val;

				var quote = function (string) {
					var escapable = /[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
					var meta = { // table of character substitutions
						'\b': '\\b',
						'\t': '\\t',
						'\n': '\\n',
						'\f': '\\f',
						'\r': '\\r',
						'"': '\\"',
						'\\': '\\\\'
					};

					escapable.lastIndex = 0;
					return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
						var c = meta[a];
						return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
					}) + '"' : '"' + string + '"';
				};

				var str = function (key, holder) {
					var gap = '';
					var indent = '    ';
					var i = 0; // The loop counter.
					var k = ''; // The member key.
					var v = ''; // The member value.
					var length = 0;
					var mind = gap;
					var partial = [];
					var value = holder[key];

					// If the value has a toJSON method, call it to obtain a replacement value.
					if (value && typeof value === 'object' && typeof value.toJSON === 'function') {
						value = value.toJSON(key);
					}

					// What happens next depends on the value's type.
					switch (typeof value) {
						case 'string':
							return quote(value);

						case 'number':
							// JSON numbers must be finite. Encode non-finite numbers as null.
							return isFinite(value) ? String(value) : 'null';

						case 'boolean':
						case 'null':
							// If the value is a boolean or null, convert it to a string. Note:
							// typeof null does not produce 'null'. The case is included here in
							// the remote chance that this gets fixed someday.
							return String(value);

						case 'object':
							// If the type is 'object', we might be dealing with an object or an array or null.
							// Due to a specification blunder in ECMAScript, typeof null is 'object', so watch out for that case.
							if (!value) {
								return 'null';
							}
							if ((this.PHPJS_Resource && value instanceof this.PHPJS_Resource) || (window.PHPJS_Resource && value instanceof window.PHPJS_Resource)) {
								throw new SyntaxError('json_encode');
							}

							// Make an array to hold the partial results of stringifying this object value.
							gap += indent;
							partial = [];

							// Is the value an array?
							if (Object.prototype.toString.apply(value) === '[object Array]') {
								// The value is an array. Stringify every element. Use null as a placeholder for non-JSON values.
								length = value.length;
								for (i = 0; i < length; i += 1) {
									partial[i] = str(i, value) || 'null';
								}

								// Join all of the elements together, separated with commas, and wrap them in brackets.
								v = partial.length === 0 ? '[]' : gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' : '[' + partial.join(',') + ']';
								gap = mind;
								return v;
							}

							// Iterate through all of the keys in the object.
							for (k in value) {
								if (Object.hasOwnProperty.call(value, k)) {
									v = str(k, value);
									if (v) {
										partial.push(quote(k) + (gap ? ': ' : ':') + v);
									}
								}
							}

							// Join all of the member texts together, separated with commas,and wrap them in braces.
							v = partial.length === 0 ? '{}' : gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' : '{' + partial.join(',') + '}';
							gap = mind;
							return v;
						case 'undefined':
						// Fall-through
						case 'function':
						// Fall-through
						default:
							throw new SyntaxError('json_encode');
					}
				};

				// Make a fake root object containing our value under the key of ''.
				// Return the result of stringifying the value.
				return str('', {
					'': value
				});

			} catch (err) { // Todo: ensure error handling above throws a SyntaxError in all cases where it could
				// (i.e., when the JSON global is not available and there is an error)
				if (!(err instanceof SyntaxError)) {
					throw new Error('Unexpected error type in json_encode()');
				}
				this.php_js = this.php_js || {};
				this.php_js.last_error_json = 4; // usable by json_last_error()
				return null;
			}
		},

		/*
		 * json_decode
		 */
		json_decode: function (str_json) {
			var json = this.window.JSON;
			if (typeof json === 'object' && typeof json.parse === 'function') {
				try {
					return json.parse(str_json);
				} catch (err) {
					if (!(err instanceof SyntaxError)) {
						throw new Error('Unexpected error type in json_decode()');
					}
					this.php_js = this.php_js || {};
					this.php_js.last_error_json = 4; // usable by json_last_error()
					return null;
				}
			}

			var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
			var j;
			var text = str_json;

			cx.lastIndex = 0;
			if (cx.test(text)) {
				text = text.replace(cx, function (a) {
					return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
				});
			}

			if ((/^[\],:{}\s]*$/).
					test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').
						replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
						replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

				j = eval('(' + text + ')');

				return j;
			}

			this.php_js = this.php_js || {};
			this.php_js.last_error_json = 4; // usable by json_last_error()
			return null;
		},

		/*
		 * trim
		 */
		trim: function (str, charlist) {
			var whitespace, l = 0,
				i = 0;
			str += '';

			if (!charlist) {
				// default list
				whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
			} else {
				// preg_quote custom list
				charlist += '';
				whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
			}

			l = str.length;
			for (i = 0; i < l; i++) {
				if (whitespace.indexOf(str.charAt(i)) === -1) {
					str = str.substring(i);
					break;
				}
			}

			l = str.length;
			for (i = l - 1; i >= 0; i--) {
				if (whitespace.indexOf(str.charAt(i)) === -1) {
					str = str.substring(0, i + 1);
					break;
				}
			}

			return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
		},
		
		// 判断一个对象是不是空对象
		isEmptyObject: function (obj) {
		    for (var name in obj) {
		        return false;
		    }
		    return true;
		}

	};
}]);