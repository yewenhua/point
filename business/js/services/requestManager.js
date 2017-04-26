'use strict';

angular.module('requestManager', []).factory('RequestManager', function() {

	var requestQueue = [];

	function get() {
		return requestQueue;
	}

	function add(request) {
		requestQueue.push(request);
	}
	
	function cancel(promise) {
		if (promise) {
			if (promise.canceller && promise.canceller.resolve) {
				promise.canceller.resolve();
			}
		} else {
			for (var i = 0, len = requestQueue.length; i < len; i++) {
				if (requestQueue[i].promise.canceller && requestQueue[i].promise.canceller.resolve) {
					requestQueue[i].promise.canceller.resolve();
				}
			}
			requestQueue = [];
		}
	}

	return {
		get: get,
		add: add,
		cancel: cancel
	};
});