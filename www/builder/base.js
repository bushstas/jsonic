'use strict';
var _G_;
new (function() {
(function(){
var cs={};
this.create=function(k){var c=this.get(k);if(c instanceof Function)cs[k]=new c()}
this.get=function(k,i){if(i){this.create(k)}return cs[k]}
this.set=function(c,k,i) {if(cs[k])return;cs[k]=c;if(i){this.create(k)}}
}).call(_G_=this);
;(function(){var c,p;
var CONFIG = {'user':{'get':'user/get.php'},'filters':{'load':'filters/get.php','save':'filters/add.php','set':'filters/set.php','subscribe':'filters/subscribe.php'},'support':{'send':'support/send.php'},'orderCall':{'send':'orderCall/send.php'},'favorites':{'get':'favorites/get.php','add':'favorites/add.php','remove':'favorites/remove.php'},'filterStat':{'load':'filters/count.php'},'settings':{'subscr':'settings/get.php','set':'settings/set.php'},'keywords':{'get':'keywords/get.php','recommendations':'keywords/getRecommendations.php'}};_G_.set(CONFIG,'_ac');
var __,__T,__V,__DICTURL;
var __LU = '/api/loadApp.php';
var __TAGS = ['div','span','table','tbody','thead','tr','td','th','ul','ol','li','p','a','form','input','img','video','audio','aside','article','b','big','blockquote','button','canvas','caption','code','col','colgroup','footer','h1','h2','h3','h4','h5','h6','header','hr','i','iframe','label','menu','pre','s','section','select','strong','textarea','small','nav','abbr','address','area','map','source','basefont','cite','datalist','dt','dl','dd','del','details','dfn','em','embed','fieldset','figcaption','figure','ins','kbd','keygen','main','mark','meter','optgroup','option','output','param','progress','q','samp','sub','summary','sup','tfoot','time','var','wbr'];
var __A = {'c':'class','i':'id','v':'value','t':'title','p':'placeholder','tp':'type','h':'href','s':'src','tr':'target','m':'method','st':'style','w':'width','ht':'height','sz':'size','mx':'maxlength','a':'action','n':'name','sc':'scope','r':'role','cp':'cellpadding','cs':'cellspacing'};
var __DW = {'filter':['фильтр','фильтра','фильтров'],'subscr':['подписка на рассылку','подписки на рассылку','подписок на рассылку']};
var __EVENTTYPES = ['click','mouseover','mouseout','mouseenter','mouseleave','mousemove','contextmenu','dblclick','mousedown','mouseup','keydown','keyup','keypress','blur','change','focus','focusin','focusout','input','invalid','reset','search','select','submit','drag','dragend','dragenter','dragleave','dragover','dragstart','drop','copy','cut','paste','popstate','wheel','storage','show','toggle','touchend','touchmove','touchstart','touchcancel','message','error','open','transitionend','abort','play','pause','load','durationchange','progress','resize','scroll','unload','hashchange','beforeunload','pageshow','pagehide'];
var __ROUTES = [{'name':'main','view':'Main','accessLevel':10,'title':'Home','load':[0,1],'params':{'text':'blablabla','name':'$2'}},{'name':'search','view':'Search','accessLevel':0,'title':'Поиск','load':[1]},{'name':'favorite','view':'Favorite','accessLevel':0,'title':'Избранное'}];
var __ERRORROUTES = {'404':'Error404','401':'Error401'};
var __HASHROUTER = true;
var __INDEXROUTE = 'main';
var __DEFAULTROUTE = null;
var __VIEWCONTAINER = 'app-view-container';
var __VIEWCONTAINER2 = 'parental-view-container';
var __TC = 'TooltipPopup';
var __TA = 'tooltip/get.php';
var __APIDIR = 'api';
var __PAGETITLE = 'Page title';
var __USEROPTIONS = {'login':'user/login.php','logout':'user/logout.php','save':'user/save.php','fullAccess':11,'adminAccess':100};
var __CTR = ['Favorites','Filters','FiltersStat','RecommendationsLoader','Subscription','UserInfoLoader'];
var __FNC = function(){return};_G_.set(__FNC,'_nf');
var __SP = function(e){e.stopPropagation()};_G_.set(__SP,'_sp');
var __PD = function(e){e.preventDefault()};_G_.set(__PD,'_pd');
_G_.set((c=function() {
	if (!this || this == window) {
		var controllers, router, dictionary;
		var routes = __ROUTES;
		var errorRoutes = __ERRORROUTES;
		var viewContainerClass = __VIEWCONTAINER;
		var defaultPagetitle = __PAGETITLE;
		var parentalContainerClass = __VIEWCONTAINER2;
		var getViewParams = function(route, allParams) {
			var params;
			if (isObject(route['dynamicParams'])) {
				params = {};
				for (var k in route['dynamicParams']) {
					params[k] = router.getPathPartAt(route['dynamicParams'][k]);
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
		var loadControllers = function(route) {
			if (isArray(route['load']) || isNumber(route['load'])) {
				controllers.load(route['load']);
			}
		};
		var loadView = function(route) {
			var script = document.createElement('script');
			script.src = '/js/base_' + route['name'] + '_chunk.js';
			document.body.appendChild(script);
			script.onload = onViewLoaded.bind(this, route);
		};
		var onViewLoaded = function(route) {
			route['view'] = _G_.get(route['view']);
			renderView.call(this, route);
		};
		var renderView = function(route) {
			var isSameView = this.currentView == route['name'];
			if (!isSameView && this.currentView && this.views[this.currentView]) {
				activateView.call(this, this.views[this.currentView], false);
			}
			this.currentView = route['name'];
			loadControllers(route);
			if (!isUndefined(dictionary)) {
				dictionary.load(route['name']);
			}
			var view = this.views[route['name']] = new route['view']();
			var viewParams = getViewParams.call(this, route, true);
			_G_.get('Core').initiate.call(view, viewParams);
			view.setOnReadyHandler(onViewReady.bind(this));
			var viewContentElement = createViewContentElement.call(this, route['name']);
			view.render(viewContentElement);
			if (isNumber(route['error'])) {
				this.onError(route['error']);
			} else {
				this.onNoErrors();
			}
		};
		var handleNavigation = function(route, changeTitle) {
			this.isChangeTitle = changeTitle;
			this.currentRoute = route;
			var isSameView = this.currentView == route['name'];
			var view = this.views[route['name']];
			if (!view) {
				view = _G_.get(route['view']);
				if (!view) {
					loadView.call(this, route);
				} else {
					route['view'] = view;
					renderView.call(this, route);
				}
			} else {
				activateView.call(this, view, true, isSameView);
			}
		};
		var	defineViews = function() {
			for (var i = 0; i < routes.length; i++) {
				this.views[routes[i]['name']] = null;
				if (isArray(routes[i]['children'])) {
					this.defineViews(routes[i]['children']);
				}
			}
			if (isObject(errorRoutes)) {
				for (var k in errorRoutes) {
					this.views[k] = null;
				}
			}
		};
		var	createViewContainer = function() {
			var viewContainer;
			if (viewContainerClass) {
				viewContainer = document.body.querySelector('.' + viewContainerClass);
			}
			if (!viewContainer) {
			 	viewContainer = document.createElement('div');
				if (viewContainerClass) {
					viewContainer.className = viewContainerClass;
				}
				this.element.appendChild(viewContainer);
			}
			this.viewContainer = viewContainer;
		};
		var activateView = function(view, isActivated, isSameView) {
			if (!view) return;
			var parentElement = _G_.get('Core').getParentElement.call(view);
			if (!isActivated) {
				this.viewContainer.removeChild(parentElement);
			} else {
				var params = getViewParams.call(this, this.currentRoute);
				if (isObject(params)) {
					view.set(params);
				}
				if (!isSameView) {
					this.viewContainer.appendChild(parentElement);
				}
			}
			view.activate(isActivated);
		};
		var onViewReady = function() {
			if (this.isChangeTitle) {
				var title = this.currentRoute['title'];
				if (isString(title)) {
					var titleParams = this.views[this.currentView].getTitleParams();
					if (isObject(titleParams)) {
						var regExp;
						for (var k in titleParams) {
							regExp = new RegExp("\\$" + k);
							title = title.replace(regExp, titleParams[k]);
						}
					}
				}
				this.setPageTitle(title || defaultPagetitle || '');
			}
		};
		var createViewContentElement = function(name) {
			var element = document.createElement('div');
			element.className = parentalContainerClass;
			element.setData('name', name);
			this.viewContainer.appendChild(element);
			return element;
		};
		p=c.prototype;
		p.initiate = function() {
			this.views = {};
		};
		p.run = function() {
			dictionary = _G_.get('Dictionary');
			router = _G_.get('Router');
			controllers = _G_.get('Controllers');
			defineViews.call(this);
			router.setNavigationHandler(handleNavigation.bind(this));
			router.init();
			this.element = document.createElement('div');
			document.body.appendChild(this.element);
			this.render(this.element);
			createViewContainer.call(this);
			router.run();
		};
		p.setPageTitle = function(title) {
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
		p.getView = function(viewName) {
			return this.views[viewName];
		};
		p.disposeView = function(viewName) {
			if (isObject(this.views[viewName])) {
				this.views[viewName].dispose();
				this.views[viewName] = null;
			}
		};
		p.onNoErrors=__FNC;
		p.onError=__FNC;
		return c;
	}
})(), 'Application');
_G_.set((c=function() {	
	if (!this || this == window) {
		var load = function() {
			var loader = Objects.get(this.initials, 'loader');
			if (isObject(loader) && isObject(loader['controller'])) {
				this.preset('__loading', true);
				var isAsync = !!loader['async'];
				var options = loader['options'];
				if (isFunction(options)) options = options();
				loader['controller'].addSubscriber('load', {
					'initiator': this,
					'callback': onDataLoad.bind(this, isAsync)
				}, !!loader['private']);			
				loader['controller'].doAction(this, 'load', options);
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
			this.toggle('__loading');
			this.onLoaded(data);
			var loader = this.initials['loader'];
			if (isFunction(loader['callback'])) {
				loader['callback'].call(this);
			}
			if (!isAsync) onReadyToRender.call(this);
		};
		var onReadyToRender = function() {
			if (!this.isRendered()) {
				doRendering.call(this);
				if (this.tempPlaceholder) {
					this.parentElement.removeChild(this.tempPlaceholder);
					this.tempPlaceholder = null;
				}
				_G_.get('Core').processPostRenderInitials.call(this);
			}
		};
		var doRendering = function() {
			var lvl = _G_.get('Level');
			this.level = new lvl(this);
			var content = this.getTemplateMain(this.props, this);
			if (content) this.level.render(content, this.parentElement, this, this.tempPlaceholder);
			this.rendered = true;
			this.onRendered();
			this.onRenderComplete();
			this.forEachChild(function(child) {
				if (isFunction(child.onParentRendered)) child.onParentRendered.call(child);
			});
			delete this.waiting;
		};
		var propagatePropertyChange = function(changedProps) {
			if (!this.updaters) return;
			var updated = [];
			for (var k in changedProps) {
				if (this.updaters[k]) {
					for (var i = 0; i < this.updaters[k].length; i++) {
						if (updated.indexOf(this.updaters[k][i]) == -1) {
							this.updaters[k][i].react(changedProps);
							updated.push(this.updaters[k][i]);
						}
					}
				}
			}
			updated = null;
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
		var updateForeach = function(propName, index, item) {
			var updaters = this.updaters[propName], o;
			if (isArray(updaters)) {
				for (i = 0; i < updaters.length; i++) {
					if (updaters[i] instanceof _G_.get('OperatorUpdater')) {
						o = updaters[i].getOperator();
						if (o instanceof _G_.get('Foreach')) {
							if (!isUndefined(item)) o.add(item, index);
							else o.remove(index);
						}
					}
				}
			}
		};
		var unrender = function() {
			this.elements = null;
			_G_.get('Core').disposeLinks.call(this);
			this.disposeInternal();
			this.level.dispose();
			this.level = this.listeners = null;
		};
		p=c.prototype;
		p.render = function(parentElement) {
			this.parentElement = parentElement;
			load.call(this);
		};
		p.isDisabled = function() {
			return !!this.disabled;
		};
		p.isRendered = function() {
			return !!this.rendered;
		};
		p.isDisposed = function() {
			return !!this.disposed;
		};
		p.instanceOf = function(classFunc) {
			if (isString(classFunc)) classFunc = _G_.get(classFunc);
			return this instanceof classFunc || (this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(classFunc) > -1);
		};
		p.disable = function(isDisabled) {
			this.disabled = isDisabled;
			this.addClass('disabled', !isDisabled);
		};
		p.dispatchEvent = function(eventType) {
			var args = Array.prototype.slice.call(arguments), l;
			args.splice(0, 1);
			if (isArray(this.listeners)) {
				for (var i = 0; i < this.listeners.length; i++) {
					l = this.listeners[i];
					if (isNumber(l['type'])) l['type'] = __EVENTTYPES[l['type']];
					if (l['type'] == eventType) {
						l['handler'].apply(l['subscriber'], args);
					}
				}
			}
		};
		p.addListener = function(target, eventType, handler) {
	 		if (isElement(target)) {
	 			var eh = _G_.get('EventHandler');
	 			this.eventHandler = this.eventHandler || new eh();
	 			this.eventHandler.listen(target, eventType, handler.bind(this));
	 		} else target.subscribe(eventType, handler, this);
	 	};
		p.removeValueFrom = function(propName, value) {
			var prop = this.get(propName);
			if (isArray(prop)) this.removeByIndexFrom(propName, prop.indexOf(value));
		};
		p.removeByIndexFrom = function(propName, index) {
			var prop = this.get(propName);
			if (isString(index) && isNumeric(index)) index = ~~index;
			if (isArray(prop) && isNumber(index) && index > -1 && !isUndefined(prop[index])) {
				prop.splice(index, 1);
				updateForeach.call(this, propName, index);
				callFollower.call(this, propName, prop);
			}
		};
		p.plusTo = function(propName, add, sign) {
			var prop = this.get(propName);
			if (!sign || sign == '+') {
				if (isNumber(prop) || isString(prop)) this.set(propName, prop + add);
			} else 	if (isNumber(prop) && isNumber(add)) {
				var v;
				if (sign == '-') v = prop - add;
				else if (sign == '*') v = prop * add;
				else if (sign == '/') v = prop / add;
				else if (sign == '%') v = prop % add;
				this.set(propName, v);
			}
		};
		p.addOneTo = function(propName, item, index) {
			this.addTo(propName, [item], index);
		};
		p.addTo = function(propName, items, index) {
			var prop = this.get(propName);
			if (!isArray(items)) items = [items];
			if (isArray(prop)) {
				for (var j = 0; j < items.length; j++) {
					if (!isNumber(index)) prop.push(items[j]);
					else if (index == 0) prop.unshift(items[j]);
					else prop.insertAt(items[j], index);
					updateForeach.call(this, propName, index, items[j]);
					if (isNumber(index)) index++;
				}
				callFollower.call(this, propName, prop);
			}
		};
		p.get = function(propName) {
			var prop = this.props[propName];
			if (isUndefined(arguments[1]) || !isArrayLike(prop)) return prop;
			var end;
			for (var i = 1; i < arguments.length; i++) {
				prop = prop[arguments[i]];
				if (isUndefined(prop)) return '';
				end = arguments.length == i + 1;
				if (end || !isArrayLike(prop)) break;
			}
			return end ? prop || '' : '';
		};
		p.showElement = function(element, isShown) {
			if (isString(element)) element = this.findElement(element);
			if (isElement(element)) element.show(isShown);
		};
		p.setStyle = function(styles) {
			if (this.isRendered()) this.getElement().css(styles);
		};
		p.setPosition = function(x, y) {
			this.setStyle({'top': y + 'px', 'left': x + 'px'});
		};
		p.setVisible = function(isVisible) {
			if (this.isRendered() && !this.isDisposed()) this.getElement().show(isVisible);
		};
		p.addClass = function(className, isAdding) {
			if (this.isRendered()) {
				if (isAdding || isUndefined(isAdding)) this.getElement().addClass(className);
				else this.getElement().removeClass(className);
			}
		};
		p.each = function(propName, callback) {
			var ar = this.get(propName);
			if (isArrayLike(ar) && isFunction(callback)) {
				if (isArray(ar)) for (var i = 0; i < ar.length; i++) callback.call(this, ar[i], i, ar);
				else for (var k in ar) callback.call(this, ar[k], k, ar);
			}
		};
		p.toggle = function(propName) {
			this.set(propName, !this.get(propName));
		};
		p.set = function(propName, propValue) {
			this.props = this.props || {};
			var props;
			if (!isUndefined(propValue)) {
				props = {};
				props[propName] = propValue;
			} else if (isObject(propName)) {
				props = propName;
			} else return;
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
		p.preset = function(propName, propValue) {
			this.props = this.props || {};
			this.props[propName] = propValue;
		};
		p.delay = function(f, n, p) {
			window.clearTimeout(this.timeout);
			if (isFunction(f)) this.timeout = window.setTimeout(f.bind(this, p), n || 200);
		};
		p.addChild = function(child, parentElement) {
			this.level.renderComponent(child, parentElement);
		};
		p.removeChild = function(child) {
			if (!child) return;
			var childId = child;
			if (isString(child)) child = this.getChild(child);
			else childId = Objects.getKey(this.children, child);
			if (isComponentLike(child)) child.dispose();
			if ((isString(childId) || isNumber(childId)) && isObject(this.children)) {
				this.children[childId] = null;
				delete this.children[childId];
			}
		 };
		p.forEachChild = function(callback) {
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
		p.forChildren = function(className, callback) {
			var children = this.getChildren(className), result;
			for (var i = 0; i < children.length; i++) {
				result = callback.call(this, children[i], i);
				if (result) return result;
			}
		};
		p.getControl = function(name) {
			return Objects.get(this.controls, name) || this.forEachChild(function(child) {
				return child.getControl(name);
			});
		};
		p.setControlValue = function(name, value) {
			var control = this.getControl(name);
			if (control) control.setValue(value);
		};
		p.enableControl = function(name, isEnabled) {
			var control = this.getControl(name);
			if (control) control.setEnabled(isEnabled);
		};
		p.forEachControl = function(callback) {
			if (isObject(this.controls)) Objects.each(this.controls, callback, this);
		};
		p.hasControls = function() {
			return !Objects.empty(this.controls);
		};
		p.getControlsData = function(data) {
			data = data || {};
			this.forEachChild(function(child) {
				if (!isControl(child)) child.getControlsData(data);
				else data[child.getName()] = child.getValue();
			});
			return data;
		};
		p.setControlsData = function(data) {
			this.forEachChild(function(child) {
				if (!isControl(child)) child.setControlsData(data);
				else child.setValue(data[child.getName()]);
			});
			return data;
		};
		p.getChildAt = function(index) {
			return Objects.getByIndex(this.children, index);
		};
		p.getChildIndex = function(child, same) {
			var idx = -1;
			this.forEachChild(function(ch) {
				if (!same || (same && ch.constructor == child.constructor)) idx++;
				if (ch == child) return true;
			});
			return idx;
		};
		p.getChildren = function(className) {
			if (!isString(className)) return this.children;
			var children = [];
			this.forEachChild(function(child) {
				if (isComponentLike(child) && child.instanceOf(className)) children.push(child);
			});
			return children;
		};
		p.getChild = function(id) {
			return Objects.get(this.children, id);
		};
		p.setId = function(id) {
			this.componentId = id;
		};
		p.getId = function() {
			return this.componentId;
		};
		p.getElement = function(id) {
			if (isString(id)) return Objects.get(this.elements, id);
			else return this.scope || this.parentElement;
		};
		p.findElement = function(selector, scopeElement) {
			return (scopeElement || this.getElement()).querySelector(selector);
		};
		p.findElements = function(selector, scopeElement) {
			return Array.prototype.slice.call((scopeElement || this.scope || this.parentElement).querySelectorAll(selector));
		};
		p.fill = function(element, data) {
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
		p.setAppended = function(isAppended) {
			if (this.level) this.level.setAppended(isAppended);
		};
		p.placeTo = function(element) {
			if (this.level) this.level.placeTo(element);
		};
		p.placeBack = function() {
			this.setAppended(true);
		};
		p.appendChild = function(child, isAppended) {
			if (isString(child)) child = this.getChild(child);
			if (isComponentLike(child)) child.setAppended(isAppended);
		};
		p.setScope = function(scope) {
			this.scope = scope;
		};
		p.getUniqueId = function() {
			return this.uniqueId = this.uniqueId || generateRandomKey();
		};
		p.dispose = function() {
			_G_.get('State').dispose(this);
			unrender.call(this);
			if (this.mouseHandler) {
				this.mouseHandler.dispose();
				this.mouseHandler = null;
			}
			this.updaters = null;
			this.parentElement = null;
			this.props = null;
			this.provider = null;
			this.children = null;
			this.disposed = true;
			this.initials = null;
			this.followers = null;
			this.correctors = null;
			this.controls = null;
		};
		p.a = function(n) {
			return _G_.get('State').get(n);
		};
		var f = function(){return};
		p.initOptions=f;
		p.onRendered=f;
		p.onRenderComplete=f;
		p.onLoaded=f;
		p.getTemplateMain=f;
		p.disposeInternal=f;
		p.g=p.get;
		p.d=p.dispatchEvent;
		return c;
	}
})(), 'Component');
_G_.set((c=function(params) {
	this.params = params;
	this.isTrue = !!this.params['i']();
}), 'Condition');
p=c.prototype;
p.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevel(false);
};
p.createLevel = function(isUpdating) {
	this.level = new (_G_.get('Level'))(this.parentLevel.getComponent());
	var nextSiblingChild = isUpdating ? _G_.get('Core').getNextSiblingChild.call(this) : null;
	this.level.render(this.getChildren(), this.parentElement, this.parentLevel, nextSiblingChild);
};
p.update = function() {
	var isTrue = !!this.params['i']();
	if (isTrue != this.isTrue) {
		this.isTrue = isTrue;
		this.disposeLevel();
		this.createLevel(true);
	}
};
p.getChildren = function() {
	if (this.isTrue) return isFunction(this.params['c']) ? this.params['c']() : this.params['c'];
	return isFunction(this.params['e']) ? this.params['e']() : this.params['e'];
};
p.disposeLevel = function() {
	if (this.level) {
		this.level.dispose();
		this.level = null;
	}
};
p.dispose = function() {
	_G_.get('Core').disposeLinks.call(this);
	this.disposeLevel();
	this.parentElement = null;
	this.parentLevel = null;
	this.params = null;
	this.nextSiblingChild = null;
};
_G_.set((c=function() {
	if (!this || this == window) {
		var onChangeChildControl = function(e) {
			this.dispatchChange();
		};
		p=c.prototype;
		p.onChange = function(e) {};
		p.dispatchChange = function() {		
			var params = this.getChangeEventParams();
			this.onChange(params);
			this.dispatchEvent('change', params);
		};
		p.getChangeEventParams = function() {
			return {value: this.getValue()};
		};
		p.initiate = function() {
			this.preset('enabled', true);
		};
		p.registerControl = function(control, name) {
			_G_.get('Component').prototype.registerControl.call(this, control, name);
		 	this.addListener(control, 'change', onChangeChildControl.bind(this));
		};
		p.setName = function(name) {
			this.name = name;
		};
		p.getName = function() {
			return this.name;
		};
		p.getValue = function() {
			var value;
			if (this.hasControls()) {
				value = {};
				for (var k in this.controls) {
					if (isArray(this.controls[k])) {
						value[k] = [];
						for (var i = 0; i < this.controls[k].length; i++) value[k].push(this.controls[k][i].getValue());
					} else value[k] = this.controls[k].getValue();
				}
			} else value = this.getControlValue();
			return value;
		};
		p.getControlValue = function() {
			return this.get('value');
		};
		p.getProperValue = function(value) {
			return value;
		};
		p.setValue = function(value, fireChange) {
			if (this.hasControls()) {
				this.setControlsData(value);
			} 
			this.setControlValue(value);
			if (fireChange) this.dispatchChange();
		};
		p.setControlValue = function(value) {
			this.set('value', value);
		};
		p.isEnabled = function() {
			return !!this.get('enabled');
		};
		p.setEnabled = function(isEnabled) {
			this.set('enabled', isEnabled);
		};
		p.clear = function(fireChange) {
			this.clearControl();
			if (fireChange) this.dispatchChange();
		};
		p.clearControl = function() {
			this.setControlValue('');
		};		
		p.disposeInternal = function() {
			this.controls = null;
			this.options = null;
		};
	}
	return c;
})(), 'Control');
_G_.set((c=function() {	
	if (!this || this == window) {
		var makeUrl = function(url, options) {
			var regExp, tmpUrl;
			for (var k in options) {
				if (isString(options[k]) || isNumber(options[k])) {
					regExp = new RegExp('\\$' + k)
					tmpUrl = url;
					url = url.replace(regExp, options[k]);
					if (tmpUrl != url) delete options[k];
				}
			}
			return url;
		};
		var gotFromStore = function(actionName, options, initiator) {
			if (actionName == 'load' && shouldStore.call(this)) {
				var storeAs = getStoreAs.call(this, options);
				if (isString(storeAs) && typeof StoreKeeper != 'undefined') {
					var storedData = StoreKeeper.getActual(storeAs, Objects.get(this.options, 'storePeriod'));
					if (isArrayLike(storedData)) {
						onActionComplete.call(this, actionName, true, initiator, storedData);
						return true;
					}
				}
			}
			return false;
		};
		var isPrivate = function(initiator) {
			return initiator && this.privateSubscribers.has(initiator.getUniqueId());
		}
		var onActionComplete = function(actionName, isFromStorage, initiator, data) {
			this.activeRequests.removeItem(actionName);
			if (initiator && !isPrivate.call(this, initiator)) initiator = null;
			this.data = this.data || {};
			this.data[actionName] = data;
			var action = getAction.call(this, actionName);
			if (isObject(action) && isFunction(action['callback'])) {
				action['callback'].call(this, data);
			}
			if (action['autoset']) autoset.call(this, action['autoset'], data, initiator);
			this.dispatchEvent(actionName, data, initiator);
			if (!isFromStorage && actionName == 'load' && shouldStore.call(this)) {
				store.call(this, true, data);
			}
		};
		var autoset = function(opts, data, initiator) {
			var props = {};
			if (isString(opts)) {
				props[opts] = data; 
			} else if (isObject(opts)) {
				for (var k in opts) props[opts[k]] = data[k];
			}
			if (initiator) initiator.set(props);
			else if (isArray(this.subscribers['load'])) {
				for (var i = 0; i < this.subscribers['load'].length; i++) {
					this.subscribers['load'][i]['initiator'].set(props);
				}
			}
		};
		var shouldStore = function() {
			var should = Objects.get(this.options, 'store');
			if (should === false) return false;
			return Objects.has(this.options, 'storeAs');
		};
		var store = function(isAdding, data) {
			if (typeof StoreKeeper == 'undefined') return;
			var storeAs = getStoreAs.call(this, data);
			if (isAdding) {		
				StoreKeeper.set(storeAs, data);
			} else {
				StoreKeeper.remove(storeAs);
			}
		};
		var getStoreAs = function(data) {
			var storeAs = Objects.get(this.options, 'storeAs');
			if (isArrayLike(data) && isString(storeAs) && (/\$[a-z_]/i).test(storeAs)) {
				var parts = storeAs.split('$');
				storeAs = parts[0];
				for (var i = 1; i < parts.length; i++) {
					if (data[parts[i]]) storeAs += data[parts[i]];
					else storeAs += parts[i];				
				}
			}
			return storeAs;
		};
		var getPrimaryKey = function() {
			return Objects.get(this.options, 'key', 'id');
		};
		var initActionRouteOptions = function(action) {
			var value;
			this.currentRouteOptions = {};
			var routeOptions = {};
			for (var k in action['routeOptions']) {
				value = Router.getPathPartAt(action['routeOptions'][k]);
				if (isString(value)) {
					routeOptions[k] = value;
				}
			}
			setCurrentRouteOptions.call(this, routeOptions, action);
			Router.subscribe(action['routeOptions'], this);
		};
		var setCurrentRouteOptions = function(routeOptions, action) {
			this.currentRouteOptions = routeOptions;
			if (!isObject(action['options'])) {
				action['options'] = {};
			}
			for (var k in routeOptions) {
				action['options'][k] = routeOptions[k];
			}
		};
		var getAction = function(actionName) {	
			var actions = Objects.get(this.initials, 'actions');
			if (isObject(actions)) {
				var action = actions[actionName];
				if (isObject(action)) {
					if (!isString(action['name'])) {
						if (isObject(action['routeOptions']) && actionName == 'load') {
							initActionRouteOptions.call(this, action);
						}
						action['name'] = actionName;
					}
					return action;
				}
			}
			return null;
		};
		var getNewRequest = function() {
			var ajr = _G_.get('AjaxRequest');
			return new ajr();
		};
		var getRequest = function(action) {
			return this.requests[action['name']] = this.requests[action['name']] || getNewRequest();
		};
		var getOptions = function(options, action, initiator) {
			if (!isObject(options)) options = {};
			if (isObject(action['options'])) Objects.merge(options, action['options']);
			if (isPrivate.call(this, initiator)) {
				Objects.merge(options, getPrivateOptions.call(this, initiator));
			}
			return options;
		};
		var getPrivateOptions = function(initiator) {
			return this.privateOptions[initiator.getUniqueId()];
		};
		var send = function(action, options, initiator) {
			var url = makeUrl(action['url'], options);
			var req = getRequest.call(this, action);
			req.setCallback(onActionComplete.bind(this, action['name'], false, initiator));
			req.send(action['method'], options, url);
			this.activeRequests.push(action['name']);		
		};
		p=c.prototype;
		p.initiate = function() {
			this.subscribers = {};
			this.requests = {};
			this.activeRequests = [];
			this.privateSubscribers = [];
			this.privateOptions = {};
		};
		p.addSubscriber = function(actionName, data, isPriv, options) {
			this.subscribers[actionName] = this.subscribers[actionName] || [];
			this.subscribers[actionName].push(data);
			if (isPriv) {
				var uid = data['initiator'].getUniqueId();
				this.privateSubscribers.push(uid); 
				if (options) this.privateOptions[uid] = options;
			}
		};
		p.removeSubscriber = function(initiator) {
			this.privateSubscribers.removeItem(initiator.getUniqueId());
			var done = false;
			for (var actionName in this.subscribers) {
				for (var i = 0; i < this.subscribers[actionName].length; i++) {
					if (this.subscribers[actionName][i]['initiator'] == initiator) {
						this.subscribers[actionName].splice(i, 1);
						break;
					}
				}
			}
		};
		p.dispatchEvent = function(actionName, data, initiator) {
			var dataToDispatch = data;
			if (Objects.has(this.options, 'clone', true)) dataToDispatch = Objects.clone(data);
			var s = this.subscribers[actionName], i, p;
			if (isArray(s)) {
				for (i = 0; i < s.length; i++) {
					p = (!initiator && !isPrivate.call(this, s[i]['initiator'])) || initiator == s[i]['initiator'];
					if (p && isFunction(s[i]['callback'])) {
						s[i]['callback'].call(s[i]['initiator'], dataToDispatch, this);
					}
				}
			}
		};
		p.instanceOf = function(classFunc) {
			if (isString(classFunc)) classFunc = _G_.get(classFunc);
			return this instanceof classFunc || (this.inheritedSuperClasses && this.inheritedSuperClasses.indexOf(classFunc) > -1);
		};
		p.getData = function(actionName) {
			return !!action && !!this.data && isObject(this.data) ? this.data[action] : this.data;
		};
		p.getItemById = function(id) {
			var primaryKey = getPrimaryKey.call(this);
			var data = this.data['load'];
			if (isArray(data)) {
				for (var i = 0; i < data.length; i++) {
					if (Objects.has(data[i], primaryKey, id)) return data[i];
				}
			}
			return null;
		};
		p.getItem = function(nameOrIndex, actionName) {
			actionName = actionName || 'load';
			return isArrayLike(this.data[actionName]) ? this.data[actionName][nameOrIndex] : null;
		};
		p.doAction = function(initiator, actionName, options) {
			if (this.activeRequests.has(actionName)) return;
			var action = getAction.call(this, actionName);
			if (isObject(action) && !gotFromStore.call(this, actionName, options, initiator)) {
				options = getOptions.call(this, options, action, initiator);
				send.call(this, action, options, initiator);
			}
		};
		p.handleRouteOptionsChange = function(routeOptions) {
			if (!Objects.equals(routeOptions, this.currentRouteOptions)) {
				setCurrentRouteOptions.call(this, routeOptions, getAction.call(this, 'load'));
				this.doAction(null, 'load');
			}
		};
		p.dispose = function() {
			this.subscribers = null;
			for (var k in this.requests) this.requests[k].dispose();
			this.options = null;
			this.request = null;
			this.data = null;
			this.initials = null;
			this.activeRequests = null;
			this.requests = null;
			this.currentRouteOptions = null;
			this.privateSubscribers = null;
			this.privateOptions = null;
		};
	}
	return c;
})(), 'Controller');
_G_.set(c=function(params) {
	var handler = params['h'];
	var isRight = !!params['r'];
	var isRandom = !!params['ra'];
	var ifEmpty = params['ie'];
	this.levels = [];
	var getKeysInRandomOrder = function() {
		var keys = Objects.getKeys(getItems());
		keys.shuffle();
		return keys;
	};
	var createIfEmptyLevel = function() {
		if (!isUndefined(ifEmpty)) {
			this.createLevel(ifEmpty);
		}
	};
	var getItems = function() {
		if (isFunction(params['p'])) {
			return params['p']();
		} 
		return params['p'];
	};
	var getLimit = function() {
		if (isFunction(params['l'])) {
			return params['l']();
		} 
		return ~~params['l'];
	};
	this.createLevels = function(isUpdating) {
		var items = getItems();
		var limit = getLimit();
		if (isArrayLike(items)) {
			if (isRandom) {
				if (!Objects.empty(items)) {
					var keys = getKeysInRandomOrder();
					for (var i = 0; i < keys.length; i++) {
						if (limit && i + 1 > limit) break;
						this.createLevel(handler(items[keys[i]], keys[i]), isUpdating);
					}
					return;
				}
			} else if (isArray(items)) {
				if (!items.isEmpty()) {
					if (!isRight) {
						for (var i = 0; i < items.length; i++) {
							if (limit && i + 1 > limit) break;
							this.createLevel(handler(items[i], i), isUpdating);
						}
					} else {
						var j = 0;
						for (var i = items.length - 1; i >= 0; i--) {
							j++;
							if (limit && j > limit) break;
							this.createLevel(handler(items[i], i), isUpdating);
						}
					}
					return;
				}
			} else if (isObject(items)) {
				if (!Objects.empty(items)) {
					if (!isRight) {
						var i = 0;
						for (var k in items) {
							i++;
							if (limit && i > limit) break;
							this.createLevel(handler(items[k], k), isUpdating);
						}
					} else {
						var keys = Objects.getKeys(items);
						keys.reverse();
						for (var i = 0; i < keys.length; i++) {
							if (limit && i + 1 > limit) break;
							this.createLevel(handler(items[keys[i]], keys[i]), isUpdating);
						}
					}
					return;
				}
			}
		}
		createIfEmptyLevel.call(this)
	};
	this.update = function() {
		this.disposeLevels();
		this.createLevels(true);
	};
	this.add = function(item, index) {
		this.createLevel(handler(item, ~~index), false, index);	
	};
	this.remove = function(index) {
		if (this.levels[index]) {
			this.levels[index].dispose();
			this.levels.splice(index, 1);
		}
	};
	this.dispose = function() {
		_G_.get('Core').disposeLinks.call(this);
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
		handler = null;
		params = null;
	};
}, 'Foreach');
p=c.prototype;
p.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevels(false);
};
p.createLevel = function(items, isUpdating, index) {
	var level = new (_G_.get('Level'))(this.parentLevel.getComponent());
	var nextSiblingChild;
	if (isNumber(index) && this.levels[index]) {
		nextSiblingChild = this.levels[index].getFirstNodeChild();
	} else {
		nextSiblingChild = isUpdating ? _G_.get('Core').getNextSiblingChild.call(this) : null;
	}
	level.render(items, this.parentElement, this.parentLevel, nextSiblingChild);
	this.levels.insertAt(level, index);
};
p.disposeLevels = function() {
	for (var i = 0; i < this.levels.length; i++) {
		this.levels[i].dispose();
	}
	this.levels = [];
};
_G_.set(c=function(params) {
	this.values = params['is'];
	this.default = params['d'];
	this.handler = params['c'];
	this.current = null;
	this.levels = [];
	this.createLevels = function(isUpdating) {
		var children = this.handler();
		var values = this.values();
		for (var i = 0; i < values.length; i++) {
			if (!!values[i]) {
				if (i === this.current) return;
				for (var j = 0; j < children[i].length; j++) this.createLevel(children[i][j], isUpdating);
				this.current = i;
				return;
			}
		}
		if (isArray(this.default)) {
			for (i = 0; i < this.default.length; i++) this.createLevel(this.default[i], isUpdating);
		}
	};
	this.dispose = function() {
		this.disposeLinks();
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.current = null;
		this.values = null;
		this.default = null;
		this.handler = null;
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
	};
}, 'IfSwitch');
p=c.prototype;
p.update = function(value) {
	this.value = value;
	this.disposeLevels();
	this.createLevels(true);
};
_G_.set(function(component) {
	var tagsList = __TAGS;
	var attrNames = __A;
	var eventTypes = __EVENTTYPES;
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
		else if (i.hasOwnProperty('t')) createElement(i);
		else if (i.hasOwnProperty('v')) createPropertyNode(i);
		else if (i.hasOwnProperty('i')) createCondition(i);
		else if (i.hasOwnProperty('h')) createForeach(i);	
		else if (i.hasOwnProperty('tmp')) includeTemplate(i);
		else if (i.hasOwnProperty('cmp')) renderComponent(i);
		else if (i.hasOwnProperty('is')) createIfSwitch(i);
		else if (i.hasOwnProperty('sw')) createSwitch(i);
		else if (i.hasOwnProperty('pl')) createPlaceholder(i);
	};
	var createLevel = function(items, pe) {
		var lvl = _G_.get('Level');
		var level = new lvl(component);
		level.render(items, pe, self);
		children.push(level);
	};
	var createTextNode = function(content) {
		if (content == '<br>') appendChild(document.createElement('br'));
		else appendChild(document.createTextNode(content));
	};
	var createUpdater = function(u, s, p) {
		updaters = updaters || [];
		if (p['n']) _G_.get('Core').createUpdater(u, component, s, p, updaters);
		if (p['g']) _G_.get('State').createUpdater(u, component, s, p);
	};
	var createPropertyNode = function(props) {
		var v = '', pv = props['v'];
		if (isFunction(pv)) pv = pv();
		if (!isUndefined(pv)) v = pv;
		var node = document.createTextNode(v);		
		appendChild(node);
		createUpdater(_G_.get('NodeUpdater'), node, props);
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
				else if (a == 'as') _G_.get('Core').registerElement.call(component, element, pr[k]);
				else if (isPrimitive(pr[k]) && pr[k] !== '') {
					element.attr(a, pr[k]);
				}
			}
			if (props['n'] || props['g']) {
				createUpdater(_G_.get('ElementUpdater'), element, props);
			}
		}
		if (isArray(props['e'])) {
			var eventType, callback, isOnce, i;
			eventHandler = eventHandler || new (_G_.get('EventHandler'))();
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
			var condition = new (_G_.get('Condition'))(props);
			condition.render(parentElement, self);				
			registerChild(condition);
			createUpdater(_G_.get('OperatorUpdater'), condition, props);
		} else if (!!props['i']) {
			renderItems(props['c']);
		} else if (!isUndefined(props['e'])) {
			renderItem(props['e']);
		}
	};
	var createForeach = function(props) {
		if (props['n'] || props['g']) {
			var foreach = new (_G_.get('Foreach'))(props);
			foreach.render(parentElement, self);
			registerChild(foreach);
			createUpdater(_G_.get('OperatorUpdater'), foreach, props);
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
			var swtch = new (_G_.get('IfSwitch'))(props);
			swtch.render(parentElement, self);
			registerChild(swtch);
			createUpdater(_G_.get('OperatorUpdater'), swtch, props);
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
			var swtch = new (_G_.get('Switch'))(props);
			swtch.render(parentElement, self);
			registerChild(swtch);
			createUpdater(_G_.get('OperatorUpdater'), swtch, props);
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
		if (prevChild) _G_.get('Core').setNextSiblingChild.call(prevChild, child);
		prevChild = isNodeChild ? null : child;
		if (!firstChild) firstChild = child;
		if (isNodeChild) {
			if (!firstNodeChild) firstNodeChild = child;
			lastNodeChild = child;
		} else children.push(child);
		if (isComponent) _G_.get('Core').registerChildComponent.call(component, child);
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
		if (isNumber(item['tmp'])) item['tmp'] = _G_.get('i_' + item['tmp']);
		else if (isString(item['tmp'])) item['tmp'] = _G_.get('Core').getTemplateById.call(component, item['tmp']);
		if (isFunction(item['tmp'])) {		
			var items = item['tmp'].call(component, props, component);
			renderItems(items);
		}
	};
	var renderComponent = function(item, pe) {
		pe = pe || parentElement;
		item['cmp'] = _G_.get(item['cmp']);
		if (isFunction(item['cmp'])) {
			var cmp = new item['cmp']();
			var ir = isFunction(item['p']);
			var i, k, p = ir ? item['p']() : item['p'];
			var props, data;
			if (isObject(p)) {
				if (p['p'] || p['ap']) props = initComponentProps(p['p'], p['ap']);
				if (isString(p['i'])) {
					cmp.setId(p['i']);
					var waiting = _G_.get('Core').getWaitingChild.call(component, p['i']);
					if (isArray(waiting)) {
						for (i = 0; i < waiting.length; i++) {
							waiting[i][0].set(waiting[i][1], cmp);
						}
					}
				}				
			}
			if (ir) createUpdater(_G_.get('ComponentUpdater'), cmp, item);
			if (isArray(item['w'])) {
				for (i = 0; i < item['w'].length; i += 2) {
					_G_.get('Core').provideWithComponent.call(component, item['w'][i], item['w'][i + 1], cmp);
				}
			}
			if (item['c']) {
				props = props || {};
				props['children'] = item['c'];
			}
			_G_.get('Core').initiate.call(cmp, props);
			cmp.render(pe);
			registerChild(cmp, true);
			if (isArray(item['e'])) {
				for (i = 0; i < item['e'].length; i++) {
					_G_.get('Core').subscribe.call(cmp, item['e'][i], item['e'][i + 1], component);
					i++;	
				}
			}
			if (item['nm']) _G_.get('Core').registerControl.call(component, cmp, item['nm']);
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
		if (firstLevel instanceof _G_.get('Level')) {
			return _G_.get('Core').getParentElement.call(firstLevel);
		} else if (firstLevel) {
			return _G_.get('Core').getFirstNodeChild.call(firstLevel);
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
			nextSiblingChild = _G_.get('Core').getNextSiblingChild.call(parentLevel);
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
				_G_.get('Core').disposeUpdater.call(component, updaters[i], updaters[i + 1]);
				updaters[i + 1] = null;
				i++;
			}
		}
		for (var i = 0; i < children.length; i++) {
			if (isComponentLike(children[i])) {
				_G_.get('Core').unregisterChildComponent.call(component, children[i]);
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
}, 'Level');
_G_.set(c=function(){}, 'Menu');
p=c.prototype;
p.doRendering = function() {
	_G_.get('Component').prototype.doRendering.call(this);
	var router = _G_.get('Router');
	if (router.hasMenu(this)) {
		this.onNavigate(router.getCurrentRouteName());
	}
};
p.onNavigate = function(viewName) {
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
p.getButton = function(viewName) {
	return this.findElement('a[role="' + viewName + '"]');
};
p.setButtonActive = function(button, isActive) {
	var activeClassName = this.activeButtonClass || 'active';
	button.toggleClass(activeClassName, isActive);
	if (isActive) {
		this.activeButton = button;
	}
};
p.disposeInternal = function() {
	this.activeButton = null;
};
_G_.set(c=function(params) {
	this.params = params;
	this.levels = [];
	this.createLevels = function(isUpdating) {
		var p = this.params['sw']();
		var v = p[0], vs = p[1], ch = p[2], d = p[3];
		if (!isArray(vs)) {
			vs = [vs]; ch = [ch];
		}
		for (var i = 0; i < vs.length; i++) {
			if (v === vs[i]) {
				for (var j = 0; j < ch[i].length; j++) this.createLevel(ch[i][j], isUpdating);
				return;
			}
		}
		if (!isUndefined(d)) this.createLevel(d, isUpdating);
	};
	this.dispose = function() {
		this.disposeLinks();
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.params = null
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
	};
}, 'Switch');
p=c.prototype;
p.update = function(value) {
	this.value = value;
	this.disposeLevels();
	this.createLevels(true);
};
_G_.set(c=function(){}, 'View');
p=c.prototype;
p.onRenderComplete = function() {
	this.dispatchReadyEvent();
};
p.setOnReadyHandler = function(handler) {
	this.onReadyHandler = handler;
};
p.dispatchReadyEvent = function() {
	if (isFunction(this.onReadyHandler)) {
		this.onReadyHandler();
	}
	this.onReady();
};
p.activate = function(isActivated) {
	if (isActivated) {
		this.dispatchReadyEvent();
	}
};
p.getTitleParams=__FNC;
p.onReady=__FNC;
_G_.set(new (function() {
	var subscribers = [];
	var options = [];
	var defaultCheckboxClass = 'checkbox';
	var defaultCheckboxCheckedClass = 'checked';
	var currentOptions, currentObject, currentScope, checkbox, labelClass,
		checkboxClass, targetClasses, currentCheckedClass, currentTarget;
	this.subscribe = function(subscriber, opts) {
		if (isFunction(opts['callback']) && subscribers.indexOf(subscriber) == -1) {
			subscribers.push(subscriber);
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
		defineOptions(index);
		defineTargetClasses();
		if (isProperTarget()) {
			defineCheckbox();
			defineCheckedClass();			
			var checked = !isChecked();
			if (currentTarget) currentTarget.toggleClass(currentCheckedClass, checked);
			if (checkbox) {
				checkbox.toggleClass(currentCheckedClass, checked);
				currentOptions['callback'].call(currentObject, {
					'target': checkbox,
					'name': getName(),
					'value': getValue(),
					'checked': checked,
					'intChecked': checked ? 1 : 0
				});
			}
		}
	};
	var defineOptions = function(index) {
		currentOptions = options[index];
		currentObject = subscribers[index];
		currentScope = currentObject.getElement();
	};
	var defineTargetClasses = function() {
		targetClasses = [];
		defineCheckboxClass();
		if (checkboxClass) targetClasses.push(checkboxClass);
		labelClass = Objects.get(currentOptions, 'labelClass');
		if (isString(labelClass)) targetClasses.push(labelClass);
		else if (isArray(labelClass)) targetClasses = targetClasses.concat(labelClass);
	};
	var defineCheckboxClass = function(options) {
		checkboxClass = Objects.get(options || currentOptions, 'checkboxClass', defaultCheckboxClass);
	};
	var isProperTarget = function() {
		while (currentTarget) {
			if (targetClasses.hasIntersections(currentTarget.getClasses())) return true;
			currentTarget = currentTarget.parentNode;
			if (currentTarget == currentScope) break;
		}
		return false;
	};
	var defineCheckbox = function() {
		if (currentTarget.hasClass(checkboxClass)) {
			checkbox = currentTarget;
			currentTarget = null;
			if (isString(labelClass)) currentTarget = checkbox.getAncestor('.' + labelClass);
			else if (isArray(labelClass)) {
				for (var i = 0; i < labelClass.length; i++) {
					currentTarget = checkbox.getAncestor('.' + labelClass[i]);
					if (currentTarget) break;
				}
			}
		} else checkbox = currentTarget.find('.' + checkboxClass);
	};
	var defineCheckedClass = function() {
		currentCheckedClass = Objects.get(currentOptions, 'checkboxCheckedClass', defaultCheckboxCheckedClass);
	};
	var isChecked = function() {
		if (checkbox) return checkbox.hasClass(currentCheckedClass);
		return currentTarget.hasClass(currentCheckedClass);
	};
	var getValue = function() {
		var value;
		if (checkbox) value = checkbox.getData('value');
		return isIntValue() ? ~~value : value;
	};
	var getName = function() {
		if (checkbox) return checkbox.getData('name');
	};
	var isIntValue = function() {
		return Objects.has(currentOptions, 'intValue', true);
	};
	var getOptionsOfSubscriber = function(subscriber) {
		return options[subscribers.indexOf(subscriber)];
	};
	var getCheckboxByName = function(name, subscriber) {
		currentOptions = getOptionsOfSubscriber(subscriber);
		defineCheckboxClass();
		return subscriber.findElement('.' + checkboxClass + '[_name="' + name + '"]');
	};
	this.isChecked = function(name, subscriber) {
		var checkbox = getCheckboxByName(name, subscriber);
		defineCheckedClass();
		return checkbox && checkbox.hasClass(currentCheckedClass);
	};
	this.getValue = function(name, subscriber) {
		var checkbox = getCheckboxByName(name, subscriber);
		if (checkbox) return checkbox.getData('value');
	};
})(), 'CheckboxHandler');
_G_.set(new (function() {
	var ds = {};
	var cid, dc, d, opts;
	this.show = function(c, options) {
		if (isFunction(c)) {
			var id;
			if (isObject(options)) {
				id = options['did'];
			}
			opts = options;
			defineId(c, id);
			defineDialog();
			showDialog();
		}
	};
	this.hide = function(c, id) {
		defineId(c, id);
		if (ds[cid]) ds[cid].close();
	};
	this.get = function(c, id) {
		defineId(c, id);
		return ds[cid];
	};
	this.expand = function(c, id) {
		defineId(c, id);
		if (ds[cid]) ds[cid].expand(true);
	};
	this.minimize = function(c, id) {
		defineId(c, id);
		if (ds[cid]) ds[cid].expand(false);
	};
	this.dispose = function(c, id) {
		defineId(c, id);
		if (ds[cid]) ds[cid].dispose();
		delete ds[cid];
	};
	var defineId = function(c, id) {
		dc = c;
		if (!isFunction(c)) return '_';
		cid = c.name + (isPrimitive(id) ? '_' + id : '');
	};
	var defineDialog = function() {
		if (isUndefined(ds[cid])) {
			ds[cid] = new dc();
			_G_.get('Core').initiate.call(ds[cid]);
			ds[cid].render(document.body);
		}
		d = ds[cid];
	};
	var showDialog = function() {
		if (isObject(opts)) {
			d.set(opts);
		}
		d.show();
	};
	var closeAll = function() {
		for (var k in ds) {
			ds[k].hide();
		}
	};
	window.addEventListener('popstate', closeAll);
})(), 'Dialoger');
var Dictionary = new (function() {
	var dictionaryUrl = __DICTURL;
	var items = {}, callbacks, loaded = {};
	this.load = function(routeName) {
		if (loaded[routeName]) return;
		if (!isNone(dictionaryUrl)) {
			Loader.get(dictionaryUrl, {'route': routeName}, onLoad, this);
		}
		loaded[routeName] = true;
	};
	this.get = function(key, callbackOrPropName, thisObj) {alert(key)
		var item = Objects.get(items, key);
		if (item) return item;
		callbacks = callbacks || [];
		callbacks.push([callbackOrPropName, thisObj, key]);
	};
	this.set = function(key, value) {
		items[key] = value;
	};
	this.setData = function(routeName, data) {
		loaded[routeName] = true;
		for (var k in data) this.set(k, data[k]);
			console.log(items)
	};
	var onLoad = function(data) {
		if (isObject(data)) {
			for (var k in data) this.set(k, data[k]);
			if (!isArray(callbacks)) return;
			for (var i = 0; i < callbacks.length; i++) {
				if (isFunction(callbacks[i][0])) {
					callbacks[i][0].call(callbacks[i][1] || null);
				} else if (isString(callbacks[i][0]) && isComponentLike(callbacks[i][1])) {
					callbacks[i][1].set(callbacks[i][0], items[callbacks[i][2]]);
				}
			}
			callbacks = null;
		}
	};
})();
var __D = Dictionary;
_G_.set(Dictionary, 'Dictionary');
_G_.set(new (function() {
	var subscribers = [];
	var options = [];	
	this.subscribe = function(subscriber, opts) {
		if (isObject(opts['callbacks']) && isString(opts['inputSelector']) && subscribers.indexOf(subscriber) == -1) {			
			var input = subscriber.findElement(opts['inputSelector']);
			var actions = Objects.getKeys(opts['callbacks']);
			if (input) {
				opts['input'] = input;
				subscribers.push(subscriber);
				options.push(opts);
				var index = subscribers.length - 1;
				if (actions.hasExcept('focus', 'blur', 'input')) input.addEventListener('keyup', onKeyup.bind(null, index), false);
				if (actions.has('input')) input.addEventListener('input', onEvent.bind(null, index, 'input'), false);
				if (actions.has('focus')) input.addEventListener('focus', onEvent.bind(null, index, 'focus'), false);
				if (actions.has('blur')) input.addEventListener('blur', onEvent.bind(null, index, 'blur'), false);				
			}
		}
	};
	var onKeyup = function(index, e) {
		var keyCode = e.keyCode;
		var keyName = getKeyName(keyCode);
		var opts = options[index];
		var subscriber = subscribers[index];
		var cb = opts['callbacks'];
		var value = e.target.value;
		if (keyName && isFunction(cb[keyName])) callSubscriber(index, keyName, value);
		else if (isFunction(cb[keyCode])) callSubscriber(index, keyCode, value);
	};
	var getKeyName = function(keyCode) {
		return ({'13': 'enter', '27': 'esc', '38': 'up', '40': 'down', '37': 'left', '39': 'right'})[keyCode];
	};
	var onEvent = function(index, eventName, e) {
		callSubscriber(index, eventName, e.target.value);
	};
	var callSubscriber = function(index, eventName, value) {
		var s = subscribers[index], r;
		var cb = Objects.get(options[index]['callbacks'], eventName);
		if (isFunction(cb)) r = cb.call(s, value);
		if (r !== false && isString(eventName)) s.dispatchEvent(eventName, value);
	}; 
})(), 'InputHandler');
var Loader = new (function() {
	var requests = {};
	var getRequest = function(url, th) {
		return requests[url] || createRequest(url, th);
	};
	var createRequest = function(url, th) {
		var ajr = _G_.get('AjaxRequest');
		requests[url] = new ajr(url, null, null, th);
		return requests[url];
	};
	this.get = function(url, data, callback, th) {
		this.doAction('GET', url, data, callback, th);
	};
	this.post = function(url, data, callback, th) {
		this.doAction('POST', url, data, callback, th);
	};
	this.doAction = function(method, url, data, callback, th) {
		var req = getRequest(url, th);
		if (isFunction(callback)) req.setCallback(callback);
		req.send(method, data);
	};
})();
_G_.set(Loader, 'Loader');
_G_.set(function() {
	var subscribers = [];
	var options = [];
	var eh = _G_.get('EventHandler');
	var eventHandler = new eh();
	var extendOptions = function(index, opts) {
		Objects.merge(options[index], opts);
	};
	this.subscribe = function(subscriber, opts) {
		var index = subscribers.indexOf(subscriber);
		if (index == -1) {
			options.push(opts);
			eventHandler.listen(subscriber.getElement(), 'click', onClick.bind(null, subscriber));
			subscribers.push(subscriber);
		} else extendOptions(index, opts);
	};
	this.unsubscribe = function(subscriber) {
		var idx = subscribers.indexOf(subscriber);
		if (idx > -1) {
			eventHandler.unlisten(subscriber.getElement(), 'click');
			subscribers.splice(idx, 1);
		}
	};
	var onClick = function(subscriber, e) {
		var index = subscribers.indexOf(subscriber);
		var opts = options[index];
		var target;
		for (var k in opts) {
			target = e.getTargetWithClass(k, true);
			if (target) {
				if (isFunction(opts[k])) {
					opts[k].call(subscriber, target, e);
					e.stopPropagation();
					break;
				}
			}
		}
	};
}, 'MouseHandler');
var Popuper = new (function() {
	var components, elements, skippedAll;
	var reset = function() {
		components = [];
		elements = [];
	};
	this.watch = function(component, element) {
		if (components.indexOf(component) == -1) {
			components.push(component);
			if (isString(element)) element = component.findElement(element);
			elements.push(element || component.getElement() || null);
		}
	};
	this.skipAll = function(isSkipped) {
		skippedAll = isSkipped;
	};
	var onBodyMouseDown = function(e) {
		if (skippedAll) return;
		var element;
		for (var i = 0; i < components.length; i++) {
			element = elements[i];
			if (!isElement(element) || !e.targetHasAncestor(element)) {
				components[i].hide();
				reset();
			}
		}
	};
	reset();
	var body = document.documentElement;
	body.addEventListener('mousedown', onBodyMouseDown, false);
})();
_G_.set(Popuper, 'Popuper');
_G_.set(function() {
	var subscribers = [], timer;	
	this.subscribe = function(subscriber, options) {
		subscribers.push([subscriber, options]);
	};
	var onResize = function() {
		window.clearTimeout(timer);
		timer = window.setTimeout(function() {
			for (var i = 0; i < subscribers.length; i++) {
				var callback = Objects.get(subscribers[i][1], 'callback');
				if (isFunction(callback)) callback.call(subscribers[i][0]);
			}
		}, 200);
	};
	window.addEventListener('resize', onResize, false);
}, 'ResizeHandler');
var State = new (function() {
	var listeners = {};
	var subscribers = {};
	var updaters = {};
	var vars = {};
	this.subscribe = function(subscriber, name, callback) {
		var s = subscribers[name] = subscribers[name] || [];
		s.push([callback, subscriber]);
	};
	this.unsubscribe = function(subscriber, name) {
		var s = subscribers[name];
		if (isArray(s)) {
			var done = false;
			while (!done) {
				done = true;
				for (var i = 0; i < s.length; i++) {
					if (s[i][1] == subscriber) {
						s.splice(i, 1);
						done = false;
						break;
					}
				}
			}
		}
	};
	this.get = function(name) {
		return vars[name];
	};
	this.set = function(name, value) {
		var updated, data = name;
		if (!isUndefined(value)) {
			data = {};
			data[name] = value;
		}
		var changed = {}, isChanged = false;
		for (var k in data) {
			if (vars[k] == data[k]) continue;
			if (isArray(vars[k]) && isArray(data[k]) && Objects.equals(vars[k], data[k])) continue;
			isChanged = true;
			changed[k] = data[k];
		}
		if (isChanged) {
			for (var k in changed) {
				vars[k] = changed[k];
				var s = subscribers[k];
				if (isArray(s)) {
					for (var i = 0; i < s.length; i++) {
						if (isFunction(s[i][0])) {
							s[i][0].call(s[i][1] || null, changed[k], k);
						}
					}
				}
				var u = updaters[k];
				if (isArray(u)) {
					updated = [];
					for (var i = 0; i < u.length; i++) {
						if (updated.indexOf(u[i]) == -1) {
							u[i].react(changed);
							updated.push(u[i]);
						}
					}
				}
			}
		}
		updated = changed = data = null;
	};
	this.listen = function(listener, name, callback) {
		if (!isArray(listeners[name])) listeners[name] = [];
		listeners[name].push([callback, listener]);
	};
	this.unlisten = function(name, listener) {
		if (isArray(listeners[name])) {
			var indexes = [];
			for (var i = 0; i < listeners[name].length; i++) {
				if (listeners[name][i][1] == listener) indexes.push(i);
			}
			listeners[name].removeIndexes(indexes);
		}
	};
	this.dispatchEvent = function(name, args) {
		if (isArray(listeners[name])) {
			for (var i = 0; i < listeners[name].length; i++) {
				if (isFunction(listeners[name][i][0])) {
					listeners[name][i][0].apply(listeners[name][i][1] || null, args);
				}
			}
		}
	};
	this.createUpdater = function(updater, component, obj, props) {
		var u = new updater(obj, props, props['g']);
		var keys = u.getKeys()
		for (var i = 0; i < keys.length; i++) {
			updaters[keys[i]] = updaters[keys[i]] || [];
			updaters[keys[i]].push(u);
		}
	};
	this.dispose = function(subscriber) {
		var k, i, s;
		for (k in subscribers) {
			s = [];
			for (i = 0; i < subscribers[k].length; i++) {
				if (subscribers[k][i] != subscriber) s.push(subscribers[k][i]);
				else alert(111222)
			}
			subscribers[k] = s;
		}
	};
})();
_G_.set(State, 'State');
var StoreKeeper = new (function() {
	var x = 'stored_';
	var s = {
		'month': 2592000,
		'day' : 86400,
		'hour' : 3600,
		'min' : 60
	};
	this.set = function(k, v) {
		var lk = g(k);
		var i = JSON.stringify({
			'data': v,
			'timestamp': Date.now().toString()
		});
		localStorage.setItem(lk, i);
	};
	this.get = function(k) {
		var i = gi(k);
		return Objects.has(i, 'data') ? i['data'] : null;
	};
	this.getActual = function(k, p) {
		var i = gi(k);
		return Objects.has(i, 'data') && ia(i['timestamp'], p) ? i['data'] : null;
	};
	this.remove = function(k) {
		var lk = g(k);
		localStorage.removeItem(lk);
	};
	var ia = function(sm, p) {
		var nm = Date.now(), pm = gm(p);
		if (isString(sm)) sm = stringToNumber(sm);
		return pm && sm && nm - sm < pm;
	};
	var gi = function(k) {
		var lk = g(k);
		var i = localStorage.getItem(lk);
		if (!i) return null;
		try {
			i = JSON.parse(i);
		} catch (e) {
			return null;
		}
		return i;
	};
	var gm = function(p) {
		var n = ~~p.replace(/[^\d]/g, '');
		var m = p.replace(/\d/g, '');
		if (!n) return 0;		
		if (!s[m]) return 0;
		return s[m] * n * 1000;
	};
	var g = function(k) {
		return x + k;
	};
})();
_G_.set(StoreKeeper, 'StoreKeeper');
_G_.set(function() {
	var target, eventHandler, event;
	var request, tooltipElement, timer;
	var tooltip = __TC,
		tooltipApi = __TA;
	var tooltipClass = 'tooltiped';
	var addClass, text, position, key,
		caption, delay, corrector;
	var onBodyMouseOver = function(e) {
		event = e;
		target = e.target;
		if (target.hasClass(tooltipClass)) {
			init();
			if (text) {
				if (delay) {
					showWithDelay()
				} else {
					show();
				}
			} else if (key) {
				load();
			}
		}
	};
	var init = function() {
		window.clearTimeout(timer);
		if (isFunction(tooltip)) {
			createPopup();
		}
		key = '';
		text = target.getData('text');
		addClass = target.getData('class');
		position = target.getData('position');
		caption = target.getData('caption');
		delay = target.getData('delay');
		corrector = target.getData('corrector');
		if (!text) {
			key = target.getData('key');
		}
		eventHandler.listenOnce(target, 'mouseleave', onLeave);
	};
	var createPopup = function() {
		tooltip = new tooltip();
		Core.initiate.call(tooltip);
		tooltip.render(document.body);
		tooltipElement = tooltip.getElement();
	};
	var showWithDelay = function() {
		timer = window.setTimeout(show, 500);
	};
	var show = function() {
		tooltip.set({
			'shown': true,
			'corrector': corrector,
			'caption': caption,
			'text': text
		});
		var coords = getCoords();
		tooltip.set({
			'left': Math.round(coords.x),
			'top': Math.round(coords.y)
		});
	};
	var getCoords = function() {
		var marginLeft = 0, marginTop = 0;		
		var rect = target.getRect();
		var tooltipRect = tooltipElement.getRect();
		var coordX = rect.left;
		var coordY = rect.top;
		var coords = {x: coordX, y: coordY};
		switch (position) {
			case 'left': 
				coords.y += Math.round(rect.height / 2) - 20;
			break;
			case 'bottom': 
				coords.x += Math.round(rect.width / 2);
				coords.y += rect.height + 5;
			break;
			case 'top': 
				coords.x += Math.round(rect.width / 2);
			break;
			case 'left-bottom': 
				coords.y += rect.height + 5;
			break;
			case 'right-bottom': 
				coords.x += rect.width;
				coords.y += rect.height + 5;
			break;
			case 'left-top': 
				coords.x += rect.width;
			break;
			case 'right-top': 
				coords.x += rect.width;
			break;
			default:
				coords.x += rect.width + 15;
				coords.y += Math.round(rect.height / 2) - 20;
		}
		if (position == 'left') {
			marginLeft = -tooltipRect.width - 10;
		} else if (position == 'top' || position == 'bottom') {
			marginLeft = -Math.round(tooltipRect.width / 2);
		} else if (position == 'right-top' || position == 'right-bottom') {
			marginLeft = -tooltipRect.width;
		} else if (position == 'left-top') {
			marginLeft = -rect.width;
		}
		if (position == 'top' || position == 'left-top' || position == 'right-top') {
			marginTop = -tooltipRect.height - 10;
		}
		if (rect.width < 30 && ['left-bottom', 'right-bottom', 'bottom', 'left-top', 'right-top', 'top'].indexOf(position) != -1) {
			coords.x -= 15;
		}
		coords.x += marginLeft;
		coords.y += marginTop;
		return coords;
	};
	var onLeave = function() {
		window.clearTimeout(timer);
		tooltip.set('shown', false);
	};
	var load = function() {
		if (isString(tooltipApi)) {
			if (isUndefined(request)) {
				request = new _G_.get('AjaxRequest')(tooltipApi, onLoad);
			}
			request.execute({'name': key});
		}
	};
	var onLoad = function(data) {
		text = Objects.get(data, 'text');
		var cap = Objects.get(data, 'caption');
		if (cap && isString(cap)) {
			caption = cap;
			target.setData('caption', cap);
		}
		if (text && isString(text)) {
			target.setData('text', text);
			show();
		}
	};
	if (isFunction(tooltip)) {
		eventHandler = new EventHandler();
		var body = document.documentElement;
		body.addEventListener('mouseover', onBodyMouseOver, false);
	}
}, 'Tooltiper');
p=Array.prototype;
p.contains = function(v) {
	var iv = ~~v;
	if (iv == v) return this.indexOf(iv) > -1 || this.indexOf(v + '') > -1;
	return this.has(v);
};
p.has = function(v) {
	return this.indexOf(v) > -1;
};
p.hasAny = function(a) {
	if (!isArray(a)) a = arguments;
	for (var i = 0; i < a.length; i++) {
		if (this.indexOf(a[i]) > -1) return true;
	}
};
p.hasExcept = function() {
	var args = Array.prototype.slice.call(arguments);
	for (var i = 0; i < this.length; i++) {
		if (args.indexOf(this[i]) == -1) return true;
	}
};
p.removeDuplicates = function() {
	this.filter(function(item, pos, self) {
	 return self.indexOf(item) == pos;
	});
	return this;
};
p.getIntersections = function(arr) {
	return this.filter(function(n) {
	 return arr.indexOf(n) != -1;
	});
};
p.hasIntersections = function(arr) {
	return !isUndefined(this.getIntersections(arr)[0]);
};
p.removeIndexes = function(indexes) {
	var deleted = 0;
	for (var i = 0; i < indexes.length; i++) {
		this.splice(indexes[i] - deleted, 1);
		deleted++;
	}
};
p.isEmpty = function() {
	return this.length == 0;
};
p.removeItems = function(items) {
	for (var i = 0; i < items.length; i++) this.removeItem(items[i]);
};
p.removeItem = function(item) {
	var index = this.indexOf(item);
	if (index > -1) this.splice(index, 1);
};
p.insertAt = function(item, index) {
	if (!isNumber(index) || index >= this.length) this.push(item);
	else this.splice(index, 0, item);
};
p.shuffle = function() {
	var tmp;
	for (var i = this.length - 1; i > 0; i--) {
		var j = Math.floor(Math.random() * (i + 1));
		tmp = this[i];
		this[i] = this[j];
		this[j] = tmp;
	}
};
p.addUnique = function(item) {
	if (!this.has(item)) this.push(item);
};
p.addRemove = function(item, add, addUnique) {
	if (add) {
		if (addUnique) this.addUnique(item);
		else this.push(item);
	} else this.removeItem(item);
};
var __StyleNameCache = {};
p=Element.prototype;
p.setClass = function(className) {
	this.className = className.trim();
}
p.toggleClass = function(className, isAdding) {
	if (isAdding) {
		this.addClass(className);
	} else {
		this.removeClass(className);
	}
};
p.switchClasses = function(className1, className2) {
	var classes = this.getClasses();
	if (classes.contains(className1)) { 
		this.removeClass(className1);
		this.addClass(className2);
	} else if (classes.contains(className2)) {
		this.removeClass(className2);
		this.addClass(className1);
	}
};
p.addClass = function(className) {
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
p.removeClass = function(className) {
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
p.hasClass = function(className) {
	return this.getClasses().indexOf(className) > -1;
};
p.getClasses = function() {
	var classNames = (this.className || '').trim().replace(/ {2,}/g, ' ');
	if (classNames) {
		return classNames.split(' ');
	}
	return [];
};
p.getAncestor = function(selector) {
	if (isNone(selector) || !isString(selector)) {
		return null;
	}
	if (isFunction(this.closest)) {
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
p.getData = function(name) {
	return this.getAttribute('_' + name) || '';
};
p.setData = function(name, value) {
	this.setAttribute('_' + name, value);
};
p.getRect = function() {
	return this.getBoundingClientRect();
};
p.setWidth = function(width) {
	this.style.width = isNumber(width) ? width + 'px' : width;
};
p.setHeight = function(height) {
	this.style.height = isNumber(height) ? height + 'px' : height;
};
p.getWidth = function() {
	return this.getRect().width;
};
p.getHeight = function() {
	return this.getRect().height;
};
p.getTop = function() {
	return this.getRect().top;
};
p.getLeft = function() {
	return this.getRect().left;
};
p.css = function(style) {
	var element = this;
	var set = function(value, style) {
		var propertyName = getVendorJsStyleName(style);	
		if (propertyName) {
			element.style[propertyName] = value;
		}
	};
	var getVendorJsStyleName = function(style) {
		var propertyName = __StyleNameCache[style];
		if (!propertyName) {
			propertyName = toCamelCase(style);
	 	__StyleNameCache[style] = propertyName;
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
p.getChildAt = function(index) {
	return this.childNodes[index];
};
p.attr = function(attrName) {
	if (!isUndefined(arguments[1])) {
		if (attrName == 'class') {
			this.setClass(arguments[1]);
		} else if (attrName == 'value') {
			this.value = arguments[1];
		} else {
			this.setAttribute(attrName, arguments[1]);
		}
	} else {
		return this.getAttribute(attrName);
	}
};
p.show = function(isShown) {
	var display = isString(isShown) ? isShown : (isUndefined(isShown) || isShown ? 'block' : 'none');
	this.style.display = display;
};
p.hide = function() {
	this.show(false);
};
p.find = function(selector) {
	return this.querySelector(selector);
};
p.finds = function(selector) {
	return this.querySelectorAll(selector);
};
p.getParent = function() {
	return this.parentNode;
};
p.scrollTo = function(pxy, duration) {
	if (isElement(pxy)) pxy = pxy.getRelativePosition(this).y
	if (!duration || !isNumber(duration)) this.scrollTop = pxy;
	else {
		var px = pxy - this.scrollTop, ratio = 15,
		steps = duration / ratio, step = Math.round(px / steps),
		currentStep = 0, e = this, 
		cb = function() {
			currentStep++;
			e.scrollTop = e.scrollTop + step;
			if (currentStep < steps) setTimeout(cb, ratio);
			else e.scrollTop = pxy;
		};
		if (px != 0) cb();
	}
};
p.getRelativePosition = function(element) {
	var a = this.getRect();
	var b = element.getRect();
	return {x: Math.round(a.left - b.left + element.scrollLeft), y: Math.round(a.top - b.top + element.scrollTop)};
};
p.clear = function() {
	if (isString(this.value)) this.value = '';
	else this.innerHTML = '';
};
p.prev = function() {
	return this.previousSibling;
};
p.next = function() {
	return this.nextSibling;
};
Function.prototype.b = Function.prototype.bind;
p=MouseEvent.prototype;
p.getTarget = function(selector) {
	return this.target.getAncestor(selector);
};
p.getTargetData = function(selector, dataAttr) {
	var target = this.getTarget(selector);
	return !!target ? target.getData(dataAttr) : '';
};
p.targetHasAncestor = function(element) {
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
p.targetHasClass = function(className) {
	return this.target.hasClass(className) || (!!this.target.parentNode && this.target.parentNode.hasClass(className));
};
p.getTargetWithClass = function(className, strict) {
	if (this.target.hasClass(className)) return this.target;
	if (!strict || !this.target.className) {
		if (!!this.target.parentNode && this.target.parentNode.hasClass(className)) return this.target.parentNode;
	}
	return null;
};
p=String.prototype;
p.isEmpty = function() {
	return !(/[^\s]/).test(this);
};
p.toArray = function(delimiter) {
	delimiter = delimiter || ',';
	var ar = [];
	var parts= this.split(delimiter);
	for (var i = 0; i < parts.length; i++) {
		if (parts[i]) ar.push(parts[i].trim());
	}
	return ar;
};
_G_.set(function(url, callback, params, thisObj) {
	var pathToApiDir = __APIDIR;
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
	this.setCallback = function(cb) {
		callback = cb;
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
		if (isString(pathToApiDir)) {
			var regExp = new RegExp('^' + pathToApiDir + "\/");
			u = pathToApiDir + '/' + u.replace(regExp, '');
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
				content.push(k + '=' + (!!pars[k] || pars[k] == 0 ? pars[k] : '').toString());
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
				callback.call(thisObj || null, data);
			}
		}
	};
}, 'AjaxRequest');
var __C = new (function() {
	var ctrlist = __CTR;
	this.get = function(id) {
		if (isString(ctrlist[id])) {
			ctrlist[id] = _G_.get(ctrlist[id]);
		}
		if (isFunction(ctrlist[id])) {
			ctrlist[id] = new ctrlist[id]();
			_G_.get('Core').initiate.call(ctrlist[id]);
		}
		return ctrlist[id];
	};
	this.load = function(ids) {
		var ctr;
		if (!isArray(ids)) ids = [ids];
		for (var i = 0; i < ids.length; i++) {
			ctr = this.get(ids[i]);
			if (isController(ctr)) {
				ctr.doAction(null, 'load');
			}
		}
	};
})();
_G_.set(__C, 'Controllers');
_G_.set(new (function() {
	var extendInitials = function(initials1, initials2) {
		if (isNull(initials1)) {
			initials1 = initials2;
		} else {
			for (var k in initials2) {
				if (isUndefined(initials1[k])) initials1[k] = initials2[k];
				else if (isObject(initials1[k]) || isObject(initials2[k])) Objects.merge(initials1[k], initials2[k]);
				else if (isArray(initials1[k]) || isArray(initials2[k])) Objects.concat(initials1[k], initials2[k]);				
			}
		}
		return initials1;
	};
	var addProps = function(initialProps) {
		for (var k in initialProps)	{
			if (isUndefined(this.props[k])) {
				this.props[k] = initialProps[k];
			}
		}
	};
	this.processInitials = function() {
		var initials = this.initials;
		if (isObject(initials)) {
			if (isController(this)) {
				this.options = initials['options'];
			}
			for (var k in initials) {
				if (isArrayLike(initials[k])) {
					if (k == 'correctors') {
						for (var j in initials[k]) addCorrector.call(this, j, initials[k][j]);
					} else if (k == 'followers') {
						for (var j in initials[k]) addFollower.call(this, j, initials[k][j]);
					} else if (k == 'controllers') {
						for (var i = 0; i < initials[k].length; i++) attachController.call(this, initials[k][i]);
					} else if (k == 'props') {
						addProps.call(this, initials[k]);
					}
				}
			}
		}
	};
	var getInitial = function(initialName) {
		return Objects.get(this.initials, initialName);
	};
	var attachController = function(options) {		
		if (isObject(options['on'])) {
			var data, ctr;
			for (var actionName in options['on']) {
				data = {'initiator': this, 'callback': options['on'][actionName]};
				options['controller'].addSubscriber(actionName, data, !!options['private'], Objects.get(options['options'], actionName));
			}
		}
	};
	var addCorrector = function(name, handler) {
		if (isFunction(handler)) {
			this.correctors = this.correctors || {};
			this.correctors[name] = handler;
		}
	};
	var addFollower = function(name, handler) {
		if (isFunction(handler)) {
			this.followers = this.followers || {};
			this.followers[name] = handler;
		}
	};
	this.processPostRenderInitials = function() {
		var events = getInitial.call(this, 'events');
		if (isObject(events)) {
			var mh = _G_.get('MouseHandler');
			this.mouseHandler = new mh(this, events);
		}
		var helpers = getInitial.call(this, 'helpers');
		if (isArray(helpers)) {
			for (var i = 0; i < helpers.length; i++) subscribeToHelper.call(this, helpers[i]);
		}
		var listeners = getInitial.call(this, 'listeners');
		var s = _G_.get('State');
		if (isObject(listeners)) {			
			for (var j in listeners) s.listen(this, j, listeners[j]);
		} 
		var globals = getInitial.call(this, 'globals');
		if (isObject(globals)) {
			for (var j in globals) s.subscribe(this, j, globals[j]);
		}
	};
	var subscribeToHelper = function(options) {
		if (isObject(options['options'])) _G_.get(options['helper']).subscribe(this, options['options']);
	};
	var isProperMethod = function(child, parent, method) {
		if (!!child.prototype[method]) return false;
		return parent.prototype[method] != parent.prototype.initiate && parent.prototype[method] != parent.prototype.getInitials;
	}
	this.inherits = function(list) {
		var children, parent, child, initials, sc;
		for (var k = 0; k < list.length; k++) {
			parent = _G_.get(list[k]);
			children = list[++k];
			for (var i = 0; i < children.length; i++) {
				child = _G_.get(children[i]);
				if (!child.prototype.inheritedSuperClasses) {
					child.prototype.inheritedSuperClasses = [];
				}
				sc = child.prototype.inheritedSuperClasses;
				var cb = function(p) {
					if (sc.indexOf(p) == -1) sc.push(p);
					var psc = p.prototype.inheritedSuperClasses;
					if (isArray(psc)) {
						for (var n = 0; n < psc.length; n++) cb(psc[n]);
					}
				};
				cb(parent);
				for (var method in parent.prototype) {
					if (isProperMethod(child, parent, method)) {
						child.prototype[method] = parent.prototype[method];
					}
				}
			}
		}
	};
	this.initiate = function(props) {
		var initials = null;
		var proto = this.constructor.prototype;
		if (isFunction(proto.getInitials)) {
			initials = proto.getInitials();
		}
		var initiateParental = function(superClasses, object) {
			var parentInitials, pproto;
			for (var i = 0; i < superClasses.length; i++) {
				pproto = superClasses[i].prototype;
				if (isFunction(pproto.initiate)) {
					pproto.initiate.call(object);
				}
				if (isFunction(pproto.getInitials)) {
					parentInitials = pproto.getInitials();
					if (isObject(parentInitials)) {
						initials = extendInitials(initials || null, parentInitials);
					}
				}
				if (isArray(pproto.inheritedSuperClasses)) {
					initiateParental(pproto.inheritedSuperClasses, object);
				}
			}
		};
		if (isArray(this.inheritedSuperClasses)) {
			initiateParental(this.inheritedSuperClasses, this);
		}
		if (isObject(this.props)) Objects.merge(this.props, props);
		else this.props = props || {};
		if (isFunction(proto.initiate)) {
			proto.initiate.call(this);
		}
		this.initials = initials;
		_G_.get('Core').processInitials.call(this);
	};
	this.getNextSiblingChild = function() {
		if (!this.nextSiblingChild) return null;
		if (this.nextSiblingChild instanceof Node) return this.nextSiblingChild;
		var firstNodeChild = _G_.get('Core').getFirstNodeChild.call(this.nextSiblingChild);
		if (firstNodeChild) return firstNodeChild;
		return _G_.get('Core').getNextSiblingChild.call(this.nextSiblingChild, this);	
	};
	this.setNextSiblingChild = function(nextSiblingChild) {
		this.nextSiblingChild = nextSiblingChild;
		if (!(nextSiblingChild instanceof Node)) _G_.get('Core').setPrevSiblingChild.call(this.nextSiblingChild, this);
	};
	this.setPrevSiblingChild = function(prevSiblingChild) {
		this.prevSiblingChild = prevSiblingChild;
	};
	this.disposeLinks = function() {
		if (this.prevSiblingChild) _G_.get('Core').setNextSiblingChild.call(this.prevSiblingChild, this.nextSiblingChild);
		this.prevSiblingChild = null;
		this.nextSiblingChild = null;
	};
	this.getFirstNodeChild = function() {
		if (this.levels) return this.levels[0].getFirstNodeChild();
		if (this.level) return this.level.getFirstNodeChild();
		return null;
	};
	this.getWaitingChild = function(componentName) {
		return Objects.get(this.waiting, componentName);
	};
	this.getTemplateById = function(tmpid) {
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
	this.subscribe = function(eventType, handler, subscriber) {
		this.listeners = this.listeners || [];
		this.listeners.push({'type': eventType, 'handler': handler, 'subscriber': subscriber});
	};
	this.registerElement = function(element, id) {
		this.elements = this.elements || {};
		this.elements[id] = element;
	};
	this.registerChildComponent = function(child) {
		this.childrenCount = this.childrenCount || 0;
		this.children = this.children || {};
		this.children[child.getId() || this.childrenCount] = child;
		this.childrenCount++;
	};
	this.unregisterChildComponent = function(child) {
		if (isControl(child)) _G_.get('Core').unregisterControl.call(this, child);
		var id = child.getId();		
		if (!id) {
			for (var k in this.children) {
				if (this.children[k] == child) {
					id = k;
					break;
				}
			}
		}
		if (isString(id)) {
			this.children[id] = null;
			delete this.children[id];
		}
	};
	this.registerControl = function(control, name) {
	 	this.controls = this.controls || {};
	 	if (!isUndefined(this.controls[name])) {
	 		if (!isArray(this.controls[name])) this.controls[name] = [this.controls[name]];
	 		this.controls[name].push(control);
	 	} else this.controls[name] = control;
	 	control.setName(name);
	};
	this.unregisterControl = function(control) {
		if (this.controls) {
			var name = control.getName();
			if (isArray(this.controls[name])) this.controls[name].removeItem(control);
			else {
				this.controls[name] = null;
				delete this.controls[name];
			}
		}
	};
	this.provideWithComponent = function(propName, componentName, waitingChild) {
		var cmp = this.getChild(componentName);
		if (cmp) waitingChild.set(propName, cmp);
		else {
			this.waiting = this.waiting || {};
			this.waiting[componentName] = this.waiting[componentName] || [];
			this.waiting[componentName].push([waitingChild, propName]);
		}
	};
	this.getParentElement = function() {
		return this.parentElement;
	};
	this.createUpdater = function(u, c, s, p, l) {
		var updater = new u(s, p, p['n']);
		this.addUpdater.call(c, updater, l);
	};
	this.addUpdater = function(u, l) {
		this.updaters = this.updaters || {};
		var keys = u.getKeys();
		for (var i = 0; i < keys.length; i++) {
			this.updaters[keys[i]] = this.updaters[keys[i]] || [];
			l.push(keys[i], u);
			this.updaters[keys[i]].push(u);
		}
	};
	this.disposeUpdater = function(t, u) {
		if (this.updaters && this.updaters[t]) {
			var i = this.updaters[t].indexOf(u);
			if (i > -1) {
				this.updaters[t][i].dispose();
				this.updaters[t].splice(i, 1);
			}
		}
	};
})(), 'Core');
_G_.set(function() {
	var listeners;
	this.listen = function(element, type, handler) {
		listeners = listeners || [];
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
}, 'EventHandler');
var Logger = new (function() {
	this.log = function(message, method, object, opts) {
		window.console.log(message);
	};
})();
_G_.set(Logger, 'Logger');
_G_.set(function() {
	var properRoutes = {};
	var routes = __ROUTES;
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
	};
	this.run = function() {
		onNavigate();
	};
	this.getPathPartAt = function(index) {
		return isArray(pathParts) ? pathParts[index] : '';
	};
	this.reload = function() {
		window.location.reload();
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
		return currentRoute || getRoute();
	};
	this.getCurrentRouteName = function() {
		if (currentRoute) return currentRoute['name'];
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
		if (isString(errorRoutes[errorCode])) {
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
}, 'Router');
_G_.set(function() {
	var log = [];
	var views = [];
	this.assert = function(t, a, k, e, c, m) {
		var i = this.check(t, a, k);
		if (!i) this.log(e, c, m);
		return i;
	};
	this.check = function(t, a, k) {
		var d = [], isa = isArray(k);
		if (isa) {
			for (var i = 0; i < k.length; i++) {
				d.push(k[i]);
				if (i < k.length - 1 && !this.check('arrayLike', a, d)) return false;
			}
		}
		d = null;
		if (isa) {
			for (var i = 0; i < k.length; i++) a = a[k[i]];
		}
		switch (t) {
			case 'string': return isString(a);
			case 'number': return isNumber(a);
			case 'numeric': return isNumeric(a);
			case 'bool': return isBool(a);
			case 'function': return isFunction(a);
			case 'array': return isArray(a);
			case 'object': return isObject(a);
			case 'arrayLike': return isArrayLike(a);
			case 'element': return isElement(a);
			case 'node': return isNode(a);
			case 'text': return isText(a);
			case 'componentLike': return isComponentLike(a);
			case 'component': return isComponent(a);
			case 'control': return isControl(a);
			case 'null': return isNull(a);
			case 'undefined': return isUndefined(a);
			case 'empty': return isNone(a);
			case 'notEmptyString': return isNotEmptyString(a);
			case 'zero': return isZero(a);
		}
		return true;
	};
	this.log = function(t, c, m) {
		t = c + '.' + m + ': ' + t;
		window.console.log(t);
		log.push(t);
	};
	this.onTested = function(view) {};
}, 'Tester');
_G_.set(function() {
	var userOptions = __USEROPTIONS;
	var app, status = {},
	attributes = {}, settings = {}, loaded = false, 
	loadRequest, saveRequest;
	var initOptions = function() {
		if (isObject(userOptions)) {
			if (userOptions['login'] && isString(userOptions['login'])) {
				var ajr = _G_.get('AjaxRequest');
				loadRequest = new ajr(userOptions['login'], this.setData.bind(this));
			}
		}
	};
	this.load = function(application) {
		if (!loaded) {
			initOptions();
			app = application;
			if (loadRequest) {
				loadRequest.execute();
				return;
			}
		}
		onLoad(getDefaultAttributes());
	};
	this.setData = function(data) {
		status = data['status'];
		attributes = data['attributes'];
		settings = data['settings'];
		loaded = true;
		if (isComponentLike(app)) {
			app.run();
		}
	};
	this.hasFullAccess = function() {
		var fullAccess = Objects.get(userOptions, 'fullAccess', null);
		var accessLevel = ~~status['accessLevel'];
		return !isNumber(fullAccess) || accessLevel >= fullAccess;
	};
	this.isAdmin = function() {
		var adminAccess = Objects.get(userOptions, 'adminAccess', null);
		var accessLevel = ~~status['accessLevel'];
		return !isNumber(adminAccess) || accessLevel >= adminAccess;
	};
	this.isBlocked = function() {
		return !!status['isBlocked'];
	};
	this.getBlockedReason = function() {
		return status['blockReason'];
	};
	this.hasAccessLevel = function(accessLevel, isEqual) {
		if (!isEqual) {
			return status['accessLevel'] >= accessLevel;
		}
		return status['accessLevel'] == accessLevel;
	};
	this.hasType = function(userType) {
		return status['type'] == userType;
	};
	this.isAuthorized = function() {
		return status['accessLevel'] > 0;
	};
	this.getAttributes = function() {
		return attributes;
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
	this.getSettings = function() {
		return settings;
	};
	this.getSetting = function(settingName) {
		return settings[settingName];
	};
	this.setSetting = function(settingName, settingValue) {
		settings[settingName] = settingValue;
		if (saveRequest) {
			saveRequest.execute({'isSetting': true, 'name': settingName, 'value': settingValue});
		}
	};
	var getDefaultAttributes = function() {
		return {
			'type': 'guest',
			'accessLevel': 0
		};
	};
}, 'User');
_G_.set(function() {
	this.assert = function(v,m,e) {
		if (!m(v)) console.log(e);
		return v;
	};
}, 'Validator');
_G_.set(function(c, p) {
	this.getKeys = function() {
		var a = [];
		for (var k in p['n']) {
			if (a.indexOf(p['n'][k]) == -1) {
				if (isString(p['n'][k])) a.push(p['n'][k]);
				else a.push.apply(a, p['n'][k]);
			}
		}
		return a;
	};
	this.react = function(d) {
		var pp = p['p'](), cp = {};
		var pc = !!p['n']['props'];
		if (pc && isObject(pp['p'])) {
			cp = pp['p'];
		}
		for (var k in p['n']) {
			if (isString(p['n'][k]) && !isUndefined(d[p['n'][k]])) {
				cp[k] = pc && pp['ap'] ? pp['ap'][k] : pp['p'][k];
			}
		}
		c.set(cp);
	};
	this.dispose = function() {
		c = p = null;
	};
}, 'ComponentUpdater');
_G_.set(function(e, p, n) {
	this.getKeys = function() {
		var a = [];
		for (var k in n) {
			if (isString(n[k])) a.push(n[k]);
			else a.push.apply(a, n[k]);
		}
		return a;
	};
	this.react = function(d) {
		var k, i, pn;
		for (k in n) {
			pn = n[k];
			if (isString(pn)) pn = [pn];
			for (i = 0; i < pn.length; i++) {
				if (!isUndefined(d[pn[i]])) {
					e.attr(__A[k] || k, p['p']()[k] || '');
					break;
				}
			}
		}
	};
	this.dispose = function() {
		e = p = null;
	};
}, 'ElementUpdater');
_G_.set(function(n, p, pn) {
	var a = isArray(pn) ? pn : [pn];
	this.getKeys = function() {
		return a;
	};
	this.react = function(d) {
		var c;
		if (isFunction(p['v'])) c = p['v'](); 
		else c = d[a[0]];
		n.textContent = c || '';
	};
	this.dispose = function() {
		n = p = a = null;
	};
}, 'NodeUpdater');
_G_.set(function(o, p, n) {
	var a = isArray(n) ? n : [n];
	this.getKeys = function() {
		return a;
	};
	this.react = function(d) {
		if (isString(n)) o.update(d[n]);
		else o.update();
	};
	this.getOperator = function() {
		return o;
	};
	this.dispose = function() {
		n = o = p = null;
	};
}, 'OperatorUpdater');
var Dates = new (function() {
	var date;
	var months = ["\u042f\u043d\u0432\u0430\u0440\u044c","\u0424\u0435\u0432\u0440\u0430\u043b\u044c","\u041c\u0430\u0440\u0442","\u0410\u043f\u0440\u0435\u043b\u044c","\u041c\u0430\u0439","\u0418\u044e\u043d\u044c","\u0418\u044e\u043b\u044c","\u0410\u0432\u0433\u0443\u0441\u0442","\u0421\u0435\u043d\u0442\u044f\u0431\u0440\u044c","\u041e\u043a\u0442\u044f\u0431\u0440\u044c","\u041d\u043e\u044f\u0431\u0440\u044c","\u0414\u0435\u043a\u0430\u0431\u0440\u044c"];
	var months2 = ["\u044f\u043d\u0432\u0430\u0440\u044f","\u0444\u0435\u0432\u0440\u0430\u043b\u044f","\u043c\u0430\u0440\u0442\u0430","\u0430\u043f\u0440\u0435\u043b\u044f","\u043c\u0430\u044f","\u0438\u044e\u043d\u044f","\u0438\u044e\u043b\u044f","\u0430\u0432\u0433\u0443\u0441\u0442\u0430","\u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","\u043e\u043a\u0442\u044f\u0431\u0440\u044f","\u043d\u043e\u044f\u0431\u0440\u044f","\u0434\u0435\u043a\u0430\u0431\u0440\u044f"];
	this.getYear = function() {
		return get().getFullYear();
	};
	this.getDay = function() {
		return get().getDate();
	};
	this.getDate = function() {
		var date = get();
		return {day: date.getDate(), month: date.getMonth(), year: date.getFullYear()};
	};
	this.getMonth = function() {
		return get().getMonth();
	};
	this.getMonthName = function() {
		if (isNumber(arguments[0])) {
			return months[arguments[0]];
		}
		return months[this.getMonth()];
	};
	this.getTimeStamp = function() {
		return new Date().getTime();
	};
	this.getDays = function(month, year) {
		return 33 - new Date(year, month, 33).getDate();
	};
	this.getWeekDay = function(day, month, year) {
		return new Date(year, month, day).getDay();
	};
	this.getFormattedDate = function(stringDate, format) {
		format = format.toLowerCase();
		stringDate = stringDate.split(/[ \.-]+/);
		var y, y2, ys, m2, d, d2;
		var s0 = ~~stringDate[0], m = ~~stringDate[1], s2 = ~~stringDate[2];
		if (s2) {
			m2 = m < 10 ? '0' + m : m;
			if (s2 > 100) {
				y = s2;
				d = s0;
			} else {
				d = s2;
				y = s0;
			}
			d2 = d < 10 ? '0' + d : d;
			ys = y + '';
			y2 = ys.charAt(2) + ys.charAt(3);
		} else {
			return stringDate;
		}
		format = format.replace(/y{4}/, y);
		format = format.replace(/y{2}/, y2);
		format = format.replace(/month/, months2[m - 1]);
		format = format.replace(/m{2}/, m2);
		format = format.replace(/m{1}/, m);
		format = format.replace(/d{2}/, d2);
		format = format.replace(/d{1}/, d);
		return format;
	};
	var get = function() {
		return new Date();
	};
})();
_G_.set(Dates, 'Dates');
var Decliner = new (function() {
	var words = __DW;
	this.getCount = function(key, num) {
		if (isArray(num)) num = num.length;
		return num + ' ' + this.get(key, num);
	};
	this.get = function(key, num) {
		if (isArray(num)) num = num.length;
		if (!isNumber(num)) return '';
		return Objects.get(Objects.get(words, key, ''), getVariant(num), '');
	};
	var getVariant = function(num) {
		var n, m;
		num = num.toString();
		m = num.charAt(num.length - 1); 		
		if (num.length > 1) n = num.charAt(num.length - 2); 
		else n = 0;
		if (n == 1) return 2;
		else { 
			if (m == 1) return 0;
			else if (m > 1 && m < 5) return 1;
			else return 2;
		}
	};
})();
_G_.set(Decliner, 'Decliner');
var Objects = new (function() {
	this.each = function(obj, callback, thisObj) {
		if (isArrayLike(obj)) {
			if (thisObj) callback = callback.bind(thisObj);
			for (var k in obj) if (callback(obj[k], k) == 'break') break;
		}
	};
	this.remove = function(obj, item) {
		if (isArray(obj)) this.removeAt(obj, obj.indexOf(item));
		else if (isObject(obj)) delete obj[obj.getKey(item)];
	};
	this.removeAt = function(arr, idx) {
		if (isArray(arr) && isNumber(idx) && idx >= 0) arr.splice(idx, 1);
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
		var objs = arguments;
		if (!isArrayLike(objs[0])) objs[0] = {};
		for (var i = 1; i < objs.length; i++) {
			if (isArrayLike(objs[i])) {
				for (var k in objs[i]) {
					if (!isUndefined(objs[i][k])) objs[0][k] = objs[i][k];
				}
			}
		}
		return objs[0];
	};
	this.concat = function() {
		var arrs = arguments;
		if (!isArray(arrs[0])) arrs[0] = [];
		for (var i = 1; i < arrs.length; i++) {
			if (isArray(arrs[i])) {
				for (var j = 0; j < arrs[i].length; j++) {
					arrs[0].push(arrs[i][j]);
				}
			}
		}
		return arrs[0];
	};
	this.clone = function(obj) {
		if (!isArrayLike(obj)) return obj;
		return JSON.parse(JSON.stringify(obj));
	};
	this.get = function(obj, key, defaultValue) {
		return this.has(obj, key) ? obj[key] : defaultValue;
	};
	this.getByIndex = function(obj, idx) {
		if (!isArrayLike(obj)) return;
		if (isArray(obj)) return obj[idx];
		var count = 0;
		for (var k in obj) {
			if (count == idx) return obj[k];
			count++;
		}
	};
	this.has = function(obj, key, value) {
		if (!isArrayLike(obj)) return false;
		var has = !isUndefined(obj[key]);
		if (has && !isUndefined(value)) return obj[key] == value;
		return has;
	};
	this.empty = function(obj) {
		if (!isArrayLike(obj)) return true;
		if (isObject(obj)) {
			for (var k in obj) return false;
			return true;
		}
		return isUndefined(obj[0]);
	};
	this.getKey = function(obj, value) {
		for (var k in obj) if (obj[k] == value) return k;
	};
	this.getValues = function(obj) {
		var vals = [];
		for (var k in obj) vals.push(obj[k]);
		return vals;
	};
	this.getKeys = function(obj) {
		var keys = [];
		if (isObject(obj)) {
			for (var k in obj) keys.push(k);
		} else if (isArray(obj)) {
			for (var i = 0; i < obj.length; i++) keys.push(i);
		}
		return keys;
	};
	this.flatten = function(obj, flattened, transformed) {
		var top = isUndefined(transformed);
		flattened = flattened || {};
		transformed = transformed || [];
		if (!isObject(obj)) return obj;
		for (var k in obj) {
			if (isObject(obj[k])) Objects.flatten(obj[k], flattened, transformed);
			else {
				if (!isUndefined(flattened[k])) {
					if (transformed.indexOf(k) == -1 || !isArray(flattened[k])) {
						flattened[k] = [flattened[k]];
						transformed.push(k);
					}					
					flattened[k].push(obj[k])
				} else flattened[k] = obj[k];
			}
		}
		if (top) transformed = null;
		return flattened;
	};
})();
_G_.set(Objects, 'Objects');
function generateRandomKey() {
	var x = 2147483648, now = +new Date();
	return Math.floor(Math.random() * x).toString(36) + Math.abs(Math.floor(Math.random() * x) ^ now).toString(36);
}
function toCamelCase(str) {
	return String(str).replace(/\-([a-z])/g, function(all, match) {
		return match.toUpperCase();
	});
}
function isComponentLike(a) {
	return isObject(a) && isFunction(a.instanceOf);
}
function isComponent(a) {
	return isComponentLike(a) && a.instanceOf('Component');
}
function isController(a) {
	return isComponentLike(a) && a.instanceOf('Controller');
}
function isControl(a) {
	return isComponentLike(a) && a.instanceOf('Control');
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
function isText(a) {
	return a instanceof Text;
}
function isFunction(a) {
	return a instanceof Function;
}
function isBool(a) {
	return typeof a == 'boolean';
}
function isBoolean(a) {
	return isBool(a);
}
function isString(a) {
	return typeof a == 'string';
}
function isNumber(a) {
	return typeof a == 'number';
}
function isPrimitive(a) {
	return isString(a) || isNumber(a) || isBool(a);
}
function isNumeric(a) {
	return isNumber(a) || (isString(a) && (/^\d+$/).test(a));
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
function isZero(a) {
	return a === 0 || a === '0';
}
function isNotEmptyString(a) {
	return isString(a) && (/[^\s]/).test(a);
}
function stringToNumber(str) {
	return Number(str);
}
function getCount(a) {
	return isArray(a) ? a.length : 0;
}


/////////////////
/////////////////
/////////////////


var Router=_G_.get('Router',1);
var User=_G_.get('User',1);
var route = Router.getCurrentRoute();

Loader.get(__LU, {'route': route['name']},
function(__DATA) {
var route = Router.getCurrentRoute();
__V = __DATA['dataConstants'];
__T = __DATA['texts'];
__ = __DATA['textsConstants'];
_G_.set(c=function(){},'DataTable');
p=c.prototype;
p.handleClick=function(){
console.log(this)
};
p.getTemplateMain=function(_,$){
	return{'c':[{'cmp':'DataTableTabPanel'},{'v':function(){return $.a('aaa')},'g':'aaa'}],'t':0,'p':{'c':'data-table_outer-container'}}
};
_G_.set(c=function(){},'DataTableTabPanel');
p=c.prototype;
p.getTemplateMain=function(_,$){
	return[__T[0],'<br>',_['children'],'324324','<br>',_['children']]
};
_G_.set(c=function(){},'DataTableFragmets');
p=c.prototype;
p.getTemplateMain=function(_,$){
	return{'t':0,'p':{'c':'data-table-fragmets','sc':1}}
};
_G_.set(c=function(){},'DataTableRow');
p=c.prototype;
p.getTemplateMain=function(_,$){};
p.getTemplateControls=function(_,$){
	return[{'t':0,'p':{'c':'data-table-row_color-mark datatable-control'}},{'c':{'tmp':1},'t':0,'p':{'c':'data-table-row_checkbox-container datatable-control'}},{'t':0,'p':{'c':'data-table-row_star datatable-control'}}]
};
p.getTemplateHotMark=function(_,$){
	return{'t':0,'p':{'_title':'fuck','c':'tooltiped datatable-tooltiped datatable-hot-tender','_timeout':'true'}}
};
p.getTemplateCount=function(_,$){
	return{'c':_['count'],'t':1,'p':{'c':'data-table-row_count'}}
};
p.templatesById={'name':p.getTemplateHotMark};
_G_.set(c=function(){},'DataTableStandartRow');
p=c.prototype;
p.getTemplateMain=function(_,$){
	return[!_['nocontrols']?[{'tmp':$.getTemplateControls}]:'',{'c':[{'c':[{'c':getFzName(_['type']),'t':0,'p':{'c':'data-table-standart-row_top-item'}},_['multiregion']?[{'c':[{'tmp':$.getTemplateCount,'p':{'count':_['multiregion']}},_['regionName']],'t':0,'p':{'c':'data-table-standart-row_top-item tooltiped','txt':_['regionnames'],'cap':__[0],'del':'1','cor':'list','pos':'left-top'}}]:{'c':_['regionName'],'t':0,'p':{'c':'data-table-standart-row_top-item'}},_['multicategory']?[{'c':[{'tmp':$.getTemplateCount,'p':{'count':_['multicategory']}},_['subcategory']],'t':0,'p':{'c':'data-table-standart-row_top-item tooltiped','txt':_['subcategories'],'cap':__[1],'del':'1','cor':'list','pos':'left-top'}}]:{'c':_['subcategory'],'t':0,'p':{'c':'data-table-standart-row_top-item'}},{'c':_['razm'] == 'N/A'?[{'tmp':0,'p':{'tariff':_['isUnavailable'],'width':'66px'}}]:_['razm'],'t':0,'p':{'c':'data-table-standart-row_top-item'}}],'t':0,'p':{'c':'data-table-standart-row_top'}},{'c':_['price'],'t':0,'p':{'c':'data-table-standart-row_price'}},{'c':[_['hot']?[{'tmp':$.getTemplateHotMark}]:'',_['name']],'t':0,'p':{'c':'data-table-standart-row_name'}},{'t':0,'p':{'c':'data-table-standart-row_bottom'}},_['fragments']?[{'cmp':'DataTableFragmets','p':{'p':{'data':_['fragments']}}}]:''],'t':12,'p':{'c':'data-table-standart-row','h':'#tender/'+_['Id'],'tr':'_blank','_id':_['Id'],'sc':1}}]
};
_G_.set(c=function(){},'TenderDataTable');
p=c.prototype;
_G_.set(c=function(){},'FilterStatistics');
p=c.prototype;
p.onRendered=function(){
this.refresh();
};
p.onRefreshButtonClick=function(){
var a=this.findElement('.filter-statistics');
this.each('filters',function(filter){
StoreKeeper.remove('filterStat_'+filter.filterId);
});
this.refresh();
};
p.onFilterClick=__FNC;
p.refresh=function(){
 this.getElement('rb').hide();
this.currentFilterIndex =0;
this.getCountForFilterWithIndex(0); 
};
p.onLoaded=function(filters){
this.set('filters',filters);
};
p.updateFilterCount=function(data){
this.fill('.row' + data.filterId, data.numbers);
this.currentFilterIndex++;
this.getCountForFilterWithIndex(this.currentFilterIndex);
};
p.getCountForFilterWithIndex=function(index){
var filter=Objects.get(this.get('filters'),index);
if(isObject(filter)){
__C.get(2).doAction(this,'load',{'filterId':filter.filterId});
}else{
 this.getElement('rb').show();
}
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':[_['title'],{'c':__[103],'t':0,'p':{'as':'rb','c':'filter-statistics_refresh'}}],'t':0,'p':{'c':'filter-statistics_title'}},{'c':{'c':[{'c':__[104],'t':7},{'c':__[105],'t':7},{'c':__[106],'t':7},{'c':__[107],'t':7},_['extended']?[{'c':__[108],'t':7},{'c':__[109],'t':7}]:''],'t':5},'t':2,'p':{'c':'filter-statistics_head','cp':'0','cs':'0'}},{'c':{'h':function(filter){return {'c':[{'c':filter.header,'t':1,'p':{'c':'filter-statistics_row-name'}},{'c':['+',{'pl':'today'}],'t':1},{'c':['+',{'pl':'yesterday'}],'t':1},{'c':{'pl':'current'},'t':1},_['extended']?[{'c':{'pl':'week'},'t':1},{'c':{'pl':'month'},'t':1}]:''],'t':0,'p':{'c':'filter-statistics_row row'+filter.filterId}}},'p':function(){return $.g('filters')},'n':'filters'},'t':0,'p':{'c':'filter-statistics_content'}}],'t':0,'p':{'c':'filter-statistics '+_['className'],'sc':1}}
};
p.getInitials=function(){
	return {
		'loader':{'controller': __C.get(1),'async': false},
		'controllers':[{'controller': __C.get(2),'on': {'load': this.updateFilterCount},'private': true}],
		'events':{'click': {'filter-statistics_refresh': this.onRefreshButtonClick,'filter-statistics_row-name': this.onFilterClick}}
	};
};
_G_.set(c=function(){},'SearchForm');
p=c.prototype;
p.onResetButtonClick=function(){
this.set('reset',true);
this.delay(function(){
this.set('reset',false);
},2500);
};
p.onResetConfirmed=__FNC;
p.getProperData=function(data){
return Objects.flatten(this.getControlsData());
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':_['title'],'t':0,'p':{'c':'search-form_title'}},{'t':0,'e':[0,$.d.b($,'expand')],'p':{'c':'search-form_close-side'}},{'t':0,'e':[0,$.d.b($,'expand')],'p':{'c':'search-form_close'}},{'tmp':$.getTemplateContent}],'t':0,'p':{'c':'search-form','sc':1}}
};
p.getTemplateReset=function(_,$){
	return{'c':[{'c':__[10],'t':0,'e':[0,$.onResetButtonClick],'p':{'c':'hover-label'}},{'c':[__[11],' ',{'c':__[12],'t':38,'p':{'c':'confirm-reset-filter'}}],'t':0,'e':[0,$.onResetConfirmed],'p':{'c':'confirm-label'}}],'t':0,'p':function(){return{'c':'search-form_reset '+($.g('reset')?'active':'')}},'n':{'c':'reset'}}
};
_G_.set(c=function(){},'SearchFormButton');
p=c.prototype;
p.getTemplateMain=function(_,$){
	return{'c':{'tmp':$.getTemplateContent},'t':0,'p':{'c':'search-form_button '+_['className']}}
};
_G_.set(c=function(){},'SearchFormPanel');
p=c.prototype;
p.show=function(){
this.addClass('shown');
Popuper.watch(this);
};
p.hide=function(){
this.addClass('shown', false);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'t':0,'e':[0,$.hide],'p':{'c':'app-search-form-panel-close'}},{'c':_['title'],'t':0,'p':{'c':'app-search-form-panel-title'}},{'tmp':$.getTemplateContent}],'t':0,'p':{'c':'app-search-form-panel '+_['className'],'sc':1}}
};
_G_.set(c=function(){},'SearchFormPanelButton');
p=c.prototype;
p.onClick=function(){
this.get('panel').show();
};
p.getTemplateMain=function(_,$){
	return{'c':{'tmp':$.getTemplateContent},'t':0,'e':[0,$.onClick],'p':{'c':'search-form_button '+_['className']}}
};
_G_.set(c=function(){},'Keywords');
p=c.prototype;
p.setControlValue=function(value){
this.set('keywords',value['tags']);
};
p.onChange=function(){
State.dispatchEvent('TenderSearchFormChanged');
};
p.addRequest=function(){
this.addOneTo('keywords',[],0);
};
p.removeRequest=function(index,isExact){
this.removeByIndexFrom('keywords',isExact?index:this.get('keywordsCount')-index-1);
};
p.onKeywordsChange=function(kw){
var kwlen=kw.length,tabs=[],i;
for(i=1;i<=kwlen;i++){
tabs.push(__[28]+' '+i);
} 
this.set({'keywordsCount':kwlen,'tabs':tabs,'activeTab':kwlen-1});
this.appendChild('tabs',kwlen>1); 
this.forChildren('KeywordsControl',function(child,i){
child.set('index',kwlen-i);
});
};
p.onSelectTab=function(index){
index=this.get('keywordsCount')-index-1;
 this.getElement('area').scrollTo( this.findElements('.keywords_block')[index],300);
};
p.onTagEdit=function(tag){
 this.getChild('editor').edit(tag);
Popuper.skipAll(true);
};
p.onTagEdited=function(){
Popuper.skipAll(false);
};
p.onRemoveRequestClick=function(target){
var block =target.getAncestor('.keywords_block');
var blocks = this.findElements('.keywords_block');
this.removeRequest(blocks.indexOf(block), true);
};
p.getTemplateMain=function(_,$){
	return[{'c':[{'c':__[16],'t':1,'p':{'c':'bold'}},{'cmp':'Select','nm':'nonmorph','p':{'p':{'options':__V[0],'className':'frameless','tooltip':true}}},{'c':__[17],'t':1,'p':{'c':'bold'}},{'cmp':'Checkbox','nm':'searchInDocumentation','p':{'p':__V[1]}},{'cmp':'Checkbox','nm':'registryContracts','p':{'p':__V[2]}},{'cmp':'Checkbox','nm':'registryProducts','p':{'p':__V[3]}},{'c':[{'c':__[21],'t':1},{'tmp':2,'p':{'className':'cb4','key':'keywordsNewReq'}}],'t':0,'p':{'c':'keywords_add-request'}},{'t':0,'p':{'c':'tooltip keywords-hint'}}],'t':0,'p':{'c':'keywords_options'}},{'cmp':'Tabs','e':[22,$.onSelectTab,'remove',$.removeRequest],'p':function(){return{'p':{'items':$.g('tabs'),'activeTab':$.g('activeTab')},'i':'tabs'}},'n':{'items':'tabs','activeTab':'activeTab'}},{'c':{'h':function(item){return {'cmp':'KeywordsControl','nm':'tags','e':['edit',$.onTagEdit,14,$.onChange],'p':{'p':{'items':item}}}},'p':function(){return $.g('keywords')},'n':'keywords'},'t':0,'p':function(){return{'as':'area','c':'keywords_area '+($.g('keywordsCount')>1?'multi':'')}},'n':{'c':'keywordsCount'}},{'cmp':'KeywordTagEditor','e':['hide',$.onTagEdited],'p':{'i':'editor'}}]
};
p.getInitials=function(){
	return {
		'events':{'click': {'keywords_add-request': this.addRequest,'keywords_remove-request': this.onRemoveRequestClick}},
		'followers':{'keywords': this.onKeywordsChange}
	};
};
_G_.set(c=function(){},'KeywordsButton');
p=c.prototype;
p.getTemplateContent=function(_,$){
	return{'c':__[13],'t':0}
};
p.getInitials=function(){
	return {
		'props':{'className': 'search-keywords'}
	};
};
_G_.set(c=function(){},'KeywordsControl');
p=c.prototype;
p.onFocus=function(switched){
this.set('switched',switched);
};
p.onRecommendationsChange=function(count){
this.set('hasRecomm',count>0);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':[{'c':[__[22],{'c':[__[28],' ',{'v':$.g('index'),'n':'index'},{'c':__[29],'t':1,'p':{'c':'keywords_remove-request'}}],'t':1,'p':{'c':'keywords_index'}}],'t':0,'p':{'c':'keywords_tags-title'}},{'cmp':'ContainKeywordTags','nm':'containKeyword','e':[15,$.onFocus.b($,false),'edit',$.d.b($,'edit'),'recchange',$.onRecommendationsChange,14,$.d.b($,'change')],'p':{'p':{'items':_['items'][0]}}}],'t':0,'p':{'c':'keywords_left'}},{'c':[{'c':__[23],'t':0,'p':{'c':'keywords_tags-title'}},{'cmp':'ExcludeKeywordTags','nm':'notcontainKeyword','e':[15,$.onFocus.b($,true),'edit',$.d.b($,'edit'),14,$.d.b($,'change')],'p':{'p':{'items':_['items'][1]}}}],'t':0,'p':{'c':'keywords_right'}}],'t':0,'p':function(){return{'c':'keywords_block'+($.g('switched')?' switched':'')+($.g('hasRecomm')?' with-recommendations':''),'sc':1}},'n':{'c':['hasRecomm','switched']}}
};
_G_.set(c=function(){},'KeywordsPanel');
p=c.prototype;
p.getTemplateContent=function(_,$){
	return{'cmp':'Keywords','nm':'keywords'}
};
p.getInitials=function(){
	return {
		'props':{'className': 'keywords-panel','title': __[13]}
	};
};
_G_.set(c=function(){},'TenderSearchForm');
p=c.prototype;
p.onRendered=function(){
this.setParams({
'registryContracts': 1
});
this.delay(function(){
State.set('aaa','super-puper-class')
State.dispatchEvent('aaa');
},6000);
};
p.onChange=function(){
var data =this.getProperData();
};
p.setParams=function(params){
params=_G_.get('SearchFormCrr').correct(params);
this.setControlsData(params);
};
p.getTemplateContent=function(_,$){
	return[{'cmp':'KeywordsPanel','p':{'i':'keywordsPanel'}},{'tmp':$.getTemplateReset},{'c':[{'cmp':'SearchFormFilters'},{'c':{'cmp':'KeywordsButton','w':['panel','keywordsPanel']},'t':0,'p':{'c':'tender-search-form_content'}}],'t':0,'p':{'c':'tender-search-form','sc':1}}]
};
p.getInitials=function(){
	return {
		'props':{'title': __[4]},
		'listeners':{'TenderSearchFormChanged': this.onChange,'TenderSearchFormGotParams': this.setParams}
	};
};
_G_.set(c=function(){},'SearchFormCreateFilterMenu');
p=c.prototype;
p.onCreateButtonClick=function(){
alert('create filter')
};
p.onWizardButtonClick=function(){
alert('create filter with wizard') 
};
p.getInitials=function(){
	return {
		'props':{'className': 'create-filters-menu','buttons': [{'name': __[5],'handler': this.onCreateButtonClick},{'name': __[6],'handler': this.onWizardButtonClick}]}
	};
};
_G_.set(c=function(){},'SearchFormFilterMenu');
p=c.prototype;
p.onLoadFilters=function(filters){
this.renderButtons(filters);
};
p.onCheckboxChange=function(e){
__C.get(1).doAction(this,'set',{'filterId': e.value, 'param': 'isAutoOpen', 'value': e.checked});
};
p.getButtonData=function(item){
return {
'value': item['filterId'],
'name': item['header'],
'isAutoOpen': item['isAutoOpen']
};
};
p.handleClick=function(value,button){
App.getView('search').openFilter(value);
};
p.getTemplateContent=function(_,$){
	return{'t':0,'p':{'c':'checkbox '+(_['item']['isAutoOpen']?'checked':''),'_value':_['item']['value']}}
};
p.getInitials=function(){
	return {
		'props':{'className': 'filters-menu','maxHeight': 400},
		'controllers':[{'controller': __C.get(1),'on': {'load': this.onLoadFilters}}],
		'helpers':[{'helper':'CheckboxHandler','options': {'callback': this.onCheckboxChange,'intValue': true}}]
	};
};
_G_.set(c=function(){},'SearchFormFilters');
p=c.prototype;
p.onLoadFilters=function(filters){
this.set('quantity',filters.length);
};
p.onSaveFilterClick=function(){
__DI.show(FilterEdit,{'filterId': State.get('filterId')});
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':[{'c':__[7],'t':1},{'cmp':'SearchFormCreateFilterMenu'}],'t':0,'p':{'c':'search-form-filters_create-button'}},{'c':[{'c':__[8],'t':0,'p':{'c':'search-form-filters_button-inner'}},{'c':[{'c':{'v':$.g('quantity'),'n':'quantity'},'t':40,'p':{'c':'search-form-filters_button-quantity'}},{'t':0,'p':{'c':'search-form-filters_button-plus'}}],'t':0,'p':{'c':'search-form-filters_button-side'}},{'cmp':'SearchFormFilterMenu'}],'t':0,'p':function(){return{'c':'search-form-filters_button'+(!$.g('quantity')?' with-plus':'')}},'n':{'c':'quantity'}},{'c':{'v':$.g('filterName'),'n':'filterName'},'t':0,'e':[0,$.onSaveFilterClick],'p':{'c':'search-form-filters_name'}},{'c':__[9],'t':0,'e':[0,$.onSaveFilterClick],'p':{'c':'search-form-filters_save-button'}}],'t':0,'p':{'c':'search-form-filters','sc':1}}
};
p.getInitials=function(){
	return {
		'controllers':[{'controller': __C.get(1),'on': {'load': this.onLoadFilters}}],
		'props':{'filterName': 'Master'}
	};
};
_G_.set(c=function(){},'Favorites');
p=c.prototype;
p.getInitials=function(){
	return {
		'actions':{'load': {'url': CONFIG.favorites.get},'add': {'url': CONFIG.favorites.add},'put': {'url': CONFIG.favorites.remove}}
	};
};
_G_.set(c=function(){},'Filters');
p=c.prototype;
p.onLoadFilters=function(data){};
p.onLoad=function(data){};
p.onAdd=function(data){};
p.onSubscribe=function(){
this.doAction(null,'load');
};
p.getInitials=function(){
	return {
		'options':{'key': 'filterId','store': false,'storeAs': 'filters','storePeriod': '1day','clone': true},
		'actions':{'load': {'url' : CONFIG.filters.load,'method' : 'GET','callback': this.onLoad},'save': {'url' : CONFIG.filters.save,'method' : 'POST','callback': this.onAdd},'set': {'url' : CONFIG.filters.set,'method': 'POST'},'subscribe': {'url' : CONFIG.filters.subscribe,'method': 'POST','callback': this.onSubscribe}}
	};
};
_G_.set(c=function(){},'FiltersStat');
p=c.prototype;
p.getInitials=function(){
	return {
		'options':{'key': 'filterId','store': false,'storeAs': 'filterStat_$filterId','storePeriod': '4hour'},
		'actions':{'load': {'url': CONFIG.filterStat.load,'method': 'GET'}}
	};
};
_G_.set(c=function(){},'RecommendationsLoader');
p=c.prototype;
p.getInitials=function(){
	return {
		'actions':{'load': {'url': CONFIG.keywords.recommendations,'method': 'POST','autoset': {'data': 'items'}}}
	};
};
_G_.set(c=function(){},'Subscription');
p=c.prototype;
p.getInitials=function(){
	return {
		'actions':{'load': {'url': CONFIG.settings.subscr,'method': 'GET','autoset': {'options': 'opts'}},'save': {'url': CONFIG.settings.set,'method': 'GET'}}
	};
};
_G_.set(c=function(){},'UserInfoLoader');
p=c.prototype;
p.getInitials=function(){
	return {
		'actions':{'load': {'url' : CONFIG.user.get,'method' : 'GET'}}
	};
};
_G_.set(c=function(){},'Checkbox');
p=c.prototype;
p.onClick=function(){
this.toggle('checked');
this.dispatchChange();
};
p.getControlValue=function(){
return this.get('checked')?1:0;
};
p.setControlValue=function(value){
this.set('checked',!!value);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'t':1,'p':function(){return{'c':'checkbox '+($.g('checked')?'checked':'')}},'n':{'c':'checked'}},_['text']],'t':1,'e':[0,$.onClick],'p':{'c':'checkbox_label'}}
};
_G_.set(c=function(){},'Input');
p=c.prototype;
p.getControlValue=function(){
return this.findElement('input').value;
};
p.getTemplateMain=function(_,$){
	return{'t':14,'e':[18,$.onChange],'p':function(){return{'tp':_['type'],'n':_['name'],'p':$.g('placeholder'),'v':$.g('value'),'readonly':!$.g('enabled')?'readonly':'','accept':_['accept']}},'n':{'p':'placeholder','v':'value','readonly':'enabled'}}
};
_G_.set(c=function(){},'Select');
p=c.prototype;
p.onRendered=function(){
var value=this.get('value');
var selected;
if (!isUndefined(value)) {
selected =this.selectByValue(value, true);
}
if (!selected) {
this.selectByIndex(0);
}
};
p.getChangeEventParams=function(){
return{value:this.get('value'),title:this.get('title')};
};
p.selectByValue=function(value,forced){
if(!forced && this.get('value')==value)return;
var options=this.get('options');
if(isArray(options)){
for(var i=0;i<options.length;i++){
if(options[i]['value']==value){
this.selectedIndex=i;
if(!forced)this.set('value',value);
this.set('title',options[i]['title']);
this.syncTooltip(i);
return true;
}
}
}
return false;
};
p.selectByIndex=function(index){
var options=this.get('options');
this.selectedIndex=index;
if(isObject(options[index])){
if(this.get('value')==options[index]['value'])return;
this.set({'value':options[index]['value'],'title':options[index]['title']});
this.syncTooltip(index);
}
};
p.syncTooltip=function(index){
var optionElement =this.getOptionElementAt(index);
var tooltipElement = this.findElement('.tooltip,optionElement'); 
};
p.enableOption=function(index,isEnabled){
this.getOptionElementAt(index).toggleClass('disabled', !isEnabled);
if (index ==this.selectedIndex) {
this.selectByIndex(index ==0 ? index + 1 : 0);
}
};
p.onOptionsClick=function(e){
var target =e.getTarget('.select_option');
if (target && !target.hasClass('disabled')) {
var value =target.getData('value');
if (this.selectByValue(value)) {
this.dispatchChange();
}
this.hide();
}
};
p.getOptionElementAt=function(index){
return this.findElement('.select_options').getChildAt(index);
};
p.setProperValue=function(value){
this.selectByValue(value);
};
p.getControlValue=function(){
return this.findElement('input').value;
};
p.onClick=function(){
this.toggle('active');
Popuper.watch(this);
};
p.hide=function(){
this.set('active',false);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':[{'v':$.g('title'),'n':'title'},_['tooltip']?[{'tmp':2,'p':{'className':'cb4','key':$.g('tooltip')}}]:''],'t':0,'e':[0,$.onClick],'p':{'c':'select_value'}},{'c':{'h':function(option){return {'c':[option.title,option.tooltip?[{'tmp':2,'p':{'className':'cb4','key':option.tooltip}}]:''],'t':0,'p':{'c':'select_option','_value':option.value}}},'p':function(){return $.g('options')},'n':'options'},'t':0,'e':[0,$.onOptionsClick],'p':{'c':'select_options'}},{'t':14,'p':function(){return{'tp':'hidden','n':$.g('name'),'v':$.g('value')}},'n':{'n':'name','v':'value'}}],'t':0,'p':function(){return{'c':'select '+(_['className']?_['className']:'')+' '+($.g('active')?'active':''),'sc':1}},'n':{'c':'active'}}
};
_G_.set(c=function(){},'ContainKeywordTags');
p=c.prototype;
p.onRendered=function(){
this.resetOptions();
};
p.onPickRecommendation=function(value){};
p.onEnter=function(value){
KeywordTags.prototype.onEnter.call(this,value);
var items=this.get('items').join(',').replace(/\#\d/g,'').split(',');
 this.getChild('recommendations').load(items);
};
p.getCorrectedText=function(text){
var opt1=this.get('opt1value');
var opt2=this.get('opt2value');
if(opt1>1||opt2>1){
return text+'#'+opt1+'#'+opt2;
}
return text;
};
p.resetOptions=function(){
this.set({'opt1':__[32],'opt2':__[34]});
this.set('opt1value',1);
this.set('opt2value',1);
};
p.getTemplateTopContent=function(_,$){
	return{'c':[{'c':[{'v':$.g('opt1'),'n':'opt1'},{'cmp':'PopupSelect','e':[14,$.onChangeOption],'p':{'p':{'options':__V[5],'title':__[30]},'i':'opt1'}}],'t':1,'p':{'c':'tags_select-button','_index':'1'}},{'c':[{'v':$.g('opt2'),'n':'opt2'},{'cmp':'PopupSelect','e':[14,$.onChangeOption],'p':{'p':{'options':__V[6],'title':__[31]},'i':'opt2'}}],'t':1,'p':{'c':'tags_select-button','_index':'2'}},{'tmp':$.getTemplateTopButtons}],'t':0,'p':{'c':'tags_top'}}
};
p.getTemplateInput=function(_,$){
	return{'cmp':'KeywordsAutoComplete','e':[15,$.d.b($,'focus'),'enter',$.onEnter,'pick',$.onPickVariant],'p':{'p':{'placeholder':__[24],'options':__V[4]}}}
};
p.getTemplateBottomContent=function(_,$){
	return{'cmp':'Recommendations','e':['pick',$.onPickRecommendation,14,$.d.b($,'recchange')],'p':{'i':'recommendations'}}
};
_G_.set(c=function(){},'ExcludeKeywordTags');
p=c.prototype;
p.onRendered=function(){
this.resetOptions();
};
p.getCorrectedText=function(text){
var opt1value=this.get('opt1value');
if (opt1value > 1) {
return text + '#' + opt1value;
}
return text;
};
p.resetOptions=function(){
this.set({'opt1':__[32],'opt1value':1});
};
p.getTemplateTopContent=function(_,$){
	return{'c':[{'c':[{'v':$.g('opt1'),'n':'opt1'},{'cmp':'PopupSelect','e':[14,$.onChangeOption],'p':{'p':{'options':__V[7],'title':__[41]},'i':'opt1'}}],'t':1,'p':{'c':'tags_select-button','_index':'1'}},{'tmp':$.getTemplateTopButtons}],'t':0,'p':{'c':'tags_top'}}
};
p.getTemplateInput=function(_,$){
	return{'cmp':'AutoComplete','e':[15,$.d.b($,'focus'),'enter',$.onEnter],'p':{'p':{'placeholder':__[24]}}}
};
_G_.set(c=function(){},'KeywordTags');
p=c.prototype;
p.onEnter=function(value){
Tags.prototype.onEnter.call(this, value);
this.resetOptions();
};
p.onOptionClick=function(target){
var select =this.getChild('opt' + target.getData('index'));
if (select) {
select.show();
}
};
p.onChangeOption=function(e,target){
var cmpid =target.getId();
this.set(cmpid, e.title);
this.set(cmpid + 'value', e.value);
target.hide();
};
p.hasOption=function(text){
return !!text.split('#')[1];
};
p.getProperTagText=function(text){
return text.split('#')[0];
};
p.tagExists=function(text){
var items=this.get('items').join(',').replace(/\#\d/g,'');
return items.split(',').has(text);
};
p.resetOptions=__FNC;
p.getTemplateTopButtons=function(_,$){
	return{'c':[{'c':[__[26],' ',{'v':$.g('count'),'n':'count'}],'t':0,'p':{'c':'tags_top-buttons-item'}},{'c':{'c':__[27],'t':1,'p':{'c':'tags_remove-all'}},'t':0,'p':{'c':'tags_top-buttons-item'}}],'t':0,'p':{'c':'tags_top-buttons'}}
};
p.getTemplateTag=function(_,$){
	return{'c':[{'c':$.getProperTagText(_['text']),'t':1,'p':{'c':'tags_item-text','_text':_['text']}},{'t':1,'p':{'c':'tags_remove'}}],'t':0,'p':{'c':'tags_item '+($.hasOption(_['text'])?'optioned':'')}}
};
p.getInitials=function(){
	return {
		'events':{'click': {'app-tags-remove-all': this.clear,'app-tags-select-button': this.onOptionClick}}
	};
};
_G_.set(c=function(){},'Tags');
p=c.prototype;
p.onEnter=function(value){
value=value.split(',');
var a=[],tv;
var b=value,idx;for(idx=0;idx<b.length;idx++){var v=b[idx];
tv=v.trim().toLowerCase();
if(!tv.isEmpty()&&!this.tagExists(tv)){
a.push(this.getCorrectedText(tv));
}
}
if(!a.isEmpty()){
this.addTo('items',a,0);
this.dispatchChange();
}
};
p.tagExists=function(text){
return this.get('items').has(text);
};
p.getCorrectedText=function(text){
return text;
};
p.onPickVariant=function(value){
this.onEnter(value);
};
p.onRemoveButtonClick=function(target){
this.removeValueFrom('items',target.prev().getData('text'));
this.dispatchChange();
};
p.onTagClick=function(target){
this.dispatchEvent('edit',target);
};
p.getControlValue=function(){
return this.get('items').join(',');
};
p.clearControl=function(){
this.set('items',[]);
};
p.onItemsChange=function(items){
this.set('count',items.length);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':[{'tmp':$.getTemplateTopContent},{'tmp':$.getTemplateInput},{'c':{'c':{'h':function(item){return {'tmp':$.getTemplateTag,'p':{'text':item}}},'p':function(){return $.g('items')},'n':'items'},'t':0,'p':{'c':'tags_placeholder'}},'t':0,'p':{'c':'tags_content'}}],'t':0,'p':{'c':'tags_container'}},{'tmp':$.getTemplateBottomContent}],'t':0,'p':{'c':'tags','sc':1}}
};
p.getTemplateInput=function(_,$){
	return{'cmp':'AutoComplete','e':[15,$.d.b($,'focus'),'enter',$.onEnter,'pick',$.onPickVariant]}
};
p.getTemplateTag=function(_,$){
	return{'c':[{'c':_['text'],'t':1,'p':{'c':'tags_item-text','_text':_['text']}},{'t':1,'p':{'c':'tags_remove'}}],'t':0,'p':{'c':'tags_item'}}
};
p.getInitials=function(){
	return {
		'props':{'items': [],'count': 0},
		'events':{'click': {'app-tags-remove': this.onRemoveButtonClick,'app-tags-item-text': this.onTagClick}},
		'followers':{'items': this.onItemsChange}
	};
};
_G_.set(c=function(){},'Textarea');
p=c.prototype;
p.getControlValue=function(){
return this.findElement('textarea').value;
};
p.getTemplateMain=function(_,$){
	return{'c':{'v':$.g('value'),'n':'value'},'t':47,'e':[18,$.onChange],'p':function(){return{'n':$.g('name'),'p':$.g('placeholder'),'readonly':$.g('enabled')?'readonly':''}},'n':{'n':'name','p':'placeholder','readonly':'enabled'}}
};
_G_.set(c=function(){},'SearchFormCrr',1);
p=c.prototype;
p.correct=function(params){
var tags =[];
var maxlen =Objects.get(params['containKeyword'], 'length', 0);
maxlen =Math.max(maxlen, Objects.get(params['notcontainKeyword'], 'length', 0));
if (maxlen > 0) {
var ck, nck;
for (var i =0; i < maxlen; i++) {
ck =Objects.get(params['containKeyword'], i, '').toArray();
nck =Objects.get(params['notcontainKeyword'], i, '').toArray();
tags.push([ck, nck]);
}
} else {
tags.push([]);
}
return {
'keywords': {
'nonmorph': params['nonmorph'],
'registryContracts': params['registryContracts'],
'registryProducts': params['registryProducts'],
'searchInDocumentation': params['searchInDocumentation'],
'tags': tags
}
};
};
_G_.set(c=function(){},'CalendarFavorites');
p=c.prototype;
p.getTemplateContent=function(_,$){
	return{'c':{'h':function(tender){return {'cmp':'DataTableStandartRow','p':{'ap':{'nocontrols':'1'},'p':tender}}},'p':function(){return $.g('tenders')},'n':'tenders'},'t':0,'p':{'c':'simple-datatable calendar-favorites-datatable'}}
};
p.getInitials=function(){
	return {
		'props':{'expandable': true,'width': 1000}
	};
};
_G_.set(c=function(){},'FilterEdit');
p=c.prototype;
p.getInitials=function(){
	return {
		'props':{'title': __[78]}
	};
};
_G_.set(c=function(){},'OrderCall');
p=c.prototype;
p.onSupportButtonClick=function(){
this.hide();
__DI.show(Support);
};
p.onShow=function(){
var form =this.getChildAt(0);
var handler =form.validateTime.bind(form);
this.interval =setInterval(handler, 60000);
handler();
};
p.onHide=function(){
clearInterval(this.interval);
};
p.getTemplateContent=function(_,$){
	return{'cmp':'OrderCallForm'}
};
p.getTemplateButtons=function(_,$){
	return{'c':__[47],'t':0,'e':[0,$.onSupportButtonClick],'p':{'c':'standart-button order-support'}}
};
p.getInitials=function(){
	return {
		'props':{'title': __[46]}
	};
};
_G_.set(c=function(){},'Support');
p=c.prototype;
p.onOrderCallButtonClick=function(){
this.hide();
__DI.show(OrderCall);
};
p.getTemplateContent=function(_,$){
	return{'cmp':'SupportForm'}
};
p.getTemplateButtons=function(_,$){
	return{'c':__[49],'t':0,'e':[0,$.onOrderCallButtonClick],'p':{'c':'standart-button order-support'}}
};
p.getInitials=function(){
	return {
		'props':{'title': __[48]}
	};
};
_G_.set(c=function(){},'App');
p=c.prototype;
p.onNoErrors=function(){
this.appendChild('menu', true);
};
p.onError=function(errorCode){
this.appendChild('menu', false);
};
p.getTemplateMain=function(_,$){
	return[{'cmp':'TopMenu','p':{'i':'menu'}},{'t':0,'p':{'c':'app-view-container'}}]
};
_G_.set(c=function(){},'AuthForm');
p=c.prototype;
p.onSuccess=function(){
Router.reload();
};
p.getTemplateContent=function(_,$){
	return{'c':'LOGO','t':0,'p':{'c':'app-authform-logo'}}
};
p.getInitials=function(){
	return {
		'props':{'action': 'user/login.php','ajax': true,'className': 'app-authform-inputs','controls': [{'caption': __[55],'controlClass': 'Input','controlProps': {'type': 'text','name': 'login','placeholder': __[54]}},{'caption': __[57],'controlClass': 'Input','controlProps': {'type': 'password','name': 'password','placeholder': __[56]}}],'submit': {'value': __[58],'class': 'app-submit'}}
	};
};
_G_.set(c=function(){},'OrderCallForm');
p=c.prototype;
p.onRendered=function(){
this.setControlValue('name', User.getAttribute('name'));
this.setControlValue('phone', User.getAttribute('phone'));
var email =User.getAttribute('email');
if (email) {
this.setControlValue('email', email);
this.enableControl('email', false);
}
};
p.getDateOptions=function(){
var monthNames =Dictionary.get('monthNames'),
date =new Date(),
year =date.getFullYear(),
time =date.getHours,
month =date.getMonth() + 1,
day =date.getDate(),
days =33 - new Date(year, month - 1, 33).getDate(),
dates =[], d, m =month, dayInWeek, count =0, index =0, txt;
var prev =0;
while (count < 10) {
d =day + index;
if (day + index > days) {
d =d - days;
m =month + 1;
if (m > 12) {
break;
}
}
dayInWeek =new Date(year, m - 1, d).getDay();
if (dayInWeek ==0 || dayInWeek > 5) {
index++;
continue;
} 
dayInWeek =Dictionary.get('dayNames')[dayInWeek];
txt =count > 1 || (!!prev &&prev !=d - 1) ? d + ' ' + monthNames[m] + ', ' + dayInWeek : (count ==0 ? __[66] : __[67]) + ', ' + d + ' ' + monthNames[m];
dates.push({'value': txt, 'title': txt});
count++;
index++;
prev =d;
}
date =day + ' ' + monthNames[month];
return dates;
};
p.validateTime=function(){
var dateSelect =this.getControl('date');
var timeSelect =this.getControl('time');
var dateValue =dateSelect.getValue();
var isToday =(new RegExp(__[66])).test(dateValue);
if (isToday) {
var d =new Date();
var hours =[11, 13, 15];
var minutes =[0, 0, 30];
var moscowTime =d.getUTCHours() + 3;
var minute =d.getMinutes();
var disabledIndexes =[];
for (var i =0; i < hours.length; i++) {
if (moscowTime > hours[i] || (moscowTime ==hours[i] && minute >=minutes[i])) {
disabledIndexes.push(i);
}
}
if (disabledIndexes.length ==hours.length) {
dateSelect.enableOption(0, false);
} else {
for (i =0; i < disabledIndexes.length; i++) {
timeSelect.enableOption(disabledIndexes[i], false);
}
}
} else {
timeSelect.enableOption(0, true);
timeSelect.enableOption(1, true);
timeSelect.enableOption(2, true);
}
};
p.getInitials=function(){
	return {
		'props':{'action': CONFIG.orderCall.send,'method': 'POST','className': 'app-order-call','controls': [__V[8],__V[9],__V[10],{'caption': __[62],'class': 'half-width','controlClass': 'Select','controlProps': {'name': 'topic','options': Dictionary.get('orderCallTopics')}},{'caption': __[63],'class': 'half-width','controlClass': 'Select','controlProps': {'name': 'date','options': this.getDateOptions()}},{'caption': __[64],'class': 'half-width','controlClass': 'Select','controlProps': {'name': 'time','options': Dictionary.get('timeOptions')}},{'caption': __[65],'controlClass': 'Textarea','controlProps': {'name': 'comment'}}],'submit': {'value': __[97],'class': __[51] + ' send-button'}}
	};
};
_G_.set(c=function(){},'SupportForm');
p=c.prototype;
p.getInitials=function(){
	return {
		'props':{'action': CONFIG.support.send,'className': 'app-order-call','controls': [__V[8],__V[9],__V[10],{'caption': __[68],'controlClass': 'Textarea','controlProps': {'name': 'comment'}},{'caption': __[69],'controlClass': 'Input','controlProps': {'name': 'screenshot','type': 'file','accept': 'image/*'}}],'submit': {'value': __[70],'class': __[51] + ' send-button'}}
	};
};
_G_.set(c=function(){
	Router.addMenu(this);
	this.isRouteMenu=true;
},'TopMenu');
p=c.prototype;
p.getTemplateMain=function(_,$){
	return{'c':{'c':[{'t':12,'p':{'h':'#main','c':'top-menu_logo'}},{'c':__T[1],'t':12,'p':{'h':'#main','r':'main'}},{'c':__T[2],'t':12,'p':{'h':'#search','r':'search'}},{'c':__T[3],'t':12,'p':{'h':'#favorite','r':'favorite'}},{'c':__T[4],'t':12,'p':{'h':'#planzakupok','r':'planzakupok'}},{'c':__T[5],'t':12,'p':{'h':'#analytics','r':'analytics'}}],'t':0,'p':{'c':'top-menu_inner'}},'t':0,'p':{'c':'top-menu','sc':1}}
};
_G_.set(c=function(){},'AutoComplete');
p=c.prototype;
p.onInput=function(value){
var options=this.get('options');
var len=value.length;
var minLength=Objects.get(options,'minLength',3);
if(isString(Objects.get(options,'url'))&& len>=minLength){
this.delay(this.load,1000,value);
}else if(len==0){
this.delay();
this.set('variants',[]);
}
};
p.load=function(value){
Loader.get(Objects.get(this.get('options'),'url'),{'token':value},this.onLoad,this);
};
p.onLoad=function(data){
this.set('currentVariant',null);
this.set('variants',data['items']);
};
p.onFocus=function(){
if(this.get('variantsCount')>0){
this.set('active',true);
}
};
p.onChangeVariants=function(variants){
var count=isArray(variants)?variants.length:0;
this.set({'variantsCount':count,'active':count>0});
};
p.onBlur=function(){
this.delay(function(){
this.set('active',false);
},200);
};
p.onEnter=function(value){
var currentVariant=this.get('currentVariant');
if (isNumber(currentVariant)) {
var e = this.findElement('.auto-complete_variant.active');
this.dispatchEvent('enter',e.getData('value'));
this.onEscape();
return false;
} else {
this.clear();
}
};
p.setValue=function(value){
 this.findElement('input').value =value;
};
p.onEscape=function(){
this.clear();
};
p.clear=function(){
this.delay();
 this.getElement('input').clear();
this.set('variants',[]);
};
p.onVariantPick=function(target){
this.dispatchEvent('pick',target.getData('value'));
this.clear();
};
p.onUp=function(){
this.highlightVariant(-1);
};
p.onDown=function(){
this.highlightVariant(1);
};
p.highlightVariant=function(step){
var variants=this.get('variants');
var currentVariant=this.get('currentVariant');
if(isArray(variants)&& variants.length>0){
var total=variants.length;
if(!isNumber(currentVariant)){
currentVariant=-1;
}
currentVariant+=step;
if(currentVariant<0){
currentVariant=total-1;
}else if(currentVariant==total){
currentVariant=0;
}
this.set('currentVariant',currentVariant);
}
};
p.onChangeCurrentVariant=function(index){
var e = this.findElement('.auto-complete_variant.active');
if(e)e.removeClass('active');
e = this.findElements('.auto-complete_variant')[index];
if(e)e.addClass('active');
};
p.onChangeActive=function(isActive){
if(!isActive)this.set('currentVariant',null);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'t':14,'p':{'as':'input','tp':'text','p':_['placeholder']}},{'tmp':$.getTemplateContent},{'c':{'c':{'h':function(variant,i){return {'tmp':$.getTemplateVariant,'p':{'props':variant,'index':i}}},'p':function(){return $.g('variants')},'n':'variants'},'t':0,'p':{'c':'auto-complete_variants-inner'}},'t':0,'p':function(){return{'as':'variants','c':'auto-complete_variants'+($.g('active')?' shown':'')}},'n':{'c':'active'}}],'t':0,'p':{'c':'auto-complete input-container','sc':1}}
};
p.getTemplateVariant=function(_,$){
	return{'c':_['name'],'t':0,'p':{'c':'auto-complete_variant','_index':_['index'],'_value':_['name']}}
};
p.getInitials=function(){
	return {
		'helpers':[{'helper':'InputHandler','options': {'callbacks': {'enter': this.onEnter,'esc': this.onEscape,'focus': this.onFocus,'blur': this.onBlur,'input': this.onInput,'up': this.onUp,'down': this.onDown},'inputSelector': 'input'}}],
		'events':{'click': {'auto-complete_variant': this.onVariantPick}},
		'followers':{'variants': this.onChangeVariants,'currentVariant': this.onChangeCurrentVariant,'active': this.onChangeActive}
	};
};
_G_.set(c=function(){},'KeywordsAutoComplete');
p=c.prototype;
p.onAddButtonClick=function(){
var value = this.findElement('input').value;
this.onEnter(value);
this.dispatchEvent('enter',value);
};
p.getTemplateContent=function(_,$){
	return{'c':__[25],'t':0,'e':[0,$.onAddButtonClick],'p':{'c':'standart-button green-button add'}}
};
_G_.set(c=function(){},'Calendar');
p=c.prototype;
p.onRendered=function(){
this.reset();
};
p.redraw=function(){
var day=this.isCurrentMonth()?Dates.getDay():0,
month=this.month,
year=this.year,
curDays=Dates.getDays(month,year),
prevMonth=month-1>=0?month-1:11,
prevYear=prevMonth<12?year:year-1,
prevDays=Dates.getDays(prevMonth,prevYear),
firstDay=Dates.getWeekDay(1,month,year),
firstCell=firstDay>0?firstDay-1:6,
count=1,
lastCell=0,
days=[];
for(var i=0;i<firstCell;i++){
days.push({num:prevDays-i,another:true});
}
days=days.reverse();
for(var i=firstCell;i<curDays+firstCell;i++){
days.push({num:count,current:count==day,marked:this.isMarked(count,month,year)});
lastCell=i;
count++;
}
var len=days.length;
var more=len<=35?35-len:42-len; 
for(var i=1;i<=more;i++){
days.push({num:i,another:true});
}
this.set({'year':year,'month':Dates.getMonthName(month),'days':days});
};
p.isCurrentMonth=function(){
return this.month ==Dates.getMonth() && this.year ==Dates.getYear();
};
p.reset=function(){
this.month =Dates.getMonth();
this.year =Dates.getYear();
this.redraw();
};
p.isMarked=function(){
return false;
};
p.onPrevClick=function(){
this.changeMonth(-1);
};
p.onNextClick=function(){
this.changeMonth(1); 
};
p.changeMonth=function(value){
this.month +=value;
if (this.month ==12) {
this.month =0;
this.year++;
} else if (this.month ==-1) {
this.month =11;
this.year--;
}
this.redraw();
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':[{'t':0,'e':[0,$.onPrevClick],'p':{'c':'calendar_prev'}},{'c':[{'v':$.g('month'),'n':'month'},{'c':{'v':$.g('year'),'n':'year'},'t':1,'p':{'c':'calendar_year'}}],'t':0,'p':{'c':'calendar_month'}},{'t':0,'e':[0,$.onNextClick],'p':{'c':'calendar_next'}}],'t':0,'p':{'c':'calendar_header'}},{'c':[{'c':[{'c':__[71],'t':1},{'c':__[72],'t':1},{'c':__[73],'t':1},{'c':__[74],'t':1},{'c':__[75],'t':1},{'c':__[76],'t':1},{'c':__[77],'t':1}],'t':0,'p':{'c':'calendar_day-names'}},{'c':{'h':function(day){return {'c':day.num,'t':1,'p':{'c':(day.another?'another':'')+' '+(day.current?'current':'')+' '+(day.marked?'marked':'')}}},'p':function(){return $.g('days')},'n':'days'},'t':0,'p':{'c':'calendar_days'}}],'t':0,'p':{'c':'calendar_content'}}],'t':0,'p':{'c':'calendar'}}
};
_G_.set(c=function(){},'FavoritesCalendar');
p=c.prototype;
p.onRendered=function(){
this.month =Dates.getMonth();
this.year =Dates.getYear();
};
p.isMarked=function(d,m,y){
return Objects.has(this.tenderByDates, d + '.' + (m + 1) + '.' + y);
};
p.onLoadFavorites=function(data){
var timestamp;
this.tenderByDates ={};
for (var i =0; i < data.length; i++) {
if (data[i]['phase_'] ==1) {
timestamp =data[i]['finishdocdate'].replace(/\.(\d+)$/, ".20$1").replace(/0(?=\d\.)/g, '');
this.tenderByDates[timestamp] =this.tenderByDates[timestamp] || [];
this.tenderByDates[timestamp].push(data[i]);
}
}
this.redraw();
};
p.onMarkedDayClick=function(target){
var timestamp =target.innerHTML + '.' + (this.month + 1) + '.' + this.year;
if (isArray(this.tenderByDates[timestamp])) {
Dialoger.show(CalendarFavorites, {
'title': __[44] + ' ' + Dates.getFormattedDate(timestamp, __[45]),
'tenders': this.tenderByDates[timestamp]
});
}
};
p.getInitials=function(){
	return {
		'controllers':[{'controller': __C.get(0),'on': {'load': this.onLoadFavorites}}],
		'events':{'click': {'marked': this.onMarkedDayClick}}
	};
};
_G_.set(c=function(){},'Dialog');
p=c.prototype;
p.show=function(){
this.set('shown',true);
this.reposition(); 
this.onShow();
};
p.reposition=function(){
var rect=this.getElement().getRect(); 
this.set({'marginTop':Math.round(rect.height/-2)+'px','marginLeft':Math.round(rect.width/-2)+'px'});
};
p.hide=function(){
this.set('shown',false);
};
p.close=function(){
this.hide();
this.onHide();
};
p.expand=function(isExpanded){
if(isBool(isExpanded)){
this.set('expanded',isExpanded);
}else{
this.toggle('expanded');
}
};
p.onShow=__FNC;
p.onHide=__FNC;
p.getTemplateMain=function(_,$){
	return[{'t':0,'e':[0,$.close],'p':function(){return{'c':'dialog_mask '+($.g('shown')?'shown':'')}},'n':{'c':'shown'}},{'c':[{'c':function(){return [{'t':0,'e':[0,$.close],'p':{'c':'dialog_close'}}]},'i':function(){return $.g('closable')},'n':['closable']},{'c':function(){return [{'t':0,'e':[0,$.expand],'p':{'c':'dialog_expand'}}]},'i':function(){return $.g('expandable')},'n':['expandable']},{'c':{'v':$.g('title'),'n':'title'},'t':0,'p':{'c':'dialog_title'}},{'c':{'tmp':$.getTemplateContent},'t':0,'p':function(){return{'c':'dialog_content','st':$.g('height')?'max-height:'+$.g('height')+'px;':''}},'n':{'st':'height'}},{'c':{'tmp':$.getTemplateButtons},'t':0,'p':{'c':'dialog_buttons'}}],'t':0,'p':function(){return{'c':'dialog '+($.g('expanded')?'expanded':'')+' '+($.g('shown')?'shown':''),'st':'width:'+$.g('width')+'px;margin-left:'+$.g('marginLeft')+';margin-top:'+$.g('marginTop')+';','sc':1}},'n':{'c':['expanded','shown'],'st':['marginLeft','marginTop','width']}}]
};
p.getInitials=function(){
	return {
		'props':{'closable': true,'width': 600},
		'followers':{'width': this.reposition,'height': this.reposition}
	};
};
_G_.set(c=function(){},'Editor');
p=c.prototype;
p.edit=function(element){
this.editedElement=element;
this.set({'text':element.innerHTML,'shown':true});
this.reposition();
};
p.reposition=function(text){
this.placeTo(document.body);
var rect =this.editedElement.getRect();
this.setPosition(rect.left, rect.top);
};
p.onChangeText=function(text){
var input = this.findElement('input');
input.value =text;
input.focus();
};
p.onEnter=function(value){
this.editedElement.innerHTML =value;
this.hide();
};
p.hide=function(){
this.close();
this.placeBack();
this.dispatchEvent('hide');
};
p.close=function(){
this.set('shown',false);
};
p.getTemplateMain=function(_,$){
	return[{'t':0,'e':[0,$.hide],'p':function(){return{'c':'editor_mask '+($.g('shown')?'shown':'')}},'n':{'c':'shown'}},{'c':[{'t':0,'e':[0,$.close],'p':{'c':'editor_close'}},{'c':__[79],'t':0,'p':{'c':'editor_title'}},{'cmp':'AutoComplete','e':['enter',$.onEnter],'p':function(){return{'p':{'options':__V[4],'active':$.g('withAutoComplete')}}},'n':{'active':'withAutoComplete'}}],'t':0,'p':function(){return{'c':'editor '+($.g('shown')?'shown':''),'sc':1}},'n':{'c':'shown'}}]
};
p.getInitials=function(){
	return {
		'followers':{'text': this.onChangeText}
	};
};
_G_.set(c=function(){},'KeywordTagEditor');
p=c.prototype;
_G_.set(c=function(){},'Form');
p=c.prototype;
p.onSubmit=function(){
if (this.isValid()) {
var form =this.getElement();
var ajax =form.getData('ajax');
if (ajax) {
this.sendAjaxRequest();
} else {
this.setFormKey();
form.submit();
}
}
};
p.sendAjaxRequest=function(){
var form =this.getElement();
var action =form.attr('action');
var method =form.attr('method');
if (action) {
Loader.doAction(method, action, this.getControlsData(), this.handleResponse, this);
}
};
p.setFormKey=function(){
this.formKey =generateRandomKey();
window[this.formKey] =this;
if (!isElement(this.keyInput)) {
this.keyInput =document.createElement('input');
this.keyInput.setAttribute('name', 'formKey');
this.keyInput.setAttribute('type', 'hidden');
this.keyInput.value =this.formKey;
this.getElement().appendChild(this.keyInput);
}
};
p.isValid=function(){
return true;
};
p.handleResponse=function(data){
if (isString(data)) {
try {
data =JSON.parse(data);
} catch (e) {
log('incorrect form response', 'handleResponse', this, {'data': data});
}
}
if (isObject(data) && data['success']) {
this.onSuccess(data);
} else {
this.onFailure(data);
}
if (isString(this.formKey)) {
this.formKey =null;
delete window[this.formKey];
}
};
p.onSuccess=function(data){};
p.onFailure=function(data){};
p.getTemplateMain=function(_,$){
	return{'c':[{'tmp':$.getTemplateContent},{'h':function(control){return {'cmp':'FormField','p':{'p':control}}},'p':_['controls']},_['submit']?[{'cmp':'Submit','e':[23,$.onSubmit],'p':{'p':_['submit']}}]:''],'t':13,'p':{'c':'app-form-controls'+(_['className']?' '+_['className']:''),'m':_['method'],'a':_['action'],'_ajax':_['ajax']?1:'','_iframe':_['iframe']?1:'','sc':1}}
};
_G_.set(c=function(){},'PopupMenu');
p=c.prototype;
p.onRendered=function(){
this.button = this.getElement().parentNode;
this.addListener(this.button, 'click', this.onShowButtonClick);
};
p.onClick=function(e){
var target =e.getTarget('.popup-menu_button');
if (!isNull(target)) {
var buttons=this.get('buttons');
var idx =target.getData('index');
var value =target.getData('value');
if (isArray(buttons) && isObject(buttons[idx]) && isFunction(buttons[idx]['handler'])) {
buttons[idx]['handler'].call(this, e);
return;
}
this.handleClick(value, target);
}
};
p.onShowButtonClick=function(){
this.onBeforeShow();
this.show();
};
p.show=function(){
var innerElement = this.findElement('.popup-menu_inner-container');
var rect =innerElement.getRect();
var height =Math.min(rect.height, Objects.get(this.options, 'maxHeight', 400));
this.setStyle({maxHeight: height + 'px', height: height + 'px'});
this.button.addClass('active');
Popuper.watch(this);
};
p.hide=function(){
this.setStyle({maxHeight: '0', height: '0'});
this.button.removeClass('active');
};
p.renderButtons=function(items){
var buttons=[];
var a=items,idx;for(idx=0;idx<a.length;idx++){var item=a[idx]; 
buttons.push(this.getButtonData(item));
}
this.set('buttons',buttons);
};
p.getButtonData=function(item){
return {
'value': item['value'],
'name': item['name']
};
};
p.handleClick=__FNC;
p.onBeforeShow=__FNC;
p.getTemplateMain=function(_,$){
	return{'c':{'c':{'h':function(button,idx){return {'c':[button['name'],{'tmp':$.getTemplateContent,'p':{'item':button}}],'t':0,'p':{'c':'popup-menu_button','_value':button['value'],'_index':idx}}},'p':function(){return $.g('buttons')},'n':'buttons'},'t':0,'e':[0,$.onClick],'p':{'c':'popup-menu_inner-container','st':_['maxHeight']?'max-height:'+_['maxHeight']+'px;':''}},'t':0,'p':{'c':'popup-menu_outer-container '+_['className'],'sc':1}}
};
_G_.set(c=function(){},'PopupSelect');
p=c.prototype;
p.show=function(){
this.set('shown',true);
Popuper.watch(this);
};
p.hide=function(){
this.set('shown',false);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':{'v':$.g('title'),'n':'title'},'t':0,'p':{'c':'app-popup-select-title'}},{'cmp':'Select','nm':'option','e':[14,$.d.b($,'change')],'p':{'p':{'options':_['options']}}}],'t':0,'p':function(){return{'c':'app-popup-select'+($.g('shown')?' shown':''),'sc':1}},'n':{'c':'shown'}}
};
_G_.set(c=function(){},'Recommendations');
p=c.prototype;
p.load=function(words){
__C.get(3).doAction(this,'load',{'excepcions': words});
};
p.onChangeItems=function(items){
var itemsCount=items.length;
this.set('itemsCount',itemsCount);
this.dispatchEvent('change',itemsCount);
};
p.getTemplateMain=function(_,$){
	return{'c':{'h':function(item){return {'c':item['keyword'],'t':0,'p':{'c':'recommendations_item'}}},'p':function(){return $.g('items')},'n':'items'},'t':0,'p':{'c':'recommendations','sc':1}}
};
p.getInitials=function(){
	return {
		'controllers':[{'controller': __C.get(3)}],
		'followers':{'items': this.onChangeItems}
	};
};
_G_.set(c=function(){},'TabPanel');
p=c.prototype;
p.onRendered=function(){
this.tabWidth=this.get('tabWidth')||200;
this.tabMargin=this.get('tabMargin')||4;
};
p.onParentRendered=function(){
var tabs=this.get('tabs');
if (isArray(tabs)) {
var a=tabs,idx;for(idx=0;idx<a.length;idx++){var tab=a[idx];
this.activateTab(idx, !!tab['active']);
}
}
this.redraw();
};
p.redraw=function(){
this.hiddenTabs=[];
var tabPanelWidth=this.getElement().getWidth();
var controlWidth=this.getControlsWidth();
var tabs=this.findElements('.tab-panel_tab');
var totalWidth=0;
var a=tabs,idx;for(idx=0;idx<a.length;idx++){var tab=a[idx];
tab.toggleClass('first',idx==0);
if(totalWidth+controlWidth+this.tabWidth+this.tabMargin>tabPanelWidth){
tab.hide();
this.hiddenTabs.push(idx);
}else{
tab.style.left=totalWidth+'px';
totalWidth+=this.tabWidth+this.tabMargin;
}
}
this.set('count',this.hiddenTabs.length);
};
p.getControlsWidth=function(){
var width =0;
var restButton = this.findElement('.tab-rest');
if(restButton)width +=restButton.getWidth() + this.tabMargin;
var plusButton = this.findElement('.tab-plus');
if(plusButton)width +=plusButton.getWidth() + this.tabMargin;
return width;
};
p.onRestTabClick=__FNC;
p.onTabClick=function(target){
if (isNumeric(this.activeTab)) {
this.activateTab(this.activeTab, false);
} 
this.activateTab(target.getData('index'), true);
};
p.activateTab=function(tabIndex,isShown){
var contents = this.getElement().next().finds('.tab-content');
if(contents[tabIndex])contents[tabIndex].show(isShown);
if (isShown) {
this.dispatchEvent('select',tabIndex);
this.activeTab =tabIndex; 
}
 this.findElements('.content-tab')[tabIndex].toggleClass('active', isShown);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'h':function(tab,idx){return {'c':tab['title'],'t':0,'p':{'c':'tab-panel_tab content-tab','_index':idx}}},'p':_['tabs']},isObject(_['rest'])?[{'c':[_['rest']['title']||__[80],_['rest']['showCount']?[' (',{'v':$.g('count'),'n':'count'},')']:''],'t':0,'p':function(){return{'c':'tab-panel_tab tab-rest'+($.g('count')?' shown':'')}},'n':{'c':'count'}}]:''],'t':0,'p':{'c':'tab-panel','sc':1}}
};
p.getInitials=function(){
	return {
		'events':{'click': {'tab-rest': this.onRestTabClick,'content-tab': this.onTabClick}}
	};
};
_G_.set(c=function(){},'Tabs');
p=c.prototype;
p.onSelect=function(target){
var index=target.getData('index');
this.set('activeTab',index);
this.dispatchEvent('select',index);
};
p.onRemove=function(target){
var index =target.getParent().getData('index');
this.dispatchEvent('remove',index);
};
p.getTemplateMain=function(_,$){
	return{'c':{'h':function(item,i){return {'c':[item,{'t':0,'p':{'c':'tabs_remove'}}],'t':0,'p':function(){return{'c':'tabs_item'+($.g('activeTab')==i?' active':''),'_index':i}},'n':{'c':'activeTab'}}},'p':function(){return $.g('items')},'n':'items'},'t':0,'p':{'c':'tabs','sc':1}}
};
p.getInitials=function(){
	return {
		'events':{'click': {'tabs_item': this.onSelect,'tabs_remove': this.onRemove}}
	};
};
_G_.set(c=function(){},'TooltipPopup');
p=c.prototype;
p.correctAndSetText=function(text,changedProps){
var corrector =changedProps['corrector'];
if (corrector =='list') {
var textParts =text.split('|');
if (textParts[1]) {
var temp =[];
for (var i =0; i < textParts.length; i++) {
textPart =textParts[i].split('^');
textPart =textPart[1] || textPart[0];
if (textPart.charAt(0) ==__[3]) textPart =__[2];
temp.push(textPart);
}
text =temp.removeDuplicates();
}
}
return text;
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':{'v':$.g('caption'),'n':'caption'},'t':0,'p':{'c':'tooltip-popup_caption'}},{'c':{'n':'corrector','sw':function(){return[$.g('corrector'),'list',{'h':function(item){return {'c':item,'t':0,'p':{'c':'tooltip-popup_item'}}},'p':function(){return $.g('text')},'n':'text'},{'v':$.g('text'),'n':'text'}]}},'t':0,'p':{'c':'tooltip-popup_text'}}],'t':0,'p':function(){return{'c':'@'+($.g('className')?' '+$.g('className'):'')+($.g('shown')?' shown':''),'st':'left:'+$.g('left')+'px;top:'+$.g('top')+'px;','sc':1}},'n':{'c':['className','shown'],'st':['left','top']}}
};
p.getInitials=function(){
	return {
		'correctors':{'text': this.correctAndSetText},
		'props':{control: 'Input'}
	};
};
_G_.set(c=function(){},'Error401');
p=c.prototype;
p.onRendered=__FNC;
p.getTemplateMain=function(_,$){
	return{'c':{'cmp':'AuthForm'},'t':0,'p':{'c':'app-auth-form-container'}}
};
_G_.set(c=function(){},'Error404');
p=c.prototype;
p.onRendered=__FNC;
p.getTemplateMain=function(_,$){
	return{'c':[{'c':'404','t':0,'p':{'c':'app-404-title'}},{'c':__T[6],'t':0,'p':{'c':'app-404-text'}}],'t':0,'p':{'c':'app-404-container'}}
};
_G_.set(c=function(){},'Favorite');
p=c.prototype;
p.onRendered=__FNC;
p.getTemplateMain=function(_,$){
	return{'t':0,'p':{'c':'view-content'}}
};
_G_.set(c=function(){},'FilterSubscription');
p=c.prototype;
p.onLoaded=function(filters){
this.set('filters',filters);
this.set({'total':this.getTotalCount(),'subscribed':this.getSubscribedCount()}); 
};
p.getTotalCount=function(){
return Decliner.getCount('filter',this.get('filters'));
};
p.getSubscribedCount=function(){
var subscribedCount=0;
this.each('filters',function(filter){
if(filter['isSubs']==1)subscribedCount++;
});
return Decliner.getCount('subscr',subscribedCount);
};
p.onFreqChange=function(e){};
p.onSubscribeButtonClick=function(target,e){
var filterId =e.getTargetData('.filter-subscription_filter-row', 'filterId');
if (filterId) {
__C.get(1).doAction(this,'subscribe',{
'filterId': filterId,
'value': target.hasClass('subscribed') ? '0' : '1'
});
}
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':[__[114],' ',{'c':__[115],'t':20}],'t':0,'p':{'c':'filter-subscription_title'}},{'c':[{'c':{'v':$.g('total'),'n':'total'},'t':0,'p':{'c':'filter-subscription_head-total'}},{'c':{'v':$.g('subscribed'),'n':'subscribed'},'t':0,'p':{'c':'filter-subscription_head-subscribed'}}],'t':0,'p':{'c':'filter-subscription_head'}},{'c':[{'c':{'c':[{'c':__[119],'t':7},{'c':__[120],'t':7},{'c':__[121],'t':7},{'c':__T[7],'t':7}],'t':5},'t':4},{'c':{'h':function(filter){return {'c':[{'c':{'c':filter.header,'t':1,'p':{'c':'filter-subscription_filter'}},'t':6},{'c':{'cmp':'Select','nm':'freqSubs','e':[14,$.onFreqChange],'p':{'p':{'options':__V[11],'value':filter.freqSubs}}},'t':6},{'c':{'t':0,'p':{'c':'filter-subscription_button '+(filter.isSubs ==1?__[51]+' subscribed':__[53])}},'t':6},{'c':__T[8],'t':6}],'t':5,'p':{'_filterid':filter.filterId,'c':'filter-subscription_filter-row'}}},'p':function(){return $.g('filters')},'n':'filters'},'t':3}],'t':2,'p':{'c':'filter-subscription_table','cp':'0px','cs':'0px'}}],'t':0,'p':{'c':'filter-subscription','sc':1}}
};
p.getInitials=function(){
	return {
		'loader':{'controller': __C.get(1)},
		'events':{'click': {'filter-subscription_button': this.onSubscribeButtonClick}}
	};
};
_G_.set(c=function(){},'FilterSubscriptionOptions');
p=c.prototype;
p.onCheckboxChange=function(e){
var params ={};
params[e['name']] =e['intChecked'];
__C.get(4).doAction(this,'save',params);
};
p.getTemplateMain=function(_,$){
	return{'c':[{'c':[{'tmp':1,'p':{'name':'tenderOfFavorite','checked':function(){return $.g('opts').tenderOfFavorite}}},' ',__[110],' ',{'c':__[111],'t':20}],'t':0,'p':{'c':'filter-subscription-options_option'}},{'c':[{'tmp':1,'p':{'name':'protocolOfFavorite','checked':function(){return $.g('opts').protocolOfFavorite}}},' ',__[110],' ',{'c':__[112],'t':20}],'t':0,'p':{'c':'filter-subscription-options_option'}},{'c':[{'tmp':1,'p':{'name':'protocolOfFilter','checked':function(){return $.g('opts').protocolOfFilter}}},' ',__[110],' ',{'c':__[113],'t':20}],'t':0,'p':{'c':'filter-subscription-options_option'}}],'t':0,'p':{'c':'filter-subscription-options','sc':1}}
};
p.getInitials=function(){
	return {
		'loader':{'controller': __C.get(4)},
		'helpers':[{'helper':'CheckboxHandler','options': {'callback': this.onCheckboxChange,'labelClass': 'filter-subscription-options_option'}}]
	};
};
_G_.set(c=function(){},'Main');
p=c.prototype;
p.onRendered=function(){
this.onResize();
};
p.onResize=function(){
var element = this.findElement('.mainpage-content');
element.setHeight('');
var height =element.getHeight();
var bodyHeight =document.body.getHeight();
if (bodyHeight - 100 - height > 0) {
element.setHeight(bodyHeight - 100);
}
};
p.getTemplateMain=function(_,$){
	return{'c':{'c':{'c':[{'c':{'c':[{'c':__[81],'t':0,'p':{'c':'mainpage-left-column-title'}},{'cmp':'UserInfo'},{'c':[__[98],{'tmp':2,'p':{'props':__V[14]}}],'t':0,'p':{'c':'mainpage-leftcolumn-title bold'}},{'cmp':'FavoritesCalendar'}],'t':0,'p':{'c':'mainpage-left-column-area'}},'t':6,'p':{'c':'mainpage-left-column'}},{'c':[{'cmp':'TabPanel','p':{'p':__V[12]}},{'c':[{'c':{'cmp':'FilterStatistics','p':{'p':__V[13]}},'t':0,'p':{'c':'tab-content'}},{'c':[{'cmp':'FilterSubscriptionOptions'},{'cmp':'FilterSubscription'}],'t':0,'p':{'c':'tab-content'}},{'c':__T[9],'t':0,'p':{'c':'tab-content'}}],'t':0,'p':{'c':'mainpage-content'}}],'t':6,'p':{'c':'mainpage-content-column'}}],'t':5},'t':2,'p':{'c':'mainpage-table','cp':'0px','cs':'0px'}},'t':0,'p':{'c':'view-content main-view-content','sc':1}}
};
p.getInitials=function(){
	return {
		'helpers':[{'helper':'ResizeHandler','options': {'callback': this.onResize}}]
	};
};
_G_.set(c=function(){},'UserInfo');
p=c.prototype;
p.onLoaded=function(data){
if (!User.hasFullAccess()) {
data['prolongButtonText'] =__[92];
} else if (data['needToProlong']) {
data['prolongButtonText'] =__[93];
}
this.set(data);
};
p.onOrderCallButtonClick=function(){
__DI.show(OrderCall);
};
p.getTemplateMain=function(_,$){
	return[{'c':[{'c':[{'c':[__[82],':'],'t':6},{'c':{'v':$.g('userName'),'n':'userName'},'t':6}],'t':5},{'c':[{'c':[__[83],':'],'t':6},{'c':{'v':$.g('companyName'),'n':'companyName'},'t':6}],'t':5},{'c':[{'c':[__[84],':'],'t':6},{'c':{'v':$.g('userEmail'),'n':'userEmail'},'t':6}],'t':5},{'c':[{'c':[__[85],':'],'t':6},{'c':{'v':$.g('typeAccess'),'n':'typeAccess'},'t':6,'p':{'c':'bold'}}],'t':5},{'c':[{'c':[__[86],':'],'t':6},{'c':{'v':$.g('beginAccessDate'),'n':'beginAccessDate'},'t':6}],'t':5},{'c':[{'c':[__[87],':'],'t':6},{'c':{'v':$.g('endAccessDate'),'n':'endAccessDate'},'t':6,'p':function(){return{'c':$.g('needToProlong')?'red':''}},'n':{'c':'needToProlong'}}],'t':5}],'t':2,'p':{'c':'mainpage-user-info','cp':'0px','cs':'0px','sc':1}},{'c':function(){return [{'c':{'v':$.g('prolongButtonText'),'n':'prolongButtonText'},'t':12,'p':{'h':__[91],'tr':'_blank','c':'access standart-button red-button'}}]},'i':function(){return $.g('prolongButtonText')},'n':['prolongButtonText']},{'c':__[89],'t':12,'p':{'h':__[88],'tr':'_blank','c':'tariffs standart-button white-button'}},{'c':__[90],'t':0,'p':{'c':'mainpage-leftcolumn-title bold'}},{'c':[{'c':{'v':$.g('managerName'),'n':'managerName'},'t':0,'p':{'c':'mainpage-manager-name'}},{'c':[{'c':[' ',{'c':__[94],'t':0,'p':{'c':'mainpage-manager-large-phone'}},{'c':__[95],'t':0,'p':{'c':'mainpage-manager-free'}}],'t':0,'p':{'c':'mainpage-free-call'}},{'v':$.g('managerPhone'),'n':'managerPhone'},' \u00A0 ',{'c':__[96],'t':48}],'t':0,'p':{'c':'mainpage-manager-phone'}},{'c':{'v':$.g('managerEmail'),'n':'managerEmail'},'t':0,'p':{'c':'mainpage-manager-email'}}],'t':0,'p':{'c':'mainpage-manager-info'}},{'c':__[97],'t':0,'e':[0,$.onOrderCallButtonClick],'p':{'c':'standart-button green-button'}}]
};
p.getInitials=function(){
	return {
		'loader':{'controller': __C.get(5),'async': true }
	};
};
_G_.set(c=function(){},'Search');
p=c.prototype;
p.onRendered=function(){
this.openInformer();
};
p.openInformer=function(){
var datatable = this.getChild('datatable');
};
p.openFilter=function(filterId){};
p.onFormExpand=function(){
this.toggle('expanded');
};
p.getTemplateMain=function(_,$){
	return{'c':[{'cmp':'TenderSearchForm','e':['expand',$.onFormExpand],'p':{'i':'form'}},{'cmp':'TenderDataTable','p':{'i':'datatable'}}],'t':0,'p':function(){return{'c':'view-content'+($.g('expanded')?' form-expanded':''),'sc':1}},'n':{'c':'expanded'}}
};
p.getInitials=function(){
	return {
		'props':{'expanded': true}
	};
};
_G_.set(c=function(){},'FormField');
p=c.prototype;
p.getTemplateMain=function(_,$){
	return{'c':[_['caption']?[{'c':_['caption'],'t':0,'p':{'c':'input-caption'}}]:'',{'cmp':_['controlClass'],'nm':_['controlProps']['name'],'p':{'p':_['controlProps']}}],'t':0,'p':{'c':'input-container'+(_['class']?' '+_['class']:''),'sc':1}}
};
_G_.set(c=function(){},'Submit');
p=c.prototype;
p.getTemplateMain=function(_,$){
	return{'c':{'c':{'v':$.g('value'),'n':'value'},'t':0,'e':[0,$.d.b($,'submit')],'p':function(){return{'c':$.g('class')}},'n':{'c':'class'}},'t':0,'p':{'c':'app-submit-container'}}
};
_G_.set(function(_){
	return{'t':0,'p':{'c':'app-unavailable-info '+(_['tariff']?'unavailable':'auth'),'st':_['width']?'width:'+_['width']:''}}
},'i_0');
_G_.set(function(_){
	return{'t':0,'p':{'c':'checkbox '+(_['checked']?'checked':''),'_name':_['name'],'_value':_['value']}}
},'i_1');
_G_.set(function(_){
	return{'t':0,'p':{'c':'tooltiped tooltip'+(_['className']?' '+_['className']:''),'_text':_['text'],'_key':_['key'],'_class':_['class'],'_caption':_['caption']}}
},'i_2');
function getFzName(type) {
	var types = Dictionary.get('fztypes');
	if (type > 4400) return types['44'];
	if (type < 128) return types['94'];
	if (type == 256) return types['223'];
	if (type == 128) return types['com'];
	return '';
}
var core=_G_.get('Core');
core.inherits(['Component',['Application','View','Control','Menu','DataTable','DataTableFragmets','DataTableRow','FilterStatistics','SearchForm','SearchFormButton','SearchFormPanel','SearchFormFilters','AutoComplete','Calendar','Dialog','Editor','Form','PopupMenu','PopupSelect','Recommendations','TabPanel','Tabs','TooltipPopup','FilterSubscription','FilterSubscriptionOptions','UserInfo','FormField','Submit'],'Foreach',['Switch','IfSwitch'],'DataTableRow',['DataTableStandartRow'],'DataTable',['TenderDataTable'],'SearchFormButton',['SearchFormPanelButton'],'Control',['Keywords','KeywordsControl','Checkbox','Input','Select','Tags','Textarea'],'SearchFormPanelButton',['KeywordsButton'],'SearchFormPanel',['KeywordsPanel'],'SearchForm',['TenderSearchForm'],'Controller',['Favorites','Filters','FiltersStat','RecommendationsLoader','Subscription','UserInfoLoader'],'Application',['App'],'Menu',['TopMenu'],'AutoComplete',['KeywordsAutoComplete'],'Calendar',['FavoritesCalendar'],'Editor',['KeywordTagEditor'],'View',['Error401','Error404','Favorite','Main','Search'],'TabPanel',['DataTableTabPanel'],'PopupMenu',['SearchFormCreateFilterMenu','SearchFormFilterMenu'],'Tags',['KeywordTags'],'Dialog',['CalendarFavorites','FilterEdit','OrderCall','Support'],'Form',['AuthForm','OrderCallForm'],'OrderCallForm',['SupportForm'],'KeywordTags',['ContainKeywordTags','ExcludeKeywordTags']]);
if (isObject(__DATA['user'])) {
	User.setData(__DATA['user']);
}
if (isObject(__DATA['dictionary'])) {
	Dictionary.setData(route['name'], __DATA['dictionary']);
}
var App=_G_.get('App',1);
core.initiate.call(App);
App.run();
});
})();
});