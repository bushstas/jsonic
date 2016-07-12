function Component() {}

Component.prototype.initiate = function() {
	this.propActivities = {};
	this.propsToSet = {};
	this.rendered = false;
	this.disposed = false;
};

Component.prototype.render = function(parentElement) {
	this.parentElement = parentElement;
	this.processInitials();
	this.load();
};

Component.prototype.processInitials = function() {
	this.initials = this.initials || {};
	if (isObject(this.props['args'])) {
		this.receivedArgs = this.props['args'];
		delete this.props['args'];
	}
	var initials = this.initials;
	if (isObject(initials)) {
		for (var k in initials) {
			if (isArrayLike(initials[k])) {
				if (k == 'correctors') {
					for (var j in initials[k]) this.addCorrector(j, initials[k][j]);
				} else if (k == 'globals') {
					for (var j in initials[k]) Globals.subscribe(j, initials[k][j], this);
				} else if (k == 'followers') {
					for (var j in initials[k]) this.addFollower(j, initials[k][j]);
				} else if (k == 'controllers') {
					for (var i = 0; i < initials[k].length; i++) this.attachController(initials[k][i]);
				} else if (k == 'props') {
					Objects.merge(this.props, initials[k]);
				} else if (k == 'options') {
					this.initOptions(initials[k]);
				}
			}
		}
	}
};

Component.prototype.initOptions = function(options) {};

Component.prototype.processPostRenderInitials = function() {
	var helpers = this.getInitial('helpers');
	if (isArray(helpers)) {
		for (var i = 0; i < helpers.length; i++) this.subscribeToHelper(helpers[i]);
	}
};

Component.prototype.attachController = function(options) {
	if (isObject(options['on'])) {
		for (var k in options['on']) options.controller.subscribe(k, options['on'][k], this);
	}
};

Component.prototype.addCorrector = function(name, handler) {
	if (isFunction(handler)) {
		this.correctors = this.correctors || {};
		this.correctors[name] = handler;
	}
};

Component.prototype.addFollower = function(name, handler) {
	if (isFunction(handler)) {
		this.followers = this.followers || {};
		this.followers[name] = handler;
	}
};

Component.prototype.subscribeToHelper = function(options) {
	if (isObject(options['options'])) options['helper'].subscribe(this, options['options']);
};

Component.prototype.getInitial = function(initialName) {
	return Objects.get(this.initials, initialName);
};

Component.prototype.load = function() {
	var loader = this.getInitial('loader');
	if (isObject(loader) && isObject(loader['controller'])) {
		this.loader = loader['controller'];
		var isAsync = !!loader['async'];
		this.loader.subscribe('load', this.onDataLoad.bind(this, isAsync), this);
		var options = loader['options'];
		if (isFunction(options)) options = options();
		this.loader.doAction('load', options);
		if (!isAsync) {
			this.renderTempPlaceholder();
			return;
		}
	}
	this.onReadyToRender();
};

Component.prototype.renderTempPlaceholder = function() {
	this.tempPlaceholder = document.createElement('span');
	this.parentElement.appendChild(this.tempPlaceholder);
};

Component.prototype.onDataLoad = function(isAsync, data) {
	this.onLoaded(data);
	if (!isAsync) {
		this.onReadyToRender();
	}
};

Component.prototype.onReadyToRender = function() {
	if (!this.isRendered()) {
		this.doRendering();
		if (this.tempPlaceholder) {
			this.parentElement.removeChild(this.tempPlaceholder);
			this.tempPlaceholder = null;
		}
		this.processPostRenderInitials();
	}
};

Component.prototype.doRendering = function() {
	this.level = new Level();
	this.args = this.getCombinedArgs();
	var content = this.getTemplateMain(this, this.args);
	if (isArray(content)) {
		this.level.setComponent(this);
		this.level.render(content, this.parentElement, this, this.tempPlaceholder);
	}
	this.rendered = true;
	this.onRenderComplete();
	this.onRendered();
	if (isArray(this.callbacks)) {
		for (var i = 0; i < this.callbacks.length; i++) {
			if (isFunction(this.callbacks[i])) {
				this.callbacks[i]();
			}
		}
		this.callbacks = null;
	}
};

Component.prototype.getArgs = function() {
	return null;
};

Component.prototype.getCombinedArgs = function() {
	return Objects.merge({}, this.initials['args'], this.getArgs(), this.receivedArgs); 
};

Component.prototype.instanceOf = function(parent) {
	return this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(parent) > -1;
};

Component.prototype.dispatchEvent = function(eventType, eventParams) {
	if (isArray(this.listeners)) {
		for (var i = 0; i < this.listeners.length; i++) {
			if (this.listeners[i]['type'] == eventType) this.listeners[i]['handler'].call(this.listeners[i]['subscriber'] || null, eventParams);
		}
	}
};

Component.prototype.forEachChild = function(callback) {
	if (isArray(this.children)) {
		for (var i = 0; i < this.children.length; i++) callback.call(this, this.children[i], i);
	} else log('this.children is not an array');
};

Component.prototype.g = function(propName) {
	return this.get(propName);
};

Component.prototype.get = function(propName) {
	return this.propsToSet[propName] || this.props[propName];
};

Component.prototype.showElement = function(element, isShown) {
	if (isString(element)) element = this.findElement(element);
	if (isElement(element)) element.show(isShown);
};

Component.prototype.each = function(propName, callback) {
	var ar = this.get(propName);
	if (isArrayLike(ar) && isFunction(callback)) {
		if (isArray(ar)) {
			for (var i = 0; i < ar.length; i++) callback.call(this, ar[i], i, ar);
		} else {
			for (var k in ar) callback.call(this, ar[k], k, ar);
		}
	}
};

Component.prototype.toggle = function(propName) {
	this.set(propName, !this.get(propName));
};

Component.prototype.set = function(propName, propValue) {
	var props;
	if (!isUndefined(propValue)) {
		props = {};
		props[propName] = propValue;
	} else props = propName;
	var isChanged = false;
	var changedProps = {};
	var currentValue;
	for (var k in props) {
		if (Objects.has(this.correctors, k)) props[k] = this.correctors[k].call(this, props[k], props);
		currentValue = this.props[k];
		if (currentValue == props[k]) continue;
		if (isArray(currentValue) && isArray(props[k]) && Objects.equals(currentValue, props[k])) continue;
		isChanged = true;
		this.props[k] = props[k];
		changedProps[k] = props[k];
	}	
	if (this.isRendered()) {
		if (isChanged) this.propagatePropertyChange(changedProps);
		for (var k in changedProps) {
			if (Objects.has(this.followers, k)) this.followers[k].call(this);
		}
	}
	changedProps = null;
};

Component.prototype.propagatePropertyChange = function(changedProps) {
	var pn, pv, i, activities, cnds = [], ifsw = [];
	for (pn in changedProps) {
		pv = changedProps[pn];
		activities = this.propActivities['cnd'];
		if (activities && isArray(activities[pn])) {
			for (i = 0; i < activities[pn].length; i++) {
				if (cnds.indexOf(activities[pn][i]) == -1) {
					activities[pn][i].update();
					cnds.push(activities[pn][i]);
				}
			}
		}
		activities = this.propActivities['isw'];
		if (activities && isArray(activities[pn])) {
			for (i = 0; i < activities[pn].length; i++) {
				if (ifsw.indexOf(activities[pn][i]) == -1) {
					activities[pn][i].update();
					ifsw.push(activities[pn][i]);
				}
			}
		}
		activities = this.propActivities['swt'];
		if (activities && isArray(activities[pn])) {
			for (i = 0; i < activities[pn].length; i++) activities[pn][i].update(pv);
		}
		activities = this.propActivities['for'];
		if (activities && isArray(activities[pn])) {
			for (i = 0; i < activities[pn].length; i++) activities[pn][i].update(pv);
		}
		activities = this.propActivities['nod'];
		if (activities && isArray(activities[pn])) {
			var node;
			for (i = 0; i < activities[pn].length; i++) activities[pn][i].textContent = pv;
		}
		activities = this.propActivities['atr'];
		if (activities && isArray(activities[pn])) {
			var key, propAttr, attrParts;
			for (i = 0; i < activities[pn].length; i++) {
				key = activities[pn][i];
				attrParts = activities[pn][i][2]();
				var attrValue = '';
				var attrVal;
				for (var j = 0; j < attrParts.length; j++) {
					attrVal = isFunction(attrParts[j]) ? attrParts[j]() : attrParts[j];
					if (!isUndefined(attrVal)) attrValue += attrVal;
				}
				attrValue = attrValue.trim();
				var attrName = __A[activities[pn][i][1]] || activities[pn][i][1];
				activities[pn][i][0].attr(attrName, attrValue);
			}
		}
		activities = this.propActivities['cmp'];
		if (activities && isArray(activities[pn])) {
			var component, value;
			for (i = 0; i < activities[pn].length; i++) {
				component = activities[pn][i][0];
				value = activities[pn][i][1]();
				if (component) {
					if (activities[pn][i][2] && isObject(value)) component.refresh(value);
					else component.set(pn, value);
				}
			}
		}
	}
	cnds = null;
	ifsw = null;
};

Component.prototype.getFirstNodeChild = function() {
	if (this.level) return this.level.getFirstNodeChild();
	return null;
};

Component.prototype.preset = function(propName, propValue) {
	this.propsToSet[propName] = propValue;
};

Component.prototype.update = function() {
	this.set(this.propsToSet);
	this.propsToSet = {};
};

Component.prototype.refresh = function(args) {
	if (args) this.receivedArgs = args;
	this.unrender();
	this.doRendering();
};

Component.prototype.delay = function() {
	this.stopDelay();
	if (isFunction(arguments[0])) this.timeout = window.setTimeout(arguments[0].bind(this), arguments[1] || 200);
};

Component.prototype.stopDelay = function() {
	window.clearTimeout(this.timeout);
};

Component.prototype.onRendered = function() {};

Component.prototype.onLoaded = function() {};

Component.prototype.onRenderComplete = function() {};

Component.prototype.getTemplateMain = function() {
	return null;
};

Component.prototype.getTemplateByKey = function(key) {
	return null;
};

Component.prototype.addChild = function(child, parentElement) {
	this.level.renderComponent(child, parentElement);
};

Component.prototype.removeChild = function(child) {
	if (!child) return;
	if (isString(child)) {
		var child = this.getChildById(child);
		if (child) child.dispose();
	} else if (isObject(child)) {
		var childIndex = this.children.indexOf(child);
		if (childIndex > -1) {
			this.children.splice(childIndex, 1);
			child.dispose();
		}
	}
 };

Component.prototype.registerChildComponent = function(childComponent) {
	this.children = this.children || [];
	if (this.children.indexOf(childComponent) == -1) this.children.push(childComponent);
};

Component.prototype.setParent = function(parentalComponent) {
	this.parentalComponent = parentalComponent;
};

Component.prototype.getParent = function() {
	return this.parentalComponent;
};

Component.prototype.getChildAt = function(index) {
	return this.children[index];
};

Component.prototype.getChildById = function(childComponentId) {
	if (!this.children) return null;
	for (var i = 0; i < this.children.length; i++) {
		if (this.children[i].getId() == childComponentId) return this.children[i];
	}
	return null;
};

Component.prototype.doOnParentReady = function(callback, params) {
	this.getParent().addCallback(callback.bind(this, params));
};

Component.prototype.addCallback = function(callback) {
	this.callbacks = this.callbacks || [];
	this.callbacks.push(callback);
};

Component.prototype.setId = function(id) {
	this.componentId = id;
};

Component.prototype.getId = function() {
	return this.componentId;
};

Component.prototype.setHtmlContent = function(htmlContent) {
	if (isElement(this.scope)) this.scope.innerHTML = htmlContent;
};

Component.prototype.getElement = function() {
	return this.scope || this.parentElement;
};

Component.prototype.findElement = function(selector, scopeElement) {
	return (scopeElement || this.getElement()).querySelector(selector);
};

Component.prototype.findElementWithinParent = function(selector) {
	return this.getParent().findElement(selector);
};

Component.prototype.findElementsWithinParent = function(selector) {
	return this.getParent().findElements(selector);
};

Component.prototype.findElements = function(selector, scopeElement) {
	return Array.prototype.slice.call((scopeElement || this.scope || this.parentElement).querySelectorAll(selector));
};

Component.prototype.fill = function(element, data) {
	if (isString(element)) element = this.findElement(element);
	if (isElement(element)) {
		var callback = function(el) {
			for (var i = 0; i < el.childNodes.length; i++) {
				if (isElement(el.childNodes[i])) {
					callback(el.childNodes[i]);
				} else if (isText(el.childNodes[i]) && el.childNodes[i].hasOwnProperty('placeholderName') && !isUndefined(data[el.childNodes[i]['placeholderName']])) {
					el.childNodes[i].textContent = data[el.childNodes[i]['placeholderName']];
				}
			}
		};
		callback(element);
	}
};

Component.prototype.removeNode = function(node) {
	if (isString(node)) node = this.findElement(node);
	if (isNode(node) && node.parentNode == this.parentElement) this.parentElement.removeChild(node);
};

Component.prototype.getParentElement = function() {
	return this.parentElement;
};

Component.prototype.isRendered = function() {
	return this.rendered;
};

Component.prototype.isDisposed = function() {
	return this.disposed;
};

Component.prototype.addListener = function(target, eventType, handler) {
	if (isElement(target)) {
		this.eventHandler = this.eventHandler || new EventHandler();
		this.eventHandler.listen(target, eventType, handler.bind(this));
	} else target.subscribe(eventType, handler, this);
};

Component.prototype.subscribe = function(eventType, handler, subscriber) {
	this.listeners = this.listeners || [];
	this.listeners.push({'type': eventType, 'handler': handler, 'subscriber': subscriber});
};

Component.prototype.setAppended = function(isAppended) {
	if (this.level) this.level.setAppended(isAppended);
};

Component.prototype.setScope = function(scope) {
	this.scope = scope;
};

Component.prototype.log = function(message, method, opts) {
	log(message, method, this, opts);
};

Component.prototype.registerPropActivity = function(type, name, data) {
	this.propActivities = this.propActivities || {};
	this.propActivities[type] = this.propActivities[type] || {};
	this.propActivities[type][name] = this.propActivities[type][name] || [];
	this.propActivities[type][name].push(data);
	return this.propActivities[type][name].length - 1;
};

Component.prototype.disposePropActivities = function(type, data) {
	var activities = this.propActivities[type];
	if (isObject(data)) {
		var deleted;
		for (var pn in data) {
			if (isArray(activities[pn])) {
				deleted = 0;
				for (var i = 0; i < data[pn].length; i++) {
					activities[pn].splice(data[pn][i] - deleted, 1);
					deleted++;
				}
			}
		}
	}
};

Component.prototype.unrender = function() {
	this.disposeLinks();
	this.disposeInternal();
	this.level.dispose();
	if (this.eventHandler) {
		this.eventHandler.dispose();
		this.eventHandler = null;
	}
	this.level = null;
	this.listeners = null;
};

Component.prototype.dispose = function() {
	this.unrender();
	this.propActivities = null;
	this.parentElement = null;
	this.props = null;
	this.propsToSet = null;	
	this.provider = null;
	this.children = null;
	this.disposed = true;
	this.loader = null;
	this.initials = null;
	this.followers = null;
	this.correctors = null;
	this.receivedArgs = null;
	this.args = null;
	this.parentalComponent = null;
};

Component.prototype.disposeInternal = function() {};