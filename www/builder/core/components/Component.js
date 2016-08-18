function Component() {	
	if (this !== window) return;
	var load = function() {
		var loader = Objects.get(this.initials, 'loader');
		if (isObject(loader) && isObject(loader['controller'])) {
			this.loader = loader['controller'];
			var isAsync = !!loader['async'];
			this.loader.subscribe('load', onDataLoad.bind(this, isAsync), this);
			var options = loader['options'];
			if (isFunction(options)) options = options();
			this.loader.doAction('load', options);
			if (!isAsync) {
				renderTempPlaceholder.call(this);
				return;
			}
		}
		onReadyToRender.call(this);
	};

	var renderTempPlaceholder = function() {
		this.tempPlaceholder = document.createElement('span');
		this.parentElement.appendChild(this.tempPlaceholder);
	};

	var onDataLoad = function(isAsync, data) {
		this.onLoaded(data);
		if (!isAsync) onReadyToRender.call(this);
	};

	var onReadyToRender = function() {
		if (!this.isRendered()) {
			doRendering.call(this);
			if (this.tempPlaceholder) {
				this.parentElement.removeChild(this.tempPlaceholder);
				this.tempPlaceholder = null;
			}
			Initialization.processPostRenderInitials.call(this);
		}
	};

	var doRendering = function() {
		this.level = new Level();
		this.args = getCombinedArgs.call(this);
		var content = this.getTemplateMain(this.args, this);
		if (isArray(content)) {
			this.level.setComponent(this);
			this.level.render(content, this.parentElement, this, this.tempPlaceholder);
		}
		this.rendered = true;
		this.onRendered();
		if (isArray(this.callbacks)) {
			for (var i = 0; i < this.callbacks.length; i++) {
				if (isFunction(this.callbacks[i])) this.callbacks[i]();
			}
		}
		delete this.callbacks;
		delete this.waiting;
	};

	var getCombinedArgs = function() {
		return Objects.merge(Objects.get(this.initials, 'args'), this.getArgs(), this.args); 
	};

	var propagatePropertyChange = function(changedProps) {
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
		cnds = ifsw = null;
	};

	Component.prototype.initiate = function() {
		this.propActivities = {};
		this.propsToSet = {};
		this.rendered = this.disposed = this.disabled = false;
	};

	Component.prototype.render = function(parentElement) {
		this.parentElement = parentElement;
		load.call(this);
	};

	Component.prototype.disable = function(isDisabled) {
		this.disabled = isDisabled;
		this.addClass('->> disabled', !isDisabled);
	};
	
	Component.prototype.isDisabled = function() {
		return this.disabled;
	};

	Component.prototype.instanceOf = function(parent) {
		return this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(parent) > -1;
	};

	Component.prototype.dispatchEvent = function(eventType, eventParams) {
		if (isArray(this.listeners)) {
			for (var i = 0; i < this.listeners.length; i++) {
				if (isNumber(this.listeners[i]['type'])) this.listeners[i]['type'] = __EVENTTYPES[this.listeners[i]['type']];
				if (this.listeners[i]['type'] == eventType) this.listeners[i]['handler'].call(this.listeners[i]['subscriber'] || null, eventParams);
			}
		}
	};

	Component.prototype.provideWithComponent = function(propName, componentName, waitingChild) {
		var cmp = this.getChild(componentName);
		if (cmp) waitingChild.set(propName, cmp);
		else {
			this.waiting = this.waiting || {};
			this.waiting[componentName] = this.waiting[componentName] || [];
			this.waiting[componentName].push([waitingChild, propName]);
		}
	};

	Component.prototype.getWaitingChild = function(componentName) {
		return Objects.get(this.waiting, componentName);
	};

	Component.prototype.get = function(propName, keys) {
		var prop = this.propsToSet[propName] || this.props[propName];
		if (!keys || !isArrayLike(prop) || !isArray(keys)) return prop;
		var end;
		for (var i = 0; i < keys.length; i++) {
			prop = prop[keys[i]];
			if (isUndefined(prop)) return '';
			end = keys.length == i + 1;
			if (end || !isArrayLike(prop)) break;
		}
		return end ? prop || '' : '';
	};

	Component.prototype.showElement = function(element, isShown) {
		if (isString(element)) element = this.findElement(element);
		if (isElement(element)) element.show(isShown);
	};

	Component.prototype.setStyle = function(styles) {
		if (this.isRendered()) this.getElement().setStyle(styles);
	};

	Component.prototype.setVisible = function(isVisible) {
		if (this.isRendered() && !this.isDisposed()) this.getElement().show(isVisible);
	};

	Component.prototype.addClass = function(className, isAdding) {
		if (this.isRendered()) {
			if (isAdding || isUndefined(isAdding)) this.getElement().addClass(className);
			else this.getElement().removeClass(className);
		}
	};

	Component.prototype.each = function(propName, callback) {
		var ar = this.get(propName);
		if (isArrayLike(ar) && isFunction(callback)) {
			if (isArray(ar)) for (var i = 0; i < ar.length; i++) callback.call(this, ar[i], i, ar);
			else for (var k in ar) callback.call(this, ar[k], k, ar);
		}
	};

	Component.prototype.toggle = function(propName) {
		this.set(propName, !this.get(propName));
	};

	Component.prototype.set = function(propName, propValue) {
		this.props = this.props || {};
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
			if (isChanged) propagatePropertyChange.call(this, changedProps);
			for (var k in changedProps) {
				if (Objects.has(this.followers, k)) this.followers[k].call(this);
			}
		}
		changedProps = null;
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
		if (args) this.args = args;
		this.unrender();
		doRendering.call(this);
	};

	Component.prototype.delay = function(f, n) {
		window.clearTimeout(this.timeout);
		if (isFunction(f)) this.timeout = window.setTimeout(f.bind(this), n || 200);
	};

	Component.prototype.addChild = function(child, parentElement) {
		this.level.renderComponent(child, parentElement);
	};

	Component.prototype.removeChild = function(child) {
		if (!child) return;
		var childId = child;
		if (isString(child)) child = this.getChild(child);
		else childId = Objects.getKey(this.children, child);
		if (isComponentLike(child)) child.dispose();
		if ((isString(childId) || isNumber(childId)) && isObject(this.children)) delete this.children[childId];	
	 };

	Component.prototype.forEachChild = function(callback) {
		if (isArrayLike(this.children)) {
			var result;
			for (var k in this.children) {
				if (!this.children[k].isDisabled()) {
					result = callback.call(this, this.children[k], k);
					if (result) return result;
				}
			}
		}
	};

	Component.prototype.registerChildComponent = function(child) {
		this.childrenCount = this.childrenCount || 0;
		this.children = this.children || {};
		this.children[child.getId() || this.childrenCount] = child;
		this.childrenCount++;
	};

	Component.prototype.registerControl = function(control, name) {
	 	this.controls = this.controls || {};
	 	if (!isUndefined(this.controls[name])) {
	 		if (!isArray(this.controls[name])) this.controls[name] = [this.controls[name]];
	 		this.controls[name].push(control);
	 	} else this.controls[name] = control;
	 	control.setName(name);
	};

	Component.prototype.getControl = function(name) {
		return Objects.get(this.controls, name) || this.forEachChild(function(child) {
			return child.getControl(name);
		});
	};

	Component.prototype.setControlValue = function(name, value) {
		var control = this.getControl(name);
		if (control) control.setValue(value);
	};

	Component.prototype.enableControl = function(name, isEnabled) {
		var control = this.getControl(name);
		if (control) control.setEnabled(isEnabled);
	};

	Component.prototype.forEachControl = function(callback) {
		if (isObject(this.controls)) Objects.each(this.controls, callback, this);
	};

	Component.prototype.hasControls = function() {
		return !Objects.empty(this.controls);
	};

	Component.prototype.getControlsData = function(data) {
		data = data || {};
		this.forEachChild(function(child) {
			if (!isControl(child)) child.getControlsData(data);
			else data[child.getName()] = child.getValue();
		});
		return data;
	};

	Component.prototype.setControlsData = function(data) {
		this.forEachChild(function(child) {
			if (!isControl(child)) child.setControlsData(data);
			else child.setValue(data[child.getName()]);
		});
		return data;
	};

	Component.prototype.setParent = function(parentalComponent) {
		this.parentalComponent = parentalComponent;
	};

	Component.prototype.getParent = function() {
		return this.parentalComponent;
	};

	Component.prototype.getChildAt = function(index) {
		return Objects.getByIndex(this.children, index);
	};

	Component.prototype.getChildren = function(classFunc) {
		var children = [];
		this.forEachChild(function(child) {
			if (isComponentLike(child) && child.instanceOf(classFunc)) children.push(child);
		});
		return children;
	};

	Component.prototype.getChild = function(id) {
		return Objects.get(this.children, id);
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

	Component.prototype.getElement = function() {
		return this.scope || this.parentElement;
	};

	Component.prototype.findElement = function(selector, scopeElement) {
		return (scopeElement || this.getElement()).querySelector(selector);
	};

	Component.prototype.findElements = function(selector, scopeElement) {
		return Array.prototype.slice.call((scopeElement || this.scope || this.parentElement).querySelectorAll(selector));
	};

	Component.prototype.findElementWithinParent = function(selector) {
		return this.getParent().findElement(selector);
	};

	Component.prototype.findElementsWithinParent = function(selector) {
		return this.getParent().findElements(selector);
	};

	Component.prototype.fill = function(element, data) {
		if (isString(element)) element = this.findElement(element);
		if (isElement(element)) {
			var callback = function(el) {
				for (var i = 0; i < el.childNodes.length; i++) {
					if (isElement(el.childNodes[i])) {
						callback(el.childNodes[i]);
					} else if (isText(el.childNodes[i]) && !isUndefined(data[el.childNodes[i].placeholderName])) {
						el.childNodes[i].textContent = data[el.childNodes[i].placeholderName];
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

	Component.prototype.appendChild = function(child, isAppended) {
		if (isString(child)) child = this.getChild(child);
		if (isComponentLike(child)) child.setAppended(isAppended);
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

	Component.prototype.getTemplateById = function(tmpid) {
		if (isObject(this.templatesById)) return this.templatesById[tmpid];
		var parents = this.inheritedSuperClasses;
		if (isArrayLike(parents)) {
			for (var i = 0; i < parents.length; i++) {
				if (isObject(parents[i].prototype.templatesById) && isFunction(parents[i].prototype.templatesById[tmpid])) {
					return parents[i].prototype.templatesById[tmpid];
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
		this.level = this.listeners = null;
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
		this.controls = null;
		this.parentalComponent = null;
	};
	
	var f = function() {
		return null;
	};
	Component.prototype.initOptions = f;
	Component.prototype.onRendered = f;
	Component.prototype.onLoaded = f;
	Component.prototype.getTemplateMain = f;
	Component.prototype.disposeInternal = f;
	Component.prototype.getArgs = f;	
	Component.prototype.g = Component.prototype.get;
}
Component();