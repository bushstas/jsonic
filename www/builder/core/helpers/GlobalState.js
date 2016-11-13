function GlobalState(isLocal) {
	var listeners = {};
	var subscribers = {};
	var globalVars = {};
	var gr = function() {
		return Router.getCurrentRouteName();
	};
	var gs = function(name) {
		return !isLocal ? subscribers[name] : Objects.get(subscribers[gr()], name);
	};
	this.subscribe = function(name, callback, subscriber) {
		var s, r;
		if (!isLocal) {
			s = subscribers[name] = subscribers[name] || [];			
		} else {
			var r = Router.getCurrentRouteName();
			subscribers[r] = subscribers[r] || {};
			s = subscribers[r][name] = subscribers[r][name] || [];
		}
		s.push([callback, subscriber]);
	};
	this.unsubscribe = function(subscriber, name) {
		var s = gs(name);
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
		return !isLocal ? globalVars[name] : Objects.get(globalVars[gr()], name);
	};
	this.set = function(name, value) {
		globalVars[name] = value;
		var s = gs(name);
		if (isArray(s)) {
			for (var i = 0; i < s.length; i++) {
				if (isFunction(s[i][0])) {
					s[i][0].call(s[i][1] || null, value, name);
				}
			}
		}
	};
	this.listen = function(name, callback, listener) {if(isLocal)alert(gr())
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
LocalState = new GlobalState(true);
GlobalState = new GlobalState();