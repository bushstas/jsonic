function Controller() {	
	if (this !== window) return;
	var makeUrl = function(url, options) {
		var regExp;
		for (var k in options) {
			if (isString(options[k]) || isNumber(options[k])) {
				regExp = new RegExp('\\$' + k)
				url = url.replace(regExp, options[k]);
			}
		}
		return url;
	};
	var gotFromStore = function(actionName, options) {
		if (shouldStore.call(this)) {
			var storeAs = getStoreAs.call(this, options);
			if (isString(storeAs)) {
				var storedData = StoreKeeper.getActual(storeAs, Objects.get(this.options, 'storePeriod'));
				if (isArrayLike(storedData)) {
					onActionComplete.call(this, actionName, storedData, true);
					return true;
				}
			}
		}
		return false;
	};
	var onActionComplete = function(actionName, data, isFromStorage) {
		this.data = this.data || {};
		this.data[actionName] = data;
		var action = getAction.call(this, actionName);
		if (isObject(action) && isFunction(action['callback'])) {
			action['callback'].call(this, data);
		}
		if (action['autoset']) autoset.call(this, action['autoset'], data);
		this.dispatchEvent(actionName, data);
		if (!isFromStorage && actionName == 'load' && shouldStore.call(this)) {
			store.call(this, true, data);
		}
		this.activeRequests.removeItem(actionName);
	};
	var autoset = function(opts, data) {
		var props = {};
		if (isString(opts)) {
			props[opts] = data; 
		} else if (isObject(opts)) {
			for (var k in opts) props[opts[k]] = data[k];
		}
		for (var i = 0; i < this.subscribers.length; i++) {
			this.subscribers[i][2].set(props);
		}
	};
	var shouldStore = function() {
		var should = Objects.get(this.options, 'store');
		if (should === false) return false;
		return Objects.has(this.options, 'storeAs');
	};
	var store = function(isAdding, data) {
		var storeAs = getStoreAs.call(this, data);
		if (isAdding) {		
			StoreKeeper.set(storeAs, data);
		} else {
			StoreKeeper.remove(storeAs);
		}
	};
	var getStoreAs = function(data) {
		var storeAs = Objects.get(this.options, 'storeAs');
		if (isArrayLike(data) && isString(storeAs) && (/\$[a-z_]/i).test(storeAs)) {
			var parts = storeAs.split('$');
			storeAs = parts[0];
			for (var i = 1; i < parts.length; i++) {
				if (data[parts[i]]) storeAs += data[parts[i]];
				else storeAs += parts[i];				
			}
		}
		return storeAs;
	};
	var getPrimaryKey = function() {
		return Objects.get(this.options, 'key', 'id');
	};
	var initActionRouteOptions = function(action) {
		var value;
		this.currentRouteOptions = {};
		var routeOptions = {};
		for (var k in action['routeOptions']) {
			value = Router.getPathPartAt(action['routeOptions'][k]);
			if (isString(value)) {
				routeOptions[k] = value;
			}
		}
		setCurrentRouteOptions.call(this, routeOptions, action);
		Router.subscribe(action['routeOptions'], this);
	};
	var setCurrentRouteOptions = function(routeOptions, action) {
		this.currentRouteOptions = routeOptions;
		if (!isObject(action['options'])) {
			action['options'] = {};
		}
		for (var k in routeOptions) {
			action['options'][k] = routeOptions[k];
		}
	};
	var getAction = function(actionName) {	
		var actions = Objects.get(this.initials, 'actions');
		if (isObject(actions)) {
			var action = actions[actionName];
			if (isObject(action)) {
				if (!isString(action['name'])) {
					if (isObject(action['routeOptions']) && actionName == 'load') {
						initActionRouteOptions.call(this, action);
					}
					action['name'] = actionName;
				}
				return action;
			}
			log('action is invalid', 'getAction', this, {action: action});
		} else {
			log('no actions', 'getAction', this, {actions: actions});
		}
		return null;
	};

	Controller.prototype.initiate = function() {
		this.subscribers = [];
		this.requests = {};
		this.activeRequests = [];
	};

	Controller.prototype.subscribe = function(eventType, callback, subscriber) {
		this.subscribers.push([eventType, callback, subscriber]);
	};

	Controller.prototype.unsubscribe = function(subscriber, eventType) {
		var done = false;
		while (!done) {
			done = true;
			for (var i = 0; i < this.subscribers.length; i++) {
				if (this.subscribers[i][2] == subscriber && (!eventType || this.subscribers[i][0] == eventType)) {
					this.subscribers.splice(i, 1);
					done = false;
					break;
				}
			}
		}
	};

	Controller.prototype.dispatchEvent = function(eventType, data) {
		var dataToDispatch = data;
		if (Objects.has(this.options, 'clone', true)) dataToDispatch = Objects.clone(data);
		if (isArray(this.subscribers)) {
			for (var i = 0; i < this.subscribers.length; i++) {
				if (this.subscribers[i][0] == eventType && isFunction(this.subscribers[i][1])) {
					this.subscribers[i][1].call(this.subscribers[i][2] || null, dataToDispatch, this);
				}
			}
		}
	};

	Controller.prototype.getData = function(actionName) {
		return !!action && !!this.data && isObject(this.data) ? this.data[action] : this.data;
	};

	Controller.prototype.getItemById = function(id) {
		var primaryKey = getPrimaryKey.call(this);
		var data = this.data['load'];
		if (isArray(data)) {
			for (var i = 0; i < data.length; i++) {
				if (Objects.has(data[i], primaryKey, id)) return data[i];
			}
		}
		return null;
	};

	Controller.prototype.getItem = function(nameOrIndex, actionName) {
		actionName = actionName || 'load';
		return isArrayLike(this.data[actionName]) ? this.data[actionName][nameOrIndex] : null;
	};

	Controller.prototype.load = function(options) {
		this.doAction('load', options);
	};

	Controller.prototype.doAction = function(actionName, options, url) {
		if (this.activeRequests.indexOf(actionName) > -1) return;
		var action = getAction.call(this, actionName);
		if (actionName == 'load' && gotFromStore.call(this, actionName, options)) return;
		if (!isObject(options)) options = {};
		if (action && isObject(action) && action['options'] && isObject(action['options'])) {
			Objects.merge(options, action['options']);
		}
		var method = action['method'] || 'POST';
		url = url || makeUrl(action['url'], options);
		if (!url || !isString(url)) {
			log('url to execute the action ' + actionName + ' is invalid or empty', 'doAction', this, {action: action});
		}		
		if (!this.requests[actionName]) {
			this.requests[actionName] = new AjaxRequest(url, onActionComplete.bind(this, actionName));
		}
		this.requests[actionName].send(method, options, url);
		this.activeRequests.push(actionName);		
	};

	Controller.prototype.handleRouteOptionsChange = function(routeOptions) {
		if (!Objects.equals(routeOptions, this.currentRouteOptions)) {
			var action = getAction.call(this, 'load');
			setCurrentRouteOptions.call(this, routeOptions, action);
			this.doAction('load');
		}
	};

	Controller.prototype.dispose = function() {
		this.subscribers = null;
		for (var k in this.requests) this.requests[k].dispose();
		this.options = null;
		this.request = null;
		this.data = null;
		this.initials = null;
		this.activeRequests = null;
		this.requests = null;
	};
}
Controller();