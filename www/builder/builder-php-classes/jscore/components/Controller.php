<?php

	$data = array(
		'name' => 'Controller',
		'condition' => CONST_ENTERCOND,
		'privateMethods' => array(
			'makeUrl' => array(
				'args' => array('url', 'options'),
				'body' => "
					var regExp, tmpUrl;
					for (var k in options) {
						if (isString(options[k]) || isNumber(options[k])) {
							regExp = new RegExp('\\$' + k);
							tmpUrl = url;
							url = url.replace(regExp, options[k]);
							if (tmpUrl != url) delete options[k];
						}
					}
					return url;
				"
			),
			'gotFromStore' => array(
				'args' => array('actionName', 'options', 'initiator'),
				'body' => "
					if (actionName == 'load' && shouldStore.call(this)) {
						var storeAs = getStoreAs.call(this, options);
						if (isString(storeAs) && typeof StoreKeeper != 'undefined') {
							var storedData = StoreKeeper.getActual(storeAs, ".CONST_OBJECTS.".get(this.options, 'storePeriod'));
							if (isArrayLike(storedData)) {
								onActionComplete.call(this, actionName, true, initiator, storedData);
								return true;
							}
						}
					}
					return false;
				"
			),
			'isPrivate' => array(
				'args' => array('initiator'),
				'body' => "
					return initiator && this.privateSubscribers.has(initiator.getUniqueId());
				"
			),
			'onActionComplete' => array(
				'args' => array('actionName', 'isFromStorage', 'initiator', 'data'),
				'body' => "
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
				"
			),
			'autoset' => array(
				'args' => array('opts', 'data', 'initiator'),
				'body' => "
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
				"
			),
			'shouldStore' => array(
				'body' => "
					var should = ".CONST_OBJECTS.".get(this.options, 'store');
					if (should === false) return false;
					return ".CONST_OBJECTS.".has(this.options, 'storeAs');
				"
			),
			'store' => array(
				'args' => array('isAdding', 'data'),
				'body' => "
					if (typeof StoreKeeper == 'undefined') return;
					var storeAs = getStoreAs.call(this, data);
					if (isAdding) {
						StoreKeeper.set(storeAs, data);
					} else {
						StoreKeeper.remove(storeAs);
					}
				"
			),
			'getStoreAs' => array(
				'args' => array('data'),
				'body' => "
					var storeAs = ".CONST_OBJECTS.".get(this.options, 'storeAs');
					if (isArrayLike(data) && isString(storeAs) && (/\$[a-z_]/i).test(storeAs)) {
						var parts = storeAs.split('$');
						storeAs = parts[0];
						for (var i = 1; i < parts.length; i++) {
							if (data[parts[i]]) storeAs += data[parts[i]];
							else storeAs += parts[i];
						}
					}
					return storeAs;
				"
			),
			'getPrimaryKey' => array(
				'body' => "
					return ".CONST_OBJECTS.".get(this.options, 'key', 'id');
				"
			),
			'initActionRouteOptions' => array(
				'args' => array('action'),
				'body' => "
					var value;
					this.currentRouteOptions = {};
					var routeOptions = {};
					for (var k in action['routeOptions']) {
						value = ".CONST_ROUTER.".getPathPartAt(action['routeOptions'][k]);
						if (isString(value)) {
							routeOptions[k] = value;
						}
					}
					setCurrentRouteOptions.call(this, routeOptions, action);
					".CONST_ROUTER.".subscribe(action['routeOptions'], this);
				"
			),
			'setCurrentRouteOptions' => array(
				'args' => array('routeOptions', 'action'),
				'body' => "
					this.currentRouteOptions = routeOptions;
					if (!isObject(action['options'])) {
						action['options'] = {};
					}
					for (var k in routeOptions) {
						action['options'][k] = routeOptions[k];
					}
				"
			),
			'getAction' => array(
				'args' => array('actionName'),
				'body' => "
					var actions = ".CONST_OBJECTS.".get(this.initials, 'actions');
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
				"
			),
			'getNewRequest' => array(
				'args' => array(''),
				'body' => "
					var ajr = ".CONST_GLOBAL.".get('AjaxRequest');
					return new ajr();
				"
			),
			'getRequest' => array(
				'args' => array('action'),
				'body' => "
					return this.requests[action['name']] = this.requests[action['name']] || getNewRequest();
				"
			),
			'getOptions' => array(
				'args' => array('options', 'action', 'initiator'),
				'body' => "
					if (!isObject(options)) options = {};
					if (isObject(action['options'])) ".CONST_OBJECTS.".merge(options, action['options']);
					if (isPrivate.call(this, initiator)) {
						".CONST_OBJECTS.".merge(options, getPrivateOptions.call(this, initiator));
					}
					return options;
				"
			),
			'getPrivateOptions' => array(
				'args' => array('initiator'),
				'body' => "
					return this.privateOptions[initiator.getUniqueId()];
				"
			),
			'send' => array(
				'args' => array('action', 'options', 'initiator'),
				'body' => "
					var url = makeUrl(action['url'], options);
					var req = getRequest.call(this, action);
					req.setCallback(onActionComplete.bind(this, action['name'], false, initiator));
					req.send(action['method'], options, url);
					this.activeRequests.push(action['name']);
				"
			)
		),
		'methods' => array(
			'initiate' => array(
				'body' => "
					this.subscribers = {};
					this.requests = {};
					this.activeRequests = [];
					this.privateSubscribers = [];
					this.privateOptions = {};
				"
			),
			'addSubscriber' => array(
				'args' => array('actionName', 'data', 'isPriv', 'options'),
				'body' => "
					this.subscribers[actionName] = this.subscribers[actionName] || [];
					this.subscribers[actionName].push(data);
					if (isPriv) {
						var uid = data['initiator'].getUniqueId();
						this.privateSubscribers.push(uid); 
						if (options) this.privateOptions[uid] = options;
					}
				"
			),
			'removeSubscriber' => array(
				'args' => array('initiator'),
				'body' => "
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
				"
			),
			'dispatchEvent' => array(
				'args' => array('actionName', 'data', 'initiator'),
				'body' => "
					var dataToDispatch = data;
					if (".CONST_OBJECTS.".has(this.options, 'clone', true)) dataToDispatch = ".CONST_OBJECTS.".clone(data);
					var s = this.subscribers[actionName], i, p;
					if (isArray(s)) {
						for (i = 0; i < s.length; i++) {
							p = (!initiator && !isPrivate.call(this, s[i]['initiator'])) || initiator == s[i]['initiator'];
							if (p && isFunction(s[i]['callback'])) {
								s[i]['callback'].call(s[i]['initiator'], dataToDispatch, this);
							}
						}
					}
				"
			),
			'instanceOf' => array(
				'args' => array('classFunc'),
				'body' => "
					if (isString(classFunc)) classFunc = ".CONST_GLOBAL.".get(classFunc);
					return this instanceof classFunc || (this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(classFunc) > -1);
				"
			),
			'getData' => array(
				'args' => array('actionName'),
				'body' => "
					return !!action && !!this.data && isObject(this.data) ? this.data[action] : this.data;
				"
			),
			'getItemById' => array(
				'args' => array('id'),
				'body' => "
					var primaryKey = getPrimaryKey.call(this);
					var data = this.data['load'];
					if (isArray(data)) {
						for (var i = 0; i < data.length; i++) {
							if (".CONST_OBJECTS.".has(data[i], primaryKey, id)) return data[i];
						}
					}
					return null;
				"
			),
			'getItem' => array(
				'args' => array('nameOrIndex', 'actionName'),
				'body' => "
					actionName = actionName || 'load';
					return isArrayLike(this.data[actionName]) ? this.data[actionName][nameOrIndex] : null;
				"
			),
			'doAction' => array(
				'args' => array('initiator', 'actionName', 'options'),
				'body' => "
					if (this.activeRequests.has(actionName)) return;
					var action = getAction.call(this, actionName);
					if (isObject(action) && !gotFromStore.call(this, actionName, options, initiator)) {
						options = getOptions.call(this, options, action, initiator);
						send.call(this, action, options, initiator);
					}
				"
			),
			'handleRouteOptionsChange' => array(
				'args' => array('routeOptions'),
				'body' => "
					if (!".CONST_OBJECTS.".equals(routeOptions, this.currentRouteOptions)) {
						setCurrentRouteOptions.call(this, routeOptions, getAction.call(this, 'load'));
						this.doAction(null, 'load');
					}
				"
			)
		),
		'overridableMethods' => array(),
		'templateCallableMethods' => array()
	);
?>