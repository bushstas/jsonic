;(function() {
var __T = ['\u0417\u0430\u043f\u0440\u0430\u0448\u0438\u0432\u0430\u0435\u043c\u043e\u0439 \u0441\u0442\u0440\u0430\u043d\u0438\u0446\u044b \u043d\u0435 \u0441\u0443\u0449\u0435\u0441\u0442\u0432\u0443\u0435\u0442','LOGO','\u0413\u043b\u0430\u0432\u043d\u0430\u044f','\u041f\u043e\u0438\u0441\u043a','\u0418\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u0435','\u041f\u043b\u0430\u043d\u044b \u0437\u0430\u043a\u0443\u043f\u043e\u043a','\u0410\u043d\u0430\u043b\u0438\u0442\u0438\u043a\u0430'];
var __ = ['\u0420\u0410\u0421\u0428\u0418\u0420\u0415\u041d\u041d\u042b\u0419 \u041f\u041e\u0418\u0421\u041a','\u0421\u043e\u0437\u0434\u0430\u0442\u044c \u043d\u043e\u0432\u044b\u0439','\u0421\u043e\u0437\u0434\u0430\u0442\u044c \u043c\u0430\u0441\u0442\u0435\u0440\u043e\u043c','\u0421\u043e\u0437\u0434\u0430\u0442\u044c \u0444\u0438\u043b\u044c\u0442\u0440','\u041c\u043e\u0438 \u0444\u0438\u043b\u044c\u0442\u0440\u044b','\u0421\u043e\u0445\u0440\u0430\u043d\u0438\u0442\u044c','\u041e\u0447\u0438\u0441\u0442\u0438\u0442\u044c \u0432\u0435\u0441\u044c \u0444\u0438\u043b\u044c\u0442\u0440','\u0423\u0432\u0435\u0440\u0435\u043d\u044b?','\u0414\u0430','Bushmakin Stas','bushstas@mail.ru','const \u0441\u043e\u0437\u0434\u0430\u0451\u0442 \u043d\u043e\u0432\u0443\u044e \u0438\u043c\u0435\u043d\u043e\u0432\u0430\u043d\u043d\u0443\u044e \u043a\u043e\u043d\u0441\u0442\u0430\u043d\u0442\u0443, \u0434\u043e\u0441\u0442\u0443\u043f\u043d\u0443\u044e \u0442\u043e\u043b\u044c\u043a\u043e \u0434\u043b\u044f \u0447\u0442\u0435\u043d\u0438\u044f. ... \u041f\u043e\u0432\u0442\u043e\u0440\u043d\u043e\u0435 \u0432\u0432\u0435\u0434\u0435\u043d\u0438\u0435 \u0432 JavaScript. \u0421\u0442\u0440\u0443\u043a\u0442\u0443\u0440\u044b \u0434\u0430\u043d\u043d\u044b\u0445 JavaScript.','\u0421\u043e\u0445\u0440\u0430\u043d\u0435\u043d\u0438\u0435 \u0444\u0438\u043b\u044c\u0442\u0440\u0430'];
var __DICTURL = 'dictionary/get.php';
var __TAGS = ['div','span','table','tbody','thead','tr','td','th','ul','ol','li','p','a','form','input','img','video','audio','aside','article','b','big','blockquote','button','canvas','caption','center','code','col','colgroup','footer','font','h1','h2','h3','h4','h5','h6','header','hr','i','iframe','label','menu','pre','s','section','select','strong','textarea','u'];
var __A = {'c':'class','i':'id','v':'value','t':'title','p':'placeholder','tp':'type','h':'href','s':'src','tr':'target','m':'method','st':'style','w':'width','ht':'height','sz':'size','mx':'maxlength','a':'action','n':'name','sc':'scope','r':'role'};
var __EVENTTYPES = ['click','mouseover','mouseout','mouseenter','mouseleave','mousemove','contextmenu','dblclick','mousedown','mouseup','keydown','keyup','keypress','blur','change','focus','focusin','focusout','input','invalid','reset','search','select','submit','drag','dragend','dragenter','dragleave','dragover','dragstart','drop','copy','cut','paste','popstate','wheel','storage','show','toggle','touchend','touchmove','touchstart','touchcancel','message','error','open','transitionend','abort','play','pause','load','durationchange','progress','resize','scroll','unload','hashchange','beforeunload','pageshow','pagehide'];
var __ROUTES = [{'name':'main','view':Main,'accessLevel':0,'title':'Home','params':{'text':'blablabla','name':'$2'}},{'name':'search','view':Search,'accessLevel':0,'title':'\u041f\u043e\u0438\u0441\u043a','load':['Filters']},{'name':'favorite','view':Favorite,'accessLevel':0,'title':'\u0418\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u0435'},{'name':'analytics','view':Analytics,'accessLevel':0,'title':'\u0410\u043d\u0430\u043b\u0438\u0442\u0438\u043a\u0430'}];
var __ERRORROUTES = {'404':Error404,'401':Error401};
var __HASHROUTER = true;
var __INDEXROUTE = 'main';
var __DEFAULTROUTE = null;
var __VIEWCONTAINER = 'app-view-container';
var __I = 'initiate';
var __GI = 'getInitials';
var __APIDIR = 'api';
var __PAGETITLE = 'Page title';
var __USEROPTIONS = {'login':'user/login.php','logout':'user/logout.php','save':'user/save.php'};
var CONFIG = {'filters': {'load': 'filters/get.php','save': 'filters/save.php','set': 'filters/set.php'}};
function Application() {}
Application.prototype.initiate = function() {
	this.views = {};
};
Application.prototype.run = function() {
	this.element = document.createElement('div');
	this.element.className = 'application-container';
	document.body.appendChild(this.element);
	this.render(this.element);
	this.createViewContainer();
	this.defineViews(__ROUTES);
	this.initRouter();
};
Application.prototype.initRouter = function() {
	Router.setNavigationHandler(this.handleNavigation.bind(this));
	Router.init();
};
Application.prototype.defineViews = function(routes) {
	for (var i = 0; i < routes.length; i++) {
		this.views[routes[i]['name']] = null;
		if (isArray(routes[i]['children'])) {
			this.defineViews(routes[i]['children']);
		}
	}
	var errorRoutes = __ERRORROUTES;
	if (isObject(errorRoutes)) {
		for (var k in errorRoutes) {
			this.views[k] = null;
		}
	}
};
Application.prototype.handleNavigation = function(route, changeTitle) {
	this.isChangeTitle = changeTitle;
	this.currentRoute = route;
	var view = this.views[route['name']];
	var isSameView = this.currentView == view;
	if (!isSameView && this.currentView) {
		this.activateView(this.currentView, false);
	}
	this.currentView = view;
	if (!isUndefined(view) && isFunction(route['view'])) {
		if (!view) {
			var viewParams = this.getViewParams(route, true);
			view = this.currentView = this.views[route['name']] = new route['view'](viewParams);
			view.setOnReadyHandler(this.onViewReady.bind(this));
			var viewContentElement = this.createViewContentElement();
			view.render(viewContentElement);
			view.initControllers();
			Globals.addView(view, route['name']);
		} else {
			this.activateView(view, true, isSameView);
		}
		if (isNumber(route['error'])) {
			this.onError(route['error']);
		} else {
			this.onNoErrors();
		}
	} else {
		this.log('no view to represent given route', 'handleNavigation', route);
	}
};
Application.prototype.getViewParams = function(route, allParams) {
	var params;
	if (isObject(route['dinamicParams'])) {
		params = {};
		for (var k in route['dinamicParams']) {
			params[k] = Router.getPathPartAt(route['dinamicParams'][k]);
		}
	}
	if (allParams) {
		if (isObject(params)) {
			Objects.merge(params, route['params']);
		} else {
			params = route['params'];
		}
	}
	return params;
};
Application.prototype.onViewReady = function() {
	if (this.isChangeTitle) {
		var title = this.currentRoute['title'];
		if (isString(title)) {
			var titleParams = this.currentView.getTitleParams();
			if (isObject(titleParams)) {
				var regExp;
				for (var k in titleParams) {
					regExp = new RegExp("\\$" + k)
					title = title.replace(regExp, titleParams[k]);
				}
			}
		}
		this.setPageTitle(title ||__PAGETITLE || '');
	}
};
Application.prototype.createViewContentElement = function() {
	var element = document.createElement('div');
	element.className = 'app-view-content-container';
	this.viewContainer.appendChild(element);
	return element;
};
Application.prototype.createViewContainer = function() {
	var viewContainer;
	var containerClass = __VIEWCONTAINER;
	if (containerClass) {
		viewContainer = document.body.querySelector('.' + containerClass);
	}
	if (!viewContainer) {
	 	viewContainer = document.createElement('div');
		if (containerClass) {
			viewContainer.className = containerClass;
		}
		this.element.appendChild(viewContainer);
	}
	this.viewContainer = viewContainer;
};
Application.prototype.activateView = function(view, isActivated, isSameView) {
	var parentElement = view.getParentElement();
	if (!isActivated) {
		this.viewContainer.removeChild(parentElement);
	} else {
		var params = this.getViewParams(this.currentRoute);
		if (isObject(params)) {
			view.set(params);
		}
		if (!isSameView) {
			this.viewContainer.appendChild(parentElement);
		}
	}
	view.activate(isActivated);
};
Application.prototype.setPageTitle = function(title) {
	var titleElement = document.getElementsByTagName('title')[0];
	if (!isElement(titleElement)) {
		var headElement = document.getElementsByTagName('head')[0];
		if (!isElement(headElement)) {
			var htmlElement = document.getElementsByTagName('html')[0];
			headElement = htmlElement.appendChild(document.createElement('head'));
		}
		titleElement = headElement.appendChild(document.createElement('title'));
	}
	titleElement.innerHTML = title;
};
Application.prototype.disposeView = function(viewName) {
	if (isObject(this.views[viewName])) {
		this.views[viewName].dispose();
		this.views[viewName] = null;
	}
};
Application.prototype.onNoErrors = function() {};
Application.prototype.onError = function(errorCode) {};
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
				}
			}
		}
	}
};
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
	if (isFunction(options['callback'])) {
		options['helper'].subscribe(this, options['callback'], options['options']);
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
		var content = this.getTemplateMain(this.provider, this.getInitial('args') || {});
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
function Condition(params) {
	this.params = params;
	this.isTrue = !!this.params['i']();
}
Condition.prototype.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevel(false);
};
Condition.prototype.createLevel = function(isUpdating) {
	var children = this.getChildren();
	if (isArray(children)) {
		this.level = new Level();
		var nextSiblingChild = isUpdating ? this.getNextSiblingChild() : null;
		this.level.render(children, this.parentElement, this.parentLevel, nextSiblingChild);
	}
};
Condition.prototype.recheck = function() {
	var isTrue = !!this.params['i']();
	if (isTrue != this.isTrue) {
		this.isTrue = isTrue;
		this.disposeLevel();
		this.createLevel(true);
	}
};
Condition.prototype.getChildren = function() {
	return this.isTrue ? this.params['c']() : (isFunction(this.params['e']) ? this.params['e']() : null);
};
Condition.prototype.propagatePropertyChange = function(propName, propValue) {
	if (this.level) {
		this.level.propagatePropertyChange(propName, propValue);
	}	
};
Condition.prototype.getFirstNodeChild = function() {
	if (this.level) {
		return this.level.getFirstNodeChild();
	}
	return null;
};
Condition.prototype.disposeLevel = function() {
	if (this.level) {
		this.level.dispose();
		this.level = null;
	}
};
Condition.prototype.dispose = function() {
	this.disposeLinks();
	this.disposeLevel();
	this.parentElement = null;
	this.parentLevel = null;
	this.params = null;
	this.nextSiblingChild = null;
};
function Control() {}
Control.prototype.initiateInternal = function() {};
Control.prototype.checkInitials = function() {
	var initials = this.getInitials();
	for (var k in initials) {
		if (initials[k] && isObject(initials[k])) {
			if (k == 'options') {
				this.options = initials[k];
			}
		}
	}	
};
Control.prototype.getName = function() {
	return this.props.name;
};
Control.prototype.getValue = function() {
	var value = (!isUndefined(this.value) ? this.value : this.props.value) || '';
	var type = Objects.get(this.options, 'type');
	switch (type) {
		case 'array':
			if (isString(value)) {
				value = value.split(',');
			} else if (!isArray(value)) {
				value = [value];
			}
		break;
		case 'string':
			if (isArray(value)) {
				value = value.join(',');
			} else if (isObject(value)) {
				value = JSON.stringify(value);
			} else if (isNumber(value)) {
				value = value + '';
			} else if (!!value) {
				value = '1';
			} else {
				value = '';
			}
		break;
		case 'number':
			if (isString(value)) {
				value = stringToNumber(value);
			} else if (isBool(value)) {
				value = !!value ? 1 : 0;
			} else if (isArray(value)) {
				value = ~~value[0];
			} else {
				value = 0;
			}
		break;
		case 'boolean':
			value = !!value && value === '0';
		break;
	}
	return this.getProperValue(value);
};
Control.prototype.getProperValue = function(value) {
	return value;
};
Control.prototype.setValue = function(value) {
	this.value = value;
	this.setProperValue(value);
};
Control.prototype.setProperValue = function(value) {
	this.set('value', value);
};
Control.prototype.onChange = function(e) {
	this.value = e.target.value;
};
Control.prototype.disposeInternal = function() {
	this.options = null;
	this.value = null;
};
function Controller() {}
Controller.prototype.initiate = function() {
	this.listeners = [];
};
Controller.prototype.processInitials = function() {
	var initials = this.initials;
	if (isObject(initials)) {
		for (var k in initials) {
			if (initials[k] && isObject(initials[k])) {
				if (k == 'globals') {
				} else if (k == 'options') {
					this.options = initials[k];
				} else if (k == 'controllers') {
					for (var i = 0; i < initials[k].length; i++) {
						this.attachController(initials[k][i]);
					}
				}
			}
		}
	}
};
Controller.prototype.attachController = function(options) {
	if (isObject(options['controller'])) {
		if (isObject(options['on'])) {
			for (var k in options['on']) {
				options.controller.subscribe(k, options['on'][k], this);
			}
		}
	}
};
Controller.prototype.subscribe = function(eventType, callback, subscriber) {
	this.listeners.push([eventType, callback, subscriber]);
};
Controller.prototype.unsubscribe = function(subscriber, eventType) {
	var done = false;
	while (!done) {
		done = true;
		for (var i = 0; i < this.listeners.length; i++) {
			if (this.listeners[i][2] == subscriber && (!eventType || this.listeners[i][0] == eventType)) {
				this.listeners.splice(i, 1);
				done = false;
				break;
			}
		}
	}
};
Controller.prototype.dispatchEvent = function(eventType, data) {
	var dataToDispatch = data;
	if (Objects.has(this.options, 'clone', true)) {
		dataToDispatch = Objects.clone(data);
	}
	if (isArray(this.listeners)) {
		for (var i = 0; i < this.listeners.length; i++) {
			if (this.listeners[i][0] == eventType && isFunction(this.listeners[i][1])) {
				this.listeners[i][1].call(this.listeners[i][2] || null, dataToDispatch, this);
			}
		}
	}
};
Controller.prototype.getData = function(actionName) {
	return !!action && !!this.data && isObject(this.data) ? this.data[action] : this.data;
};
Controller.prototype.getItemById = function(id) {
	var primaryKey = this.getPrimaryKey();
	var data = this.data['load'];
	if (isArray(data)) {
		for (var i = 0; i < data.length; i++) {
			if (Objects.has(data[i], primaryKey, id)) return data[i];
		}
	}
	return null;
};
Controller.prototype.getItem = function(nameOrIndex, actionName) {
	actionName = actionName || 'load';
	return isArrayLike(this.data[actionName]) ? this.data[actionName][nameOrIndex] : null;
};
Controller.prototype.load = function() {
	this.doAction('load');
};
Controller.prototype.doAction = function(actionName, options, url) {
	var action = this.getAction(actionName);
	this.action = action;	
	if (actionName == 'load' && this.gotFromStore()) {
		return;
	}
	if (!isObject(options)) {
		options = {};
	}
	if (action && isObject(action) && action['options'] && isObject(action['options'])) {
		Objects.merge(options, action['options']);
	}
	var method = action['method'] || 'POST';
	url = url || this.makeUrl(action['url'], options);
	if (!url || !isString(url)) {
		log('url to execute the action ' + actionName + ' is invalid or empty', 'doAction', this, {action: action});
	}
	this.request = this.request || new AjaxRequest(url, this.onActionComplete.bind(this));
	this.request.send(method, options, url);
};
Controller.prototype.gotFromStore = function() {
	if (this.shouldStore()) {
		var storeAs = Objects.get(this.options, 'storeAs');
		if (isString(storeAs)) {
			var storedData = StoreKeeper.getActual(storeAs, Objects.get(this.options, 'storePeriod'));
			if (isArrayLike(storedData)) {
				this.onActionComplete(storedData);
				return true;
			}
		}
	}
	return false;
};
Controller.prototype.makeUrl = function(url, options) {
	var regExp;
	for (var k in options) {
		if (isString(options[k]) || isNumber(options[k])) {
			regExp = new RegExp('\\$' + k)
			url = url.replace(regExp, options[k]);
		}
	}
	return url;
};
Controller.prototype.onActionComplete = function(data) {
	var actionName = this.action['name'];
	this.data = this.data || {};
	this.data[actionName] = data;
	if (isFunction(this.action['callback'])) {
		this.action['callback'].call(this, data);
	}
	this.dispatchEvent(actionName, data);
	if (actionName == 'load' && this.shouldStore()) {
		this.store(true, data);
	}
};
Controller.prototype.shouldStore = function() {
	var shouldStore = Objects.get(this.options, 'store');
	if (shouldStore === false) return false;
	return Objects.has(this.options, 'storeAs');
};
Controller.prototype.store = function(isAdding, data) {
	if (isAdding) {
		StoreKeeper.set(this.options['storeAs'], data);
	} else {
		StoreKeeper.remove(this.options['storeAs']);
	}
};
Controller.prototype.getPrimaryKey = function() {
	return Objects.get(this.options, 'key', 'id');
};
Controller.prototype.getAction = function(actionName) {	
	var actions = Objects.get(this.initials, 'actions');
	if (isObject(actions)) {
		var action = actions[actionName];
		if (isObject(action)) {
			if (!isString(action['name'])) {
				if (isObject(action['routeOptions']) && actionName == 'load') {
					this.initActionRouteOptions(action);
				}
				action['name'] = actionName;
			}
			return action;
		}
		log('action is invalid', 'getAction', this, {action: action});
	} else {
		log('no actions', 'getAction', this, {actions: actions});
	}
	return null;
};
Controller.prototype.initActionRouteOptions = function(action) {
	var value;
	this.currentRouteOptions = {};
	var routeOptions = {};
	for (var k in action['routeOptions']) {
		value = Router.getPathPartAt(action['routeOptions'][k]);
		if (isString(value)) {
			routeOptions[k] = value;
		}
	}
	this.setCurrentRouteOptions(routeOptions, action);
	Router.subscribe(action['routeOptions'], this);
};
Controller.prototype.setCurrentRouteOptions = function(routeOptions, action) {
	this.currentRouteOptions = routeOptions;
	if (!isObject(action['options'])) {
		action['options'] = {};
	}
	for (var k in routeOptions) {
		action['options'][k] = routeOptions[k];
	}
};
Controller.prototype.handleRouteOptionsChange = function(routeOptions) {
	if (!Objects.equals(routeOptions, this.currentRouteOptions)) {
		var action = this.getAction('load');
		this.setCurrentRouteOptions(routeOptions, action);
		this.doAction('load');
	}
};
Controller.prototype.dispose = function() {
	this.listeners = null;
	if (this.request) {
		this.request.dispose();
	}
	this.options = null;
	this.request = null;
	this.data = null;
	this.action = null;
	this.initials = null;
};
function Core() {}
Core.prototype.getNextSiblingChild = function() {
	if (!this.nextSiblingChild) {
		return null;
	}
	if (this.nextSiblingChild instanceof Node) {
		return this.nextSiblingChild;
	}
	var firstNodeChild = this.nextSiblingChild.getFirstNodeChild();
	if (firstNodeChild) {
		return firstNodeChild;
	}
	return this.nextSiblingChild.getNextSiblingChild();	
};
Core.prototype.setNextSiblingChild = function(nextSiblingChild) {
	this.nextSiblingChild = nextSiblingChild;
	if (!(nextSiblingChild instanceof Node)) {
		this.nextSiblingChild.setPrevSiblingChild(this);
	}
};
Core.prototype.setPrevSiblingChild = function(prevSiblingChild) {
	this.prevSiblingChild = prevSiblingChild;
};
Core.prototype.disposeLinks = function() {
	if (this.prevSiblingChild) {
		this.prevSiblingChild.setNextSiblingChild(this.nextSiblingChild);
	}
	this.prevSiblingChild = null;
	this.nextSiblingChild = null;
};
Core.prototype.setScope = function(scope) {
	this.parentLevel.setScope(scope);
};
Core.prototype.getPropName = function(i) {
	this.parentLevel.getPropName(i);
};
function Foreach(params) {
	this.items = params['p'];
	this.handler = params['h'];
	this.levels = [];
}
Foreach.prototype.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevels(false);
};
Foreach.prototype.createLevels = function(isUpdating) {
	if (this.items instanceof Array) {
		for (var i = 0; i < this.items.length; i++) {
			this.createLevel(this.handler(this.items[i], i), isUpdating);
		}
	}
};
Foreach.prototype.createLevel = function(items, isUpdating) {
	var level = new Level();
	var nextSiblingChild = isUpdating ? this.getNextSiblingChild() : null;
	level.render(items, this.parentElement, this.parentLevel, nextSiblingChild);
	this.levels.push(level);
};
Foreach.prototype.update = function(items) {
	this.items = items;
	this.disposeLevels();
	this.createLevels(true);
};
Foreach.prototype.propagatePropertyChange = function(propName, propValue) {
	for (var i = 0; i < this.levels.length; i++) {
		this.levels[i].propagatePropertyChange(propName, propValue);
	}
};
Foreach.prototype.getFirstNodeChild = function() {
	if (this.levels[0]) {
		return this.levels[0].getFirstNodeChild();
	}
	return null;
};
Foreach.prototype.disposeLevels = function() {
	for (var i = 0; i < this.levels.length; i++) {
		this.levels[i].dispose();
	}
	this.levels = [];
};
Foreach.prototype.dispose = function() {
	this.disposeLinks();
	this.disposeLevels();
	this.levels = null;
	this.parentElement = null;
	this.parentLevel = null;
	this.items = null
	this.handler = null;
	this.nextSiblingChild = null;
	this.prevSiblingChild = null;
};
function Form() {}
Form.prototype.checkInitials = function() {
	var initials = this.getInitials();
	for (var k in initials) {
		if (isObject(initials[k])) {
			if (k == 'globals') {
			} else if (k == 'options') {
				this.initOptions(initials[k]);
			}
		}
	}	
};
Form.prototype.initOptions = function(options) {
	if (isObject(options)) {
		this.options = options;
		if (!options.ajax) {
			this.createFormElement();
		} else {
			this.createAjaxRequest();
		}
	} else {
		log('no form options');
	}
};
Form.prototype.createFormElement = function() {
	var formElement = document.createElement('form');
	if (isString(this.options.method)) {
		formElement.setAttribute('method', this.options.method);
	}
	if (isString(this.options.action)) {
		formElement.setAttribute('action', this.options.action);
	}
	this.parentElement.appendChild(formElement);
	this.parentElement = formElement;
	if (isBool(this.options.iframe)) {
		var iframeId = generateRandomKey();
		this.createTargetIframe(iframeId);
		formElement.setAttribute('target', iframeId);
	}
	this.formElement = formElement;
};
Form.prototype.onRenderComplete = function() {
	var controlsContainer = this.options.container;
	if (controlsContainer && isString(controlsContainer)) {
		controlsContainer = this.findElement('.' + controlsContainer);
	}
	controlsContainer = controlsContainer || this.parentElement;	
	if (isArray(this.options.controls)) {
		var control;
		for (var i = 0; i < this.options.controls.length; i++) {
			if (isObject(this.options.controls[i])) {
				this.createControl(this.options.controls[i], controlsContainer);
			}
		}
	}
	if (isObject(this.options.submit)) {
		this.createSubmit(this.options.submit, controlsContainer);
	}
};
Form.prototype.createControl = function(options, parentElement) {
	var control;
	switch (options.type) {
		case 'select':
		break;
		case 'textarea':
		break;
		default:
			control = this.createInput(options);
	}
	this.addChild(control, parentElement);
};
Form.prototype.createInput = function(options) {
	return new Input(options);
};
Form.prototype.createSubmit = function(options, parentElement) {
	var control = new Submit(options);
	this.addChild(control, parentElement);
	this.addListener(control, 'submit', this.onSubmit);
};
Form.prototype.createTargetIframe = function(id) {
	var iframe = document.createElement('iframe');
	iframe.setAttribute('id', id);
	iframe.setAttribute('name', id);
	iframe.style.display = 'none';
	this.parentElement.appendChild(iframe);
};
Form.prototype.createAjaxRequest = function() {
	this.request = new AjaxRequest(this.options.action, this.onRequestComplete.bind(this));
};
Form.prototype.onSubmit = function() {
	if (this.isValid()) {
		if (this.formElement) {
			this.formElement.submit();
		} else if (this.request) {
			this.request.send(this.options.method || 'POST', this.getData());
		}
	}
};
Form.prototype.isValid = function() {
	return true;
};
Form.prototype.getData = function() {
	var data = {};
	this.forEachControlChild(function(child) {
		var name = child.getName();
		if (!!name && isString(name)) {
			data[name] = child.getValue();
		}
	});
	return data;
};
Form.prototype.forEachControlChild = function(callback) {
	this.forEachChild(function(child, index) {
		if (child.instanceOf(Control)) {
			callback.call(this, child, index);
		}
	});
};
Form.prototype.onRequestComplete = function(data) {
	if (isObject(data) && data['success']) {
		this.onSuccess(data);
	}
	this.onFailure(data);
};
Form.prototype.onSuccess = function(data) {};
Form.prototype.onFailure = function(data) {
	var error = isObject(data) && isString(data['error']) ? data['error'] : '';
	this.log(error, 'onFailure', data);
};
Form.prototype.disposeInternal = function() {
	this.options = null;
	this.request = null;
	this.formElement = null;
};
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
					element.setAttribute(attrName, props['p'][k]);
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
					element.setAttribute(__A[k] || k, attrValue);
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
					propAttr[0].setAttribute(attrName, attrValue);
					if (attrName == 'value') {
						propAttr[0].value = attrValue;
					}
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
function Menu() {};
Menu.prototype.onRenderComplete = function() {
	if (Router.hasMenu(this)) {
		this.onNavigate(Router.getCurrentRouteName());
	}
};
Menu.prototype.onNavigate = function(viewName) {
	if (this.rendered) {
		if (isElement(this.activeButton)) {
			this.setButtonActive(this.activeButton, false);	
		}
		var button = this.getButton(viewName);
		if (isElement(button)) {
			this.setButtonActive(button, true);
		}
	}
};
Menu.prototype.getButton = function(viewName) {
	return this.findElement('a[role="' + viewName + '"]');
};
Menu.prototype.setButtonActive = function(button, isActive) {
	var activeClassName = this.activeButtonClass || 'active';
	button.toggleClass(activeClassName, isActive);
	if (isActive) {
		this.activeButton = button;
	}
};
Menu.prototype.disposeInternal = function() {
	this.activeButton = null;
};
function View() {}
View.prototype.onRenderComplete = function() {
	this.dispatchReadyEvent();
};
View.prototype.setOnReadyHandler = function(handler) {
	this.onReadyHandler = handler;
};
View.prototype.dispatchReadyEvent = function() {
	if (isFunction(this.onReadyHandler)) {
		this.onReadyHandler();
	}
};
View.prototype.activate = function(isActivated) {
	if (isActivated) {
		this.dispatchReadyEvent();
	}
};
View.prototype.initControllers = function() {
	var controllersToLoad = this.getControllersToLoad();
	if (isArray(controllersToLoad)) {
		for (var i = 0; i < controllersToLoad.length; i++) {
			if (isObject(controllersToLoad[i])) {
				controllersToLoad[i].load();
			}
		}
	}
};
View.prototype.getTitleParams = function() {};
View.prototype.getControllersToLoad = function() {
	return null;
};
function CheckboxHandler() {
	var subscribers = [];
	var handlers = [];
	var options = [];
	var defaultCheckboxClass = 'app-ui-checkbox';
	var defaultCheckboxCheckedClass = 'checked';
	var currentOptions, currentHandler, currentObject,
		currentClasses, currentCheckedClass, currentTarget;
	this.subscribe = function(subscriber, handler, opts) {
		if (isFunction(handler) && subscribers.indexOf(subscriber) == -1) {
			subscribers.push(subscriber);
			handlers.push(handler);
			options.push(opts || null);
			var element = subscriber.getElement();
			if (element) {
				var index = subscribers.length - 1;
				element.addEventListener('click', onClick.bind(null, index), false);
			}
		}
	};
	var onClick = function(index, e) {
		currentTarget = e.target;
		currentClasses = currentTarget.getClasses();
		if (isProperTarget()) {
			defineOptions(index);
			defineCheckedClass();			
			var checked = isChecked();
			currentTarget.toggleClass(currentCheckedClass, checked);
			currentHandler.call(currentObject, {
				'target': currentTarget,
				'value': getValue(),
				'checked': checked,
				'intChecked': checked ? 1 : 0
			});
		}
	};
	var defineOptions = function(index) {
		currentOptions = options[index];
		currentHandler = handlers[index];
		currentObject = subscribers[index];
	};
	var isProperTarget = function() {
		var checkboxClass = Objects.get(currentOptions, 'checkboxClass', defaultCheckboxClass);
		var is = currentClasses.contains(checkboxClass);
		return is || isLabelTarget();
	};
	var isLabelTarget = function() {
		var labelClass = Objects.get(currentOptions, 'labelClass');
		if (isString(labelClass)) {
			return currentClasses.contains(labelClass);
		}
		return false;
	};
	var defineCheckedClass = function() {
		currentCheckedClass = Objects.get(currentOptions, 'checkboxCheckedClass', defaultCheckboxCheckedClass);
	};
	var isChecked = function() {
		return !currentClasses.contains(currentCheckedClass);
	};
	var getValue = function() {
		var value = currentTarget.getData('value');
		return isIntValue() ? ~~value : value;
	};
	var isIntValue = function() {
		return Objects.has(currentOptions, 'intValue', true);
	};
}
CheckboxHandler = new CheckboxHandler();
function ClickHandler() {
	var subscribers = [];
}
ClickHandler = new ClickHandler();
function Dialoger() {
	var dialogs = {};
	var currentDialogId, currentDialogClass, currentDialog,
		currentDialogOptions;
	this.show = function(dialogClass, options, dialogId) {
		if (isFunction(dialogClass)) {
			currentDialogClass = dialogClass;
			currentDialogOptions = options;
			defineDialogId(dialogId);
			defineDialog();
			showDialog();
		}
	};
	var defineDialogId = function(dialogId) {
		currentDialogId = currentDialogClass.constructor.name + (isString(dialogId) ? '_' + dialogId : '');
	};
	var defineDialog = function() {
		if (isUndefined(dialogs[currentDialogId])) {
			dialogs[currentDialogId] = new currentDialogClass();
			dialogs[currentDialogId].render(document.body);
		}
		currentDialog = dialogs[currentDialogId];
	};
	var showDialog = function() {
		if (isObject(currentDialogOptions)) {
			currentDialog.set(currentDialogOptions);
		}
		currentDialog.show();
	};
}
Dialoger = new Dialoger();
function Dictionary() {
	var onLoadCallback;
	var items = {};
	this.load = function(callback) {
		var url = __DICTURL;
		if (!isNone(url)) {
			onLoadCallback = callback;
			var request = new AjaxRequest(url, onLoad.bind(this));
			request.send('GET');
		} else {
			callback();
		}
	};
	this.get = function(key, defaultValue) {
		return Objects.get(items, key, defaultValue);
	};
	this.set = function(key, value) {
		items[key] = value;
	};
	var onLoad = function(data) {
		if (isObject(data)) {
			items = data;
		}
		onLoadCallback();
	};
}
Dictionary = new Dictionary();
var __D = Dictionary;
function Globals() {
	var listeners = {};
	var components = {};
	var views = {};
	var globalVars = {};
	this.addView = function(view, key) {
		if (isUndefined(views[key])) {
			views[key] = view;
		}
	};
	this.getView = function(key) {
		return views[key];
	};
	this.addComponent = function(component, key) {
		if (isUndefined(components[key])) {
			components[key] = component;
		}
	};
	this.getComponent = function(key) {
		return components[key];
	};
	this.removeComponent = function(key) {
		delete components[key];
	};
	this.subscribe = function(globalVarName, callback, subscriber) {
		if (!isArray(listeners[globalVarName])) {
			listeners[globalVarName] = [];
		}
		listeners[globalVarName].push([callback, subscriber]);
	};
	this.unsubscribe = function(subscriber, globalVarName) {
		if (isArray(listeners[globalVarName])) {
			var done = false;
			while (!done) {
				done = true;
				for (var i = 0; i < listeners[globalVarName].length; i++) {
					if (listeners[globalVarName][i][1] == subscriber) {
						listeners[globalVarName].splice(i, 1);
						done = false;
						break;
					}
				}
			}
		}
	};
	this.get = function(globalVarName) {
		return globalVars[globalVarName];
	};
	this.set = function(globalVarName, globalVarValue) {
		globalVars[globalVarName] = globalVarValue;
		if (isArray(listeners[globalVarName])) {
			for (var i = 0; i < listeners[globalVarName].length; i++) {
				if (isFunction(listeners[globalVarName][i][0])) {
					listeners[globalVarName][i][0].call(listeners[globalVarName][i][1] || null, globalVarValue, globalVarName);
				}
			}
		}
	};
	this.has = function(globalVarName, globalVarValue) {
		return Objects.has(globalVars, globalVarName, globalVarValue);
	};
}
Globals = new Globals();
function Popuper() {
	var components;
	var elements;
	var reset = function() {
		components = [];
		elements = [];
	};
	this.watch = function(component, element) {
		if (components.indexOf(component) == -1) {
			components.push(component);
			elements.push(element || null);
		}
	};
	var onBodyMouseDown = function(e) {
		for (var i = 0; i < components.length; i++) {
			if (!isElement(elements[i]) || !e.targetHasAncestor(elements[i])) {
				components[i].hide();
				reset();
			}
		}
	};
	reset();
	var body = document.documentElement;
	body.addEventListener('mousedown', onBodyMouseDown, false);
}
Popuper = new Popuper();
function StoreKeeper() {
	var prefix = 'stored_';
	var secondsInMeasures = {
		'month': 2592000,
		'day'  : 86400,
		'hour' : 3600,
		'min'  : 60
	};
	this.set = function(key, value) {
		var localStorageKey = getLocalStorageKey(key);
		var item = JSON.stringify({
			'data': value,
			'timestamp': (window['Date']['now']()).toString()
		});
		window.localStorage.setItem(localStorageKey, item);
	};
	this.get = function(key) {
		var item = getItem(key);
		return Objects.has(item, 'data') ? item['data'] : null;
	};
	this.getActual = function(key, period) {
		var item = getItem(key);
		return Objects.has(item, 'data') && isActual(item['timestamp'], period) ? item['data'] : null;
	};
	this.remove = function(key) {
		var localStorageKey = getLocalStorageKey(key);
		window.localStorage.removeItem(localStorageKey);
	};
	var isActual = function(savedMilliseconds, period) {
		var nowMilliseconds    = window['Date']['now'](),
			periodMilliseconds = getMilliseconds(period);
		if (isString(savedMilliseconds)) {
			savedMilliseconds = stringToNumber(savedMilliseconds);
		}
		return periodMilliseconds && savedMilliseconds && nowMilliseconds - savedMilliseconds < periodMilliseconds;
	};
	var getItem = function(key) {
		var localStorageKey = getLocalStorageKey(key);
		var item = window.localStorage.getItem(localStorageKey);
		if (!item) return null;
		try {
			item = JSON.parse(item);
		} catch (exception) {
			log('Json parse exception', 'getItem', this, {'item': item});
			return null;	
		}
		return item;
	};
	var getMilliseconds = function(period) {
		var periodNumber  = ~~period.replace(/[^\d]/g, '');
		var periodMeasure =   period.replace(/\d/g, '');
		if (!periodNumber) {
			log('Given period number is empty', 'getMilliseconds', this);
			return 0;		
		}
		if (!secondsInMeasures[periodMeasure]) {
			log('No given measure: ' + periodMeasure, 'getMilliseconds', this);
			return 0;
		}
		return secondsInMeasures[periodMeasure] * periodNumber * 1000;
	};
	var getLocalStorageKey = function(key) {
		return prefix + key;
	};
}
StoreKeeper = new StoreKeeper();
Array.prototype.contains = function(v) {
	var iv = ~~v;
	if (iv == v) return this.indexOf(iv) > -1 || this.indexOf(v + '') > -1;
	return this.indexOf(v) > -1;
};
var StyleNameCache = {};
Element.prototype.setClass = function(className) {
	this.className = className;
}
Element.prototype.toggleClass = function(className, isAdding) {
	if (isAdding) {
		this.addClass(className);
	} else {
		this.removeClass(className);
	}
};
Element.prototype.addClass = function(className) {
	if (isString(className)) {
		var classNames = this.getClasses();
		var addedClasses = className.split(' ');
		for (var i = 0; i < addedClasses.length; i++) {
			if (classNames.indexOf(addedClasses[i]) == -1) {
				classNames.push(addedClasses[i]);
			}
		}
		this.className = classNames.join(' ');
	}
};
Element.prototype.removeClass = function(className) {
	if (isString(className)) {
		var classNames = this.getClasses();
		var removedClasses = className.split(' ');
		var newClasses = [];
		for (var i = 0; i < classNames.length; i++) {
			if (removedClasses.indexOf(classNames[i]) == -1) {
				newClasses.push(classNames[i]);
			}
		}
		this.className = newClasses.join(' ');
	}
};
Element.prototype.hasClass = function(className) {
	return this.getClasses().indexOf(className) > -1;
};
Element.prototype.getClasses = function() {
	var classNames = (this.className || '').trim().replace(/ {2,}/g, ' ');
	if (classNames) {
		return classNames.split(' ');
	}
	return [];
};
Element.prototype.getAncestor = function(selector) {
	if (isNone(selector) || !isString(selector)) {
		return null;
	}
	if (isFunction(Element.prototype.closest)) {
		return this.closest(selector);
	}
	var parts = selector.trim().split(' ');
	var properSelector = parts[parts.length - 1];
	var classes = properSelector.split('.');
	var selectorTag;
	var thisTag = this.tagName.toLowerCase();
	if (!isNone(classes[0])) {
		selectorTag = classes[0].toLowerCase();
	}
	Objects.removeAt(classes, 0);
	var element = this, isSameTag, foundClasses, elementClasses;
	while (element) {
		elementClasses = element.getClasses();
		isSameTag = isUndefined(selectorTag) || selectorTag == thisTag;
		foundClasses = 0;
		for (var i = 0; i < elementClasses.length; i++) {
			if (classes.indexOf(elementClasses[i]) > -1) {
				foundClasses++;
			}
		}
		if (foundClasses == classes.length && isSameTag) {
			return element;
		}
		element = element.parentNode;
	}
	return null;
};
Element.prototype.getData = function(name) {
	return this.getAttribute('data-' + name) || '';
};
Element.prototype.getRect = function() {
	return this.getBoundingClientRect();
};
Element.prototype.setStyle = function(style) {
	var element = this;
	var set = function(value, style) {
		var propertyName = getVendorJsStyleName(style);	
		if (propertyName) {
			element.style[propertyName] = value;
		}
	};
	var getVendorJsStyleName = function(style) {
		var propertyName = StyleNameCache[style];
		if (!propertyName) {
			propertyName = toCamelCase(style);
	    	StyleNameCache[style] = propertyName;
	  	}	
		return propertyName;
	};
	if (typeof style == 'string') {
	    set(value, style);
	} else {
		for (var key in style) {
	  		set(style[key], key);
	   	}
	}
};
MouseEvent.prototype.getTarget = function(selector) {
	return this.target.getAncestor(selector);
};
MouseEvent.prototype.targetHasAncestor = function(element) {
	if (isElement(element)) {
		var target = this.target;		
		while (target) {
			if (target == element) {
				return true;
			}
			target = target.parentNode;
		}
	}
	return false;
};
function AjaxRequest(url, callback, params) {
	var self = this, tempUrl, active = false, 
		withCredentials = false, headers, request, 
		responseType;
	this.setHeaders = function(head) {
		headers = head;
	};
	this.setResponseType = function(respType) {
		responseType = respType;
	};
	this.setWithCredentials = function(withCred) {
		withCredentials = withCred;
	};
	this.execute = function(pars) {
		active = true;
		pars = pars || params;
		var u = tempUrl || url,
			method = this.method || 'POST',
			content = getRequestContent(method, pars);
		createRequest();
		if (method == 'GET') {
			u += content;
			content = '';
		}
		try {
		    request.open(method, correctUrl(u), true);
		} catch (err) {
		    log('Error opening XMLHttpRequest: ' + err.message, 'execute', this);
		    return;
		}
		if (isObject(headers)) {
			for (var k in headers) {
		    	request.setRequestHeader(k, headers[k]);
			};
		}
		if (method != 'GET' && (!headers || !headers['Content-Type'])) {
			request.setRequestHeader('Content-Type', 'application/json');
		}
		if (responseType) {
			request.responseType = responseType;
		}
		request.withCredentials = withCredentials;
		request.send(content);
	};
	this.send = function(method, pars, u) {
		this.method = method;
		tempUrl = u;
		this.execute(pars);
		this.method = null;
		tempUrl = null;
	};
	var correctUrl = function(u) {
		u = u.replace(/^[\.\/]+/, '');
		if (isString(__APIDIR)) {
			var regExp = new RegExp('^' + __APIDIR + "\/");
			u = __APIDIR + '/' + u.replace(regExp, '');
		}
		return '/' + u;
	};
	var createRequest = function() {
		request = new XMLHttpRequest();
		request.onreadystatechange = onReadyStateChange.bind(self);
	};
	var getRequestContent = function(method, pars) {
		if (Objects.empty(pars)) return '';
		if (!isObject(pars)) {
			return pars.toString();
		} else if (pars instanceof FormData) {
			return pars;
		} else if (method == 'GET') {
			var content = [];
			for (var k in pars) {
				content.push(k + '=' + (pars[k] || '').toString());
			}
			return '?' + content.join('&');
		}
		return JSON.stringify(pars || '');
	};
	var onReadyStateChange = function(e) {
		var req = e.target;
		if (active && req.readyState == 4) {
			active = false;
			var response = req.response;
			var data;
			try {
				data = JSON.parse(response);
			} catch (e) {
				data = response;
			}
			if (isFunction(callback)) {
				callback(data);
			}
		}
	};
}
function EventHandler() {
	var listeners = [];
	this.listen = function(element, type, handler) {
		listeners.push([element, type, handler]);
		element.addEventListener(type, handler, false);
	};
	this.listenOnce = function(element, type, handler) {
		var callback = function() {
			handler();
			element.removeEventListener(type, callback, false);
		}
		element.addEventListener(type, callback, false);
	};
	this.unlisten = function(element, type) {
		var listener;
		for (var i = 0; i < listeners.length; i++) {
			listener = listeners[i];
			if (listener && listener[0] == element && listener[1] == type) {
				listener[0].removeEventListener(listener[1], listener[2], false);
				listeners[i] = null;
			}
		}
	};
	this.dispose = function() {
		var listener;
		for (var i = 0; i < listeners.length; i++) {
			listener = listeners[i];
			if (listener) {
				listener[0].removeEventListener(listener[1], listener[2], false);
			}
		}
		listeners = null;
	};
}
function Initialization() {
	this.inherits = function(list) {
		var children, parent, child, initials;
		for (var k = 0; k < list.length; k++) {
			parent = list[k];
			children = list[++k];
			for (var i = 0; i < children.length; i++) {
				child = children[i];
				if (parent != Core) {
					if (!child.prototype.inheritedSuperClasses) {
						child.prototype.inheritedSuperClasses = [];
					}
					child.prototype.inheritedSuperClasses.push(parent);
				}
				for (var method in parent.prototype) {
					if (!child.prototype[method] && isMethodToInherit(method)) {
						child.prototype[method] = parent.prototype[method];
					}
				}
			}
		}
	};
	this.initiate = function(props) {
		var initials = null;
		var initiateParental = function(superClasses, object) {
			for (var i = 0; i < superClasses.length; i++) {
				if (isFunction(superClasses[i].prototype.initiate)) {
					superClasses[i].prototype.initiate.call(object);
				}
				if (isFunction(superClasses[i].prototype.getInitials)) {
					var parentInitials = superClasses[i].prototype.getInitials();
					if (isObject(parentInitials)) {
						initials = extendInitials(initials, parentInitials);
					}
				}
				if (isArray(superClasses[i].prototype.inheritedSuperClasses)) {
					initiateParental(superClasses[i].prototype.inheritedSuperClasses, object);
				}
			}
		};
		if (isArray(this.inheritedSuperClasses)) {
			initiateParental(this.inheritedSuperClasses, this);
		}
		this.props = props || {};
		if (isFunction(this.constructor.prototype.initiate)) {
			this.constructor.prototype.initiate.call(this);
		}
		if (isFunction(this.constructor.prototype.getInitials)) {
			var ownInitials = this.constructor.prototype.getInitials();
			if (isNull(initials)) {
				initials = ownInitials;
			} else if (isObject(ownInitials)) {
				initials = extendInitials(initials, ownInitials);
			}
		}
		this.initials = initials;
	};
	var isMethodToInherit = function(method) {
		return method != __I && method != __GI;
	};
	var extendInitials = function(initials1, initials2) {
		if (isNull(initials1)) {
			initials1 = initials2;
		} else {
			for (var k in initials2) {
				if (isUndefined(initials1[k])) {
					initials1[k] = initials2[k];
				} else {
					Objects.merge(initials1[k], initials2[k]);
				}
			}
		}
		return initials1;
	};
}
Initialization = new Initialization();
function Router() {
	var routes = __ROUTES;
	var properRoutes = {};
	var isHashRouter = !!__HASHROUTER;
	var defaultRoute = __DEFAULTROUTE;
	var indexRoute = __INDEXROUTE;
	var errorRoutes = __ERRORROUTES;
	var handler, bodyElement, menues, currentRoute,
		subscribers, pathParts;
	this.setNavigationHandler = function(handlr) {
		handler = handlr;
	};
	this.init = function() {
		bodyElement = document.querySelector('body');
		initRoutes(routes);
		if (isHashRouter) {
			window.addEventListener('popstate', onNavigate.bind(this));
		}
		onNavigate();
	};
	this.getPathPartAt = function(index) {
		return isArray(pathParts) ? pathParts[index] : '';
	};
	this.redirect = function(viewName, replState) {
		var route;
		var intViewName = ~~viewName;
		if (intViewName == viewName) {
			viewName = intViewName;
		}
		if (isNumber(viewName)) {
			route = getErrorRoute(viewName);
		} else if (isString(viewName)) {
			route = getRouteByName(viewName);
		} else {
			log('redirect view name is invalid', 'redirect', this, {'viewName': viewName});
			return;
		}
		if (!isObject(route)) {
			log('redirect route is invalid', 'redirect', this, {'route': route});
		} else {
			if (replState && !isNumber(viewName)) {
				replaceState(route);
			}
			changeRoute(route, !!replState);
		}
	};
	this.subscribe = function(options, controller) {
		var routeName = currentRoute['name'];
		subscribers = subscribers || [];
		subscribers.push([routeName, options, controller]);
	};
	this.addMenu = function(menu) {
		if (isObject(menu) && isFunction(menu.onNavigate)) {
			menues = menues || [];
			menues.push(menu);
		}
	};
	this.hasMenu = function(menu) {
		return menues.indexOf(menu) > -1;
	};
	this.getCurrentRoute = function() {
		return currentRoute;
	};
	this.getCurrentRouteName = function() {
		if (currentRoute) {
			return currentRoute['name'];
		}
		return null;
	};
	var getRoute = function() {
		var params = window.location.search;
		var path;
		if (isHashRouter) {
			path = window.location.hash;
		} else {
			path = window.location.pathname;
		}
		pathParts = [];
		var properPaths = [];
		path = path.replace(/^[\#\/]+|\/$/g, '').split('/');		
		if (!path[0]) {
			path[0] = indexRoute;
		}
		for (var i = 0; i < path.length; i++) {
			pathParts.push(path[i]);
			var pathName = pathParts.join('/');			
			if (properRoutes[pathName]) {
				properPaths.push(pathName);
			}
		}
		path = properPaths[properPaths.length - 1];
		if (path) {
			return properRoutes[path];
		} else if (defaultRoute && properRoutes[defaultRoute]) {
			return properRoutes[defaultRoute];
		}
		return getErrorRoute(404);
	};
	var initRoutes = function(rts, parents) {
		parents = parents || [];
		var tempParents = Objects.clone(parents);
		var name, path;
		for (var i = 0; i < rts.length; i++) {
			name = rts[i]['name'];
			tempParents.push(name);
			var children = Objects.clone(rts[i]['children']);
			delete rts[i]['children'];
			path = rts[i]['path'] = tempParents.join('/');
			initRouteParams(rts[i]);
			properRoutes[path] = rts[i];
			if (isArray(children)) {
				initRoutes(children, Objects.clone(tempParents));
			}
			tempParents = Objects.clone(parents);
		}
	};
	var initRouteParams = function(route) {
		if (isObject(route['params'])) {
			var dinamicParams = {};
			for (var k in route['params']) {
				if ((/^\$\d+$/).test(route['params'][k])) {
					dinamicParams[k] = route['params'][k].replace(/[^\d]/g, '');
				}
			}
			for (var k in dinamicParams) {
				delete route['params'][k];
			}
			route['dinamicParams'] = dinamicParams;
		}
	};
	var onNavigate = function() {
		var route = getRoute();
		bodyElement.setClass(route['name'] + '-page');
		var accessLevel = route['accessLevel'];
		if (isNumber(accessLevel) && !User.hasAccessLevel(accessLevel)) {
			route = getErrorRoute(401);
		}
		changeRoute(route, true);
	};
	var getRouteByName = function(viewName) {
		for (var k in properRoutes) {
			if (properRoutes[k]['name'] == viewName) {
				return properRoutes[k];
			}
		}
	};
	var getErrorRoute = function(errorCode) {
		if (isFunction(errorRoutes[errorCode])) {
			errorRoutes[errorCode] = {'name': errorCode, 'view': errorRoutes[errorCode], 'error': errorCode};
		}
		return errorRoutes[errorCode];
	};
	var changeRoute = function(route, changeTitle) {
		currentRoute = route;
		if (isFunction(handler)) {
			handler(route, changeTitle);
		} else {
			log('navigation handler is not function', 'changeRoute', this, {'handler': handler});
		}
		if (!isObject(route)) {
			log('route is invalid', 'changeRoute', this, {'route': route});
		}
		informSubscribers();
		informMenues();
	};
	var replaceState = function(route) {
		if (isHashRouter) {
			window.history.replaceState({}, '', '#' + route['path']);
		} else {
			window.location.href = '/' + route['path'];
		}
	};
	var informSubscribers = function() {
		if (isArray(subscribers)) {
			var subscrView, opts, subscriber;
			for (var i = 0; i < subscribers.length; i++) {
				subscrView = subscribers[i][0]
				opts = subscribers[i][1];
				subscriber = subscribers[i][2];
				if (isObject(opts) && subscrView == currentRoute['name']) {
					var routeOptions = {};
					for (var k in opts) {
						routeOptions[k] = this.getPathPartAt(opts[k]);
					}
					subscriber.handleRouteOptionsChange(routeOptions);
				}
			}
		}
	};
	var informMenues = function() {
		if (isArray(menues)) {
			for (var i = 0; i < menues.length; i++) {
				menues[i].onNavigate(currentRoute['name']);
			}
		}
	};
}
function User() {
	var app;
	var loadedItems = 0;
	var attributes = {};
	var loaded = false;
	var loadRequest;
	var saveRequest;
	var initOptions = function() {
		var userOptions = __USEROPTIONS;
		if (isObject(userOptions)) {
			if (userOptions['login'] && isString(userOptions['login'])) {
				loadRequest = new AjaxRequest(userOptions['login'], onLoad.bind(this));
			}
		}
	};
	var loadDictionary = function() {
		Dictionary.load(onLoadDictionary.bind(this));
	};
	this.load = function(application) {
		if (!loaded) {
			app = application;
			if (loadRequest) {
				loadRequest.execute();
				return;
			}
		}
		onLoad(getDefaultAttributes());
	};
	var onLoad = function(attrs) {
		attributes = attrs;
		loadedItems++;
		onLoadItem();
	};
	var onLoadDictionary = function() {
		loadedItems++;
		onLoadItem();
	};
	var onLoadItem = function() {
		if (loadedItems == 2) {
			loaded = true;
			if (app instanceof Function) {
				app = new app();
				app.run();
			}
		}
	};
	this.hasAccessLevel = function(accessLevel, isEqual) {
		if (!isEqual) {
			return attributes['accessLevel'] >= accessLevel;
		}
		return attributes['accessLevel'] == accessLevel;
	};
	this.hasType = function(userType) {
		return attributes['type'] == userType;
	};
	this.isAuthorized = function() {
		return attributes['accessLevel'] > 0;
	};
	this.getAttribute = function(attributeName) {
		return attributes[attributeName];
	};
	this.setAttribute = function(attributeName, attributeValue, isToSave) {
		var attrs = {};
		attrs[attributeName] = attributeValue;
		this.setAttributes(attrs, isToSave);
	};
	this.setAttributes = function(attrs, isToSave) {
		if (isObject(attrs)) {
			for (var k in attrs) {
				attributes[k] = attrs[k];
			}		
			if (isToSave && saveRequest) {
				saveRequest.execute(attributes);
			}
		}
	};
	var getDefaultAttributes = function() {
		return {
			'type': 'guest',
			'accessLevel': 0
		};
	};
	initOptions();
	loadDictionary();
}
function Objects() {
	this.each = function(arr, callback) {
		if (arguments[2]) {
			callback = callback.bind(arguments[2]);
		}
		for (var i = 0; i < arr.length; i++) {
			var result = callback(arr[i], i);
			if (result == '__break') break;
		}
	};
	this.remove = function(arr, item) {
		var idx = arr.indexOf(item);
		if (idx > -1) {
			this.removeAt(arr, idx);
		}
	};
	this.removeAt = function(arr, idx) {
		arr.splice(idx, 1);
	};
	this.equals = function(arr1, arr2) {
		if (typeof arr1 !== typeof arr2) return false;
	    if (isArray(arr1) && isArray(arr2) && arr1.length !== arr2.length) return false;
	    if (isObject(arr1)) {
	        for (var p in arr1) {
	        	if (arr1.hasOwnProperty(p)) {
		            if (isFunction(arr1[p]) && isFunction(arr2[p])) continue;
		            if (isArray(arr1[p]) && isArray(arr2[p]) && arr1[p].length !== arr2[p].length) return false;
		            if (typeof arr1[p] !== typeof arr2[p]) return false;
		            if (isObject(arr1[p]) && isObject(arr2[p])) {
		            	if (!this.equals(arr1[p], arr2[p])) return false; 
		            } else if (arr1[p] !== arr2[p]) {
		            	return false;
		            }
	        	}
	        }
	    } else return arr1 === arr2;
	    return true;		
	};
	this.merge = function() {
		var arrs = arguments;
		if (!isObject(arrs[0])) {
			arrs[0] = {};
		}
		for (var i = 1; i < arrs.length; i++) {
			if (isArrayLike(arrs[i])) {
				for (var k in arrs[i]) {
					arrs[0][k] = arrs[i][k];
				}
			}
		}
	};
	this.clone = function(obj) {
		if (!isArrayLike(obj)) return obj;
		return JSON.parse(JSON.stringify(obj));
	};
	this.get = function(obj, key, defaultValue) {
		return this.has(obj, key) ? obj[key] : defaultValue;
	};
	this.has = function(obj, key, value) {
		if (!isObject(obj)) return false;
		var has = !isUndefined(obj[key]);
		if (has && !isUndefined(value)) {
			return obj[key] == value;
		}
		return has;
	};
	this.empty = function(obj) {
		if (!isArrayLike(obj)) {
			return true;
		}
		if (isObject(obj)) {
			for (var k in obj) {
				return false;
			}
			return true;
		}
		return isUndefined(obj[0]);
	};
}
function log(message, method, object, opts) {
	window.console.log(method === undefined ? message : new Error(message, method, object, opts));
}
function Error(message, method, object, opts) {
	this.object = object;
	this.method = method;
	this.message = message;
	this.opts = opts;
}
function generateRandomKey() {
	var x = 2147483648, now = +new Date();
	return Math.floor(Math.random() * x).toString(36) + Math.abs(Math.floor(Math.random() * x) ^ now).toString(36);
}
function toCamelCase(str) {
	return String(str).replace(/\-([a-z])/g, function(all, match) {
		return match.toUpperCase();
	});
}
function isObject(a) {
	return !!a && typeof a == 'object' && !isNode(a) && !isArray(a);
}
function isArray(a) {
	return a instanceof Array;
}
function isArrayLike(a) {
	return isArray(a) || isObject(a);
}
function isElement(a) {
	return a instanceof Element;
}
function isNode(a) {
	return a instanceof Node;
}
function isFunction(a) {
	return a instanceof Function;
}
function isBool(a) {
	return typeof a == 'boolean';
}
function isString(a) {
	return typeof a == 'string';
}
function isNumber(a) {
	return typeof a == 'number';
}
function isUndefined(a) {
	return a === undefined;
}
function isNull(a) {
	return a === null;
}
function isNone(a) {
	return isUndefined(a) || isNull(a) || a === false || a === 0 || a === '0' || a === '';
}
function stringToNumber(str) {
	return Number(str);
}
function App(props) {
	Initialization.initiate.call(this, props);
};
App.prototype.onNoErrors = function() {
	var menu = this.getChildById('menu');
	//menu.setAppended(true);
};
App.prototype.onError = function(errorCode) {
	var menu = this.getChildById('menu');
	menu.setAppended(false);
};
App.prototype.getTemplateMain = function(p, args) {
	return[{'cmp':TopMenu,'p':{'cmpid':'menu'}},{'t':0,'p':{'c':'app-view-container'}}]
};
function Analytics(props) {
	Initialization.initiate.call(this, props);
};
Analytics.prototype.getTemplateMain = function(p, args) {
	return[{'t':0,'p':{'c':'view-content'}}]
};
function Error401(props) {
	Initialization.initiate.call(this, props);
};
Error401.prototype.onRendered = function() {
};
Error401.prototype.getTemplateMain = function(p, args) {
	return[{'c':{'cmp':AuthForm},'t':0,'p':{'c':'app-auth-form-container'}}]
};
function Error404(props) {
	Initialization.initiate.call(this, props);
};
Error404.prototype.onRendered = function() {
};
Error404.prototype.getTemplateMain = function(p, args) {
	return[{'c':[{'c':'404','t':0,'p':{'c':'app-404-title'}},{'c':__T[0],'t':0,'p':{'c':'app-404-text'}}],'t':0,'p':{'c':'app-404-container'}}]
};
function Favorite(props) {
	Initialization.initiate.call(this, props);
};
Favorite.prototype.onRendered = function() {
};
Favorite.prototype.getTemplateMain = function(p, args) {
	return[{'t':0,'p':{'c':'view-content'}}]
};
function Main(props) {
	Initialization.initiate.call(this, props);
};
Main.prototype.onRendered = function() {
};
Main.prototype.getTemplateMain = function(p, args) {
	return[{'t':0,'p':{'c':'view-content','sc':1}}]
};
function Search(props) {
	Initialization.initiate.call(this, props);
};
Search.prototype.onRendered = function() {
	this.openInformer();
};
Search.prototype.openInformer = function() {
	var datatable = this.getChildById('datatable');
};
Search.prototype.openFilter = function(filterId) {
};
Search.prototype.onFormExpand = function() {
	this.toggle('expanded');
};
Search.prototype.getTemplateMain = function(p, args) {
	return[{'c':[{'cmp':SearchForm,'e':['expand',this.onFormExpand.bind(this)],'p':{'cmpid':'form'}},{'cmp':TendersDataTable,'p':{'cmpid':'datatable'}}],'t':0,'p':{'c':function(){return['view-content',p('expanded')?' form-expanded':'']},'sc':1},'n':{'c':'expanded'}}]
};
Search.prototype.getInitials = function() {
	return {
		'props':{'expanded': true}
	};
};
Search.prototype.getControllersToLoad = function() {
	return [Filters];
};
function DataTable(props) {
	Initialization.initiate.call(this, props);
};
DataTable.prototype.getTemplateMain = function(p, args) {
	return[{'c':{'cmp':DataTableTabPanel},'t':0,'p':{'c':'app-datatable-outer-container'}}]
};
function DataTableTabPanel(props) {
	Initialization.initiate.call(this, props);
};
function TendersDataTable(props) {
	Initialization.initiate.call(this, props);
};
function SearchForm(props) {
	Initialization.initiate.call(this, props);
};
SearchForm.prototype.toggleExpand = function() {
	this.dispatchEvent('expand');
};
SearchForm.prototype.getTemplateMain = function(p, args) {
	return[{'c':[{'c':args.title,'t':0,'p':{'c':'app-search-form-title'}},{'t':0,'e':[0,this.toggleExpand.bind(this)],'p':{'c':'app-search-form-close-side'}},{'t':0,'e':[0,this.toggleExpand.bind(this)],'p':{'c':'app-search-form-close'}},{'cmp':TenderSearchForm}],'t':0,'p':{'c':'app-search-form','sc':1}}]
};
SearchForm.prototype.getInitials = function() {
	return {
		'args':{'title': __[0]}
	};
};
function SearchFormCreateFilterMenu(props) {
	Initialization.initiate.call(this, props);
};
SearchFormCreateFilterMenu.prototype.onCreateButtonClick = function() {
	alert('create filter')
};
SearchFormCreateFilterMenu.prototype.onWizardButtonClick = function() {
	alert('create filter with wizard')	
};
SearchFormCreateFilterMenu.prototype.getInitials = function() {
	return {
		'args':{'className': 'create-filters-menu'},
		'props':{'buttons': [{'name': __[1],'handler': this.onCreateButtonClick},{'name': __[2],'handler': this.onWizardButtonClick}]}
	};
};
function SearchFormFilterMenu(props) {
	Initialization.initiate.call(this, props);
};
SearchFormFilterMenu.prototype.onRendered = function() {
	PopupMenu.prototype.onRendered.call(this);
};
SearchFormFilterMenu.prototype.onLoadFilters = function(filters) {
	this.renderButtons(filters);
};
SearchFormFilterMenu.prototype.onCheckboxChange = function(e) {
	Filters.doAction('set', {'filterId': e.value, 'param': 'isAutoOpen', 'value': e.checked});
};
SearchFormFilterMenu.prototype.getButtonData = function(item) {
	return {
		'value': item['filterId'],
		'name': item['header'],
		'isAutoOpen': item['isAutoOpen']
	};
};
SearchFormFilterMenu.prototype.handleClick = function(value, button) {
	Globals.getView('search').openFilter(value);
};
SearchFormFilterMenu.prototype.getTemplateContent = function(p, args) {
	return[{'t':0,'p':{'c':'app-ui-checkbox'+(args.item.isAutoOpen?' checked':''),'data-value':args.item.value}}]
};
SearchFormFilterMenu.prototype.getInitials = function() {
	return {
		'args':{'className': 'filters-menu','maxHeight': 400},
		'controllers':[{'controller': Filters,'on': {'load': this.onLoadFilters}}],
		'helpers':[{'helper': CheckboxHandler,'callback': this.onCheckboxChange,'options': {'intValue': true,'checkboxClass': 'app-ui-checkbox','checkboxCheckedClass': 'checked','labelClass': null}}]
	};
};
function SearchFormFilters(props) {
	Initialization.initiate.call(this, props);
};
SearchFormFilters.prototype.onLoadFilters = function(filters) {
	this.set('quantity', filters.length);
};
SearchFormFilters.prototype.onSaveFilterClick = function() {
	Dialoger.show(FilterEdit, {'filterId': Globals.get('filterId')});
};
SearchFormFilters.prototype.getTemplateMain = function(p, args) {
	return[{'c':[{'c':[{'c':__[3],'t':1},{'cmp':SearchFormCreateFilterMenu}],'t':0,'p':{'c':'app-search-form-filters-create-button'}},{'c':[{'c':__[4],'t':0,'p':{'c':'app-search-form-filters-button-inner'}},{'c':[{'c':{'pr':'quantity','p':p('quantity')},'t':42,'p':{'c':'app-search-form-filters-button-quantity'}},{'t':0,'p':{'c':'app-search-form-filters-button-plus'}}],'t':0,'p':{'c':'app-search-form-filters-button-side'}},{'cmp':SearchFormFilterMenu}],'t':0,'p':{'c':function(){return['app-search-form-filters-button',!p('quantity')?' with-plus':'']}},'n':{'c':'quantity'}},{'c':{'pr':'filterName','p':p('filterName')},'t':0,'e':[0,this.onSaveFilterClick.bind(this)],'p':{'c':'app-search-form-filter-name'}},{'c':__[5],'t':0,'e':[0,this.onSaveFilterClick.bind(this)],'p':{'c':'app-search-form-filter-save-button'}}],'t':0,'p':{'c':'app-search-form-filters','sc':1}}]
};
SearchFormFilters.prototype.getInitials = function() {
	return {
		'controllers':[{'controller': Filters,'on': {'load': this.onLoadFilters}}],
		'props':{'filterName': 'Master'}
	};
};
function TenderSearchForm(props) {
	Initialization.initiate.call(this, props);
};
TenderSearchForm.prototype.onResetButtonClick = function(e) {
	var button = e.target.getAncestor('.app-search-form-reset');
	button.addClass('active');
	this.delay(function() {
		button.removeClass('active');
	}, 2500);
};
TenderSearchForm.prototype.onResetConfirmed = function() {
};
TenderSearchForm.prototype.getTemplateMain = function(p, args) {
	return[{'c':[{'c':__[6],'t':0,'e':[0,this.onResetButtonClick.bind(this)],'p':{'c':'hover-label'}},{'c':[__[7],{'c':__[8],'t':40,'p':{'c':'confirm-reset-filter'}}],'t':0,'e':[0,this.onResetConfirmed.bind(this)],'p':{'c':'confirm-label'}}],'t':0,'p':{'c':'app-search-form-reset'}},{'c':{'cmp':SearchFormFilters},'t':0,'p':{'c':'app-tender-search-form','sc':1}}]
};
function Submit(props) {
	Initialization.initiate.call(this, props);
};
Submit.prototype.onSubmit = function() {
	this.dispatchEvent('submit');
};
Submit.prototype.getTemplateMain = function(p, args) {
	return[{'c':{'c':{'pr':'value','p':p('value')},'t':0,'e':[0,this.onSubmit.bind(this)],'p':{'c':function(){return[p('class')]}},'n':{'c':'class'}},'t':0,'p':{'c':'app-submit-container'}}]
};
function Checkbox(props) {
	Initialization.initiate.call(this, props);
};
Checkbox.prototype.getTemplateMain = function(p, args) {
	return[{'t':0,'p':{'c':'app-ui-checkbox','data-value':function(){return[p('value')]}},'n':{'data-value':'value'}}]
};
function Dialog(props) {
	Initialization.initiate.call(this, props);
};
Dialog.prototype.show = function() {
	this.set('shown', true);
	this.reposition();	
	this.onShow();
};
Dialog.prototype.reposition = function() {
	var element = this.getElement();
	var rect = element.getRect();
	this.set({
		'marginTop': Math.round(rect.height / -2) + 'px',
		'marginLeft': Math.round(rect.width / -2) + 'px'
	});
};
Dialog.prototype.hide = function() {
	this.set('shown', false);
	this.onHide();
};
Dialog.prototype.expand = function() {
	this.toggle('expanded');
};
Dialog.prototype.onShow = function() {
};
Dialog.prototype.onHide = function() {
};
Dialog.prototype.getTemplateMain = function(p, args) {
	return[{'t':0,'e':[0,this.hide.bind(this)],'p':{'c':function(){return['app-dialog-mask',p('shown')?' shown':'']}},'n':{'c':'shown'}},{'c':[{'c':{'t':0,'e':[0,this.hide.bind(this)],'p':{'c':'app-dialog-close'}},'i':!!args.closable},{'c':{'t':0,'e':[0,this.expand.bind(this)],'p':{'c':'app-dialog-expand'}},'i':!!args.expandable},{'c':{'pr':'title','p':p('title')},'t':0,'p':{'c':'app-dialog-title'}},{'c':{'tmp':this.getTemplateContent},'t':0,'p':{'c':'app-dialog-content','st':function(){return[p('height')?'max-height:'+p('height')+'px;':'']}},'n':{'st':'height'}}],'t':0,'p':{'c':function(){return['app-dialog',p('expanded')?' expanded':'',p('shown')?' shown':'']},'sc':1,'st':function(){return['width:',p('width'),'px;margin-left:',p('marginLeft'),';margin-top:',p('marginTop'),';']}},'n':{'c':['expanded','shown'],'st':['width','marginLeft','marginTop']}}]
};
Dialog.prototype.getTemplateContent = function(p, args) {
	return null
};
Dialog.prototype.getInitials = function() {
	return {
		'args':{'closable': true,'expandable': true},
		'followers':{'width': this.reposition,height: this.reposition},
		'props':{'width': 600}
	};
};
function PopupMenu(props) {
	Initialization.initiate.call(this, props);
};
PopupMenu.prototype.onRendered = function() {
	var element = this.getElement();
	this.button = element.parentNode;
	this.addListener(this.button, 'click', this.onShowButtonClick);
};
PopupMenu.prototype.onClick = function(e) {
	var target = e.getTarget('.app-popup-menu-button');
	if (!isNull(target)) {
		var idx = target.getData('index');
		var value = target.getData('value');
		var buttons = this.get('buttons');
		if (isArray(buttons) && isObject(buttons[idx]) && isFunction(buttons[idx]['handler'])) {
			buttons[idx]['handler'].call(this, e);
			return;
		}
		this.handleClick(value, target);
	}
};
PopupMenu.prototype.onShowButtonClick = function() {
	this.onBeforeShow();
	this.show();
};
PopupMenu.prototype.handleClick = function(value, button) {
};
PopupMenu.prototype.onBeforeShow = function() {
};
PopupMenu.prototype.show = function() {
	var outerElement = this.getElement();
	var innerElement = this.findElement('.app-popup-menu-inner-container');
	var rect = innerElement.getRect();
	var height = Math.min(rect.height, Objects.get(this.options, 'maxHeight', 400));
	outerElement.setStyle({'max-height': height + 'px', 'height': height + 'px'});
    this.button.addClass('active');
    Popuper.watch(this, outerElement);
};
PopupMenu.prototype.hide = function() {
	var outerElement = this.getElement();
	outerElement.setStyle({'max-height': '0', 'height': '0'});
	this.button.removeClass('active');
};
PopupMenu.prototype.renderButtons = function(items) {
	var buttons = [];
	for (var i = 0; i < items.length; i++) {	
		buttons.push(this.getButtonData(items[i]));
	}
	this.set('buttons', buttons);
};
PopupMenu.prototype.getButtonData = function(item) {
	return {
		'value': item['value'],
		'name': item['name']
	};
};
PopupMenu.prototype.getTemplateMain = function(p, args) {
	return[{'c':{'c':{'h':(function(button,idx){return[{'c':[button.name,{'tmp':this.getTemplateContent,'p':{'item':button}}],'t':0,'p':{'c':'app-popup-menu-button','data-value':button.value,'data-index':idx}}]}).bind(this),'p':p('buttons'),'f':'buttons'},'t':0,'e':[0,this.onClick.bind(this)],'p':{'c':'app-popup-menu-inner-container','st':(args.maxHeight?'max-height:'+args.maxHeight+'px;' :'')}},'t':0,'p':{'c':'app-popup-menu-outer-container '+args.className,'sc':1}}]
};
PopupMenu.prototype.getTemplateContent = function(p, args) {
	return null
};
function TabPanel(props) {
	Initialization.initiate.call(this, props);
};
TabPanel.prototype.getTemplateMain = function(p, args) {
	return[{'t':0,'p':{'c':'app-tab-panel'}}]
};
function Filters() {
	Initialization.initiate.call(this);
	this.processInitials();
};
Filters.prototype.onLoadFilters = function(data) {
};
Filters.prototype.onLoad = function(data) {
};
Filters.prototype.onAdd = function(data) {
};
Filters.prototype.getInitials = function() {
	return {
		'options':{'key': 'filterId','store': false,'storeAs': 'filters','storePeriod': '1day','clone': true},
		'actions':{'load': {'url' : CONFIG.filters.load,'method' : 'GET','callback': this.onLoad.bind(this)},'save': {'url' : CONFIG.filters.add,'method' : 'POST','callback': this.onAdd},'set': {'url' : CONFIG.filters.set,'method': 'POST'}}
	};
};
function FilterEdit(props) {
	Initialization.initiate.call(this, props);
};
FilterEdit.prototype.initiate = function() {
	this.controller = Filters;
};
FilterEdit.prototype.getInitials = function() {
	return {
		'props':{'title': __[12]}
	};
};
function AuthForm(props) {
	Initialization.initiate.call(this, props);
};
AuthForm.prototype.onSuccess = function() {
	window.location.reload();
};
AuthForm.prototype.getTemplateMain = function(p, args) {
	return[{'c':__T[1],'t':0,'p':{'c':'app-authform-logo'}},{'c':__[11],'t':0,'p':{'c':'app-authform-text'}},{'t':0,'p':{'c':'app-authform-inputs'}}]
};
AuthForm.prototype.getInitials = function() {
	return {
		'loader':{controller: Filters,async: true,options: {a: this.ass}},
		'options':{'action': 'api/user/login.php','method': 'POST','ajax': true,'container': 'app-authform-inputs','controls': [{'cmpid': 'login','type': 'text','name': 'login','placeholder': 'Введите логин','caption': 'Логин'},{'cmpid': 'password','type': 'password','name': 'password','placeholder': 'Введите пароль','caption': 'Пароль'}],'submit': {'value': 'Войти','class': 'app-submit'}}
	};
};
function Input(props) {
	Initialization.initiate.call(this, props);
};
Input.prototype.getTemplateMain = function(p, args) {
	return[{'c':[{'c':function(){return[{'c':{'pr':'caption','p':p('caption')},'t':0,'p':{'c':'app-input-caption'}}]},'p':['caption'],'i':function(){return(p('caption'))}},{'t':14}],'t':0,'p':{'c':'app-input-container'}}]
};
function TopMenu(props) {
	Initialization.initiate.call(this, props);
	Router.addMenu(this);
	this.isRouteMenu = true;
};
TopMenu.prototype.getTemplateMain = function(p, args) {
	return[{'c':[{'t':12,'p':{'h':'#main','c':'app-top-menu-logo'}},{'c':__T[2],'t':12,'p':{'h':'#main','r':'main'}},{'c':__T[3],'t':12,'p':{'h':'#search','r':'search'}},{'c':__T[4],'t':12,'p':{'h':'#favorite','r':'favorite'}},{'c':__T[5],'t':12,'p':{'h':'#planzakupok','r':'planzakupok'}},{'c':__T[6],'t':12,'p':{'h':'#analytics','r':'analytics'}}],'t':0,'p':{'c':'app-top-menu','sc':1}}]
};
Initialization.inherits([Core,[Component,Foreach,Condition],Component,[Application,View,Form,Control,Menu,DataTable,SearchForm,SearchFormFilters,TenderSearchForm,Submit,Checkbox,Dialog,PopupMenu,TabPanel],Application,[App],View,[Analytics,Error401,Error404,Favorite,Main,Search],DataTable,[TendersDataTable],Controller,[Filters],Dialog,[FilterEdit],Form,[AuthForm],Control,[Input],Menu,[TopMenu],TabPanel,[DataTableTabPanel],PopupMenu,[SearchFormCreateFilterMenu,SearchFormFilterMenu]]);
Objects = new Objects();
Router = new Router();
User = new User();
Filters = new Filters();
User.load(App);
})();