function Globals() {
	var listeners = {};
	var subscribers = {};
	var components = {};
	var globalVars = {};
	this.addComponent = function(component, key) {
		if (isUndefined(components[key])) components[key] = component;
	};
	this.getComponent = function(key) {
		return components[key];
	};
	this.removeComponent = function(key) {
		delete components[key];
	};
	this.subscribe = function(globalVarName, callback, subscriber) {
		if (!isArray(subscribers[globalVarName])) subscribers[globalVarName] = [];
		subscribers[globalVarName].push([callback, subscriber]);
	};
	this.unsubscribe = function(subscriber, globalVarName) {
		if (isArray(subscribers[globalVarName])) {
			var done = false;
			while (!done) {
				done = true;
				for (var i = 0; i < subscribers[globalVarName].length; i++) {
					if (subscribers[globalVarName][i][1] == subscriber) {
						subscribers[globalVarName].splice(i, 1);
						done = false;
						break;
					}
				}
			}
		}
	};
	this.get = function(globalVarName) {
		return globalVars[globalVarName];
	};
	this.set = function(globalVarName, globalVarValue) {
		globalVars[globalVarName] = globalVarValue;
		if (isArray(subscribers[globalVarName])) {
			for (var i = 0; i < subscribers[globalVarName].length; i++) {
				if (isFunction(subscribers[globalVarName][i][0])) {
					subscribers[globalVarName][i][0].call(subscribers[globalVarName][i][1] || null, globalVarValue, globalVarName);
				}
			}
		}
	};
	this.has = function(globalVarName, globalVarValue) {
		return Objects.has(globalVars, globalVarName, globalVarValue);
	};
	this.listen = function(globalEventName, callback, listener) {
		if (!isArray(listeners[globalEventName])) listeners[globalEventName] = [];
		listeners[globalEventName].push([callback, listener]);
	};
	this.unlisten = function(globalEventName, listener) {
		if (isArray(listeners[globalEventName])) {
			var indexes = [];
			for (var i = 0; i < listeners[globalEventName].length; i++) {
				if (listeners[globalEventName][i][1] == listener) indexes.push(i);
			}
			listeners[globalEventName].removeIndexes(indexes);
		}
	};
	this.dispatchEvent = function(globalEventName, params) {
		if (isArray(listeners[globalEventName])) {
			for (var i = 0; i < listeners[globalEventName].length; i++) {
				if (isFunction(listeners[globalEventName][i][0])) {
					listeners[globalEventName][i][0].call(listeners[globalEventName][i][1] || null, params);
				}
			}
		}
	};
}
Globals = new Globals();