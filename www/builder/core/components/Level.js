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
	} else if (item['i']) {
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
	var propNode = document.createTextNode(props['p'] || '');
	this.appendChild(propNode);	
	this.propNodes = this.propNodes || {};
	this.propNodesByProps = this.propNodesByProps || {};
	var propName = props['pr'];
	var key = generateRandomKey();
	this.propNodes[key] = propNode;
	this.propNodesByProps[propName] = this.propNodesByProps[propName] || [];
	this.propNodesByProps[propName].push(key);
};

Level.prototype.createElement = function(props) {
	var element = document.createElement(__TAGS[props['t']] || 'span');
	this.appendChild(element);
	if (isObject(props['p'])) {
		var attrName;		
		for (var k in props['p']) {
			if (isString(props['p'][k]) || isNumber(props['p'][k])) {
				attrName = __A[k] || k;
				if (attrName == 'scope') {
					this.parentLevel.setScope(element);
				} else {
					element.attr(attrName, props['p'][k]);
				}
			} else if (isFunction(props['p'][k])) {
				if (props['n'] && (isArray(props['n'][k]) || isString(props['n'][k]))) {
					this.propAttrs = this.propAttrs || {};
					this.propAttrsByProps = this.propAttrsByProps || {};
					var key = generateRandomKey();
					this.propAttrs[key] = [element, k, props['p'][k]];
					if (isString(props['n'][k])) {
						this.propAttrsByProps[props['n'][k]] = this.propAttrsByProps[props['n'][k]] || [];
						this.propAttrsByProps[props['n'][k]].push(key);
					} else {
						for (var i = 0; i < props['n'][k].length; i++) {					
							this.propAttrsByProps[props['n'][k][i]] = this.propAttrsByProps[props['n'][k][i]] || [];
							this.propAttrsByProps[props['n'][k][i]].push(key);
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
	if (params['i'] === true) {
		this.renderItems(params['c']);
	} else if (isFunction(params['i']) && isFunction(params['c'])) {
		var propNames = params['p'];
		if (isArray(propNames)) {
			this.conditions = this.conditions || {};
			this.conditionsByProps = this.conditionsByProps || {};
			var condition = new Condition(params);
			condition.render(this.parentElement, this);
			var key = generateRandomKey();
			this.conditions[key] = condition;
			for (var i = 0; i < propNames.length; i++) {
				this.conditionsByProps[propNames[i]] = this.conditionsByProps[propNames[i]] || [];
				this.conditionsByProps[propNames[i]].push(key);
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
		var key = generateRandomKey();
		this.foreaches[key] = foreach;
		this.foreachesByProps[propName] = this.foreachesByProps[propName] || [];
		this.foreachesByProps[propName].push(foreach);		
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
		var rawProps = item['p'];
		var props = {};
		var key, value, i, k, cmpid;
		if (isObject(rawProps)) {
			for (k in rawProps) {
				value = rawProps[k];
				if (k == 'cmpid') {
					cmpid = value;
					continue;
				}
				if (isFunction(rawProps[k])) {
					value = rawProps[k]();
					if (isArray(value)) {
						value = value.join('');
					}
					if (item['n'] && isObject(item['n']) && (isArray(item['n'][k]) || isString(item['n'][k]))) {
						this.propComps = this.propComps || {};
						this.propCompsByProps = this.propCompsByProps || {};
						key = key || generateRandomKey();
						if (isString(item['n'][k])) {
							this.propCompsByProps[item['n'][k]] = this.propCompsByProps[item['n'][k]] || [];
							if (this.propCompsByProps[item['n'][k]].indexOf(key) == -1) {
								this.propCompsByProps[item['n'][k]].push([key, rawProps[k]]);
							}
						} else {
							for (i = 0; i < item['n'][k].length; i++) {
								this.propCompsByProps[item['n'][k][i]] = this.propCompsByProps[item['n'][k][i]] || [];
								if (this.propCompsByProps[item['n'][k][i]].indexOf(key) == -1) {
									this.propCompsByProps[item['n'][k][i]].push([key, rawProps[k]]);
								}
							}
						}
					}
				}
				props[k] = value;
			}
		}
		var component = new item['cmp'](props);
		component.render(parentElement);
		this.registerChild(component, true);
		if (cmpid) {
			component.setId(cmpid);
		}
		if (key) {
			this.propComps[key] = component;
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

Level.prototype.registerChildComponent = function(childComponent) {
	this.parentLevel.registerChildComponent(childComponent);
};

Level.prototype.getComponent = function() {
	return this.parentLevel.getComponent();
};

Level.prototype.propagatePropertyChange = function(changedProps) {
	var propName, propValue, i;
	for (propName in changedProps) {
		propValue = changedProps[propName];
		if (this.conditionsByProps && isArray(this.conditionsByProps[propName])) {
			var conditionKey;
			for (i = 0; i < this.conditionsByProps[propName].length; i++) {
				conditionKey = this.conditionsByProps[propName][i];
				if (this.conditions[conditionKey]) {
					this.conditions[conditionKey].recheck();
				}
			}
		}
		if (this.foreachesByProps && isArray(this.foreachesByProps[propName])) {
			for (i = 0; i < this.foreachesByProps[propName].length; i++) {
				this.foreachesByProps[propName][i].update(propValue);
			}
		}
		if (this.propNodesByProps && isArray(this.propNodesByProps[propName])) {
			var node;
			for (i = 0; i < this.propNodesByProps[propName].length; i++) {
				node = this.propNodes[this.propNodesByProps[propName][i]];
				if (node) {
					node.textContent = propValue;
				}
			}
		}
		if (this.propAttrsByProps && isArray(this.propAttrsByProps[propName])) {
			var key, propAttr, attrParts;
			for (i = 0; i < this.propAttrsByProps[propName].length; i++) {
				key = this.propAttrsByProps[propName][i];
				propAttr = this.propAttrs[key];
				if (isArray(propAttr)) {
					attrParts = propAttr[2]();
					var attrValue = '';
					var attrVal;
					for (var j = 0; j < attrParts.length; j++) {
						attrVal = isFunction(attrParts[j]) ? attrParts[j]() : attrParts[j];
						if (!isUndefined(attrVal)) {
							attrValue += attrVal;
						}
					}
					attrValue = attrValue.trim();
					var attrName = __A[propAttr[1]] || propAttr[1];
					propAttr[0].attr(attrName, attrValue);
				}
			}
		}
		if (this.propCompsByProps && isArray(this.propCompsByProps[propName])) {
			var component, value;
			for (i = 0; i < this.propCompsByProps[propName].length; i++) {
				component = this.propComps[this.propCompsByProps[propName][i][0]];
				value = this.propCompsByProps[propName][i][1]();
				if (isArray(value)) {
					value = value.join('');
				}
				if (component) {
					component.set(propName, value);
				}
			}
		}
		for (i = 0; i < this.children.length; i++) {
			this.children[i].propagatePropertyChange(changedProps);
		}
	}
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

Level.prototype.dispose = function() {
	for (var i = 0; i < this.children.length; i++) {
		this.children[i].dispose();
	}
	if (this.eventHandler) {
		this.eventHandler.dispose();
	}
	this.disposeDom();
	this.conditions = null;
	this.foreaches = null;
	this.children = null;
	this.conditionsByProps = null;
	this.foreachesByProps = null;
	this.propNodes = null;
	this.propNodesByProps = null;
	this.propAttrs = null;
	this.propAttrsByProps = null;
	this.propComps = null;
	this.propCompsByProps = null;	
	this.parentElement = null;
	this.parentLevel = null;
	this.firstChild = null;
	this.firstNodeChild = null;
	this.lastNodeChild = null;
	this.eventHandler = null;
	this.realParentElement = null;
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