function Component() {}

Component.prototype.initiate = function() {
	this.propsToSet = {};
	this.provider = this.get.bind(this);
	this.followers = {};
	this.rendered = false;
	this.disposed = false;
};

Component.prototype.render = function(parentElement) {
	this.parentElement = parentElement;
	this.processInitials();
	this.load();
};

Component.prototype.processInitials = function() {
	var initials = this.initials;
	if (isObject(initials)) {
		for (var k in initials) {
			if (isArrayLike(initials[k])) {
				if (k == 'globals') {

				} else if (k == 'followers') {
					for (var j in initials[k]) {
						this.addFollower(j, initials[k][j]);
					}
				} else if (k == 'controllers') {
					for (var i = 0; i < initials[k].length; i++) {
						this.attachController(initials[k][i]);
					}
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
		for (var i = 0; i < helpers.length; i++) {
			this.subscribeToHelper(helpers[i]);
		}
	}
};

Component.prototype.attachController = function(options) {
	if (isObject(options['on'])) {
		for (var k in options['on']) {
			options.controller.subscribe(k, options['on'][k], this);
		}
	}
};

Component.prototype.addFollower = function(name, handler) {
	if (isFunction(handler)) {
		this.followers[name] = handler;
	}
};

Component.prototype.subscribeToHelper = function(options) {
	if (isFunction(options['handler'])) {
		options['helper'].subscribe(this, options['handler'], options['options']);
	}
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
		if (isFunction(options)) {
			options = options();
		}
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
		this.level = new Level();
		var content = this.getTemplateMain(this.provider, this.getArgs());
		if (isArray(content)) {
			this.level.render(content, this.parentElement, this, this.tempPlaceholder);
		}
		this.rendered = true;
		this.onRenderComplete();
		this.onRendered();
		if (this.tempPlaceholder) {
			this.parentElement.removeChild(this.tempPlaceholder);
			this.tempPlaceholder = null;
		}
		this.processPostRenderInitials();
	}
};

Component.prototype.getArgs = function() {
	return this.getInitial('args') || {};
};

Component.prototype.instanceOf = function(parent) {
	return this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(parent) > -1;
};

Component.prototype.dispatchEvent = function(eventType, eventParams) {
	if (isArray(this.listeners)) {
		for (var i = 0; i < this.listeners.length; i++) {
			if (this.listeners[i].type == eventType) {
				this.listeners[i].handler.call(this.listeners[i].subscriber || null, eventParams);
			}
		}
	}
};

Component.prototype.forEachChild = function(callback) {
	if (isArray(this.children)) {
		for (var i = 0; i < this.children.length; i++) {
			callback.call(this, this.children[i], i);
		}
	} else {
		log('this.children is not an array');
	}
};

Component.prototype.get = function(propName) {
	return this.propsToSet[propName] || this.props[propName];
};

Component.prototype.toggle = function(propName) {
	this.set(propName, !this.get(propName));
};

Component.prototype.set = function(propName, propValue) {
	var props;
	if (!isUndefined(propValue)) {
		props = {};
		props[propName] = propValue;
	} else {
		props = propName;
	}
	var isChanged = false;
	var changedProps = {};
	var currentValue;
	for (var k in props) {
		currentValue = this.props[k];
		if (currentValue == props[k]) continue;
		if (isArray(currentValue) && isArray(props[k])) {
			if (Objects.equals(currentValue, props[k])) continue;
		}
		isChanged = true;
		this.props[k] = props[k];
		changedProps[k] = props[k];
	}
	
	if (this.level && isChanged) {
		this.level.propagatePropertyChange(changedProps);
	}
	for (var k in changedProps) {
		if (!isUndefined(this.followers[k])) {
			this.followers[k].call(this);
		}
	}
	changedProps = null;
};

Component.prototype.getFirstNodeChild = function() {
	if (this.level) {
		return this.level.getFirstNodeChild();
	}
	return null;
};

Component.prototype.preset = function(propName, propValue) {
	this.propsToSet[propName] = propValue;
};

Component.prototype.fire = function() {
	for (var k in this.propsToSet) {
		this.set(k, this.propsToSet[k]);
		delete this.propsToSet[k];
	}
};

Component.prototype.delay = function() {
	this.stopDelay();
	if (isFunction(arguments[0])) {
		this.timeout = window.setTimeout(arguments[0].bind(this), arguments[1] || 200);
	}
};

Component.prototype.stopDelay = function() {
	window.clearTimeout(this.timeout);
};

Component.prototype.propagatePropertyChange = function() {};

Component.prototype.onRendered = function() {};

Component.prototype.onLoaded = function() {};

Component.prototype.onRenderComplete = function() {};

Component.prototype.getTemplateMain = function() {
	return null;
};

Component.prototype.addChild = function(child, parentElement) {
	this.level.renderComponent(child, parentElement);
};

Component.prototype.removeChild = function(child) {
	if (!child) return;
	if (isString(child)) {
		var child = this.getChildById(child);
		if (child) {
			child.dispose();
		}
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
	if (this.children.indexOf(childComponent) == -1) {
		this.children.push(childComponent);
	}
};

Component.prototype.getChildById = function(childComponentId) {
	if (!this.children) return null;
	for (var i = 0; i < this.children.length; i++) {
		if (this.children[i].getId() == childComponentId) {
			return this.children[i];
		}
	}
	return null;
};

Component.prototype.setId = function(id) {
	this.componentId = id;
};

Component.prototype.getId = function() {
	return this.componentId;
};

Component.prototype.getProvider = function() {
	return this.provider;
};

Component.prototype.getComponent = function() {
	return this;
};

Component.prototype.getElement = function() {
	return this.scope || this.parentElement;
};

Component.prototype.findElement = function(selector, scopeElement) {
	return (scopeElement || this.getElement()).querySelector(selector);
};

Component.prototype.findElements = function(selector, scopeElement) {
	return Array.prototype.slice.call((scopeElement || this.scope || this.parentElement).querySelectorAll(selector));
};

Component.prototype.removeNode = function(node) {
	if (isString(node)) {
		node = this.findElement(node);
	}
	if (isNode(node) && node.parentNode == this.parentElement) {
		this.parentElement.removeChild(node);
	}
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
	} else {
		target.subscribe(eventType, handler, this);
	}
};

Component.prototype.subscribe = function(eventType, handler, subscriber) {
	this.listeners = this.listeners || [];
	this.listeners.push({'type': eventType, 'handler': handler, 'subscriber': subscriber});
};

Component.prototype.setAppended = function(isAppended) {
	if (this.level) {
		this.level.setAppended(isAppended);
	}
};

Component.prototype.setScope = function(scope) {
	this.scope = scope;
};

Component.prototype.log = function(message, method, opts) {
	log(message, method, this, opts);
};

Component.prototype.dispose = function() {
	this.disposeLinks();
	this.disposeInternal();
	this.level.dispose();
	this.level = null;
	this.parentElement = null;
	this.props = null;
	this.propsToSet = null;	
	this.provider = null;
	this.children = null;
	this.disposed = true;
	this.listeners = null;
	this.loader = null;
	this.initials = null;
};

Component.prototype.disposeInternal = function() {};