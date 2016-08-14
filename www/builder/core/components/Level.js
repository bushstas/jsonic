function Level() {
	var self = this, parentElement, realParentElement, parentLevel,
		nextSiblingChild, children = [], detached = false,
		prevChild, firstChild, component, propAttrs, propNodes,
		eventHandler, firstNodeChild, lastNodeChild, foreaches,
		conditions, ifSwitches, switches, propComps;

	var renderItems = function(items) {
		if (isArray(items)) {
			for (var i = 0; i < items.length; i++) renderItem(items[i]);
		} else renderItem(items);
	};

	var renderItem = function(item) {
		if (!item && item !== 0) return;
		if (isFunction(item)) {
			renderItems(item());
			return;
		}
		if (!isObject(item)) createTextNode(item);
		else if (item.hasOwnProperty('t')) createElement(item);
		else if (item.hasOwnProperty('pr')) createPropertyNode(item);
		else if (item.hasOwnProperty('i')) createCondition(item);
		else if (isFunction(item['h'])) createForeach(item);	
		else if (item.hasOwnProperty('tmp')) includeTemplate(item);
		else if (item.hasOwnProperty('cmp')) renderComponent(item);
		else if (item.hasOwnProperty('is')) createIfSwitch(item);
		else if (item.hasOwnProperty('sw')) createSwitch(item);
		else if (item.hasOwnProperty('pl')) createPlaceholder(item);
	};

	var createLevel = function(items, pe) {
		var level = new Level();
		level.setComponent(component);
		level.render(items, pe, self);
		children.push(level);
	};

	var createTextNode = function(content) {
		if (content == '<br>') appendChild(document.createElement('br'));
		else appendChild(document.createTextNode(content));
	};

	var createPropertyNode = function(props) {
		var name = props['pr'];
		var node = document.createTextNode(props['p'] || '');
		appendChild(node);
		propNodes = propNodes || {};
		propNodes[name] = propNodes[name] || [];
		propNodes[name].push(component.registerPropActivity('nod', name, node));
	};

	var createElement = function(props) {
		var element = document.createElement(__TAGS[props['t']] || 'span');
		appendChild(element);
		if (isObject(props['p'])) {
			var attrName, pn, attr;		
			for (var k in props['p']) {
				if (isString(props['p'][k]) || isNumber(props['p'][k])) {
					attrName = __A[k] || k;
					if (attrName == 'scope') component.setScope(element);
					else element.attr(attrName, props['p'][k]);
				} else if (isFunction(props['p'][k])) {
					pn = props['n'][k];
					if (props['n'] && (isArray(pn) || isString(pn))) {
						propAttrs = propAttrs || {};
						attr = [element, k, props['p'][k]];					
						if (isString(pn)) {
							propAttrs[pn] = propAttrs[pn] || [];
							propAttrs[pn].push(component.registerPropActivity('atr', pn, attr));
						} else {
							for (var i = 0; i < pn.length; i++) {					
								propAttrs[pn[i]] = propAttrs[pn[i]] || [];
								propAttrs[pn[i]].push(component.registerPropActivity('atr', pn[i], attr));
							}
						}
					}
					var attrParts = props['p'][k]();
					if (!isArray(attrParts)) attrParts = [attrParts];
					var attrValue = '', partValue;
					for (i = 0; i < attrParts.length; i++) {
						partValue = isFunction(attrParts[i]) ? attrParts[i]() : attrParts[i];
						if (partValue) attrValue += partValue;
					}
					if (attrValue) element.attr(__A[k] || k, attrValue);
				}
			}
		}
		if (isArray(props['e'])) {
			var eventType, callback;
			eventHandler = eventHandler || new EventHandler();
			for (i = 0; i < props['e'].length; i++) {
				eventType = __EVENTTYPES[props['e'][i]] || eventType;
				callback = props['e'][i + 1];
				var isOnce = props['e'][i + 2] === true;
				if (isString(callback)) callback = component.dispatchEvent.bind(component, callback);
				if (isString(eventType) && isFunction(callback)) {					
					if (isOnce) {
						eventHandler.listenOnce(element, eventType, callback.bind(component));
						i++;
					} else eventHandler.listen(element, eventType, callback.bind(component));
				}
				i++;
			}
		}
		if (isArray(props['c'])) {
			if (props['c'].length == 1 && (isString(props['c'][0]) || isNumber(props['c'][0]))) element.innerHTML = props['c'][0];
			else createLevel(props['c'], element);
		} else if (isObject(props['c'])) {
			createLevel(props['c'], element);
		} else if (!isUndefined(props['c'])) {
			element.innerHTML = props['c'];
		}
	};

	var appendChild = function(child) {
		if (nextSiblingChild) parentElement.insertBefore(child, nextSiblingChild);	
		else parentElement.appendChild(child);	
		registerChild(child);
	};

	var createCondition = function(params) {
		if (isBool(params['i'])) {
			if (params['i']) {
				renderItems(params['c']);
			} else if (!isUndefined(params['e'])) {
				renderItem(params['e']);
			}
		} else if (isFunction(params['i']) && isFunction(params['c'])) {
			var propNames = params['p'], pn;
			if (isArray(propNames)) {
				conditions = conditions || {};
				var condition = new Condition(params);
				condition.render(parentElement, self);
				for (var i = 0; i < propNames.length; i++) {
					pn = propNames[i];
					conditions[pn] = conditions[pn] || [];
					conditions[pn].push(component.registerPropActivity('cnd', pn, condition));
				}
				registerChild(condition);
			} else if (params['i']()) {
				renderItems(params['c']());
			} else if (isFunction(params['e'])) {
				renderItems(params['e']());
			}
		}
	};

	var createForeach = function(params) {
		var propName = params['f'];
		var isLocal = !propName;
		if (!isLocal) {
			var foreach = new Foreach(params);
			foreach.render(parentElement, self);
			foreaches = foreaches || {};
			foreaches[propName] = foreaches[propName] || [];
			foreaches[propName].push(component.registerPropActivity('for', propName, foreach));
			registerChild(foreach);
		} else {
			if (isArray(params['p'])) {
				for (var i = 0; i < params['p'].length; i++) renderItems(params['h'](params['p'][i], i));
			} else if (isObject(params['p'])) {
				for (var k in params['p']) renderItems(params['h'](params['p'][k], k));
			}
		}
	};

	var createIfSwitch = function(params) {
		var propNames = params['p'];
		var isLocal = !isArray(propNames) || isUndefined(propNames[0]);
		if (!isLocal) {
			var swtch = new IfSwitch(params);
			swtch.render(parentElement, self);
			ifSwitches = ifSwitches || {};
			for (var i = 0; i < propNames.length; i++) {
				ifSwitches[propNames[i]] = ifSwitches[propNames[i]] || [];
				ifSwitches[propNames[i]].push(component.registerPropActivity('isw', propNames[i], swtch));
			}
		} else {
			for (var i = 0; i < params['is'].length; i++) {
				if (!!params['is'][i]) {
					renderItems(params['c'][i]);
					return;
				}
			}
			if (isArray(params['d'])) renderItems(params['d']);
		}
	};

	var createSwitch = function(params) {
		var propName = params['p'];
		var isLocal = !propName;
		if (!isLocal) {
			var swtch = new Switch(params);
			swtch.render(parentElement, self);
			switches = switches || {};
			switches[propName] = switches[propName] || [];
			switches[propName].push(component.registerPropActivity('swt', propName, swtch));
		} else {
			for (var i = 0; i < params['s'].length; i++) {
				if (params['sw'] === params['s'][i]) {
					renderItems(params['c'][i]);
					return;
				}
			}
			if (isArray(params['d'])) renderItems(params['d']);
		}
	};

	var createPlaceholder = function(params) {
		var placeholderNode = document.createTextNode('');
		if (isString(params['d'])) placeholderNode.textContent = params['d'];
		placeholderNode.placeholderName = params['pl'];
		appendChild(placeholderNode);	
	};

	var registerChild = function(child, isComponent) {
		var isNodeChild = isNode(child);
		if (prevChild) prevChild.setNextSiblingChild(child);
		prevChild = isNodeChild ? null : child;
		if (!firstChild) firstChild = child;
		if (isNodeChild) {
			if (!firstNodeChild) firstNodeChild = child;
			lastNodeChild = child;
		} else children.push(child);
		if (isComponent) component.registerChildComponent(child);
	};

	var includeTemplate = function(item) {
		var args = item['p'];
		if (isObject(args) && isObject(args['args'])) {
			tempArgs = args['args'];
			delete args['args'];
			for (var k in args) tempArgs[k] = args[k];
			args = tempArgs;
		}
		if (isString(item['tmp'])) item['tmp'] = component.getTemplateById(item['tmp']);
		if (isFunction(item['tmp'])) {		
			var items = item['tmp'].call(component, args, component);
			if (isArray(items)) {
				for (var i = 0; i < items.length; i++) renderItem(items[i]);
			}
		}
	};

	var renderComponent = function(item, pe) {
		pe = pe || parentElement;
		if (isFunction(item['cmp'])) {
			var cmp = new item['cmp']();
			var i, k, p = item['p'];
			var props, args, data;
			if (isObject(p)) {
				props = initComponentProps(p['p'], p['ap'], item['n'], cmp);
				args = initComponentProps(p['a'], p['aa'], item['na'], cmp, true);
				if (isString(p['i'])) {
					cmp.setId(p['i']);
					var waiting = component.getWaitingChild(p['i']);
					if (isArray(waiting)) {
						for (i = 0; i < waiting.length; i++) {
							waiting[i][0].set(waiting[i][1], cmp);
						}
					}
				}				
			}
			if (isArray(item['w'])) {
				for (i = 0; i < item['w'].length; i += 2) {
					component.provideWithComponent(item['w'][i], item['w'][i + 1], cmp);
				}
			}
			Initialization.initiate.call(cmp, props, args);
			cmp.setParent(component);
			cmp.render(pe);
			registerChild(cmp, true);			
			var events = item['e'];
			if (isArray(events)) {
				for (i = 0; i < events.length; i++) {
					if (isString(events[i + 1])) events[i + 1] = component.dispatchEvent.bind(component, events[i + 1]);
					cmp.subscribe(events[i], events[i + 1], component);
					i++;	
				}
			}
			if (item['nm']) component.registerControl(cmp, item['nm']);
		} else if (item && isObject(item)) {
			if (!item.isRendered()) item.render(pe);
			registerChild(item, true);
		}
	};

	var initComponentProps = function(p, ap, n, cmp, isArgs) {
		var props = {}, k, isReactive;
		var f = function(pr) {
			if (isObject(pr)) {
				for (k in pr) {
					isReactive = Objects.has(n, k) && isFunction(pr[k]);
					props[k] = isReactive ? pr[k]() : pr[k];
					if (isReactive) {
						registerPropComp(n[k], [cmp, pr[k], isArgs]);
					}
				}
			}
		};
		f(p); f(ap); 
		return props;
	};

	var registerPropComps = function(cmp, propNames, argNames, props) {
		propComps = propComps || {};
		var data;
		for (var k in props) {
			data = [cmp, props[k], k == 'args'];
			if (isObject(names) && (isArray(names[k]) || isString(names[k]))) {
				if (isString(names[k])) {
					registerPropComp(names[k], data);
				} else {
					for (i = 0; i < names[k].length; i++) registerPropComp(names[k][i], data);
				}
			}
		}
	};

	var registerPropComp = function(pn, data) {
		propComps[pn] = propComps[pn] || [];
		propComps[pn].push(component.registerPropActivity('cmp', pn, data));
	};

	var disposeDom = function() {
		var elementsToDispose = getElements();
		for (var i = 0; i < elementsToDispose.length; i++) parentElement.removeChild(elementsToDispose[i]);
		elementsToDispose = null;
	};

	var getElements = function() {
		var elements = [];
		if (firstNodeChild && lastNodeChild) {
			var isAdding = false;
			for (var i = 0; i < parentElement.childNodes.length; i++) {
				if (parentElement.childNodes[i] == firstNodeChild) isAdding = true;
				if (isAdding) elements.push(parentElement.childNodes[i]);
				if (parentElement.childNodes[i] == lastNodeChild) break;
			}
		}
		return elements;
	};

	this.render = function(items, pe, pl, nsc) {
		parentElement = pe;
		parentLevel = pl;
		nextSiblingChild = nsc;
		renderItems(items);
		prevChild = null;
		nextSiblingChild = null;
	};

	this.getParentElement = function() {
		return parentElement;
	};

	this.getFirstNodeChild = function() {
		if (isNode(firstChild)) return firstChild;
		var firstLevel = children[0];
		if (firstLevel instanceof Level) {
			return firstLevel.getParentElement();
		} else if (firstLevel) {
			return firstLevel.getFirstNodeChild();
		}
		return null;
	};

	this.setComponent = function(c) {
		component = c;
	};

	this.getComponent = function() {
		return component;
	};

	this.setAppended = function(isAppended) {
		var isDetached = !isAppended;
		if (isDetached === !!this.detached) return;
		this.detached = isDetached;
		var elements = getElements();
		if (isDetached) {
			realParentElement = parentElement;
			parentElement = document.createElement('div'); 
			for (var i = 0; i < elements.length; i++) parentElement.appendChild(elements[i]);
		} else {
			nextSiblingChild = parentLevel.getNextSiblingChild();
			parentElement = realParentElement;
			realParentElement = null;
			for (var i = 0; i < elements.length; i++) appendChild(elements[i]);
		}
	};

	this.dispose = function() {
		for (var i = 0; i < children.length; i++) children[i].dispose();
		if (eventHandler) {
			eventHandler.dispose();
			eventHandler = null;
		}
		disposeDom();
		if (propComps) {
			component.disposePropActivities('cmp', propComps);
			propComps = null;
		}
		if (conditions) {
			component.disposePropActivities('cnd', conditions);
			conditions = null;
		}
		if (foreaches) {
			component.disposePropActivities('for', foreaches);
			foreaches = null;
		}
		if (propNodes) {
			component.disposePropActivities('nod', propNodes);
			propNodes = null;
		}
		if (propAttrs) {
			component.disposePropActivities('atr', propAttrs);
			propAttrs = null;
		}
		if (ifSwitches) {
			component.disposePropActivities('isw', ifSwitches);
			ifSwitches = null;
		}
		if (switches) {
			component.disposePropActivities('swt', switches);
			switches = null;
		}
		children = null;
		parentElement = null;
		parentLevel = null;
		firstChild = null;
		firstNodeChild = null;
		lastNodeChild = null;
		realParentElement = null;
		component = null;
	};
}