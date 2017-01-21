var Core = new (function() {
	var extendInitials = function(initials1, initials2) {
		if (isNull(initials1)) {
			initials1 = initials2;
		} else {
			for (var k in initials2) {
				if (isUndefined(initials1[k])) {
					initials1[k] = initials2[k];
				} else {
					if (isObject(initials1[k]) || isObject(initials2[k])) Objects.merge(initials1[k], initials2[k]);
					else if (isArray(initials1[k]) || isArray(initials2[k])) Objects.concat(initials1[k], initials2[k]);
				}
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
					} else if (k == 'options') {
						if (isObject(this.options)) Objects.merge(this.options, initials[k]);
						else this.options = initials[k];
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
			var data;
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
		var helpers = getInitial.call(this, 'helpers');
		if (isArray(helpers)) {
			for (var i = 0; i < helpers.length; i++) subscribeToHelper.call(this, helpers[i]);
		}
		var listeners = getInitial.call(this, 'listeners');
		var s = State;
		if (isObject(listeners)) {			
			for (var j in listeners) s.listen(this, j, listeners[j]);
		} 
		var globals = getInitial.call(this, 'globals');
		if (isObject(globals)) {
			for (var j in globals) s.subscribe(this, j, globals[j]);
		}
	};
	var subscribeToHelper = function(options) {
		if (isObject(options['options'])) options['helper'].subscribe(this, options['options']);
	};
	var isProperMethod = function(child, parent, method) {
		if (!!child.prototype[method]) return false;
		return parent.prototype[method] != parent.prototype.initiate && parent.prototype[method] != parent.prototype.getInitials;
	}
	this.inherits = function(list) {
		var children, parent, child, initials, sc;
		for (var k = 0; k < list.length; k++) {
			parent = list[k];
			children = list[++k];
			for (var i = 0; i < children.length; i++) {
				child = children[i];
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
	this.initiate = function(props, opts) {
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
		if (opts) this.options = opts;
		Core.processInitials.call(this);
	};
	this.getNextSiblingChild = function() {
		if (!this.nextSiblingChild) return null;
		if (this.nextSiblingChild instanceof Node) return this.nextSiblingChild;
		var firstNodeChild = Core.getFirstNodeChild.call(this.nextSiblingChild);
		if (firstNodeChild) return firstNodeChild;
		return Core.getNextSiblingChild.call(this.nextSiblingChild, this);	
	};
	this.setNextSiblingChild = function(nextSiblingChild) {
		this.nextSiblingChild = nextSiblingChild;
		if (!(nextSiblingChild instanceof Node)) Core.setPrevSiblingChild.call(this.nextSiblingChild, this);
	};
	this.setPrevSiblingChild = function(prevSiblingChild) {
		this.prevSiblingChild = prevSiblingChild;
	};
	this.disposeLinks = function() {
		if (this.prevSiblingChild) Core.setNextSiblingChild.call(this.prevSiblingChild, this.nextSiblingChild);
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
		if (isControl(child)) Core.unregisterControl.call(this, child);
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
});