function Controller() {	
	if (this !== window) return;
	var makeUrl = function(url, options) {
		var regExp, tmpUrl;
		for (var k in options) {
			if (isString(options[k]) || isNumber(options[k])) {
				regExp = new RegExp('\\$' + k)
				tmpUrl = url;
				url = url.replace(regExp, options[k]);
				if (tmpUrl != url) delete options[k];
			}
		}
		return url;
	};
	var gotFromStore = function(actionName, options, initiator) {
		if (actionName == 'load' && shouldStore.call(this)) {
			var storeAs = getStoreAs.call(this, options);
			if (isString(storeAs) && typeof StoreKeeper != 'undefined') {
				var storedData = StoreKeeper.getActual(storeAs, Objects.get(this.options, 'storePeriod'));
				if (isArrayLike(storedData)) {
					onActionComplete.call(this, actionName, true, initiator, storedData);
					return true;
				}
			}
		}
		return false;
	};
	var isPrivate = function(initiator) {
		return initiator && this.privateSubscribers.has(initiator.getUniqueId());
	}
	var onActionComplete = function(actionName, isFromStorage, initiator, data) {
		this.activeRequests.removeItem(actionName);
		if (initiator && !isPrivate.call(this, initiator)) initiator = null;
		this.data = this.data || {};
		this.data[actionName] = data;
		var action = getAction.call(this, actionName);
		if (isObject(action) && isFunction(action['callback'])) {
			action['callback'].call(this, data);
		}
		if (action['autoset']) autoset.call(this, action['autoset'], data, initiator);
		this.dispatchEvent(actionName, data, initiator);
		if (!isFromStorage && actionName == 'load' && shouldStore.call(this)) {
			store.call(this, true, data);
		}
	};
	var autoset = function(opts, data, initiator) {
		var props = {};
		if (isString(opts)) {
			props[opts] = data; 
		} else if (isObject(opts)) {
			for (var k in opts) props[opts[k]] = data[k];
		}
		if (initiator) initiator.set(props);
		else if (isArray(this.subscribers['load'])) {
			for (var i = 0; i < this.subscribers['load'].length; i++) {
				this.subscribers['load'][i]['initiator'].set(props);
			}
		}
	};
	var shouldStore = function() {
		var should = Objects.get(this.options, 'store');
		if (should === false) return false;
		return Objects.has(this.options, 'storeAs');
	};
	var store = function(isAdding, data) {
		if (typeof StoreKeeper == 'undefined') return;
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
		}
		return null;
	};
	var getRequest = function(action) {
		return this.requests[action['name']] = this.requests[action['name']] || new AjaxRequest();
	};
	var getOptions = function(options, action, initiator) {
		if (!isObject(options)) options = {};
		if (isObject(action['options'])) Objects.merge(options, action['options']);
		if (isPrivate.call(this, initiator)) {
			Objects.merge(options, getPrivateOptions.call(this, initiator));
		}
		return options;
	};
	var getPrivateOptions = function(initiator) {
		return this.privateOptions[initiator.getUniqueId()];
	};
	var send = function(action, options, initiator) {
		var url = makeUrl(action['url'], options);
		var req = getRequest.call(this, action);
		req.setCallback(onActionComplete.bind(this, action['name'], false, initiator));
		req.send(action['method'], options, url);
		this.activeRequests.push(action['name']);		
	};

	Controller.prototype.initiate = function() {
		this.subscribers = {};
		this.requests = {};
		this.activeRequests = [];
		this.privateSubscribers = [];
		this.privateOptions = {};
	};

	Controller.prototype.addSubscriber = function(actionName, data, isPriv, options) {
		this.subscribers[actionName] = this.subscribers[actionName] || [];
		this.subscribers[actionName].push(data);
		if (isPriv) {
			var uid = data['initiator'].getUniqueId();
			this.privateSubscribers.push(uid); 
			if (options) this.privateOptions[uid] = options;
		}
	};

	Controller.prototype.removeSubscriber = function(initiator) {
		this.privateSubscribers.removeItem(initiator.getUniqueId());
		var done = false;
		for (var actionName in this.subscribers) {
			for (var i = 0; i < this.subscribers[actionName].length; i++) {
				if (this.subscribers[actionName][i]['initiator'] == initiator) {
					this.subscribers[actionName].splice(i, 1);
					break;
				}
			}
		}
	};

	Controller.prototype.dispatchEvent = function(actionName, data, initiator) {
		var dataToDispatch = data;
		if (Objects.has(this.options, 'clone', true)) dataToDispatch = Objects.clone(data);
		var s = this.subscribers[actionName], i, p;
		if (isArray(s)) {
			for (i = 0; i < s.length; i++) {
				p = (!initiator && !isPrivate.call(this, s[i]['initiator'])) || initiator == s[i]['initiator'];
				if (p && isFunction(s[i]['callback'])) {
					s[i]['callback'].call(s[i]['initiator'], dataToDispatch, this);
				}
			}
		}
	};

	Controller.prototype.instanceOf = function(classFunc) {
		return this instanceof classFunc || (this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(classFunc) > -1);
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


	Controller.prototype.doAction = function(initiator, actionName, options) {
		if (this.activeRequests.has(actionName)) return;
		var action = getAction.call(this, actionName);
		if (isObject(action) && !gotFromStore.call(this, actionName, options, initiator)) {
			options = getOptions.call(this, options, action, initiator);
			send.call(this, action, options, initiator);
		}
	};

	Controller.prototype.handleRouteOptionsChange = function(routeOptions) {
		if (!Objects.equals(routeOptions, this.currentRouteOptions)) {
			setCurrentRouteOptions.call(this, routeOptions, getAction.call(this, 'load'));
			this.doAction(null, 'load');
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
		this.currentRouteOptions = null;
		this.privateSubscribers = null;
		this.privateOptions = null;
	};
}
Controller();