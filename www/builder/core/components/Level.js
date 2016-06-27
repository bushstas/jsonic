function Level() {
	this.children = [];
	this.detached = false;
}

Level.prototype.render = function(items, parentElement, parentLevel, nextSiblingChild) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.nextSiblingChild = nextSiblingChild;
	this.renderItems(items);
	this.prevChild = null;
	this.nextSiblingChild = null;
};

Level.prototype.setComponent = function(component) {
	this.component = component;
};

Level.prototype.getComponent = function() {
	return this.component;
};

Level.prototype.renderItems = function(items) {
	if (isArray(items)) {
		for (var i = 0; i < items.length; i++) {
			this.renderItem(items[i]);
		}
	} else {
		this.renderItem(items);
	}
};

Level.prototype.renderItem = function(item) {
	if (!item && item !== 0) return;
	if (!isObject(item)) {
		this.createTextNode(item);
	} else if (!isUndefined(item['t'])) {
		this.createElement(item);
	} else if (!isUndefined(item['i'])) {
		this.createCondition(item);
	} else if (item['h']) {
		this.createForeach(item);
	} else if (!isUndefined(item['pr'])) {
		this.createPropertyNode(item);
	} else if (isFunction(item['tmp'])) {
		this.includeTemplate(item);
	} else if (item['cmp']) {
		this.renderComponent(item);
	}
};

Level.prototype.createLevel = function(items, parentElement) {
	var level = new Level();
	level.setComponent(this.component);
	level.render(items, parentElement, this);
	this.children.push(level);
};

Level.prototype.createTextNode = function(content) {
	if (content == '<br>') {
		this.appendChild(document.createElement('br'));
	} else {
		this.appendChild(document.createTextNode(content));
	}
};

Level.prototype.createPropertyNode = function(props) {
	var propName = props['pr'];
	var propNode = document.createTextNode(props['p'] || '');
	this.appendChild(propNode);	
	this.propNodes = this.propNodes || {};
	this.propNodes[propName] = this.propNodes[propName] || [];
	this.propNodes[propName].push(this.component.registerPropActivity('nod', propName, propNode));
};

Level.prototype.createElement = function(props) {
	var element = document.createElement(__TAGS[props['t']] || 'span');
	this.appendChild(element);
	if (isObject(props['p'])) {
		var attrName, pn, attr;		
		for (var k in props['p']) {
			if (isString(props['p'][k]) || isNumber(props['p'][k])) {
				attrName = __A[k] || k;
				if (attrName == 'scope') {
					this.parentLevel.setScope(element);
				} else {
					element.attr(attrName, props['p'][k]);
				}
			} else if (isFunction(props['p'][k])) {
				pn = props['n'][k];
				if (props['n'] && (isArray(pn) || isString(pn))) {
					this.propAttrs = this.propAttrs || {};
					attr = [element, k, props['p'][k]];					
					if (isString(pn)) {
						this.propAttrs[pn] = this.propAttrs[pn] || [];
						this.propAttrs[pn].push(this.component.registerPropActivity('atr', pn, attr));
					} else {
						for (var i = 0; i < pn.length; i++) {					
							this.propAttrs[pn[i]] = this.propAttrs[pn[i]] || [];
							this.propAttrs[pn[i]].push(this.component.registerPropActivity('atr', pn[i], attr));
						}
					}
				}
				var attrParts = props['p'][k]();
				if (!isArray(attrParts)) {
					attrParts = [attrParts];
				}
				var attrValue = '', partValue;
				for (i = 0; i < attrParts.length; i++) {
					partValue = isFunction(attrParts[i]) ? attrParts[i]() : attrParts[i];
					if (partValue) {
						attrValue += partValue;
					}
				}
				if (attrValue) {
					element.attr(__A[k] || k, attrValue);
				}
			}
		}
	}
	if (isArray(props['e'])) {
		var eventType, eventHandler;
		this.eventHandler = this.eventHandler || new EventHandler();
		for (i = 0; i < props['e'].length; i++) {
			eventType = __EVENTTYPES[props['e'][i]] || eventType;
			eventHandler = props['e'][i + 1];
			var isOnce = props['e'][i + 2] === true;
			if (isString(eventType) && isFunction(eventHandler)) {
				if (isOnce) {
					this.eventHandler.listenOnce(element, eventType, eventHandler);
					i++;
				} else {
					this.eventHandler.listen(element, eventType, eventHandler);
				}
			}
			i++;
		}
	}
	if (isArray(props['c'])) {
		if (props['c'].length == 1 && (isString(props['c'][0]) || isNumber(props['c'][0]))) {
			element.innerHTML = props['c'][0];
		} else {
			this.createLevel(props['c'], element);
		}
	} else if (isObject(props['c'])) {
		this.createLevel(props['c'], element);
	} else if (!isUndefined(props['c'])) {
		element.innerHTML = props['c'];
	}
};

Level.prototype.appendChild = function(child) {
	if (this.nextSiblingChild) {
		this.parentElement.insertBefore(child, this.nextSiblingChild);	
	} else {	
		this.parentElement.appendChild(child);	
	}
	this.registerChild(child);
};

Level.prototype.createCondition = function(params) {
	if (isBool(params['i'])) {
		if (params['i']) {
			this.renderItems(params['c']);
		} else if (!isUndefined(params['e'])) {
			this.renderItem(params['e']);
		}
	} else if (isFunction(params['i']) && isFunction(params['c'])) {
		var propNames = params['p'], pn;
		if (isArray(propNames)) {
			this.conditions = this.conditions || {};
			var condition = new Condition(params);
			condition.render(this.parentElement, this);
			for (var i = 0; i < propNames.length; i++) {
				pn = propNames[i];
				this.conditions[pn] = this.conditions[pn] || [];
				this.conditions[pn].push(this.component.registerPropActivity('cnd', pn, condition));
			}
			this.registerChild(condition);
		} else if (params['i']()) {
			this.renderItems(params['c']());
		} else if (isFunction(params['e'])) {
			this.renderItems(params['e']());
		}
	}
};

Level.prototype.createForeach = function(params) {
	var propName = params['f'];
	var isLocal = !propName;
	this.foreaches = this.foreaches || {};
	this.foreachesByProps = this.foreachesByProps || {};
		
	var foreach = new Foreach(params);
	foreach.render(this.parentElement, this);
	if (!isLocal) {
		this.foreaches = this.foreaches || {};
		this.foreaches[propName] = this.foreaches[propName] || [];
		this.foreaches[propName].push(this.component.registerPropActivity('for', propName, foreach));
	}
	this.registerChild(foreach);
};

Level.prototype.registerChild = function(child, isComponent) {
	var isNodeChild = isNode(child);
	if (this.prevChild) {
		this.prevChild.setNextSiblingChild(child);
	}
	this.prevChild = isNodeChild ? null : child;
	if (!this.firstChild) {
		this.firstChild = child;
	}	
	if (isNodeChild) {
		if (!this.firstNodeChild) {
			this.firstNodeChild = child;
		}
		this.lastNodeChild = child;
	} else {
		this.children.push(child);
	}
	if (isComponent) {
		this.registerChildComponent(child);
	}
};

Level.prototype.includeTemplate = function(item) {
	var component = this.getComponent();
	var items = item['tmp'].call(component, component.getProvider(), item['p']);
	if (isArray(items)) {
		for (var i = 0; i < items.length; i++) {
			this.renderItem(items[i]);
		}
	}
};

Level.prototype.renderComponent = function(item, parentElement) {
	parentElement = parentElement || this.parentElement;
	if (isFunction(item['cmp'])) {
		var props = {};
		var value, i, k, cmpid;
		var isProps = isObject(item['p']);
		if (isProps) {
			for (k in item['p']) {
				value = item['p'][k];
				if (k == 'cmpid') {
					cmpid = value;
					continue;
				}
				if (isFunction(item['p'][k])) {
					value = item['p'][k]();
				}
				props[k] = value;
			}
		}
		var component = new item['cmp'](props);
		component.render(parentElement);
		this.registerChild(component, true);
		if (isProps) {
			this.registerPropComps(component, item['n'], item['p']);
		}
		if (cmpid) {
			component.setId(cmpid);
		}
		var events = item['e'];
		if (isArray(events)) {
			for (i = 0; i < events.length; i++) {
				component.subscribe(events[i], events[i + 1], this);
				i++;	
			}
		}
	} else if (item && isObject(item)) {
		if (!item.isRendered()) {
			item.render(parentElement);
		}
		this.registerChild(item, true);
	}
};

Level.prototype.registerPropComps = function(component, names, props) {
	this.propComps = this.propComps || {};
	var data;
	for (var k in props) {
		data = [component, props[k], k == 'args'];
		if (isObject(names) && (isArray(names[k]) || isString(names[k]))) {
			if (isString(names[k])) {
				this.registerPropComp(names[k], data);
			} else {
				for (i = 0; i < names[k].length; i++) {
					this.registerPropComp(names[k][i], data);
				}
			}
		}
	}
};

Level.prototype.registerPropComp = function(pn, data) {
	this.propComps[pn] = this.propComps[pn] || [];
	this.propComps[pn].push(this.component.registerPropActivity('cmp', pn, data));
};

Level.prototype.registerChildComponent = function(childComponent) {
	this.parentLevel.registerChildComponent(childComponent);
};

Level.prototype.getComponent = function() {
	return this.parentLevel.getComponent();
};

Level.prototype.getParentElement = function() {
	return this.parentElement;
};

Level.prototype.getFirstNodeChild = function() {
	if (isNode(this.firstChild)) {
		return this.firstChild;
	}
	var firstLevel = this.children[0];
	if (firstLevel instanceof Level) {
		return firstLevel.getParentElement();
	} else if (firstLevel) {
		return firstLevel.getFirstNodeChild();
	}
	return null;
};

Level.prototype.disposeDom = function() {
	var elementsToDispose = this.getElements();
	for (var i = 0; i < elementsToDispose.length; i++) {
		this.parentElement.removeChild(elementsToDispose[i]);
	}
	elementsToDispose = null;
};

Level.prototype.setAppended = function(isAppended) {
	var isDetached = !isAppended;
	if (isDetached === this.detached) return;
	this.detached = isDetached;
	var elements = this.getElements();
	if (isDetached) {
		this.realParentElement = this.parentElement;
		this.parentElement = document.createElement('div'); 
		for (var i = 0; i < elements.length; i++) {
			this.parentElement.appendChild(elements[i]);
		}
	} else {
		this.nextSiblingChild = this.parentLevel.getNextSiblingChild();
		this.parentElement = this.realParentElement;
		this.realParentElement = null;
		for (var i = 0; i < elements.length; i++) {
			this.appendChild(elements[i]);
		}
	}
};

Level.prototype.getElements = function() {
	var elements = [];
	if (this.firstNodeChild && this.lastNodeChild) {
		var isAdding = false;
		for (var i = 0; i < this.parentElement.childNodes.length; i++) {
			if (this.parentElement.childNodes[i] == this.firstNodeChild) {
				isAdding = true;
			}
			if (isAdding) {
				elements.push(this.parentElement.childNodes[i]);
			}
			if (this.parentElement.childNodes[i] == this.lastNodeChild) {
				break;
			}
		}
	}
	return elements;
};

Level.prototype.dispose = function() {
	for (var i = 0; i < this.children.length; i++) {
		this.children[i].dispose();
	}
	if (this.eventHandler) {
		this.eventHandler.dispose();
		this.eventHandler = null;
	}
	this.disposeDom();
	if (this.propComps) {
		this.component.disposePropActivities('cmp', this.propComps);
		this.propComps = null;
	}
	if (this.conditions) {
		this.component.disposePropActivities('cnd', this.conditions);
		this.conditions = null;
	}
	if (this.foreaches) {
		this.component.disposePropActivities('for', this.foreaches);
		this.foreaches = null;
	}
	if (this.propNodes) {
		this.component.disposePropActivities('nod', this.propNodes);
		this.propNodes = null;
	}
	if (this.propAttrs) {
		this.component.disposePropActivities('atr', this.propAttrs);
		this.propAttrs = null;
	}
	this.children = null;
	this.parentElement = null;
	this.parentLevel = null;
	this.firstChild = null;
	this.firstNodeChild = null;
	this.lastNodeChild = null;
	this.realParentElement = null;
	this.component = null;
};