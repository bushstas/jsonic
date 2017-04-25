<?php

	$data = array(
		'name' => 'Application',
		'condition' => CONST_ENTERCOND,
		'before' => "
			var controllers, dictionary;
		",
		'privateMethods' => array(
			'getViewParams' => array(
				'args' => array('route', 'allParams'),
				'body' => "
					var params;
					if (isObject(route['dynamicParams'])) {
						params = {};
						for (var k in route['dynamicParams']) {
							params[k] = ".CONST_ROUTER.".getPathPartAt(route['dynamicParams'][k]);
						}
					}
					if (allParams) {
						if (isObject(params)) {
							".CONST_OBJECTS.".merge(params, route['params']);
						} else {
							params = route['params'];
						}
					}
					return params;
				"
			),
			'loadControllers' => array(
				'args' => array('route'),
				'body' => "
					if (isArray(route['load']) || isNumber(route['load'])) {
						controllers.load(route['load']);
					}
				"
			),
			'loadView' => array(
				'args' => array('route'),
				'body' => "
					var script = document.createElement('script');
					script.src = '/js/".CONST_JSBASE."_' + route['name'] + '_chunk.js';
					document.body.appendChild(script);
					script.onload = onViewLoaded.bind(this, route);
				"
			),
			'onViewLoaded' => array(
				'args' => array('route'),
				'body' => "
					route['view'] = ".CONST_GLOBAL.".get(route['view']);
					renderView.call(this, route);
				"
			),
			'activateView' => array(
				'args' => array('view', 'isSameView'),
				'body' => "
					if (!view) return;
					var parentElement = ".CONST_CORE.".getParentElement.call(view);
					var params = getViewParams.call(this, this.currentRoute);
					if (isObject(params)) {
						view.set(params);
					}
					if (!isSameView) {
						this.viewContainer.appendChild(parentElement);
					}
					view.activate(true);	
				"
			),
			'disactivateView' => array(
				'body' => "
					var view = this.views[this.currentView];
					if (view) {
						var parentElement = ".CONST_CORE.".getParentElement.call(view);			
						this.viewContainer.removeChild(parentElement);
						view.activate(false);
					}
				"
			),
			'renderView' => array(
				'args' => array('route'),
				'body' => "
					loadControllers(route);
					if (!isUndefined(dictionary)) {
						dictionary.load(route['name']);
					}
					var view = this.views[route['name']] = new route['view']();
					var viewParams = getViewParams.call(this, route, true);
					".CONST_CORE.".initiate.call(view, viewParams);
					view.setOnReadyHandler(onViewReady.bind(this));
					var viewContentElement = createViewContentElement.call(this, route['name']);
					view.render(viewContentElement);
					if (isNumber(route['error'])) {
						this.onError(route['error']);
					} else {
						this.onNoErrors();
					}	
				"
			),
			'handleNavigation' => array(
				'args' => array('route', 'changeTitle'),
				'body' => "
					if (this.currentRoute && route['name'] != this.currentRoute) {
						disactivateView.call(this);
					}
					this.isChangeTitle = changeTitle;
					this.currentRoute = route;
					var isSameView = this.currentView == route['name'];
					this.currentView = route['name'];
					var view = this.views[route['name']];
					if (!view) {
						view = ".CONST_GLOBAL.".get(route['view']);
						if (!view) {
							loadView.call(this, route);
						} else {
							route['view'] = view;
							renderView.call(this, route);
						}
					} else {
						activateView.call(this, view, isSameView);
					}
				"
			),
			'defineViews' => array(
				'body' => "
					for (var i = 0; i < ".CONST_ROUTES.".length; i++) {
						this.views[".CONST_ROUTES."[i]['name']] = null;
						if (isArray(".CONST_ROUTES."[i]['children'])) {
							this.defineViews(".CONST_ROUTES."[i]['children']);
						}
					}
					if (isObject(".CONST_ERRORROUTES.")) {
						for (var k in ".CONST_ERRORROUTES.") {
							this.views[k] = null;
						}
					}
				"
			),
			'createViewContainer' => array(
				'body' => "
					var viewContainer;
					if (".CONST_VIEWCONTAINER.") {
						viewContainer = document.body.querySelector('.' + ".CONST_VIEWCONTAINER.");
					}
					if (!viewContainer) {
					 	viewContainer = document.createElement('div');
						if (".CONST_VIEWCONTAINER.") {
							viewContainer.className = ".CONST_VIEWCONTAINER.";
						}
						this.element.appendChild(viewContainer);
					}
					this.viewContainer = viewContainer;
				"
			),
			'onViewReady' => array(
				'body' => "
					if (this.isChangeTitle) {
						var title = this.currentRoute['title'];
						if (isString(title)) {
							var titleParams = this.views[this.currentView].getTitleParams();
							if (isObject(titleParams)) {
								var regExp;
								for (var k in titleParams) {
									regExp = new RegExp(\"\\$\" + k);
									title = title.replace(regExp, titleParams[k]);
								}
							}
						}
						this.setPageTitle(title || ".CONST_PAGETITLE." || '');
					}
				"
			),
			'createViewContentElement' => array(
				'args' => array('name'),
				'body' => "
					var element = document.createElement('div');
					element.className = ".CONST_PARENTALVIEWCNT.";
					element.setData('name', name);
					this.viewContainer.appendChild(element);
					return element;
				"
			)
		),
		'methods' => array(
			'initiate' => array(
				'body' => "
					this.views = {};
				"
			),
			'run' => array(
				'body' => "
					dictionary = ".CONST_GLOBAL.".get('Dictionary');
					controllers = ".CONST_GLOBAL.".get('Controllers');
					defineViews.call(this);
					".CONST_ROUTER.".setNavigationHandler(handleNavigation.bind(this));
					".CONST_ROUTER.".init();
					this.element = document.createElement('div');
					document.body.appendChild(this.element);
					this.render(this.element);
					createViewContainer.call(this);
					".CONST_ROUTER.".run();
				"
			),
			'setPageTitle' => array(
				'args' => array('title'),
				'body' => "
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
				"
			),
			'getView' => array(
				'args' => array('viewName'),
				'body' => "
					return this.views[viewName];
				"
			),
			'disposeView' => array(
				'args' => array('viewName'),
				'body' => "
					if (isObject(this.views[viewName])) {
						this.views[viewName].dispose();
						this.views[viewName] = null;
					}
				"
			),
			'onNoErrors' => array(
				'body' => ""
			),
			'onError' => array(
				'body' => ""
			)
		),
		'overridableMethods' => array(
			'onNoErrors', 'onError'
		),
		'templateCallableMethods' => array()
	);

?>