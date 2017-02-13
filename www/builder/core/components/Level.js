_c = function(component) {
	var tagsList = {{TAGS}};
	var attrNames = {{ATTRIBUTES}};
	var eventTypes = {{EVENTTYPES}};

	var self = this, parentElement, realParentElement, parentLevel,
		nextSiblingChild, children = [], detached = false,
		prevChild, firstChild, updaters,
		eventHandler, firstNodeChild, lastNodeChild;

	var renderItems = function(items) {
		if (isArray(items)) {
			for (var i = 0; i < items.length; i++) {
				if (!isArray(items[i])) renderItem(items[i]);
				else renderItems(items[i]);
			}
		} else renderItem(items);
	};

	var renderItem = function(i) {
		if (!i && i !== 0) return;
		if (isFunction(i)) {
			renderItems(i());
			return;
		}
		if (!isObject(i)) createTextNode(i);
		else if (i.hasOwnProperty('t'))   createElement(i);
		else if (i.hasOwnProperty('v'))   createPropertyNode(i);
		else if (i.hasOwnProperty('i'))   createCondition(i);
		else if (i.hasOwnProperty('h'))   createForeach(i);	
		else if (i.hasOwnProperty('tmp')) includeTemplate(i);
		else if (i.hasOwnProperty('cmp')) renderComponent(i);
		else if (i.hasOwnProperty('is'))  createIfSwitch(i);
		else if (i.hasOwnProperty('sw'))  createSwitch(i);
		else if (i.hasOwnProperty('pl'))  createPlaceholder(i);
	};

	var createLevel = function(items, pe) {
		var level = new {{GLOBAL}}.get('Level')(component);
		level.render(items, pe, self);
		children.push(level);
	};

	var createTextNode = function(content) {
		if (content == '<br>') appendChild(document.createElement('br'));
		else appendChild(document.createTextNode(content));
	};

	var createUpdater = function(u, s, p) {
		updaters = updaters || [];
		if (p['n']) {{GLOBAL}}.get('Core').createUpdater(u, component, s, p, updaters);
		if (p['g']) {{GLOBAL}}.get('State').createUpdater(u, component, s, p);
	};

	var createPropertyNode = function(props) {
		var v = '', pv = props['v'];
		if (isFunction(pv)) pv = pv();
		if (!isUndefined(pv)) v = pv;
		var node = document.createTextNode(v);		
		appendChild(node);
		createUpdater({{GLOBAL}}.get('NodeUpdater'), node, props);
	};

	var createElement = function(props) {
		var element = document.createElement(tagsList[props['t']] || 'span');
		appendChild(element);
		if (props['p']) {
			var pr = isFunction(props['p']) ? props['p']() : props['p'];
			var a;
			for (var k in pr) {
				a = attrNames[k] || k;
				if (a == 'scope') component.setScope(element);
				else if (a == 'as') {{GLOBAL}}.get('Core').registerElement.call(component, element, pr[k]);
				else if (isPrimitive(pr[k]) && pr[k] !== '') {
					element.attr(a, pr[k]);
				}
			}
			if (props['n'] || props['g']) {
				createUpdater({{GLOBAL}}.get('ElementUpdater'), element, props);
			}
		}
		if (isArray(props['e'])) {
			var eventType, callback, isOnce, i;
			eventHandler = eventHandler || new {{GLOBAL}}.get('EventHandler')();
			for (i = 0; i < props['e'].length; i++) {
				eventType = eventTypes[props['e'][i]] || eventType;
				callback = props['e'][i + 1];
				isOnce = props['e'][i + 2] === true;
				if (isString(eventType) && isFunction(callback)) {					
					if (isOnce) {
						eventHandler.listenOnce(element, eventType, callback.bind(component));
						i++;
					} else eventHandler.listen(element, eventType, callback.bind(component));
				}
				i++;
			}
		}
		createLevel(props['c'], element);
	};

	var appendChild = function(child) {
		if (nextSiblingChild) parentElement.insertBefore(child, nextSiblingChild);	
		else parentElement.appendChild(child);	
		registerChild(child);
	};

	var createCondition = function(props) {
		if (isFunction(props['i'])) {			
			var condition = new {{GLOBAL}}.get('Condition')(props);
			condition.render(parentElement, self);				
			registerChild(condition);
			createUpdater({{GLOBAL}}.get('OperatorUpdater'), condition, props);
		} else if (!!props['i']) {
			renderItems(props['c']);
		} else if (!isUndefined(props['e'])) {
			renderItem(props['e']);
		}
	};

	var createForeach = function(props) {
		if (props['n'] || props['g']) {
			var foreach = new {{GLOBAL}}.get('Foreach')(props);
			foreach.render(parentElement, self);
			registerChild(foreach);
			createUpdater({{GLOBAL}}.get('OperatorUpdater'), foreach, props);
		} else {
			if (isArray(props['p'])) {
				for (var i = 0; i < props['p'].length; i++) renderItems(props['h'](props['p'][i], i));
			} else if (isObject(props['p'])) {
				for (var k in props['p']) renderItems(props['h'](props['p'][k], k));
			}
		}
	};

	var createIfSwitch = function(props) {
		if (props['p'] || props['g']) {
			var swtch = new {{GLOBAL}}.get('IfSwitch')(props);
			swtch.render(parentElement, self);
			registerChild(swtch);
			createUpdater({{GLOBAL}}.get('OperatorUpdater'), swtch, props);
		} else {
			for (var i = 0; i < props['is'].length; i++) {
				if (!!props['is'][i]) {
					renderItems(props['c'][i]);
					return;
				}
			}
			if (isArray(props['d'])) renderItems(props['d']);
		}
	};

	var createSwitch = function(props) {
		if (props['p'] || props['g']) {
			var swtch = new {{GLOBAL}}.get('Switch')(props);
			swtch.render(parentElement, self);
			registerChild(swtch);
			createUpdater({{GLOBAL}}.get('OperatorUpdater'), swtch, props);
		} else {
			props = props['sw'];
			if (!isArray(props[1])) {
				props[1] = [props[1]];
				props[2] = [props[2]];
			}
			for (var i = 0; i < props[1].length; i++) {					
				if (props[0] === props[1][i]) {
					renderItems(props[1][i]);
					return;
				}
			}
			if (!isUndefined(props[3])) renderItems(props[3]);
		}
	};

	var createPlaceholder = function(props) {
		var placeholderNode = document.createTextNode('');
		if (isString(props['d'])) placeholderNode.textContent = props['d'];
		placeholderNode.placeholderName = props['pl'];
		appendChild(placeholderNode);	
	};

	var registerChild = function(child, isComponent) {
		var isNodeChild = isNode(child);
		if (prevChild) {{GLOBAL}}.get('Core').setNextSiblingChild.call(prevChild, child);
		prevChild = isNodeChild ? null : child;
		if (!firstChild) firstChild = child;
		if (isNodeChild) {
			if (!firstNodeChild) firstNodeChild = child;
			lastNodeChild = child;
		} else children.push(child);
		if (isComponent) {{GLOBAL}}.get('Core').registerChildComponent.call(component, child);
	};

	var includeTemplate = function(item) {
		var props = item['p'];
		if (isObject(props) && isObject(props['props'])) {
			var tempProps = props['props'];
			delete props['props'];
			for (var k in props) tempProps[k] = props[k];
			props = tempProps;
		}
		if (item['c']) {
			props = props || {};
			props['children'] = item['c'];
		}
		if (isString(item['tmp'])) item['tmp'] = {{GLOBAL}}.get('Core').getTemplateById.call(component, item['tmp']);
		if (isFunction(item['tmp'])) {		
			var items = item['tmp'].call(component, props, component);
			renderItems(items);
		}
	};

	var renderComponent = function(item, pe) {
		pe = pe || parentElement;
		if (isFunction(item['cmp'])) {
			var cmp = new item['cmp']();
			var ir = isFunction(item['p']);
			var i, k, p = ir ? item['p']() : item['p'];
			var props, data;
			if (isObject(p)) {
				if (p['p'] || p['ap']) props = initComponentProps(p['p'], p['ap']);
				if (isString(p['i'])) {
					cmp.setId(p['i']);
					var waiting = {{GLOBAL}}.get('Core').getWaitingChild.call(component, p['i']);
					if (isArray(waiting)) {
						for (i = 0; i < waiting.length; i++) {
							waiting[i][0].set(waiting[i][1], cmp);
						}
					}
				}				
			}
			if (ir) createUpdater({{GLOBAL}}.get('ComponentUpdater'), cmp, item);
			if (isArray(item['w'])) {
				for (i = 0; i < item['w'].length; i += 2) {
					{{GLOBAL}}.get('Core').provideWithComponent.call(component, item['w'][i], item['w'][i + 1], cmp);
				}
			}
			if (item['c']) {
				props = props || {};
				props['children'] = item['c'];
			}
			{{GLOBAL}}.get('Core').initiate.call(cmp, props);
			cmp.render(pe);
			registerChild(cmp, true);
			if (isArray(item['e'])) {
				for (i = 0; i < item['e'].length; i++) {
					{{GLOBAL}}.get('Core').subscribe.call(cmp, item['e'][i], item['e'][i + 1], component);
					i++;	
				}
			}
			if (item['nm']) {{GLOBAL}}.get('Core').registerControl.call(component, cmp, item['nm']);
		} else if (item && isObject(item)) {
			if (!item.isRendered()) item.render(pe);
			registerChild(item, true);
		}
	};

	var initComponentProps = function(p, ap) {
		var props = {}, k;
		var f = function(pr) {
			if (isObject(pr)) {
				for (k in pr) props[k] = pr[k];				
			}
		};
		f(p); f(ap);
		return props;
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

	var disposeDom = function() {
		var elementsToDispose = getElements();
		for (var i = 0; i < elementsToDispose.length; i++) parentElement.removeChild(elementsToDispose[i]);
		elementsToDispose = null;
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
		if (firstLevel instanceof {{GLOBAL}}.get('Level')) {
			return {{GLOBAL}}.get('Core').getParentElement.call(firstLevel);
		} else if (firstLevel) {
			return {{GLOBAL}}.get('Core').getFirstNodeChild.call(firstLevel);
		}
		return null;
	};

	this.getComponent = function() {
		return component;
	};

	this.setAppended = function(isAppended, p) {
		var isDetached = !isAppended;
		if (isDetached === !!this.detached) return;
		this.detached = isDetached;
		var elements = getElements();
		if (isDetached) {
			realParentElement = parentElement;
			parentElement = p || document.createElement('div'); 
			for (var i = 0; i < elements.length; i++) parentElement.appendChild(elements[i]);
		} else {
			nextSiblingChild = {{GLOBAL}}.get('Core').getNextSiblingChild.call(parentLevel);
			parentElement = realParentElement;
			realParentElement = null;
			for (var i = 0; i < elements.length; i++) appendChild(elements[i]);
		}
	};

	this.placeTo = function(element) {
		this.setAppended(false, element);
	};

	this.dispose = function() {
		if (updaters) {
			for (var i = 0; i < updaters.length; i++) {
				{{GLOBAL}}.get('Core').disposeUpdater.call(component, updaters[i], updaters[i + 1]);
				updaters[i + 1] = null;
				i++;
			}
		}
		for (var i = 0; i < children.length; i++) {
			if (isComponentLike(children[i])) {
				{{GLOBAL}}.get('Core').unregisterChildComponent.call(component, children[i]);
			}
			children[i].dispose();
			children[i] = null;
		}
		if (eventHandler) {
			eventHandler.dispose();
			eventHandler = null;
		}
		disposeDom();
		updaters = null;
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
{{GLOBAL}}.set(_c, 'Level');