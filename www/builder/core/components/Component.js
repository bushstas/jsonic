function Component() {
	var $;
	var processInitials = function() {
		$.initials = $.initials || {};
		if (isObject($.props['args'])) {
			$.receivedArgs = $.props['args'];
			delete $.props['args'];
		}
		if (isObject($.props['props'])) {
			Objects.merge($.props, $.props['props']);
			delete $.props['props'];
		}
		var initials = $.initials;
		if (isObject(initials)) {
			for (var k in initials) {
				if (isArrayLike(initials[k])) {
					if (k == 'correctors') {
						for (var j in initials[k]) addCorrector(j, initials[k][j]);
					} else if (k == 'globals') {
						for (var j in initials[k]) Globals.subscribe(j, initials[k][j], $);
					} else if (k == 'followers') {
						for (var j in initials[k]) addFollower(j, initials[k][j]);
					} else if (k == 'controllers') {
						for (var i = 0; i < initials[k].length; i++) attachController(initials[k][i]);
					} else if (k == 'props') {
						Objects.merge($.props, initials[k]);
					} else if (k == 'options') {
						$.initOptions(initials[k]);
					}
				}
			}
		}
	};

	var attachController = function(options) {
		if (isObject(options['on'])) {
			for (var k in options['on']) options.controller.subscribe(k, options['on'][k], $);
		}
	};

	var addCorrector = function(name, handler) {
		if (isFunction(handler)) {
			$.correctors = $.correctors || {};
			$.correctors[name] = handler;
		}
	};

	var addFollower = function(name, handler) {
		if (isFunction(handler)) {
			$.followers = $.followers || {};
			$.followers[name] = handler;
		}
	};

	var subscribeToHelper = function(options) {
		if (isObject(options['options'])) options['helper'].subscribe($, options['options']);
	};

	var getInitial = function(initialName) {
		return Objects.get($.initials, initialName);
	};

	var processPostRenderInitials = function() {
		var helpers = getInitial('helpers');
		if (isArray(helpers)) {
			for (var i = 0; i < helpers.length; i++) subscribeToHelper(helpers[i]);
		}
	};

	var load = function() {
		var loader = getInitial('loader');
		if (isObject(loader) && isObject(loader['controller'])) {
			$.loader = loader['controller'];
			var isAsync = !!loader['async'];
			$.loader.subscribe('load', onDataLoad.bind($, isAsync), $);
			var options = loader['options'];
			if (isFunction(options)) options = options();
			$.loader.doAction('load', options);
			if (!isAsync) {
				renderTempPlaceholder();
				return;
			}
		}
		onReadyToRender();
	};

	var renderTempPlaceholder = function() {
		$.tempPlaceholder = document.createElement('span');
		$.parentElement.appendChild($.tempPlaceholder);
	};

	var onDataLoad = function(isAsync, data) {
		$.onLoaded(data);
		if (!isAsync) onReadyToRender();
	};

	var onReadyToRender = function() {
		if (!$.isRendered()) {
			doRendering();
			if ($.tempPlaceholder) {
				$.parentElement.removeChild($.tempPlaceholder);
				$.tempPlaceholder = null;
			}
			processPostRenderInitials();
		}
	};

	var doRendering = function() {
		$.level = new Level();
		$.args = getCombinedArgs();
		var content = $.getTemplateMain($, $.args);
		if (isArray(content)) {
			$.level.setComponent($);
			$.level.render(content, $.parentElement, $, $.tempPlaceholder);
		}
		$.rendered = true;
		$.onRendered();
		if (isArray($.callbacks)) {
			for (var i = 0; i < $.callbacks.length; i++) {
				if (isFunction($.callbacks[i])) $.callbacks[i]();
			}
		}
		$.callbacks = null;
		$.waiting = null;
	};

	var getCombinedArgs = function() {
		return Objects.merge({}, $.initials['args'], $.getArgs(), $.receivedArgs); 
	};

	var propagatePropertyChange = function(changedProps) {$=this;
		var pn, pv, i, activities, cnds = [], ifsw = [];
		for (pn in changedProps) {
			pv = changedProps[pn];
			activities = $.propActivities['cnd'];
			if (activities && isArray(activities[pn])) {
				for (i = 0; i < activities[pn].length; i++) {
					if (cnds.indexOf(activities[pn][i]) == -1) {
						activities[pn][i].update();
						cnds.push(activities[pn][i]);
					}
				}
			}
			activities = $.propActivities['isw'];
			if (activities && isArray(activities[pn])) {
				for (i = 0; i < activities[pn].length; i++) {
					if (ifsw.indexOf(activities[pn][i]) == -1) {
						activities[pn][i].update();
						ifsw.push(activities[pn][i]);
					}
				}
			}
			activities = $.propActivities['swt'];
			if (activities && isArray(activities[pn])) {
				for (i = 0; i < activities[pn].length; i++) activities[pn][i].update(pv);
			}
			activities = $.propActivities['for'];
			if (activities && isArray(activities[pn])) {
				for (i = 0; i < activities[pn].length; i++) activities[pn][i].update(pv);
			}
			activities = $.propActivities['nod'];
			if (activities && isArray(activities[pn])) {
				var node;
				for (i = 0; i < activities[pn].length; i++) activities[pn][i].textContent = pv;
			}
			activities = $.propActivities['atr'];
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
			activities = $.propActivities['cmp'];
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

	Component.prototype.initiate = function() {$=this;
		$.propActivities = {};
		$.propsToSet = {};
		$.rendered = false;
		$.disposed = false;
	};

	Component.prototype.render = function(parentElement) {$=this;
		$.parentElement = parentElement;
		processInitials();
		load();
	};

	Component.prototype.instanceOf = function(parent) {$=this;
		return $.inheritedSuperClasses && $.inheritedSuperClasses.indexOf(parent) > -1;
	};

	Component.prototype.dispatchEvent = function(eventType, eventParams) {$=this;
		if (isArray($.listeners)) {
			for (var i = 0; i < $.listeners.length; i++) {
				if (isNumber($.listeners[i]['type'])) $.listeners[i]['type'] = __EVENTTYPES[$.listeners[i]['type']];
				if ($.listeners[i]['type'] == eventType) $.listeners[i]['handler'].call($.listeners[i]['subscriber'] || null, eventParams);
			}
		}
	};

	Component.prototype.provideWithComponent = function(propName, componentName, waitingChild) {$=this;
		var cmp = $.getChildById(componentName);
		if (cmp) waitingChild.set(propName, cmp);
		else {
			$.waiting = $.waiting || {};
			$.waiting[componentName] = $.waiting[componentName] || [];
			$.waiting[componentName].push([waitingChild, propName]);
		}
	};

	Component.prototype.getWaitingChild = function(componentName) {$=this;
		return Objects.get($.waiting, componentName);
	};

	Component.prototype.get = function(propName) {$=this;
		return $.propsToSet[propName] || $.props[propName];
	};

	Component.prototype.showElement = function(element, isShown) {$=this;
		if (isString(element)) element = $.findElement(element);
		if (isElement(element)) element.show(isShown);
	};

	Component.prototype.setStyle = function(styles) {$=this;
		if ($.isRendered()) $.getElement().setStyle(styles);
	};

	Component.prototype.addClass = function(className, isAdding) {$=this;
		if ($.isRendered()) {
			if (isAdding || isUndefined(isAdding)) $.getElement().addClass(className);
			else $.getElement().removeClass(className);
		}
	};

	Component.prototype.each = function(propName, callback) {$=this;
		var ar = $.get(propName);
		if (isArrayLike(ar) && isFunction(callback)) {
			if (isArray(ar)) for (var i = 0; i < ar.length; i++) callback.call($, ar[i], i, ar);
			else for (var k in ar) callback.call($, ar[k], k, ar);
		}
	};

	Component.prototype.toggle = function(propName) {$=this;
		$.set(propName, !$.get(propName));
	};

	Component.prototype.set = function(propName, propValue) {$=this;
		var props;
		if (!isUndefined(propValue)) {
			props = {};
			props[propName] = propValue;
		} else props = propName;
		var isChanged = false;
		var changedProps = {};
		var currentValue;
		for (var k in props) {
			if (Objects.has($.correctors, k)) props[k] = $.correctors[k].call($, props[k], props);
			currentValue = $.props[k];
			if (currentValue == props[k]) continue;
			if (isArray(currentValue) && isArray(props[k]) && Objects.equals(currentValue, props[k])) continue;
			isChanged = true;
			$.props[k] = props[k];
			changedProps[k] = props[k];
		}	
		if ($.isRendered()) {
			if (isChanged) propagatePropertyChange(changedProps);
			for (var k in changedProps) {
				if (Objects.has($.followers, k)) $.followers[k].call($);
			}
		}
		changedProps = null;
	};

	Component.prototype.getFirstNodeChild = function() {$=this;
		if ($.level) return $.level.getFirstNodeChild();
		return null;
	};

	Component.prototype.preset = function(propName, propValue) {$=this;
		$.propsToSet[propName] = propValue;
	};

	Component.prototype.update = function() {$=this;
		$.set($.propsToSet);
		$.propsToSet = {};
	};

	Component.prototype.refresh = function(args) {$=this;
		if (args) $.receivedArgs = args;
		$.unrender();
		doRendering();
	};

	Component.prototype.delay = function(f, n) {$=this;
		window.clearTimeout($.timeout);
		if (isFunction(f)) $.timeout = window.setTimeout(f.bind($), n || 200);
	};

	Component.prototype.addChild = function(child, parentElement) {$=this;
		$.level.renderComponent(child, parentElement);
	};

	Component.prototype.removeChild = function(child) {$=this;
		if (!child) return;
		var childId = child;
		if (isString(child)) child = $.getChild(child);
		else childId = Objects.getKey($.children, child);
		if (isComponentLike(child)) child.dispose();
		if ((isString(childId) || isNumber(childId)) && isObject($.children)) delete $.children[childId];	
	 };

	Component.prototype.forEachChild = function(callback) {$=this;
		if (isObject($.children)) Objects.each($.children, callback, $);
	};

	Component.prototype.registerChildComponent = function(child) {$=this;
		$.childrenCount = $.childrenCount || 0;
		$.children = $.children || {};
		$.children[child.getId() || $.childrenCount] = child;
		$.childrenCount++;
	};

	Component.prototype.setParent = function(parentalComponent) {$=this;
		$.parentalComponent = parentalComponent;
	};

	Component.prototype.getParent = function() {$=this;
		return $.parentalComponent;
	};

	Component.prototype.getChildAt = function(index) {$=this;
		return Objects.getByIndex($.children, index);
	};

	Component.prototype.getChildren = function(classFunc) {$=this;
		var children = [];
		$.forEachChild(function(child) {
			if (isComponentLike(child) && child.instanceOf(classFunc)) children.push(child);
		});
		return children;
	};

	Component.prototype.getChild = function(id) {$=this;
		return Objects.get($.children, id);
	};

	Component.prototype.doOnParentReady = function(callback, params) {$=this;
		$.getParent().addCallback(callback.bind($, params));
	};

	Component.prototype.addCallback = function(callback) {$=this;
		$.callbacks = $.callbacks || [];
		$.callbacks.push(callback);
	};

	Component.prototype.setId = function(id) {$=this;
		$.componentId = id;
	};

	Component.prototype.getId = function() {$=this;
		return $.componentId;
	};

	Component.prototype.getElement = function() {$=this;
		return $.scope || $.parentElement;
	};

	Component.prototype.findElement = function(selector, scopeElement) {$=this;
		return (scopeElement || $.getElement()).querySelector(selector);
	};

	Component.prototype.findElements = function(selector, scopeElement) {$=this;
		return Array.prototype.slice.call((scopeElement || $.scope || $.parentElement).querySelectorAll(selector));
	};

	Component.prototype.findElementWithinParent = function(selector) {$=this;
		return $.getParent().findElement(selector);
	};

	Component.prototype.findElementsWithinParent = function(selector) {$=this;
		return $.getParent().findElements(selector);
	};

	Component.prototype.fill = function(element, data) {$=this;
		if (isString(element)) element = $.findElement(element);
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

	Component.prototype.removeNode = function(node) {$=this;
		if (isString(node)) node = $.findElement(node);
		if (isNode(node) && node.parentNode == $.parentElement) $.parentElement.removeChild(node);
	};

	Component.prototype.getParentElement = function() {$=this;
		return $.parentElement;
	};

	Component.prototype.isRendered = function() {$=this;
		return $.rendered;
	};

	Component.prototype.isDisposed = function() {$=this;
		return $.disposed;
	};

	Component.prototype.addListener = function(target, eventType, handler) {$=this;
		if (isElement(target)) {
			$.eventHandler = $.eventHandler || new EventHandler();
			$.eventHandler.listen(target, eventType, handler.bind($));
		} else target.subscribe(eventType, handler, $);
	};

	Component.prototype.subscribe = function(eventType, handler, subscriber) {$=this;
		$.listeners = $.listeners || [];
		$.listeners.push({'type': eventType, 'handler': handler, 'subscriber': subscriber});
	};

	Component.prototype.setAppended = function(isAppended) {$=this;
		if ($.level) $.level.setAppended(isAppended);
	};

	Component.prototype.setScope = function(scope) {$=this;
		$.scope = scope;
	};

	Component.prototype.log = function(message, method, opts) {$=this;
		log(message, method, $, opts);
	};

	Component.prototype.registerPropActivity = function(type, name, data) {$=this;
		$.propActivities = $.propActivities || {};
		$.propActivities[type] = $.propActivities[type] || {};
		$.propActivities[type][name] = $.propActivities[type][name] || [];
		$.propActivities[type][name].push(data);
		return $.propActivities[type][name].length - 1;
	};

	Component.prototype.disposePropActivities = function(type, data) {$=this;
		var activities = $.propActivities[type];
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

	Component.prototype.unrender = function() {$=this;
		$.disposeLinks();
		$.disposeInternal();
		$.level.dispose();
		if ($.eventHandler) {
			$.eventHandler.dispose();
			$.eventHandler = null;
		}
		$.level = null;
		$.listeners = null;
	};

	Component.prototype.dispose = function() {$=this;
		$.unrender();
		$.propActivities = null;
		$.parentElement = null;
		$.props = null;
		$.propsToSet = null;	
		$.provider = null;
		$.children = null;
		$.disposed = true;
		$.loader = null;
		$.initials = null;
		$.followers = null;
		$.correctors = null;
		$.receivedArgs = null;
		$.args = null;
		$.parentalComponent = null;
	};

	(function(){
		var f = function(){return null};
		Component.prototype.initOptions = f;
		Component.prototype.onRendered = f;
		Component.prototype.onLoaded = f;
		Component.prototype.getTemplateMain = f;
		Component.prototype.getTemplateByKey = f;
		Component.prototype.disposeInternal = f;
		Component.prototype.getArgs = f;
	})();
	Component.prototype.g = Component.prototype.get;
}
Component();