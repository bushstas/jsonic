function State() {
	var listeners = {};
	var subscribers = {};
	var vars = {};
	this.subscribe = function(subscriber, name, callback) {
		var s = subscribers[name] = subscribers[name] || [];
		s.push([callback, subscriber]);
	};
	this.unsubscribe = function(subscriber, name) {
		var s = subscribers[name];
		if (isArray(s)) {
			var done = false;
			while (!done) {
				done = true;
				for (var i = 0; i < s.length; i++) {
					if (s[i][1] == subscriber) {
						s.splice(i, 1);
						done = false;
						break;
					}
				}
			}
		}
	};
	this.get = function(name) {
		return vars[name];
	};
	this.set = function(name, value) {
		var data = name;
		if (!isUndefined(value)) {
			data = {};
			data[name] = value;
		}
		for (var k in data) {
			vars[k] = data[k];
			var s = subscribers[k];
			if (isArray(s)) {
				for (var i = 0; i < s.length; i++) {
					if (isFunction(s[i][0])) {
						s[i][0].call(s[i][1] || null, data[k], k);
					}
				}
			}
		}
	};
	this.listen = function(listener, name, callback) {
		if (!isArray(listeners[name])) listeners[name] = [];
		listeners[name].push([callback, listener]);
	};
	this.unlisten = function(name, listener) {
		if (isArray(listeners[name])) {
			var indexes = [];
			for (var i = 0; i < listeners[name].length; i++) {
				if (listeners[name][i][1] == listener) indexes.push(i);
			}
			listeners[name].removeIndexes(indexes);
		}
	};
	this.dispatchEvent = function(name, params) {
		if (isArray(listeners[name])) {
			for (var i = 0; i < listeners[name].length; i++) {
				if (isFunction(listeners[name][i][0])) {
					listeners[name][i][0].call(listeners[name][i][1] || null, params);
				}
			}
		}
	};
}