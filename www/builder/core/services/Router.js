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