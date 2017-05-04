<?php

	$data = array(
		'mode' => 2,
		'var' => CONST_CORE,
		'name' => CONST_CORE,
		'privateMethods' => array(
			'extendInitials' => array(
				'args' => array('initials1', 'initials2'),
				'body' => "
					if (isNull(initials1)) {
						initials1 = initials2;
					} else {
						for (var k in initials2) {
							if (isUndefined(initials1[k])) initials1[k] = initials2[k];
							else if (isObject(initials1[k]) || isObject(initials2[k])) ".CONST_OBJECTS.".merge(initials1[k], initials2[k]);
							else if (isArray(initials1[k]) || isArray(initials2[k])) ".CONST_OBJECTS.".concat(initials1[k], initials2[k]);				
						}
					}
					return initials1;
				"
			),
			'addProps' => array(
				'args' => array('initialProps'),
				'body' => "
					for (var k in initialProps)	{
						if (isUndefined(this.props[k])) {
							this.props[k] = initialProps[k];
						}
					}
				"
			),
			'processInitials' => array(
				'body' => "
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
				"
			),
			'getInitial' => array(
				'args' => array('initialName'),
				'body' => "
					return ".CONST_OBJECTS.".get(this.initials, initialName);
				"
			),
			'attachController' => array(
				'args' => array('options'),
				'body' => "
					if (isObject(options['on'])) {
						var data, ctr;
						for (var actionName in options['on']) {
							data = {'initiator': this, 'callback': options['on'][actionName]};
							options['controller'].addSubscriber(actionName, data, !!options['private'], ".CONST_OBJECTS.".get(options['options'], actionName));
						}
					}
				"
			),
			'addCorrector' => array(
				'args' => array('name', 'handler'),
				'body' => "
					if (isFunction(handler)) {
						this.correctors = this.correctors || {};
						this.correctors[name] = handler;
					}
				"
			),
			'addFollower' => array(
				'args' => array('name', 'handler'),
				'body' => "
					if (isFunction(handler)) {
						this.followers = this.followers || {};
						this.followers[name] = handler;
					}
				"
			),
			'subscribeToHelper' => array(
				'args' => array('options'),
				'body' => "
					var helper = ".CONST_GLOBAL.".get(options['helper']);
					if (helper && isObject(options['options'])) helper.subscribe(this, options['options']);
				"
			),
			'isProperMethod' => array(
				'args' => array('child', 'parent', 'method'),
				'body' => "
					if (!!child.prototype[method]) return false;
					return parent.prototype[method] != parent.prototype.initiate && parent.prototype[method] != parent.prototype.getInitials;
				"
			),
			'addUpdater' => array(
				'args' => array('u', 'l'),
				'body' => "
					this.updaters = this.updaters || {};
					var keys = u.getKeys();
					for (var i = 0; i < keys.length; i++) {
						this.updaters[keys[i]] = this.updaters[keys[i]] || [];
						l.push(keys[i], u);
						this.updaters[keys[i]].push(u);
					}
				"
			)
		),
		'thisMethods' => array(
			'processPostRenderInitials' => array(
				'body' => "
					var events = getInitial.call(this, 'events');
					if (isObject(events)) {
						var mh = ".CONST_GLOBAL.".get('MouseHandler');
						this.mouseHandler = new mh(this, events);
					}
					var helpers = getInitial.call(this, 'helpers');
					if (isArray(helpers)) {
						for (var i = 0; i < helpers.length; i++) subscribeToHelper.call(this, helpers[i]);
					}
					var listeners = getInitial.call(this, 'listeners');
					var s = ".CONST_GLOBAL.".get('State');
					if (isObject(listeners)) {			
						for (var j in listeners) s.listen(this, j, listeners[j]);
					} 
					var globals = getInitial.call(this, 'globals');
					if (isObject(globals)) {
						for (var j in globals) s.subscribe(this, j, globals[j]);
					}
				"
			),
			'inherits' => array(
				'args' => array('list'),
				'body' => "
					var children, parent, child, initials, sc;
					for (var k = 0; k < list.length; k++) {
						parent = ".CONST_GLOBAL.".get(list[k]);
						children = list[++k];
						for (var i = 0; i < children.length; i++) {
							child = ".CONST_GLOBAL.".get(children[i]);
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
				"
			),
			'initiate' => array(
				'args' => array('props'),
				'body' => "
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
					if (isObject(this.props)) ".CONST_OBJECTS.".merge(this.props, props);
					else this.props = props || {};
					if (isFunction(proto.initiate)) {
						proto.initiate.call(this);
					}
					this.initials = initials;
					processInitials.call(this);
				"
			),
			'getNextSiblingChild' => array(
				'body' => "
					if (!this.nextSiblingChild) return null;
					if (this.nextSiblingChild instanceof Node) return this.nextSiblingChild;
					var firstNodeChild = ".CONST_CORE.".getFirstNodeChild.call(this.nextSiblingChild);
					if (firstNodeChild) return firstNodeChild;
					return ".CONST_CORE.".getNextSiblingChild.call(this.nextSiblingChild, this);
				"
			),
			'setNextSiblingChild' => array(
				'args' => array('nextSiblingChild'),
				'body' => "
					this.nextSiblingChild = nextSiblingChild;
					if (!(nextSiblingChild instanceof Node)) ".CONST_CORE.".setPrevSiblingChild.call(this.nextSiblingChild, this);
				"
			),
			'setPrevSiblingChild' => array(
				'args' => array('prevSiblingChild'),
				'body' => "
					this.prevSiblingChild = prevSiblingChild;
				"
			),
			'disposeLinks' => array(
				'body' => "
					if (this.prevSiblingChild) ".CONST_CORE.".setNextSiblingChild.call(this.prevSiblingChild, this.nextSiblingChild);
					this.prevSiblingChild = null;
					this.nextSiblingChild = null;
				"
			),
			'getFirstNodeChild' => array(
				'body' => "
					if (this.levels) return this.levels[0].getFirstNodeChild();
					if (this.level) return this.level.getFirstNodeChild();
					return null;
				"
			),
			'getWaitingChild' => array(
				'args' => array('componentName'),
				'body' => "
					return ".CONST_OBJECTS.".get(this.waiting, componentName);
				"
			),
			'getTemplateById' => array(
				'args' => array('tmpid'),
				'body' => "
					if (isObject(this.templatesById)) return this.templatesById[tmpid];
					var parents = this.inheritedSuperClasses;
					if (isArrayLike(parents)) {
						for (var i = 0; i < parents.length; i++) {
							if (isObject(parents[i].prototype.templatesById) && isFunction(parents[i].prototype.templatesById[tmpid])) {
								return parents[i].prototype.templatesById[tmpid];
							}
						}
					}
				"
			),
			'subscribe' => array(
				'args' => array('eventType', 'handler', 'subscriber'),
				'body' => "
					this.listeners = this.listeners || [];
					this.listeners.push({'type': eventType, 'handler': handler, 'subscriber': subscriber});
				"
			),
			'registerElement' => array(
				'args' => array('element', 'id'),
				'body' => "
					this.elements = this.elements || {};
					this.elements[id] = element;
				"
			),
			'registerChildComponent' => array(
				'args' => array('child'),
				'body' => "
					this.childrenCount = this.childrenCount || 0;
					this.children = this.children || {};
					this.children[child.getId() || this.childrenCount] = child;
					this.childrenCount++;
				"
			),
			'unregisterChildComponent' => array(
				'args' => array('child'),
				'body' => "
					if (isControl(child)) ".CONST_CORE.".unregisterControl.call(this, child);
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
				"
			),
			'registerControl' => array(
				'args' => array('control', 'name'),
				'body' => "
					this.controls = this.controls || {};
				 	if (!isUndefined(this.controls[name])) {
				 		if (!isArray(this.controls[name])) this.controls[name] = [this.controls[name]];
				 		this.controls[name].push(control);
				 	} else this.controls[name] = control;
				 	control.setName(name);
				"
			),
			'unregisterControl' => array(
				'args' => array('control'),
				'body' => "
					if (this.controls) {
						var name = control.getName();
						if (isArray(this.controls[name])) this.controls[name].removeItem(control);
						else {
							this.controls[name] = null;
							delete this.controls[name];
						}
					}
				"
			),
			'provideWithComponent' => array(
				'args' => array('propName', 'componentName', 'waitingChild'),
				'body' => "
					var cmp = this.getChild(componentName);
					if (cmp) waitingChild.set(propName, cmp);
					else {
						this.waiting = this.waiting || {};
						this.waiting[componentName] = this.waiting[componentName] || [];
						this.waiting[componentName].push([waitingChild, propName]);
					}
				"
			),
			'getParentElement' => array(
				'body' => "
					return this.parentElement;
				"
			),
			'createUpdater' => array(
				'args' => array('u', 'c', 's', 'p', 'l'),
				'body' => "
					var updater = new u(s, p, p['n']);
					addUpdater.call(c, updater, l);
				"
			),			
			'disposeUpdater' => array(
				'args' => array('t', 'u'),
				'body' => "
					if (this.updaters && this.updaters[t]) {
						var i = this.updaters[t].indexOf(u);
						if (i > -1) {
							this.updaters[t][i].dispose();
							this.updaters[t].splice(i, 1);
						}
					}
				"
			),
			'setId' => array(
				'args' => array('id'),
				'body' => "
					this.componentId = id;
				"
			),
			'createLevel' => array(
				'args' => array('items', 'isUpdating', 'index'),
				'body' => "
					var level = new (".CONST_GLOBAL.".get('Level'))(this.parentLevel.getComponent());
					var nextSiblingChild;
					if (isNumber(index) && this.levels[index]) {
						nextSiblingChild = this.levels[index].getFirstNodeChild();
					} else {
						nextSiblingChild = isUpdating ? ".CONST_CORE.".getNextSiblingChild.call(this) : null;
					}
					level.render(items, this.parentElement, this.parentLevel, nextSiblingChild);
					this.levels.insertAt(level, index);
				"
			),
			'initOperator' => array(
				'args' => array('pe', 'pl'),
				'body' => "
					this.parentElement = pe;
					this.parentLevel = pl;
					this.levels = [];
				"
			),
			'disposeLevels' => array(
				'body' => "
					for (var i = 0; i < this.levels.length; i++) {
						this.levels[i].dispose();
					}
					this.levels = [];
				"
			),
			'disposeOperator' => array(
				'body' => "
					".CONST_CORE.".disposeLinks.call(this);
					".CONST_CORE.".disposeLevels.call(this);
					this.levels = null;
					this.parentElement = null;
					this.parentLevel = null;
					this.params = null;
				"
			)
		)
	);
?>