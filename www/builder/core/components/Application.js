function Application() {
	if (this !== window) return;
	var routes = {{ROUTES}};
	var errorRoutes = {{ERRORROUTES}};
	var viewContainerClass = {{VIEWCONTAINER}};
	var defaultPagetitle = {{PAGETITLE}};
	
	var getViewParams = function(route, allParams) {
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
	var handleNavigation = function(route, changeTitle) {
		this.isChangeTitle = changeTitle;
		this.currentRoute = route;
		var view = this.views[route['name']];
		var isSameView = this.currentView == view;
		if (!isSameView && this.currentView) {
			activateView.call(this, this.currentView, false);
		}
		this.currentView = view;
		if (!isUndefined(view) && isFunction(route['view'])) {
			if (!view) {
				var viewParams = getViewParams.call(this, route, true);
				view = this.currentView = this.views[route['name']] = new route['view']();
				Core.initiate.call(view, viewParams);
				view.setOnReadyHandler(onViewReady.bind(this));
				var viewContentElement = createViewContentElement.call(this);
				view.render(viewContentElement);
				view.initControllers();
			} else {
				activateView.call(this, view, true, isSameView);
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
	var initRouter = function() {
		Router.setNavigationHandler(handleNavigation.bind(this));
		Router.init();
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
		var parentElement = view._getParentElement();
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
				var titleParams = this.currentView.getTitleParams();
				if (isObject(titleParams)) {
					var regExp;
					for (var k in titleParams) {
						regExp = new RegExp("\\$" + k)
						title = title.replace(regExp, titleParams[k]);
					}
				}
			}
			this.setPageTitle(title || defaultPagetitle || '');
		}
	};
	var createViewContentElement = function() {
		var element = document.createElement('div');
		this.viewContainer.appendChild(element);
		return element;
	};
	Application.prototype.initiate = function() {
		this.views = {};
	};
	Application.prototype.run = function() {
		this.element = document.createElement('div');
		document.body.appendChild(this.element);
		this.render(this.element);
		createViewContainer.call(this);
		defineViews.call(this);
		initRouter.call(this);
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
	Application.prototype.getView = function(viewName) {
		return this.views[viewName];
	};
	Application.prototype.disposeView = function(viewName) {
		if (isObject(this.views[viewName])) {
			this.views[viewName].dispose();
			this.views[viewName] = null;
		}
	};
	Application.prototype.onNoErrors = function() {};
	Application.prototype.onError = function(errorCode) {};
}
Application();