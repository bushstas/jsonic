function Controller() {}

Controller.prototype.initiate = function() {
	this.listeners = [];
};

Controller.prototype.processInitials = function() {
	var initials = this.initials;
	if (isObject(initials)) {
		for (var k in initials) {
			if (initials[k] && isObject(initials[k])) {
				if (k == 'globals') {

				} else if (k == 'options') {
					this.options = initials[k];
				} else if (k == 'controllers') {
					for (var i = 0; i < initials[k].length; i++) {
						this.attachController(initials[k][i]);
					}
				}
			}
		}
	}
};

Controller.prototype.attachController = function(options) {
	if (isObject(options['controller'])) {
		if (isObject(options['on'])) {
			for (var k in options['on']) {
				options.controller.subscribe(k, options['on'][k], this);
			}
		}
	}
};

Controller.prototype.subscribe = function(eventType, callback, subscriber) {
	this.listeners.push([eventType, callback, subscriber]);
};

Controller.prototype.unsubscribe = function(subscriber, eventType) {
	var done = false;
	while (!done) {
		done = true;
		for (var i = 0; i < this.listeners.length; i++) {
			if (this.listeners[i][2] == subscriber && (!eventType || this.listeners[i][0] == eventType)) {
				this.listeners.splice(i, 1);
				done = false;
				break;
			}
		}
	}
};

Controller.prototype.dispatchEvent = function(eventType, data) {
	var dataToDispatch = data;
	if (Objects.has(this.options, 'clone', true)) {
		dataToDispatch = Objects.clone(data);
	}
	if (isArray(this.listeners)) {
		for (var i = 0; i < this.listeners.length; i++) {
			if (this.listeners[i][0] == eventType && isFunction(this.listeners[i][1])) {
				this.listeners[i][1].call(this.listeners[i][2] || null, dataToDispatch, this);
			}
		}
	}
};

Controller.prototype.getData = function(actionName) {
	return !!action && !!this.data && isObject(this.data) ? this.data[action] : this.data;
};

Controller.prototype.getItemById = function(id) {
	var primaryKey = this.getPrimaryKey();
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
	var action = this.getAction(actionName);
	this.action = action;	
	if (actionName == 'load' && this.gotFromStore(options)) {
		return;
	}
	if (!isObject(options)) {
		options = {};
	}
	if (action && isObject(action) && action['options'] && isObject(action['options'])) {
		Objects.merge(options, action['options']);
	}
	var method = action['method'] || 'POST';
	url = url || this.makeUrl(action['url'], options);
	if (!url || !isString(url)) {
		log('url to execute the action ' + actionName + ' is invalid or empty', 'doAction', this, {action: action});
	}
	this.request = this.request || new AjaxRequest(url, this.onActionComplete.bind(this));
	this.request.send(method, options, url);
};

Controller.prototype.gotFromStore = function(options) {
	if (this.shouldStore()) {
		var storeAs = this.getStoreAs(options);
		if (isString(storeAs)) {
			var storedData = StoreKeeper.getActual(storeAs, Objects.get(this.options, 'storePeriod'));
			if (isArrayLike(storedData)) {
				this.onActionComplete(storedData, true);
				return true;
			}
		}
	}
	return false;
};

Controller.prototype.makeUrl = function(url, options) {
	var regExp;
	for (var k in options) {
		if (isString(options[k]) || isNumber(options[k])) {
			regExp = new RegExp('\\$' + k)
			url = url.replace(regExp, options[k]);
		}
	}
	return url;
};

Controller.prototype.onActionComplete = function(data, isFromStorage) {
	var actionName = this.action['name'];
	this.data = this.data || {};
	this.data[actionName] = data;
	if (isFunction(this.action['callback'])) {
		this.action['callback'].call(this, data);
	}
	this.dispatchEvent(actionName, data);
	if (!isFromStorage && actionName == 'load' && this.shouldStore()) {
		this.store(true, data);
	}
};

Controller.prototype.shouldStore = function() {
	var shouldStore = Objects.get(this.options, 'store');
	if (shouldStore === false) return false;
	return Objects.has(this.options, 'storeAs');
};

Controller.prototype.store = function(isAdding, data) {console.log('store')
	var storeAs = this.getStoreAs(data);
	if (isAdding) {		
		StoreKeeper.set(storeAs, data);
	} else {
		StoreKeeper.remove(storeAs);
	}
};

Controller.prototype.getStoreAs = function(data) {
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

Controller.prototype.getPrimaryKey = function() {
	return Objects.get(this.options, 'key', 'id');
};

Controller.prototype.getAction = function(actionName) {	
	var actions = Objects.get(this.initials, 'actions');
	if (isObject(actions)) {
		var action = actions[actionName];
		if (isObject(action)) {
			if (!isString(action['name'])) {
				if (isObject(action['routeOptions']) && actionName == 'load') {
					this.initActionRouteOptions(action);
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

Controller.prototype.initActionRouteOptions = function(action) {
	var value;
	this.currentRouteOptions = {};
	var routeOptions = {};
	for (var k in action['routeOptions']) {
		value = Router.getPathPartAt(action['routeOptions'][k]);
		if (isString(value)) {
			routeOptions[k] = value;
		}
	}
	this.setCurrentRouteOptions(routeOptions, action);
	Router.subscribe(action['routeOptions'], this);
};

Controller.prototype.setCurrentRouteOptions = function(routeOptions, action) {
	this.currentRouteOptions = routeOptions;
	if (!isObject(action['options'])) {
		action['options'] = {};
	}
	for (var k in routeOptions) {
		action['options'][k] = routeOptions[k];
	}
};

Controller.prototype.handleRouteOptionsChange = function(routeOptions) {
	if (!Objects.equals(routeOptions, this.currentRouteOptions)) {
		var action = this.getAction('load');
		this.setCurrentRouteOptions(routeOptions, action);
		this.doAction('load');
	}
};

Controller.prototype.dispose = function() {
	this.listeners = null;
	if (this.request) {
		this.request.dispose();
	}
	this.options = null;
	this.request = null;
	this.data = null;
	this.action = null;
	this.initials = null;
};