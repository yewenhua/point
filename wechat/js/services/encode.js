'use strict';

angular.module('pwd', []).factory('pwd', function() {

	function addNumPwd(string) {
		var code = 'cat@'+ Number(string) * 9527 + '@cnvp';
		return escape(code);
	}

	function removeNumPwd(string) {
		var pwdString = unescape(string);
		var codeArray = pwdString.split('@');
		var originalString = codeArray.length == 3 ? codeArray[1] : '';
		var code = Number(originalString)/9527;
		return code;
	}
	
	function addPhonePwd(string) {
		var code = Number(string) + 9527;
		return escape(code);
	}

	function removePhonePwd(string) {
		var pwdString = unescape(string);
		var originalString = Number(pwdString - 9527);
		return originalString;
	}

	return {
		addNumPwd: addNumPwd,
		removeNumPwd: removeNumPwd,
		addPhonePwd: addPhonePwd,
		removePhonePwd: removePhonePwd,
	};
});