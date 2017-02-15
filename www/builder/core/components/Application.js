_c = function() {
	if (!this || this == window) {
		var controllers, router, dictionary;
		var routes = {{ROUTES}};
		var errorRoutes = {{ERRORROUTES}};
		var viewContainerClass = {{VIEWCONTAINER}};
		var defaultPagetitle = {{PAGETITLE}};
		var parentalContainerClass = {{PARENTALVIEWCNT}}
		
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
			script.src = '/js/' + route['name'] + '_chunk.js';
			document.body.appendChild(script);
			script.onload = onViewLoaded.bind(this, route);
		};
		var onViewLoaded = function(route) {
			route['view'] = {{GLOBAL}}.get(route['view']);
			renderView.call(this, route);
		};
		var renderView = function(route) {
			var isSameView = this.currentView == route['name'];
			if (!isSameView && this.currentView && this.views[this.currentView]) {
				activateView.call(this, this.currentView, false);
			}
			this.currentView = route['name'];
			loadControllers(route);
			if (!isUndefined(dictionary)) {
				dictionary.load(route['name']);
			}
			var view = this.views[route['name']] = new route['view']();
			var viewParams = getViewParams.call(this, route, true);
			{{GLOBAL}}.get('Core').initiate.call(view, viewParams);
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
			var view = this.views[route['name']];
			if (!view) {
				view = {{GLOBAL}}.get(route['view']);
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
		var initRouter = function() {
			router.setNavigationHandler(handleNavigation.bind(this));
			router.init();
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
			var parentElement = {{GLOBAL}}.get('Core').getParentElement.call(view);
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
		_p.initiate = function() {
			this.views = {};
		};
		_p.run = function() {
			{{GLOBAL}}.create('Loader');
			dictionary = {{GLOBAL}}.get('Dictionary');
			router = {{GLOBAL}}.get('Router');
			controllers = {{GLOBAL}}.get('Controllers');
			this.element = document.createElement('div');
			document.body.appendChild(this.element);
			this.render(this.element);
			createViewContainer.call(this);
			defineViews.call(this);
			initRouter.call(this);
		};
		_p.setPageTitle = function(title) {
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
		_p.getView = function(viewName) {
			return this.views[viewName];
		};
		_p.disposeView = function(viewName) {
			if (isObject(this.views[viewName])) {
				this.views[viewName].dispose();
				this.views[viewName] = null;
			}
		};
		_p.onNoErrors=function(){};
		_p.onError=function(){};
	}
}
_p=_c.prototype;_c();{{GLOBAL}}.set(_c, 'Application');