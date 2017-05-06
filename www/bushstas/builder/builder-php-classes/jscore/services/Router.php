<?php

	$data = array(
		'name' => CONST_ROUTER,
		'mode' => 3,
		'before' => "
			var properRoutes = {},
				routes = ".CONST_ROUTES.",
				isHashRouter = !!".CONST_HASHROUTER.",
				defaultRoute = ".CONST_DEFAULTROUTE.",
				indexRoute = ".CONST_INDEXROUTE.",
				errorRoutes = ".CONST_ERRORROUTES.",
				handler, bodyElement, menues, currentRoute,
				subscribers, pathParts;
		",
		'privateMethods' => array(
			'getRoute' => array(
				'body' => "
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
				"
			),
			'initRoutes' => array(
				'args' => array('rts', 'parents'),
				'body' => "
					parents = parents || [];
					var tempParents = ".CONST_OBJECTS.".clone(parents);
					var name, path;
					for (var i = 0; i < rts.length; i++) {
						name = rts[i]['name'];
						tempParents.push(name);
						var children = ".CONST_OBJECTS.".clone(rts[i]['children']);
						delete rts[i]['children'];
						path = rts[i]['path'] = tempParents.join('/');
						initRouteParams(rts[i]);
						properRoutes[path] = rts[i];
						if (isArray(children)) {
							initRoutes(children, ".CONST_OBJECTS.".clone(tempParents));
						}
						tempParents = ".CONST_OBJECTS.".clone(parents);
					}
				"
			),
			'initRouteParams' => array(
				'args' => array('route'),
				'body' => "
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
				"
			),
			'onNavigate' => array(
				'args' => array(''),
				'body' => "
					var route = getRoute();
					bodyElement.setClass(route['name'] + '-page');
					var accessLevel = route['accessLevel'];
					if (isNumber(accessLevel) && !".CONST_USER.".hasAccessLevel(accessLevel)) {
						route = getErrorRoute(401);
					}
					changeRoute(route, true);
				"
			),
			'getRouteByName' => array(
				'args' => array('viewName'),
				'body' => "
					for (var k in properRoutes) {
						if (properRoutes[k]['name'] == viewName) {
							return properRoutes[k];
						}
					}
				"
			),
			'getErrorRoute' => array(
				'args' => array('errorCode'),
				'body' => "
					if (isString(errorRoutes[errorCode])) {
						errorRoutes[errorCode] = {'name': errorCode, 'view': errorRoutes[errorCode], 'error': errorCode};
					}
					return errorRoutes[errorCode];	
				"
			),
			'changeRoute' => array(
				'args' => array('route', 'changeTitle'),
				'body' => "
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
				"
			),
			'replaceState' => array(
				'args' => array('route'),
				'body' => "
					if (isHashRouter) {
						window.history.replaceState({}, '', '#' + route['path']);
					} else {
						window.location.href = '/' + route['path'];
					}
				"
			),
			'informSubscribers' => array(
				'body' => "
					if (isArray(subscribers)) {
						var subscrView, opts, subscriber;
						for (var i = 0; i < subscribers.length; i++) {
							subscrView = subscribers[i][0];
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
				"
			),
			'informMenues' => array(
				'args' => array(''),
				'body' => "
					if (isArray(menues)) {
						for (var i = 0; i < menues.length; i++) {
							menues[i].onNavigate(currentRoute['name']);
						}
					}
				"
			)
		),
		'thisMethods' => array(
			'setNavigationHandler' => array(
				'args' => array('h'),
				'body' => "
					handler = h;
				"
			),
				'init' => array(
				'body' => "
					bodyElement = document.querySelector('body');
					initRoutes(routes);
					if (isHashRouter) {
						window.addEventListener('popstate', onNavigate.bind(this));
					}
				"
			),
				'run' => array(
				'body' => "
					onNavigate();
				"
			),
				'getPathPartAt' => array(
				'args' => array('index'),
				'body' => "
					return isArray(pathParts) ? pathParts[index] : '';
				"
			),
				'reload' => array(
				'body' => "
					window.location.reload();
				"
			),
				'redirect' => array(
				'args' => array('viewName', 'replState'),
				'body' => "
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
				"
			),
				'subscribe' => array(
				'args' => array('options', 'controller'),
				'body' => "
					var routeName = currentRoute['name'];
					subscribers = subscribers || [];
					subscribers.push([routeName, options, controller]);
				"
			),
				'addMenu' => array(
				'args' => array('menu'),
				'body' => "
					if (isObject(menu) && isFunction(menu.onNavigate)) {
						menues = menues || [];
						menues.push(menu);
					}
				"
			),
				'hasMenu' => array(
				'args' => array('menu'),
				'body' => "
					return menues.indexOf(menu) > -1;
				"
			),
				'getCurrentRoute' => array(
				'body' => "
					return currentRoute || getRoute();
				"
			),
				'getCurrentRouteName' => array(
				'body' => "
					if (currentRoute) return currentRoute['name'];
				"
			)
		)
	);
?>