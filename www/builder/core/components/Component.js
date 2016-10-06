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
			Core.processPostRenderInitials.call(this);
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
		this.onRenderComplete();
		this.forEachChild(function(child) {
			if (isFunction(child.onParentRendered)) child.onParentRendered.call(child);
		});
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
						if (activities[pn][i][3] && isObject(value)) refresh.call(this,  value);
						else component.set(activities[pn][i][2], value);
					}
				}
			}
		}
		cnds = ifsw = null;
		callFollowers.call(this, changedProps);
	};

	var callFollowers = function(changedProps) {
		for (var k in changedProps) {
			callFollower.call(this, k, changedProps[k]);
		}
	};

	var callFollower = function(propName, propValue) {
		if (Objects.has(this.followers, propName)) this.followers[propName].call(this, propValue);
	};

	var refresh = function(args) {
		if (args) this.args = args;
		unrender.call(this);
		doRendering.call(this);
	};

	var unrender = function() {
		this.elements = null;
		Core.disposeLinks.call(this);
		this.disposeInternal();
		this.level.dispose();
		this.level = this.listeners = null;
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

	Component.prototype.isDisabled = function() {
		return this.disabled;
	};

	Component.prototype.isRendered = function() {
		return this.rendered;
	};

	Component.prototype.isDisposed = function() {
		return this.disposed;
	};

	Component.prototype.instanceOf = function(classFunc) {
		return this instanceof classFunc || (this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(classFunc) > -1);
	};

	Component.prototype.disable = function(isDisabled) {
		this.disabled = isDisabled;
		this.addClass('->> disabled', !isDisabled);
	};

	Component.prototype.dispatchEvent = function(eventType, eventParams) {
		if (isArray(this.listeners)) {
			for (var i = 0; i < this.listeners.length; i++) {
				if (isNumber(this.listeners[i]['type'])) this.listeners[i]['type'] = __EVENTTYPES[this.listeners[i]['type']];
				if (this.listeners[i]['type'] == eventType) {
					this.listeners[i]['handler'].call(this.listeners[i]['subscriber'] || null, eventParams, this);
				}
			}
		}
	};

	Component.prototype.removeValueFrom = function(propName, value) {
		var prop = this.get(propName);
		if (isArray(prop)) this.removeByIndexFrom(propName, prop.indexOf(value));
	};

	Component.prototype.removeByIndexFrom = function(propName, index) {
		var prop = this.get(propName);
		if (isString(index) && isNumeric(index)) index = ~~index;
		if (isArray(prop) && isNumber(index) && index > -1 && !isUndefined(prop[index])) {
			prop.splice(index, 1);
			var activities = this.propActivities['for'];
			if (activities && isArray(activities[propName])) {
				for (i = 0; i < activities[propName].length; i++) activities[propName][i].remove(index);
			}
			callFollower.call(this, propName, prop);
		}
	};

	Component.prototype.plusTo = function(propName, add, sign) {
		var prop = this.get(propName);
		if (!sign || sign == '+') {
			if (isNumber(prop) || isString(prop)) this.set(propName, prop + add);
		} else 	if (isNumber(prop) && isNumber(add)) {
			var v;
			if (sign == '-') v = prop - add;
			else if (sign == '*') v =  prop * add;
			else if (sign == '/') v = prop / add;
			else if (sign == '%') v = prop % add;
			this.set(propName, v);
		}
	};

	Component.prototype.addOneTo = function(propName, item, index) {
		this.addTo(propName, [item], index);
	};

	Component.prototype.addTo = function(propName, items, index) {
		var prop = this.get(propName);
		if (!isArray(items)) items = [items];
		if (isArray(prop)) {
			for (var j = 0; j < items.length; j++) {
				if (!isNumber(index)) prop.push(items[j]);
				else if (index == 0) prop.unshift(items[j]);
				else prop.insertAt(items[j], index);
				var activities = this.propActivities['for'];
				if (activities && isArray(activities[propName])) {
					for (i = 0; i < activities[propName].length; i++) activities[propName][i].add(items[j], index);
				}
				if (isNumber(index)) index++;
			}
			callFollower.call(this, propName, prop);
		}
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

	Component.prototype.setPosition = function(x, y) {
		this.setStyle({'top': y + 'px', 'left': x + 'px'});
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
		}
		changedProps = null;
	};

	Component.prototype.preset = function(propName, propValue) {
		this.propsToSet[propName] = propValue;
	};

	Component.prototype.update = function() {
		this.set(this.propsToSet);
		this.propsToSet = {};
	};

	Component.prototype.delay = function(f, n, p) {
		window.clearTimeout(this.timeout);
		if (isFunction(f)) this.timeout = window.setTimeout(f.bind(this, p), n || 200);
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
	
	Component.prototype.forChildren = function(classFunc, callback) {
		var children = this.getChildren(classFunc), result;
		for (var i = 0; i < children.length; i++) {
			result = callback.call(this, children[i], i);
			if (result) return result;
		}
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

	Component.prototype.getParent = function() {
		return this.parentalComponent;
	};

	Component.prototype.getChildAt = function(index) {
		return Objects.getByIndex(this.children, index);
	};

	Component.prototype.getChildIndex = function(child, same) {
		var idx = -1;
		this.forEachChild(function(ch) {
			if (!same || (same && ch.constructor == child.constructor)) idx++;
			if (ch == child) return true;
		});
		return idx;
	};

	Component.prototype.getChildren = function(classFunc) {
		if (!isFunction(classFunc)) return this.children;
		var children = [];
		this.forEachChild(function(child) {
			if (isComponentLike(child) && child.instanceOf(classFunc)) children.push(child);
		});
		return children;
	};

	Component.prototype.getChild = function(id) {
		return Objects.get(this.children, id);
	};

	Component.prototype.setId = function(id) {
		this.componentId = id;
	};

	Component.prototype.getId = function() {
		return this.componentId;
	};

	Component.prototype.getElement = function(id) {
		if (isString(id)) return Objects.get(this.elements, id);
		else return this.scope || this.parentElement;
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

	Component.prototype.setAppended = function(isAppended) {
		if (this.level) this.level.setAppended(isAppended);
	};

	Component.prototype.placeTo = function(element) {
		if (this.level) this.level.placeTo(element);
	};

	Component.prototype.placeBack = function() {
		this.setAppended(true);
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

	Component.prototype.getUniqueId = function() {
		return this.uniqueId = this.uniqueId || generateRandomKey();
	};

	Component.prototype.dispose = function() {
		unrender.call(this);
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
	Component.prototype.onRenderComplete = f;
	Component.prototype.onLoaded = f;
	Component.prototype.getTemplateMain = f;
	Component.prototype.disposeInternal = f;
	Component.prototype.getArgs = f;	
	Component.prototype.g = Component.prototype.get;
}
Component();