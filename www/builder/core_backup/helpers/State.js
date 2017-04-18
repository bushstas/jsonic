var State = new (function() {
	var listeners = {};
	var subscribers = {};
	var updaters = {};
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
		var updated, data = name;
		if (!isUndefined(value)) {
			data = {};
			data[name] = value;
		}
		var changed = {}, isChanged = false;
		for (var k in data) {
			if (vars[k] == data[k]) continue;
			if (isArray(vars[k]) && isArray(data[k]) && Objects.equals(vars[k], data[k])) continue;
			isChanged = true;
			changed[k] = data[k];
		}
		if (isChanged) {
			for (var k in changed) {
				vars[k] = changed[k];
				var s = subscribers[k];
				if (isArray(s)) {
					for (var i = 0; i < s.length; i++) {
						if (isFunction(s[i][0])) {
							s[i][0].call(s[i][1] || null, changed[k], k);
						}
					}
				}
				var u = updaters[k];
				if (isArray(u)) {
					updated = [];
					for (var i = 0; i < u.length; i++) {
						if (updated.indexOf(u[i]) == -1) {
							u[i].react(changed);
							updated.push(u[i]);
						}
					}
				}
			}
		}
		updated = changed = data = null;
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
	this.dispatchEvent = function(name, args) {
		if (isArray(listeners[name])) {
			for (var i = 0; i < listeners[name].length; i++) {
				if (isFunction(listeners[name][i][0])) {
					listeners[name][i][0].apply(listeners[name][i][1] || null, args);
				}
			}
		}
	};
	this.createUpdater = function(updater, component, obj, props) {
		var u = new updater(obj, props, props['g']);
		var keys = u.getKeys()
		for (var i = 0; i < keys.length; i++) {
			updaters[keys[i]] = updaters[keys[i]] || [];
			updaters[keys[i]].push(u);
		}
	};
	this.dispose = function(subscriber) {
		var k, i, s;
		for (k in subscribers) {
			s = [];
			for (i = 0; i < subscribers[k].length; i++) {
				if (subscribers[k][i] != subscriber) s.push(subscribers[k][i]);
				else alert(111222)
			}
			subscribers[k] = s;
		}
	};
})();
{{GLOBAL}}.set(State, 'State');