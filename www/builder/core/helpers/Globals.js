function Globals() {
	var listeners = {};
	var components = {};
	var views = {};
	var globalVars = {};
	this.addView = function(view, key) {
		if (isUndefined(views[key])) {
			views[key] = view;
		}
	};
	this.getView = function(key) {
		return views[key];
	};
	this.addComponent = function(component, key) {
		if (isUndefined(components[key])) {
			components[key] = component;
		}
	};
	this.getComponent = function(key) {
		return components[key];
	};
	this.removeComponent = function(key) {
		delete components[key];
	};
	this.subscribe = function(globalVarName, callback, subscriber) {
		if (!isArray(listeners[globalVarName])) {
			listeners[globalVarName] = [];
		}
		listeners[globalVarName].push([callback, subscriber]);
	};
	this.unsubscribe = function(subscriber, globalVarName) {
		if (isArray(listeners[globalVarName])) {
			var done = false;
			while (!done) {
				done = true;
				for (var i = 0; i < listeners[globalVarName].length; i++) {
					if (listeners[globalVarName][i][1] == subscriber) {
						listeners[globalVarName].splice(i, 1);
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
		if (isArray(listeners[globalVarName])) {
			for (var i = 0; i < listeners[globalVarName].length; i++) {
				if (isFunction(listeners[globalVarName][i][0])) {
					listeners[globalVarName][i][0].call(listeners[globalVarName][i][1] || null, globalVarValue, globalVarName);
				}
			}
		}
	};
	this.has = function(globalVarName, globalVarValue) {
		return Objects.has(globalVars, globalVarName, globalVarValue);
	};
}
Globals = new Globals();